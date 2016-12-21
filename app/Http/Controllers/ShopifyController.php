<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use App\Shop;

use Exception;

class ShopifyController extends Controller
{
    
    /**
     * Main method for authenticating with Shopify
     */
    public function installOrAuthenticate(Request $req) {


        // check for code from shopify (this happen on new install)
        if($req->has('code')) {

            $shop_url = $req->input('shop');
            $code     = $req->input('code');

            Log::info('New install: ' . $shop_url);

            $shopify = App::make('ShopifyAPI', [
                'API_KEY'     => env('API_KEY'),
                'API_SECRET'  => env('API_SECRET'),
                'SHOP_DOMAIN' => $shop_url,
            ]);


            // attempt to get the access token
            $accessToken = '';
            try {

                // oauth verification
                $verify = $shopify->verifyRequest($req->all());
                if(!$verify) {
                    Log::error('Unable to verify oauth authentication');
                    return ;
                }

                $accessToken = $shopify->getAccessToken($code);
            }
            catch(Exception $e) {
                Log::error($e->getMessage());
                return ;
            }


            // check to see if we already have the shop record stored
            $shop = Shop::where('permanent_domain', $shop_url)->first();

            if(!$shop) {
                // add a new shop record
                $shop = new Shop;
            }

            // save or update shop data
            $shop->setDomain($shop_url);
            $shop->access_token = $accessToken;
            $shop->save();

            $this->updateShopInfo($shop);


            // create webhook for uninstall (will do later)
            $webhookData = [
                'webhook' => [
                    'topic'   => 'app/uninstalled',
                    'address' => env('DOMAIN') . '/uninstall',
                    'format'  => 'json',
                ],
            ];

            // always with the try catches for api call, past experiences and all lol
            try {
                $shopify->setup(['ACCESS_TOKEN' => $shop->access_token]);
                $shopify->call(['URL' => '/admin/webhooks.json', 'METHOD' => 'POST', 'DATA' => $webhookData]);
            }
            catch(Exception $e) {
                Log::error('Something weird with webhooks... ' . $e->getMessage());
            }

            // store shop in session, of course, duh
            Session::put('shop', $shop->permanent_domain);

            return redirect('/dashboard'); // or something idk yet

        }
        else { // authenticating from store apps screen, after first install

            $shop_url = $req->has('shop') ? $req->input('shop') : Session::get('shop');
            //if(!$shop_url) {
               // return 'Session ended: go back to apps and reopen the application';
            //}

            $shop = Shop::where('permanent_domain', $shop_url)->first();
            if($shop) {

                // so everything seems to have gone good
                // update shop info (in case it has changed)
                $this->updateShopInfo($shop);

                // store the shop in the session, so authentication will persist
                Session::put('shop', $shop->permanent_domain);

                return redirect('/dashboard'); // or something idk yet

            }
            else {

                // no shop at this point means something weird happened
                Log::error('Something weird happened with the authentication of ' . $shop_url);

                // lets redirect them back to the install url
                $shopify = App::make('ShopifyAPI', [
                    'API_KEY'     => env('API_KEY'),
                    'SHOP_DOMAIN' => $shop_url,
                ]);
                
                return redirect($shopify->installURL(['permissions' => ['read_themes', 'write_themes'], 'redirect' => env('DOMAIN') . '/auth']));
            }
        }
    }


    // uninstall app webhook
    public function uninstall(Request $req) {
        
        $shop = Shop::where('permanent_domain', $req->input('myshopify_domain'))->first();

        if($shop) {
            $shop->delete();
        }

        return 'Thank you webhook robot!';
    }



    // use the shopify api to update any shop data
    private function updateShopInfo($shop = null) {
        // in case something weird happen, wrap in try catch
        try {

            $shopify = App::make('ShopifyAPI', [
                'API_KEY'      => env('API_KEY'),
                'API_SECRET'   => env('API_SECRET'),
                'SHOP_DOMAIN'  => $shop->permanent_domain,
                'ACCESS_TOKEN' => $shop->access_token,
            ]);

            $shopData = $shopify->call(['URL' => '/admin/shop.json']);

            if($shopData) {

                $shop->shopify_id = $shopData->shop->id; // don't know why this would change but just in case
                $shop->public_domain = $shopData->shop->domain;
                $shop->setDomain($shopData->shop->myshopify_domain);
                // $shop->shop_type = $shopData->shop->plan_name (for the future)
                $shop->shop_owner_email = $shopData->shop->email;
                $shop->save();
            }

        }
        catch(Exception $e) {
            Log::error('Some weird stuff happened... ' . $e->getMessage());
        }
    }

}
