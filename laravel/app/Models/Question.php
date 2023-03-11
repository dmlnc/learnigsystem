<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Question extends Model implements HasMedia
{
    
    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    public $table = 'questions';

    protected $appends = [
        // 'question_image',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $orderable = [
        'id',
        'course.title',
        'question_text',
        'points',
    ];

    protected $filterable = [
        'id',
        'course.title',
        'question_text',
        'points',
    ];

    protected $fillable = [
        'test_id',
        'question_text',
        'points',
        'position',
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

        $this->addMediaConversion('thumbnail')
            ->width($thumbnailWidth)
            ->height($thumbnailHeight)
            ->fit('fill', $thumbnailWidth, $thumbnailHeight)
            ->background('ffffff');
        $this->addMediaConversion('preview_thumbnail')
            ->width($thumbnailPreviewWidth)
            ->height($thumbnailPreviewHeight)
            ->fit('fill', $thumbnailWidth, $thumbnailHeight)
            ->background('ffffff');

    }

    public function course()
    {
        return $this->belongsTo(Test::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    // public function getQuestionImageAttribute()
    // {
    //     return $this->getMedia('question_question_image')->map(function ($item) {
    //         $media = $item->toArray();
    //         $media['url'] = $item->getUrl();
    //         $media['thumbnail'] = $item->getUrl('thumbnail');
    //         $media['preview_thumbnail'] = $item->getUrl('preview_thumbnail');

    //         return $media;
    //     });
    // }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
