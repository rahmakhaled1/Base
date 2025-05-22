<?php

namespace App\Traits;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;

trait HasMedia
{
    /**
     * العلاقة بين الموديل والميديا (morph)
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * إضافة ملف ميديا للموديل
     */
    public function addMedia(UploadedFile $file, string $folder = 'uploads')
    {
        return MediaService::attach($this, $file, $folder);
    }

    /**
     * تحديث ملف ميديا موجود
     */
    public function updateMedia(int $mediaId, UploadedFile $file, string $folder = 'uploads')
    {
        $media = $this->media()->find($mediaId);
        if ($media) {
            return MediaService::updateMedia($media, $file, $folder);
        }
        return null;
    }

    /**
     * حذف ميديا حسب الـ ID
     */
    public function deleteMedia(int $mediaId)
    {
        $media = $this->media()->find($mediaId);
        if ($media) {
            MediaService::deleteMedia($media);
        }
    }

    /**
     * روابط جميع ملفات الميديا (صور - فيديوهات - الخ)
     */
    public function getMediaUrls()
    {
        return $this->media->map(function ($media) {
            return asset('storage/' . $media->file);
        });
    }
}
