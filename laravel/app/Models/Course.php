<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Course extends Model implements HasMedia
{
    
    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    public $table = 'courses';

    protected $appends = [
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $orderable = [
        'id',
        // 'teacher.name',
        'title',
        'description',
        'price',
        'is_published',
    ];

    protected $filterable = [
        'id',
        // 'teacher.name',
        'title',
        'description',
        // 'price',
        // 'students.name',
    ];

    protected $fillable = [
        // 'teacher_id',
        'title',
        'description',
        'company_id',
        'is_published',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $thumbnailWidth  = 300;
        $thumbnailHeight = 300;

        $thumbnailPreviewWidth  = 500;
        $thumbnailPreviewHeight = 500;

        $previewWidth = 900;
        $previewHeight = 900;

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

    public function images()
    {
        return $this->getMedia('course_thumbnail')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();
            $media['thumbnail'] = $item->getUrl('thumbnail');
            $media['preview_thumbnail'] = $item->getUrl('preview_thumbnail');
            $media['preview'] = $item->getUrl('preview');

            return $media;
        });
    }


    public function thumbnail()
    {
        return $this->getMedia('course_thumbnail')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();
            $media['thumbnail'] = $item->getUrl('thumbnail');
            $media['preview_thumbnail'] = $item->getUrl('preview_thumbnail');
            $media['preview'] = $item->getUrl('preview');

            return $media;
        });
    }

    

   
    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'faculty_course');
    }


    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
