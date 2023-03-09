<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Lesson extends Model implements HasMedia
{
    
    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    public $table = 'lessons';


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected $filterable = [
        'id',
        'course.title',
        'title',
        'short_text',
        'long_text',
        'position',
    ];

    protected $orderable = [
        'id',
        'course.title',
        'title',
        'short_text',
        'long_text',
        'position',
        'is_published',
    ];

    protected $fillable = [
        'course_id',
        'title',
        'short_text',
        'long_text',
        'position',
        'is_published',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function test_results()
    {
        return $this->hasManyThrough(TestResult::class,Test::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
