<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing page.
     * Jika sudah login, langsung redirect ke dashboard.
     */
    public function index()
    {
        if (auth()->check()) {
            return redirect('/dashboard');
        }

        return view('landing');
    }
}
