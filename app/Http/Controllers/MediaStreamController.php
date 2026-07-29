<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaStreamController extends Controller
{
    public function stream($id)
    {
        $media = Media::find($id);

        if (!$media) {
            abort(404, 'Media tidak ditemukan.');
        }

        $path = $media->getPath();

        if (file_exists($path)) {
            return response()->file($path, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        abort(404, 'File media tidak ditemukan di server.');
    }
}
