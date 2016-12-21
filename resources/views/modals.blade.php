
<!-- Import shopify theme setting file -->
<div class="modal fade" id="import_file_modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Import Theme Settings File</h4>
            </div>
            <div class="modal-body">

                <form method="post" action="#">

                    <p class="error-msg text-danger"></p>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" name="name" class="form-control">
                        <p class="help-block">Give your file a name.</p>
                    </div>

                    <div class="form-group">
                        <label for="shop_url">Shopify Theme</label>
                        <select class="form-control selected-theme" name="shopify_theme">
                            @foreach($themes as $theme)
                                <option value="{{ $theme->id }}">{{ $theme->name }} @if($theme->role == 'main')(Currently Live)@endif</option>
                            @endforeach
                        </select>
                        <p class="help-block">Select the Shopify theme you wish to use.</p>
                    </div>
                    
                </form>
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary import">Import</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete imported files -->
<div class="modal fade" id="delete_file_modal" data-file-id="" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete file</h4>
            </div>
            <div class="modal-body">
                <p class="error-msg text-danger"></p>
                <p>Are you sure you want to do delete this file?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger delete-file">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Add section -->
<div class="modal fade" id="add_section_modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add section</h4>
            </div>
            <div class="modal-body">

                <form method="post" action="#">

                    <p class="error-msg text-danger"></p>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" name="name" class="form-control">
                    </div>
                    
                </form>
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add-section">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete section -->
<div class="modal fade" id="delete_section_modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete section</h4>
            </div>
            <div class="modal-body">
                <p class="error-msg text-danger"></p>
                <p>Are you sure you want to do delete this file section?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger delete-section">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit setting -->
<div class="modal fade" id="change_setting_modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit setting</h4>
            </div>
            <div class="modal-body">

                <form method="post" action="#">

                    <p class="error-msg text-danger"></p>

                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control setting-type" name="type">
                            @foreach($settingTypes as $type)
                                <option value="{{ $type }}">{{ ucwords($type) }}</option>
                            @endforeach
                        </select>
                        <p class="help-block"></p>
                    </div>

                    <div class="form-group setting-title">
                        <label>Visual Title</label>
                        <input name="title" class="form-control setting-title">
                    </div>

                    <div class="form-group setting-id">
                        <label>ID</label>
                        <input name="id" class="form-control setting-id">
                    </div>

                    <div class="form-group setting-label">
                        <label>Label</label>
                        <input name="label" class="form-control setting-label">
                    </div>

                    <div class="form-group setting-content">
                        <label>Content</label>
                        <textarea name="content" class="form-control setting-content"></textarea>
                    </div>

                    <div class="form-group setting-info">
                        <label>Info (optional)</label>
                        <input name="info" class="form-control setting-info">
                    </div>

                    <div class="form-group setting-default">
                        <label>Default (optional)</label>
                        <input name="default" class="form-control setting-default">
                    </div>
                    
                    <div class="form-group setting-placeholder">
                        <label>Placeholder (optional)</label>
                        <input name="placeholder" class="form-control setting-placeholder">
                    </div>
        
                    <div class="form-group setting-max-width">
                        <label>Max Width (optional)</label>
                        <input name="max-width" type="number" class="form-control setting-max-width">
                    </div>
                    <div class="form-group setting-max-height">
                        <label>Max Height (optional)</label>
                        <input name="max-height" type="number" class="form-control setting-max-height">
                    </div>

                    <div class="row setting-radio-options-form">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <hr />
                            <p class="help-block">Setup options</p>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <label>Label:</label>
                            <input class="form-control label-input">
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <label>Value:</label>
                            <input class="form-control value-input">
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <button class="btn btn-sm btn-secondary add-option" data-toggle="tooltip" title="Add option" style="margin-top: 25px;"><i class="fa fa-plus"></i> Add Option</button>
                        </div>
                    </div>
                    <div class="form-group setting-radio-options">
                        <!-- options - add / remove options (value / label) -->
                        <label>Options:</label>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <th>Label</th>
                                    <th>Value</th>
                                    <th>&nbsp;</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row setting-select-options-form">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <hr />
                            <p class="help-block">Setup options with group</p>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <label>Group:</label>
                            <input class="form-control group-input">
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <label>Label:</label>
                            <input class="form-control label-input">
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <label>Value:</label>
                            <input class="form-control value-input">
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <button class="btn btn-sm btn-secondary add-option-with-group" data-toggle="tooltip" title="Add option" style="margin-top: 25px;"><i class="fa fa-plus"></i> Add Option</button>
                        </div>
                    </div>
                    <div class="form-group setting-select-options">
                        <!-- options - add / remove options (group(optional) / value / label) -->
                        <label>Options</label>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <th>Group</th>
                                    <th>Label</th>
                                    <th>Value</th>
                                    <th>&nbsp;</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </form>
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary edit-setting">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete setting -->
<div class="modal fade" id="delete_setting_modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete setting</h4>
            </div>
            <div class="modal-body">
                <p class="error-msg text-danger"></p>
                <p>Are you sure you want to do delete this file setting?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger delete-setting">Delete</button>
            </div>
        </div>
    </div>
</div>
