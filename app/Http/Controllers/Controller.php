<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use ThemeSettingBuilder\ShopifyConnector;
use ThemeSettingBuilder\ShopifyData;
use App\Shop;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $shop;
    protected $shopifyData;

    public function __construct() {

    	$this->shop = Shop::where('permanent_domain', Session::get('shop'))->first();

        if($this->shop) {
            $shopifyConnector = ShopifyConnector::connect($this->shop);
            $this->shopifyData = new ShopifyData($shopifyConnector);

            $themes = $this->shopifyData->getThemes();

            View::share('shop', $this->shop);
            View::share('api_key', env('API_KEY'));
            View::share('themes', $themes);
        }
    }
}
