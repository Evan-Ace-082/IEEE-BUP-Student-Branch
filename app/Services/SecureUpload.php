<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SecureUpload
{
    public static function image(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        self::assertSafeClientName($file);

        $path = $file->getRealPath();
        if ($path === false || ! is_file($path)) {
            throw ValidationException::withMessages(['file' => 'The uploaded file could not be read.']);
        }

        $info = @getimagesize($path);
        if ($info === false) {
            throw ValidationException::withMessages(['file' => 'Upload a real JPG, PNG, WEBP, or GIF image.']);
        }

        $ext = match ($info[2]) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            IMAGETYPE_GIF => 'gif',
            default => null,
        };

        if ($ext === null) {
            throw ValidationException::withMessages(['file' => 'This image type is not allowed.']);
        }

        if (($info[0] ?? 0) < 1 || ($info[1] ?? 0) < 1 || $info[0] > 8000 || $info[1] > 8000) {
            throw ValidationException::withMessages(['file' => 'Image dimensions must be between 1 and 8000 pixels.']);
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path) ?: '';
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages(['file' => 'The file content is not an allowed image.']);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'Images must be 2 MB or smaller.']);
        }

        $binary = self::resizedBinary($path, (int) $info[2], (int) $info[0], (int) $info[1]);
        $name = Str::uuid()->toString().'.'.$ext;
        $relative = trim($directory, '/').'/'.$name;

        Storage::disk($disk)->put($relative, $binary);

        return $relative;
    }

    public static function pdf(UploadedFile $file, string $directory): string
    {
        self::assertSafeClientName($file);

        $path = $file->getRealPath();
        if ($path === false || ! is_file($path)) {
            throw ValidationException::withMessages(['file' => 'The uploaded file could not be read.']);
        }

        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            throw ValidationException::withMessages(['file' => 'Upload a PDF file.']);
        }

        if ($file->getSize() > 8 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'PDF files must be 8 MB or smaller.']);
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path) ?: '';
        $head = (string) file_get_contents($path, false, null, 0, 1024);
        if (! str_starts_with($head, '%PDF-') || str_contains($head, '<?php') || str_contains(strtolower($head), '<script')) {
            throw ValidationException::withMessages(['file' => 'The PDF file is not valid.']);
        }

        if (! in_array($mime, ['application/pdf', 'application/octet-stream'], true)) {
            throw ValidationException::withMessages(['file' => 'Upload a PDF file.']);
        }

        $name = Str::uuid()->toString().'.pdf';
        $relative = trim($directory, '/').'/'.$name;
        Storage::disk('local')->put($relative, (string) file_get_contents($path));

        return $relative;
    }

    public static function document(UploadedFile $file, string $directory): string
    {
        self::assertSafeClientName($file);

        $path = $file->getRealPath();
        if ($path === false || ! is_file($path)) {
            throw ValidationException::withMessages(['file' => 'The uploaded file could not be read.']);
        }

        if ($file->getSize() > 8 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'Documents must be 8 MB or smaller.']);
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path) ?: '';
        $map = [
            'pdf' => ['application/pdf'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        ];

        if (! isset($map[$ext]) || ! in_array($mime, $map[$ext], true)) {
            throw ValidationException::withMessages(['file' => 'Upload a PDF, DOCX, or PPTX file.']);
        }

        $head = (string) file_get_contents($path, false, null, 0, 1024);
        if (str_contains($head, '<?php') || str_contains(strtolower($head), '<script')) {
            throw ValidationException::withMessages(['file' => 'This file was rejected by the security check.']);
        }

        if ($ext === 'pdf' && ! str_starts_with($head, '%PDF-')) {
            throw ValidationException::withMessages(['file' => 'The PDF file is not valid.']);
        }

        $binary = (string) file_get_contents($path);
        $name = Str::uuid()->toString().'.'.$ext;
        $relative = trim($directory, '/').'/'.$name;
        Storage::disk('local')->put($relative, $binary);

        return $relative;
    }

    public static function delete(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    private static function assertSafeClientName(UploadedFile $file): void
    {
        $name = $file->getClientOriginalName();
        if (str_contains($name, "\0") || preg_match('/\.(php|phtml|phar|exe|js|html|svg|htaccess)(\.|$)/i', $name)) {
            throw ValidationException::withMessages(['file' => 'This file name is not allowed.']);
        }
    }

    private static function resizedBinary(string $path, int $type, int $width, int $height): string
    {
        $max = 1600;
        $create = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            IMAGETYPE_GIF => imagecreatefromgif($path),
            default => false,
        };

        if ($create === false) {
            return (string) file_get_contents($path);
        }

        if ($width <= $max && $height <= $max) {
            imagedestroy($create);

            return (string) file_get_contents($path);
        }

        $ratio = min($max / $width, $max / $height);
        $newW = max(1, (int) round($width * $ratio));
        $newH = max(1, (int) round($height * $ratio));
        $canvas = imagecreatetruecolor($newW, $newH);

        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $clear = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $newW, $newH, $clear);
        }

        imagecopyresampled($canvas, $create, 0, 0, 0, 0, $newW, $newH, $width, $height);
        imagedestroy($create);

        ob_start();
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($canvas, null, 85),
            IMAGETYPE_PNG => imagepng($canvas, null, 6),
            IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($canvas, null, 85) : imagejpeg($canvas, null, 85),
            IMAGETYPE_GIF => imagegif($canvas),
            default => imagejpeg($canvas, null, 85),
        };
        imagedestroy($canvas);

        return (string) ob_get_clean();
    }
}
