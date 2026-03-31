<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    /** Allowed MIME types verified by magic bytes */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm',
    ];

    /** Max sizes: 10 MB for images, 100 MB for videos */
    private const MAX_IMAGE_SIZE = 10 * 1024 * 1024;
    private const MAX_VIDEO_SIZE = 100 * 1024 * 1024;

    /**
     * Store all uploaded files for a service request.
     *
     * @param  ServiceRequest  $serviceRequest
     * @param  UploadedFile[]  $files
     * @return int  Number of files stored
     * @throws \InvalidArgumentException on invalid file
     */
    public function storeForRequest(ServiceRequest $serviceRequest, array $files): int
    {
        $stored = 0;

        foreach ($files as $file) {
            if (!($file instanceof UploadedFile) || !$file->isValid()) {
                continue;
            }

            $this->validateFile($file);

            $path = $file->store('request-media', 'public');

            $serviceRequest->media()->create([
                'uploaded_by' => Auth::id(),
                'path'        => $path,
                'type'        => $this->resolveFileType($file),
            ]);

            $stored++;
        }

        return $stored;
    }

    /**
     * Delete a stored media file and its DB record.
     */
    public function delete(\App\Models\RequestMedia $media): void
    {
        Storage::disk('public')->delete($media->path);
        $media->delete();
    }

    /**
     * Validate file using magic bytes (not just extension/MIME header).
     *
     * @throws \InvalidArgumentException
     */
    private function validateFile(UploadedFile $file): void
    {
        // Read actual magic bytes from file content
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        if (!in_array($realMime, self::ALLOWED_MIME_TYPES, true)) {
            throw new \InvalidArgumentException(
                "نوع الملف غير مسموح به: {$realMime}"
            );
        }

        $isVideo = str_starts_with($realMime, 'video/');
        $maxSize = $isVideo ? self::MAX_VIDEO_SIZE : self::MAX_IMAGE_SIZE;

        if ($file->getSize() > $maxSize) {
            $maxMB = $maxSize / (1024 * 1024);
            throw new \InvalidArgumentException(
                "حجم الملف يتجاوز الحد المسموح به ({$maxMB} ميجابايت)."
            );
        }
    }

    private function resolveFileType(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return str_starts_with($finfo->file($file->getRealPath()), 'video/') ? 'video' : 'image';
    }
}
