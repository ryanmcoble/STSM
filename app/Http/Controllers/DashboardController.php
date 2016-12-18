<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

use ThemeSettingBuilder\ShopifyConnector;
use ThemeSettingBuilder\ShopifyData;

use App\Shop;
use App\File;

class DashboardController extends Controller
{

    public function index()
    { 
    	$files = File::where('shop_id', $this->shop->id)->get();

        return view('dashboard')->with(['files' => $files]);
    }
}
