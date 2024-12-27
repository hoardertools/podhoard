<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    use SoftDeletes;

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

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
            'published_at' => 'datetime',
            'downloaded' => 'boolean',
            'downloaded_at' => 'datetime',
        ];
    }
}
