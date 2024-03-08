<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UUID;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, UUID, SoftDeletes;

    protected $table = 'posts';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bio', 'created_by', 'updated_by', 'deleted_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function assets()
    {
        return $this->hasMany(PostAsset::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}