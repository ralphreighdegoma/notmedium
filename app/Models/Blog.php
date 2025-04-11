<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    //
    protected $fillable = ['title', 'content', 'status', 'created_by', 'slug', 'featured_image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $dates = ['deleted_at'];
}
