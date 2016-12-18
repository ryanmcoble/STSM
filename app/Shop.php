<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

    protected $table = 'shops';
    protected $fillable = [
        'shopify_id',
        'shop_owner_email',
        'public_domain',
        'permanent_domain',
        'access_token'
    ];

    // get shop's files
    public function files() {
    	return $this->hasMany('App\File');
    }


    // format the domain to handle weird stuff
    private function formatDomain($domain = '') {
    	$noProtocol = preg_replace('/(http(s)?:\/\/)?/', '', $domain);

    	if(strpos($noProtocol, '/')) {
    		return substr($noProtocol, 0, strpos($noProtocol, '/'));
    	}

    	return $noProtocol;
    }

    // set the domain
    public function setDomain($domain) {
    	$this->permanent_domain = $this->formatDomain($domain);
    }

    // encryption / decryption of access token because we should not be storing them unencrypted
    private function encrypt($text = '', $key = '') {
    	return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
    private function decrypt($text = '', $key = '') {
    	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    // get the access token
    public function getAccessToken() {
    	return $this->decrypt($this->access_token, env('ACTK_KEY'));
    }

    // set the access token
    public function setAccessToken($accessToken) {
    	return $this->encrypt($accessToken, env('ACTK_KEY')); // just for testing
    }
}
