<?php
namespace ThemeSettingBuilder;

use Illuminate\Support\Facades\App;

use App\Shop;

class ShopifyConnector {

	private static $instance;

	public $api;

	protected function __construct(Shop $shop) {
		$this->api = App::make('ShopifyAPI', [
            'API_KEY'      => env('API_KEY'),
            'API_SECRET'   => env('API_SECRET'),
            'SHOP_DOMAIN'  => $shop->permanent_domain,
            'ACCESS_TOKEN' => $shop->access_token,
        ]);
	}

	public static function connect(Shop $shop) {

		if(!static::$instance) static::$instance = new static($shop);

		return static::$instance;
	}
}