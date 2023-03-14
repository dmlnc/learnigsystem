<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model 
{
    
    use SoftDeletes;
    use HasFactory;

    public $table = 'faculties';


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'academy_id',
    ];

    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function academy()
    {
        return $this->belongsTo(Academy::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'faculty_course');
    }



}
