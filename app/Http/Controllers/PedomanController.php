<?php

namespace App\Http\Controllers;

use App\Modules\Core\Models\LandingPageContent;
use Illuminate\Support\Facades\Storage;

class PedomanController extends Controller
{
    public function stream()
    {
        $fileUrl = LandingPageContent::where('key', 'pedoman_file_url')->value('value');
        if (empty($fileUrl)) {
            abort(404, 'Berkas Pedoman PDF belum diunggah.');
        }

        $path = preg_replace('#^/storage/#', '', $fileUrl);

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, 'Buku-Pedoman-Santri-Al-Fithroh.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Buku-Pedoman-Santri-Al-Fithroh.pdf"',
            ]);
        }

        $publicPath = public_path(ltrim($fileUrl, '/'));
        if (file_exists($publicPath)) {
            return response()->file($publicPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Buku-Pedoman-Santri-Al-Fithroh.pdf"',
            ]);
        }

        abort(404, 'File PDF Pedoman Santri tidak ditemukan di storage server.');
    }

    public function download()
    {
        $fileUrl = LandingPageContent::where('key', 'pedoman_file_url')->value('value');
        if (empty($fileUrl)) {
            abort(404, 'Berkas Pedoman PDF belum diunggah.');
        }

        $path = preg_replace('#^/storage/#', '', $fileUrl);

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, 'Buku-Pedoman-Santri-Al-Fithroh.pdf');
        }

        $publicPath = public_path(ltrim($fileUrl, '/'));
        if (file_exists($publicPath)) {
            return response()->download($publicPath, 'Buku-Pedoman-Santri-Al-Fithroh.pdf');
        }

        abort(404, 'File PDF Pedoman Santri tidak ditemukan di storage server.');
    }
}
