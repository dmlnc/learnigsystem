<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Company extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'companies';

    protected $fillable = ['name'];


    public function registerMediaConversions(Media $media = null): void
    {
        $thumbnailWidth  = 300;
        $thumbnailHeight = 100;

        $thumbnailPreviewWidth  = 500;
        $thumbnailPreviewHeight = 300;

        // $previewWidth = 600;
        // $previewHeight = 600;


        $this->addMediaConversion('thumbnail')
            ->width($thumbnailWidth)
            ->height($thumbnailHeight)
            ->fit('fill', $thumbnailWidth, $thumbnailHeight)
            ->background('f0f2f5');
            // ->background('ffffff');

        $this->addMediaConversion('preview_thumbnail')
            ->width($thumbnailPreviewWidth)
            ->height($thumbnailPreviewHeight)
            ->fit('fill', $thumbnailPreviewWidth, $thumbnailPreviewHeight)
            ->background('f0f2f5');


        // $this->addMediaConversion('preview')
        //     ->width($previewWidth)
        //     ->height($previewHeight)
        //     ->fit('contain', $previewWidth, $previewHeight);
    }

    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }
}
