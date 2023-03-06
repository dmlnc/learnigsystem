<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Academy extends Model implements HasMedia
{
    
    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    public $table = 'academies';

    protected $appends = [
        'thumbnail',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'company_id',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $thumbnailWidth  = 50;
        $thumbnailHeight = 50;

        $thumbnailPreviewWidth  = 120;
        $thumbnailPreviewHeight = 120;

        $previewWidth = 1000;
        $previewHeight = 500;

        $this->addMediaConversion('thumbnail')
            ->width($thumbnailWidth)
            ->height($thumbnailHeight)
            ->fit('crop', $thumbnailWidth, $thumbnailHeight);
        $this->addMediaConversion('preview_thumbnail')
            ->width($thumbnailPreviewWidth)
            ->height($thumbnailPreviewHeight)
            ->fit('crop', $thumbnailPreviewWidth, $thumbnailPreviewHeight);

        $this->addMediaConversion('preview')
            ->width($previewWidth)
            ->height($previewHeight)
            ->fit('contain', $previewWidth, $previewHeight);

    }

    // public function teacher()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function getThumbnailAttribute()
    {
        return $this->getMedia('academy_thumbnail')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();
            $media['thumbnail'] = $item->getUrl('thumbnail');
            $media['preview_thumbnail'] = $item->getUrl('preview_thumbnail');
            $media['preview'] = $item->getUrl('preview');

            return $media;
        });
    }

    // public function students()
    // {
    //     return $this->belongsToMany(User::class);
    // }

    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
