<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_dapat_melihat_halaman_login()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function user_dapat_login_dengan_kredensial_yang_benar()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_tidak_dapat_login_dengan_password_salah()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_benar'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password_salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function validasi_email_dan_password_wajib_diisi()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function rate_limiting_berfungsi_setelah_lima_kali_percobaan_gagal()
    {
        $email = 'user@example.com';

        // Lakukan percobaan gagal sebanyak 5 kali
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $email,
                'password' => 'wrong-password',
            ]);
        }

        // Percobaan ke-6 harusnya diblokir oleh RateLimiter
        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login', session('errors')->get('email')[0]);
    }

    /** @test */
    public function user_dapat_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}