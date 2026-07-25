<?php

namespace Mca\Settings\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

final class ImageUploadField
{
    public static function isAvailable(): bool
    {
        return function_exists('mca_upload');
    }

    public static function isImageUploadWidget(string $widget): bool
    {
        return $widget === 'image_upload';
    }

    /**
     * File input uses uploads[setting.key] so the key may contain dots.
     * Do not use Request::file('uploads.branding.favicon') — that nests by segment.
     */
    public static function uploadedFile(Request $request, string $key): ?UploadedFile
    {
        $uploads = $request->file('uploads');

        if (! is_array($uploads)) {
            return null;
        }

        $file = $uploads[$key] ?? null;

        return $file instanceof UploadedFile ? $file : null;
    }

    /** @return list<string|\Illuminate\Contracts\Validation\ValidationRule> */
    public static function pathRules(): array
    {
        return ['nullable', 'string', 'max:500'];
    }

    /** @return list<string|\Illuminate\Contracts\Validation\ValidationRule> */
    public static function fileRules(): array
    {
        return ['nullable', 'file', 'max:5120'];
    }

    /**
     * Apply uploaded files onto the filtered settings payload (path strings).
     *
     * @param  array<string, mixed>  $filtered
     * @param  list<array<string, mixed>>  $groupItems
     * @return array<string, mixed>
     */
    public static function applyUploads(Request $request, array $filtered, array $groupItems): array
    {
        if (! self::isAvailable()) {
            return $filtered;
        }

        foreach ($groupItems as $item) {
            $key = (string) ($item['key'] ?? '');
            $widget = (string) ($item['widget'] ?? '');

            if ($key === '' || ! self::isImageUploadWidget($widget)) {
                continue;
            }

            $file = self::uploadedFile($request, $key);

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $old = $filtered[$key] ?? null;
            if (! is_string($old) || $old === '') {
                $old = function_exists('mca_setting') ? mca_setting($key) : null;
            }
            $oldPath = is_string($old) ? $old : null;

            try {
                $stored = mca_upload()->replace($file, $oldPath, $key);
            } catch (InvalidArgumentException $e) {
                throw ValidationException::withMessages([
                    'uploads.'.$key => $e->getMessage(),
                ]);
            } catch (Throwable $e) {
                throw ValidationException::withMessages([
                    'uploads.'.$key => $e->getMessage() !== ''
                        ? $e->getMessage()
                        : 'Dosya yüklenemedi.',
                ]);
            }

            $filtered[$key] = $stored->path;
        }

        return $filtered;
    }
}
