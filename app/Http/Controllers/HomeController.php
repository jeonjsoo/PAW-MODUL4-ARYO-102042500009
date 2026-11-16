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
        // TODO 3: Lengkapi logika index():
        // 1. Buat query pencarian berdasarkan judul, penulis, atau penerbit jika ada input 'search'
        // 2. Tambahkan filter penerbit dan filter ketersediaan stok jika ada input 'penerbit' / 'status'
        // 3. Tambahkan pengurutan data berdasarkan input 'sort' (judul, tahun, stok, default: latest)
        // 4. Lakukan paginasi 8 data dengan withQueryString()
        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        // 6. Return view 'home' dengan data buku dan list penerbit

        $query = Buku::query();

        // Pencarian berdasarkan judul, penulis, atau penerbit
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan penerbit
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->input('penerbit'));
        }

        // Filter berdasarkan ketersediaan stok
        if ($request->input('status') === 'tersedia') {
            $query->where('stok', '>', 0);
        } elseif ($request->input('status') === 'habis') {
            $query->where('stok', '<=', 0);
        }

        // Pengurutan data
        switch ($request->input('sort')) {
            case 'judul_asc':
                $query->orderBy('judul', 'asc');
                break;
            case 'judul_desc':
                $query->orderBy('judul', 'desc');
                break;
            case 'tahun_desc':
                $query->orderBy('tahun_terbit', 'desc');
                break;
            case 'tahun_asc':
                $query->orderBy('tahun_terbit', 'asc');
                break;
            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $buku = $query->paginate(8)->withQueryString();

        // Ambil daftar penerbit unik untuk dropdown filter
        $listPenerbit = Buku::select('penerbit')
            ->distinct()
            ->orderBy('penerbit', 'asc')
            ->pluck('penerbit');

        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // TODO 3: Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}
