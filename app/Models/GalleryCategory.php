<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function albums(): HasMany
    {
        return $this->hasMany(GalleryAlbum::class);
    }
}
