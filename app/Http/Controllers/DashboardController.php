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

    	// lets redirect them back to the install url
    	$shopify = App::make('ShopifyAPI', [
    	    'API_KEY'     => env('API_KEY'),
    	    'SHOP_DOMAIN' => Session::get('shop'),
    	]);
    	
        return view('dashboard')->with(['files' => $files]);
    }
}
