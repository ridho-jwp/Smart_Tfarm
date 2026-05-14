<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DeteksiHama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeteksiHamaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::disk('public')->makeDirectory('deteksi');
    }

    /** @test */
    public function ia_menolak_jika_tidak_ada_file_gambar()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyApiKey::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/analisis', []);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Gambar tidak ada'
            ]);
    }

    /** @test */
    public function ia_berhasil_menganalisis_hama_dan_menyimpan_data_ke_database()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyApiKey::class);

        $user = User::factory()->create();

        Http::fake([
            'detect.roboflow.com/*' => Http::response([
                'predictions' => [
                    [
                        'x' => 100,
                        'y' => 100,
                        'width' => 50,
                        'height' => 50,
                        'class' => 'ulat',
                        'confidence' => 0.9
                    ]
                ],
                'image' => [
                    'width' => 640,
                    'height' => 480
                ]
            ], 200)
        ]);

        $file = UploadedFile::fake()->image(
            'tanaman_pakcoy.jpg',
            640,
            480
        );

        $sessionId = 'session_test_999';

        $response = $this->actingAs($user)
            ->postJson('/api/v1/analisis', [
                'image' => $file,
                'session_id' => $sessionId
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'label_status' => 'hama',
                'side_left' => true,
                'side_right' => false,
                'action' => 'PUMP_ON'
            ]);

        $this->assertDatabaseHas('hamadetection', [
            'session_id' => $sessionId,
            'label_hama' => 'hama',
            'is_pestisida_pump' => 1,
            'side_left' => 1,
            'side_right' => 0
        ]);
    }

    /** @test */
    public function ia_dapat_mengecek_status_pompa_terbaru()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyApiKey::class);

        $user = User::factory()->create();

        DeteksiHama::create([
            'session_id' => 'session_unique_123',
            'image_url' => 'http://localhost/storage/deteksi/test.jpg',
            'confidence' => 0.95,
            'is_pestisida_pump' => true,
            'label_hama' => 'hama',
            'side_left' => true,
            'side_right' => false,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/pump-status');

        $response->assertStatus(200)
            ->assertJson([
                'is_pestisida_pump' => true,
                'label_hama' => 'hama',
                'side_left' => true,
                'side_right' => false,
            ]);
    }
}