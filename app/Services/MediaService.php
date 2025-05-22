<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;


class MediaService
{
    /**
     * رفع ملف وربطه بأي موديل باستخدام علاقة مورف
     *
     * @param  mixed  $model
     * @param  UploadedFile  $file
     * @param  string  $folder
     * @return \App\Models\Media|null
     */
     /**
     * رفع ملف وربطه بأي موديل باستخدام علاقة مورف
     */
    public static function attach($model, UploadedFile $file, string $folder = 'uploads')
    {
        if (!$model || !$file instanceof UploadedFile) {
            return null;
        }

        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'image') ? 'image' : (str_starts_with($mime, 'video') ? 'video' : 'other');

        // رفع الملف إلى التخزين العام
        $path = $file->store($folder, 'public');

        // إنشاء سجل ميديا مربوط بالموديل
        return $model->media()->create([
            'file' => $path,
            'type' => $type,
        ]);
    }

    /**
     * تحديث ملف ميديا موجود
     */
    public static function updateMedia(Media $media, UploadedFile $file, string $folder = 'uploads')
    {
        // حذف الملف القديم
        Storage::disk('public')->delete($media->file);

        // رفع الملف الجديد
        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'image') ? 'image' : (str_starts_with($mime, 'video') ? 'video' : 'other');
        $path = $file->store($folder, 'public');

        // تحديث بيانات الميديا
        $media->update([
            'file' => $path,
            'type' => $type,
        ]);

        return $media;
    }

    /**
     * حذف ملف ميديا
     */
    public static function deleteMedia(Media $media)
    {
        // حذف الملف من التخزين
        Storage::disk('public')->delete($media->file);

        // حذف السجل من قاعدة البيانات
        $media->delete();
    }
}
