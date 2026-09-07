<?php

namespace App\Modules\Keuangan\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProofImageCompressionService
{
    /**
     * Compress and save uploaded transfer proof image with optimal quality and compact size.
     *
     * @param  UploadedFile|string  $file
     * @param  string  $disk
     * @param  string  $folder
     * @param  int     $maxDimension
     * @param  int     $quality
     * @return array   ['path' => string, 'size' => int, 'original_size' => int]
     */
    public function compressAndStore(
        UploadedFile|string $file,
        string $disk = 'public',
        string $folder = 'transfer-proofs',
        int $maxDimension = 1600,
        int $quality = 80
    ): array {
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $originalSize = is_string($file) ? filesize($file) : $file->getSize();

        // Ensure target folder exists
        Storage::disk($disk)->makeDirectory($folder);

        $filename = 'proof_' . date('Ymd_His') . '_' . Str::random(8) . '.webp';
        $relativeTarget = $folder . '/' . $filename;
        $absoluteTarget = Storage::disk($disk)->path($relativeTarget);

        // Try processing with PHP GD
        if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            $compressed = $this->compressWithGD($filePath, $absoluteTarget, $maxDimension, $quality);
            if ($compressed) {
                return [
                    'path'          => $relativeTarget,
                    'size'          => file_exists($absoluteTarget) ? filesize($absoluteTarget) : 0,
                    'original_size' => $originalSize,
                ];
            }
        }

        // Fallback: standard direct store if GD fails
        if ($file instanceof UploadedFile) {
            $fallbackPath = $file->store($folder, $disk);
        } else {
            $fallbackPath = $folder . '/' . basename($file);
            Storage::disk($disk)->put($fallbackPath, file_get_contents($file));
        }

        return [
            'path'          => $fallbackPath,
            'size'          => Storage::disk($disk)->size($fallbackPath),
            'original_size' => $originalSize,
        ];
    }

    /**
     * Compress using PHP GD with auto-orientation and WebP/JPEG encoding.
     */
    protected function compressWithGD(string $sourcePath, string $targetPath, int $maxDimension, int $quality): bool
    {
        $raw = file_get_contents($sourcePath);
        if (!$raw) {
            return false;
        }

        $sourceImage = @imagecreatefromstring($raw);
        if (!$sourceImage) {
            return false;
        }

        // Correct EXIF Orientation if JPEG
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                $sourceImage = match ($exif['Orientation']) {
                    3 => imagerotate($sourceImage, 180, 0),
                    6 => imagerotate($sourceImage, -90, 0),
                    8 => imagerotate($sourceImage, 90, 0),
                    default => $sourceImage,
                };
            }
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Calculate proportional scale
        $ratio = min($maxDimension / $origWidth, $maxDimension / $origHeight, 1.0);
        $newWidth = (int) round($origWidth * $ratio);
        $newHeight = (int) round($origHeight * $ratio);

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve alpha transparency
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        // Save as WebP if supported, otherwise fallback to JPEG
        $success = false;
        if (function_exists('imagewebp')) {
            $success = imagewebp($resizedImage, $targetPath, $quality);
        } else {
            $jpgTarget = preg_replace('/\.webp$/i', '.jpg', $targetPath);
            $success = imagejpeg($resizedImage, $jpgTarget, $quality);
        }

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $success;
    }
}
