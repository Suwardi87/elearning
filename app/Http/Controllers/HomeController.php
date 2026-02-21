<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Halaman awal platform.
     * Data yang dikirim di sini akan diterima sebagai props di Vue.
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'heroTitle' => 'Belajar Laravel + Vue dengan Project-Based Learning',
            'heroDescription' => 'Bangun produk nyata sambil memahami alur MVC + Inertia tanpa API REST terpisah.',
            'learningSteps' => [
                'Mulai dari requirement dan desain fitur.',
                'Implementasi backend Laravel (route, controller, model).',
                'Render halaman dengan Inertia + Vue 3 Composition API.',
                'Iterasi produk sampai siap dipublikasikan.',
            ],
        ]);
    }
}
