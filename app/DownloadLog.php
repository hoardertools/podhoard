<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DownloadLog extends Model
{
    use SoftDeletes;

    public function download(): BelongsTo
    {
        return $this->belongsTo(Download::class);
    }

    protected function casts(): array
    {
        return [
            'downloaded' => 'boolean',
            'error' => 'boolean',
        ];
    }
}
