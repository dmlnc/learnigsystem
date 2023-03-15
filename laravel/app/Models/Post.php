<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{

    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    public $table = 'posts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'text',
        'postable_id',
        'postable_type',
        'company_id',
        'user_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'postable_id')
            ->where('posts.postable_type', Company::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'postable_id')
            ->where('posts.postable_type', Faculty::class);
    }

    public function academy()
    {
        return $this->belongsTo(Academy::class, 'postable_id')
            ->where('posts.postable_type', Academy::class);
    }

    // public function company()
    // {
    //     return $this->morphToMany(Company::class, 'postable');
    // }

    public function faculties()
    {
        return $this->morphToMany(Faculty::class, 'postable');
    }


    public function postable(): MorphTo
    {
        return $this->morphTo();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $thumbnailWidth  = 400;
        $thumbnailHeight = 200;

        $thumbnailPreviewWidth  = 500;
        $thumbnailPreviewHeight = 500;

        $previewWidth = 900;
        $previewHeight = 900;

        $this->addMediaConversion('thumbnail')
            ->width($thumbnailWidth)
            ->height($thumbnailHeight)
            ->fit('fill', $thumbnailWidth, $thumbnailHeight)
            ->background('ffffff');

        $this->addMediaConversion('preview_thumbnail')
            ->width($thumbnailPreviewWidth)
            ->height($thumbnailPreviewHeight)
            ->fit('contain', $thumbnailPreviewWidth, $thumbnailPreviewHeight);

        $this->addMediaConversion('preview')
            ->width($previewWidth)
            ->height($previewHeight)
            ->fit('contain', $previewWidth, $previewHeight);

    }
}
