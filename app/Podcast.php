<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Podcast extends Model
{


    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class);
    }

    public function library(): BelongsTo
    {
        return $this->belongsTo(Library::class);
    }

    protected function casts(): array
    {
        return [
            'last_scanned_at' => 'datetime',
        ];
    }
    public function episodes(){
        return $this->hasMany(Episode::class);
    }

    public function image(): hasOne
    {
        return $this->hasOne(Image::class);
    }

    protected $fillable = [
        'name',
        'description',
        'publisher',
        'rssUrl',
        'image_id',
        'directory_id',
        'library_id',
        'last_scanned_at'
    ];
}
