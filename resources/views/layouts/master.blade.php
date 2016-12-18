<!DOCTYPE html>
<html>
    <head>
        <title>Shopify Theme Settings Builder</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        
        <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/jquery-simplecolorpicker.css') }}">

        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    </head>
    <body class="{{ $body_class }}">


        <?php 

        $settingTypes = [
            'header',
            'paragraph',
            'text',
            'textarea',
            'image',
            'radio',
            'select',
            'checkbox',
            'color',
            'font',
            'collection',
            'product',
            'blog',
            'page',
            'link_list',
            'snippet'
        ];

        $settingTypeFieldMap = [
            'header' => [
                'title',
                'content',
                'info',
            ],
            'paragraph' => [
                'title',
                'content',
            ],
            'text' => [
                'title',
                'id',
                'label',
                'default',
                'info',
                'placeholder',
            ],
            'textarea' => [
                'title',
                'id',
                'label',
                'default',
                'info',
                'placeholder',
            ],
            'image' => [
                'title',
                'id',
                'label',
                'max-width',
                'max-height',
                'info',
            ],
            'radio' => [
                'title',
                'id',
                'label',
                'default',
                'info',
                //'seperator',
                'radio-options-form',
                'radio-options',
            ],
            'select' => [
                'title',
                'id',
                'label',
                'default',
                'info',
                //'seperator',
                'select-options-form',
                'select-options',
            ],
            'checkbox' => [
                'title',
                'id',
                'label',
                'default',
                'info',
            ],
            'color' => [
                'title',
                'id',
                'label',
                'default',
                'info',
            ],
            'font' => [
                'title',
                'id',
                'label',
                'info',
            ],
            'collection' => [
                'title',
                'id',
                'label',
                'info',
            ],
            'product' => [
                'title',
                'id',
                'label',
                'info',
            ],
            'blog' => [
                'title',
                'id',
                'label',
                'info',
            ],
            'page' => [
                'title',
                'id',
                'label',
                'info',
            ],
            'link_list' => [
                'title',
                'id',
                'label',
                'info',
            ],
            'snippet' => [
                'title',
                'id',
                'label',
                'info',
            ],
        ];

        ?>

        <script>
            var settingTypeFieldMap = <?php echo json_encode($settingTypeFieldMap); ?>;
        </script>

    	@include('header')

        <div class="container">
            @yield('content')
        </div>

        @include('modals')

        @include('scripts')

        <!-- Shopify Embedded API-->
        <script src="//cdn.shopify.com/s/assets/external/app.js"></script>
        <script>
        ShopifyApp.init({
            apiKey: '{{ isset($api_key) ? $api_key : '' }}',
            shopOrigin: 'https://{{ $shop->permanent_domain }}',
            debug: true
        });
        </script>

        @yield('additional-scripts')
    </body>
</html>