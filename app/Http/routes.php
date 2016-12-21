<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// home page / dev install
Route::get('/', 'HomeController@index');
// post dev install
Route::post('install', 'InstallationController@install');

// shopify authentication
Route::get('auth', 'ShopifyController@installOrAuthenticate');
// uninstall webhook (why not)
Route::post('uninstall', 'ShopifyController@uninstall');


// after install
Route::group(['middleware' => ['auth.shopify']], function() {
	Route::get('dashboard', 'DashboardController@index');
	Route::get('files/{file_id}/build', 'BuildController@get');
});


// version 1 of the api
Route::group(['prefix' => 'api/v1'], function() {
	// get all imported files
	Route::get('files', 'FileController@index');

	// import a specific theme's settings file
	Route::post('files/{theme_id}/import', 'FileController@import');

	// sync file to shopify theme
	Route::post('files/{file_id}/sync', 'FileController@sync');

	// change the selected shopify theme
	Route::put('files/{file_id}/change-theme', 'FileController@changeTheme');

	// import a specific theme's settings file
	Route::put('files/{file_id}/edit', 'FileController@edit');

	// get all sections for a file by id
	Route::get('files/{file_id}/sections', 'FileController@getSections');

	// add a new section to a file by file id
	Route::post('files/{file_id}/add-section', 'FileController@addSection');

	// edit an file section
	Route::put('files/sections/{section_id}/edit', 'FileController@editSection');

	// delete an file section
	Route::delete('files/sections/{section_id}/delete', 'FileController@deleteSection');

	// add a setting
	Route::post('files/settings/create', 'FileController@addSetting');

	// edit a setting
	Route::put('files/settings/{setting_id}/edit', 'FileController@editSetting');

	// delete an file setting
	Route::delete('files/settings/{setting_id}/delete', 'FileController@deleteSetting');

	// build / edit an imported file
	Route::post('files/{file_id}/build', 'BuildController@save');

	// delete an imported file
	Route::delete('files/{file_id}/delete', 'FileController@delete');
});


