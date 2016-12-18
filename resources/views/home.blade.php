@extends('layouts.no-auth')

<?php

$body_class = 'install';

?>

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h2 class="text-center">Shopify Theme Settings Builder</h2>
    </div>
</div>

<div class="row install-container">
    <div class="col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Installation</h3>
            </div>
            <div class="panel-body">
                
                <div class="error-container">
                    @if(count($errors))
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Error!</strong> We were unable to install the application.
                    </div>
                    @endif
                </div>

                <form method="post" action="/install" id="install_form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="shop_url">Shop URL</label>
                        <input type="text" class="form-control" name="shop_url" id="shop_url" placeholder="Shopify URL">
                        <p class="help-block">Just enter your Shopify shop url (https://example.myshopify.com) to install the app</p>
                    </div>
                    
                    <button type="submit" class="btn btn-default">Install</button>
                </form>

            </div>
        </div>

    </div>
</div>

@include('scripts')

@endsection