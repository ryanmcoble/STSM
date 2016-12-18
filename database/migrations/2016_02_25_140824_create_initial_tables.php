<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // shops table
        Schema::create('shops', function(Blueprint $t) {
            $t->increments('id');

            $t->string('shopify_id');
            
            $t->string('shop_owner_email');
            
            $t->string('public_domain'); // actual store's domain
            $t->string('permanent_domain'); // .myshopify.com domain
            
            $t->string('access_token');

            $t->timestamps();
        });

        // files table
        Schema::create('files', function(Blueprint $t) {
            $t->increments('id');

            $t->integer('shop_id')->unsigned();
            $t->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');

            // we need a way to know what shopify theme to sync our changes to
            

            $t->string('title');

            $t->timestamps();
        });


        // sections table
        Schema::create('sections', function(Blueprint $t) {
            $t->increments('id');

            $t->integer('file_id')->unsigned();
            $t->foreign('file_id')->references('id')->on('files')->onDelete('cascade');

            $t->string('title');

            $t->timestamps();
        });


        // settings table
        Schema::create('settings', function(Blueprint $t) {
            $t->increments('id');

            $t->integer('section_id')->unsigned();
            $t->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');

            $t->string('title');
            $t->string('json_value');

            $t->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop tables
        Schema::drop('settings');
        Schema::drop('sections');
        Schema::drop('files');
        Schema::drop('shops');
    }
}
