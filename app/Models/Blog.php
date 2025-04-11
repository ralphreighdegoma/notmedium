<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use SoftDeletes;

    //
    protected $fillable = ['title', 'content', 'status', 'created_by', 'slug', 'image', 'slug',  'created_at', 'updated_at'];
    protected $appends = ['image_preview'];


    public function getImagePreviewAttribute()
    {
        if ($this->image) {
            if (Storage::disk('public')->exists($this->image)) {
                return asset('storage/' . $this->image);
            }
        }

        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    protected $dates = ['deleted_at'];
}
