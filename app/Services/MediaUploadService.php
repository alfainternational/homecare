<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    /**
     * Store all uploaded files for a service request.
     *
     * @param  ServiceRequest  $serviceRequest
     * @param  UploadedFile[]  $files
     * @return int  Number of files stored
     */
    public function storeForRequest(ServiceRequest $serviceRequest, array $files): int
    {
        $stored = 0;
        foreach ($files as $file) {
            if (!($file instanceof UploadedFile) || !$file->isValid()) {
                continue;
            }

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

    private function resolveFileType(UploadedFile $file): string
    {
        return str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
    }
}
