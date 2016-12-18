<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    public function shop() {
    	return $this->belongsTo('App\Shop');
    }

    public function sections() {
    	return $this->hasMany('App\Section');
    }
}
