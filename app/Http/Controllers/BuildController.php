<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ThemeSettingBuilder\Builder;

use App\File;

class BuildController extends Controller
{
    public function get($id) {
        $file = File::where('id', $id)->first();

        if(!$file) {
            return redirect('/dashboard');
        }

        return view('builder')->with('file', $file);
    }

}
