@extends('layouts.master')

<?php

$body_class = 'dashboard';

?>

@section('content')

<div class="row theme-file-container">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Your theme settings files</h3>
            </div>
            <div class="panel-body">
                <table class="table">
                    <tbody class="files">
                        @if(count($files))
                            @foreach($files as $file)
                            <tr data-file-id="{{ $file->id }}">
                                <td>
                                    <span class="fa fa-file"></span>
                                    &nbsp;&nbsp;&nbsp;{{ $file->title }}
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="/files/{{ $file->id }}/build" class="btn btn-xs btn-default build-file" data-toggle="tooltip" data-placement="top" title="Edit file">
                                        <i class="fa fa-wrench"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete-file-modal" data-toggle="tooltip" data-placement="top" title="Delete file">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2">
                                    You have not imported any files...
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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
            title: '',//'Dashboard',
            buttons: {
                primary: [
                    {
                        label: 'Import',
                        callback: function(message, data) {
                            console.log(message, data);
                            $('#import_file_modal').modal('show');
                        }
                    }
                ]
            }
        });

        ShopifyApp.Bar.loadingOff();

        //window.parent.postMessage('{"message": "Shopify.API.Bar.error.404"}', '*');
    });
</script>
@endsection
