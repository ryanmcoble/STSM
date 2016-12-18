@extends('layouts.master')

<?php

$body_class = 'builder';

?>

@section('content')

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <p class="help-block pull-right last-update">Last updated: {{ date('l F jS Y h:i:s A', strtotime($file->updated_at)) }}</p>
    </div>
</div>

<div class="row file-builder" data-file-id="{{ $file->id }}">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="form-group">
            <label for="shop_url">Shopify Theme</label>
            <select class="form-control selected-theme" name="shopify_theme">
                @foreach($themes as $theme)
                    <option value="{{ $theme->id }}" {{ $theme->id == $file->shopify_theme_id ? 'selected=""' : '' }}>{{ $theme->name }} @if($theme->role == 'main')(Currently Live)@endif</option>
                @endforeach
            </select>
            <p class="help-block">Select the Shopify theme you wish to use.</p>
        </div>
       
        <div class="form-group">
            <label>Name:</label>
            <input class="form-control name" value="{{ $file->title }}">
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-10 col-sm-10 col-md-10">
                        <p class="panel-title">Sections</p>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <button class="btn btn-md pull-right btn-default add-section-modal" data-toggle="tooltip" title="Add section"><i class="fa fa-plus"></i> Add Section</button>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <ul class="sections">
                    @if(count($file->sections))

                        <?php $i = 0; ?>
                        @foreach($file->sections as $section)
                            <li class="section" data-section-id="{{ $section->id }}">

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="input-group">
                                            <input class="form-control section-name" value="{{ $section->title }}">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default add-setting-modal" data-toggle="tooltip" title="Add setting"><i class="fa fa-plus"></i> Add Setting</button>
                                                <button class="btn btn-danger delete-section-modal" data-toggle="tooltip" title="Delete section"><i class="fa fa-trash"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--<p>Settings</p>-->
                                <ul class="settings">
                                    @if(count($section->settings))

                                        <?php $j = 1; ?>
                                        @foreach($section->settings as $setting)

                                        <?php
                                            $settingValue = $setting->json_value ? json_decode($setting->json_value) : '';

                                            if($settingValue) {
                                                $setting->type = $settingValue->type;
                                            }
                                        ?>

                                        <li class="setting" data-setting-id="{{ $setting->id }}" data-setting-type="{{ $setting->type }}" data-setting="{{ json_encode($setting) }}">

                                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                <span>{{ $setting->type }}</span>
                                            </div>

                                            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 settings-btn-container">
                                                {{ $setting->title }}

                                                <span class="pull-right">
                                                    <button class="btn btn-xs btn-info edit-setting-modal" data-toggle="tooltip" title="Edit setting"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs btn-danger delete-setting-modal" data-toggle="tooltip" title="Delete setting"><i class="fa fa-trash"></i></button>
                                                </span>
                                            </div>
                                        </li>
                                        <div class="clearfix"></div>

                                        <?php $j++; ?>
                                        @endforeach

                                    @else
                                    <li>
                                        <span>No settings</span>
                                    </li>
                                    @endif
                                </ul>

                                @if($i < count($file->sections) - 1)
                                    <hr>
                                @endif
                            </li>
                            <?php $i++; ?>
                        @endforeach
                    @else
                        <li>
                            <span>No sections</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional-scripts')
<script>
    ShopifyApp.ready(function() {
        ShopifyApp.Bar.initialize({
            forceRedirect: false,
            debug: true,
            icon: '',//'/nothing.jpg',
            title: 'Builder',
            buttons: {
                primary: [
                    {
                        label: 'Sync',
                        callback: function(message, data) {
                            var builder = ThemeSettingsBuilder.Builder;
                            builder.sync(function(data) {
                                if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice(data.message);

                                // redirect to dashboard
                                setTimeout(function() {
                                    window.location.href = '/dashboard';
                                }, 800);
                            });
                        }
                    }
                ],
                secondary: [
                    {
                        label: 'Cancel',
                        href: '/dashboard',
                        target: 'app'
                    }
                ]
            }
        });

        ShopifyApp.Bar.loadingOff();
    });
</script>
@endsection