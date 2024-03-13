<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UUID;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostAsset extends Model
{
    use HasFactory, UUID, SoftDeletes;

    protected $table = 'post_assets';

    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id', 'type', 'asset_url', 'created_by', 'updated_by', 'deleted_by',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
