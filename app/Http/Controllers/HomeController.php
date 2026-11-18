<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $penerbit = $request->input('penerbit');
        $status   = $request->input('status');
        $sort     = $request->input('sort');

        $buku = Buku::query()
            // 1. Pencarian: judul, penulis, atau penerbit
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%")
                      ->orWhere('penerbit', 'like', "%{$search}%");
                });
            })
            // 2a. Filter penerbit
            ->when($penerbit, function ($query, $penerbit) {
                $query->where('penerbit', $penerbit);
            })
            // 2b. Filter ketersediaan stok
            ->when($status === 'tersedia', function ($query) {
                $query->where('stok', '>', 0);
            })
            ->when($status === 'habis', function ($query) {
                $query->where('stok', '<=', 0);
            })
            // 3. Pengurutan (default: terbaru), nilainya sama dengan dropdown di home.blade.php
            ->when(true, function ($query) use ($sort) {
                match ($sort) {
                    'judul_asc'  => $query->orderBy('judul', 'asc'),
                    'judul_desc' => $query->orderBy('judul', 'desc'),
                    'tahun_desc' => $query->orderBy('tahun_terbit', 'desc'),
                    'tahun_asc'  => $query->orderBy('tahun_terbit', 'asc'),
                    'stok_desc'  => $query->orderBy('stok', 'desc'),
                    default      => $query->latest(),
                };
            })
            // 4. Paginasi 8 data, parameter URL tetap terbawa saat pindah halaman
            ->paginate(8)
            ->withQueryString();

        // 5. Daftar penerbit unik untuk dropdown filter
        $listPenerbit = Buku::query()
            ->whereNotNull('penerbit')
            ->distinct()
            ->orderBy('penerbit')
            ->pluck('penerbit');

        // 6. Kirim ke view
        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        return view('home_show', compact('buku'));
    }
}