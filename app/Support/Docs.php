<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Private storage for providers' identity and vehicle documents.
 * These files live in a private bucket and are only ever opened through short-lived links.
 */
class Docs
{
    // The provider columns that hold private documents
    public const FIELDS = ['id_image_path', 'id_document', 'vehicle_document'];

    public static function diskName(): string
    {
        return (string) config('filesystems.docs_disk', 'private_docs');
    }

    // False when the private bucket has not been attached yet
    public static function available(): bool
    {
        try {
            Storage::disk(self::diskName());
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function store(UploadedFile $file): string
    {
        return Storage::disk(self::diskName())->putFile('provider-docs', $file);
    }

    // Files uploaded before the private bucket existed sit in the public bucket under "providers/"
    public static function isPrivate(string $path): bool
    {
        return str_starts_with($path, 'provider-docs/');
    }

    public static function temporaryUrl(string $path, int $minutes = 5): string
    {
        return Storage::disk(self::diskName())->temporaryUrl($path, now()->addMinutes($minutes));
    }

    // Deletes files from whichever bucket they are in; a failure never blocks the caller
    public static function deleteAll(array $paths): void
    {
        $private = array_values(array_filter($paths, fn ($p) => $p && self::isPrivate($p)));
        $public = array_values(array_filter($paths, fn ($p) => $p && !self::isPrivate($p)));

        try {
            if ($private && self::available()) {
                Storage::disk(self::diskName())->delete($private);
            }
            if ($public) {
                Storage::disk('s3')->delete($public);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not delete files: ' . $e->getMessage());
        }
    }
}
