<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Directory extends Model
{


    protected $fillable = [
        'path',
        'library_id'
    ];

    public function library(): BelongsTo
    {
        return $this->belongsTo(Library::class);
    }
}
