<?php
namespace ThemeSettingBuilder;

use Illuminate\Support\Facades\Log;

use Exception;
use StdClass;


class ShopifyData {

	protected $api;

	// we pass in the API not to tie ourselves to this implementation just the contract
	public function __construct(ShopifyConnector $connector) {
		$this->api = $connector->api;
	}

	// get an array of all the curent themes
	public function getThemes() {

		$themes = [];

		try {
			$themeData = $this->api->call(['URL' => '/admin/themes.json']);

			if($themeData) $themes = $themeData->themes;
		}
		catch(Exception $e) {
			Log::error($e->getMessage());
		}

		return $themes;
	}

	// get an array of all the products, paginated of course (we do have the access because we only defined the theme scope, not products, lol)
	public function getProducts($page = 1) {

		$products = [];

		try {
			$productData = $this->api->call(['URL' => '/admin/products.json']);

			if($productData) $products = $productData->products;
		}
		catch(Exception $e) {
			Log::error($e->getMessage());
		}

		return $products;
	}

	// get a theme settings file by theme id
	public function getThemeSettingsFile($theme_id) {

		try {
			$themeSettingsData = $this->api->call(['URL' => '/admin/themes/' . $theme_id . '/assets.json?asset[key]=config/settings_schema.json']);

			if($themeSettingsData) return $themeSettingsData->asset;
		}
		catch(Exception $e) {
			Log::error($e->getMessage());
		}

		return null;
	}

	// set a theme setting file by theme id
	public function setThemeSettingsFile($theme_id, $data) {

		$dataArr = [
			'asset' => [
				'key' => 'config/settings_schema.json',
				'value' => json_encode($data, true)
			]
		];

		Log::info('Theme: ' . $theme_id);
		Log::info('Data: ' . json_encode($data, true));

		try {
			$themeSettingsData = $this->api->call(['METHOD' => 'PUT', 'URL' => '/admin/themes/' . $theme_id . '/assets.json', 'DATA' => $dataArr]);

			Log::info('Result: ' . json_encode($themeSettingsData));
			Log::info('');

			if(isset($themeSettingsData->asset)) return $themeSettingsData->asset;
		}
		catch(Exception $e) {
			Log::error($e->getMessage());
		}

		return false;
	}
}