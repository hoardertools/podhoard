<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Library extends Model
{

    public function directories(){
        return $this->hasMany(Directory::class);
    }
    public function podcasts(){
        return $this->hasMany(Podcast::class);
    }
    public function episodes(){
        return $this->hasMany(Episode::class);
    }

}
