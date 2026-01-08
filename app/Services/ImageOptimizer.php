<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Optimise une image uploadée : redimensionne et convertit en WebP
     *
     * @param UploadedFile $file
     * @param string $disk Le disque de stockage (r2, public, etc.)
     * @param string $directory Le répertoire de destination
     * @param int $maxWidth Largeur maximale en pixels
     * @param int $quality Qualité WebP (0-100)
     * @return string Le chemin du fichier optimisé
     */
    public function optimize(
        UploadedFile $file,
        string $disk = 'r2',
        string $directory = 'images',
        int $maxWidth = 1920,
        int $quality = 80
    ): string {
        // Créer une image depuis le fichier uploadé
        $image = $this->createImageFromFile($file);

        if (!$image) {
            // Si on ne peut pas traiter l'image, l'uploader telle quelle
            return $file->store($directory, $disk);
        }

        // Obtenir les dimensions originales
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculer les nouvelles dimensions en conservant le ratio
        if ($originalWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Créer une nouvelle image redimensionnée
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Préserver la transparence pour PNG
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        // Redimensionner
        imagecopyresampled(
            $resizedImage,
            $image,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Générer un nom unique pour le fichier WebP
        $filename = uniqid() . '.webp';
        $tempPath = sys_get_temp_dir() . '/' . $filename;

        // Sauvegarder en WebP
        imagewebp($resizedImage, $tempPath, $quality);

        // Libérer la mémoire
        imagedestroy($image);
        imagedestroy($resizedImage);

        // Uploader sur le disque de stockage
        $storedPath = Storage::disk($disk)->putFileAs(
            $directory,
            new \Illuminate\Http\File($tempPath),
            $filename,
            'public'
        );

        // Nettoyer le fichier temporaire
        @unlink($tempPath);

        return $storedPath;
    }

    /**
     * Crée une ressource image GD depuis un fichier uploadé
     *
     * @param UploadedFile $file
     * @return \GdImage|false
     */
    private function createImageFromFile(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        $path = $file->getRealPath();

        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
            default => false,
        };
    }

    /**
     * Optimise une image existante sur un disque
     *
     * @param string $path Chemin du fichier sur le disque
     * @param string $disk Le disque de stockage
     * @param int $maxWidth Largeur maximale en pixels
     * @param int $quality Qualité WebP (0-100)
     * @return string|null Le nouveau chemin ou null en cas d'échec
     */
    public function optimizeExisting(
        string $path,
        string $disk = 'r2',
        int $maxWidth = 1920,
        int $quality = 80
    ): ?string {
        try {
            // Télécharger le fichier temporairement
            $content = Storage::disk($disk)->get($path);
            $tempPath = sys_get_temp_dir() . '/' . basename($path);
            file_put_contents($tempPath, $content);

            // Créer une image depuis le fichier
            $image = $this->createImageFromPath($tempPath);

            if (!$image) {
                @unlink($tempPath);
                return null;
            }

            // Obtenir les dimensions
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calculer nouvelles dimensions
            if ($originalWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Redimensionner
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);

            imagecopyresampled(
                $resizedImage,
                $image,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // Générer nom WebP
            $pathInfo = pathinfo($path);
            $webpFilename = $pathInfo['filename'] . '.webp';
            $webpTempPath = sys_get_temp_dir() . '/' . $webpFilename;

            // Sauvegarder en WebP
            imagewebp($resizedImage, $webpTempPath, $quality);

            // Libérer mémoire
            imagedestroy($image);
            imagedestroy($resizedImage);

            // Upload sur le disque
            $directory = dirname($path);
            $storedPath = Storage::disk($disk)->putFileAs(
                $directory,
                new \Illuminate\Http\File($webpTempPath),
                $webpFilename,
                'public'
            );

            // Nettoyer
            @unlink($tempPath);
            @unlink($webpTempPath);

            return $storedPath;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Crée une ressource GD depuis un chemin de fichier
     */
    private function createImageFromPath(string $path)
    {
        $imageInfo = @getimagesize($path);
        if (!$imageInfo) {
            return false;
        }

        $mimeType = $imageInfo['mime'];

        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
            default => false,
        };
    }
}
