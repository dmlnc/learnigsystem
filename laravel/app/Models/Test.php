<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
/**
 * @internal
 * @coversNothing
 */
class Test extends Model implements HasMedia
{
    
    use SoftDeletes;
    use InteractsWithMedia;
    use HasFactory;

    public $table = 'tests';

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $filterable = [
        'id',
        'course.title',
        'lesson.title',
        'title',
        'description',
    ];

    protected $orderable = [
        'id',
        'course.title',
        'lesson.title',
        'title',
        'description',
        'is_published',
    ];

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'is_published',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function test_results()
    {
        return $this->hasMany(TestResult::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
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
}
