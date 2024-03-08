<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UUID;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use HasFactory, UUID, SoftDeletes;

    protected $table = 'post_comments';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment', 'created_by', 'updated_by', 'deleted_by',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(PostComment::class, 'comment_id');
    }
}
