<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;

use Log;

class InstallationController extends Controller
{
    public function install(Request $req) {

        if(!$req->has('shop_url')) {
            return redirect('/')->with('errors', ['You need to provide a Shopify shop url.']);
        }

        $shop_url = str_ireplace('https://', '', $req->input('shop_url'));
        $shop_url = str_ireplace('http://', '', $shop_url); // whatever for now

        // generate an install url from the provided shop url

        $shopify = App::make('ShopifyAPI', [
        	'API_KEY'     => env('API_KEY'),
        	'SHOP_DOMAIN' => $shop_url,
        ]);

        $installURL = $shopify->installURL(['permissions' => ['read_themes', 'write_themes'], 'redirect' => env('DOMAIN') . '/auth']);

        return redirect($installURL);

    }
}
