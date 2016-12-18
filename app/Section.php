<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    public function file() {
    	return $this->belongsTo('App\File');
    }

    public function settings() {
    	return $this->hasMany('App\Setting');
    }
}
