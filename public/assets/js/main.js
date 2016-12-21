var ThemeSettingsBuilder = {};

/**
 * Initialize the app
 */
ThemeSettingsBuilder.init = function() {
	// setup tooltips
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip();
	});

	// global setup of csrf token for ajax requests
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
}


ThemeSettingsBuilder.Dashboard = (function() {
	/**
	 * Get all the files
	 */
	function getFiles(cb) {
		$.ajax({
			url: '/api/v1/files',
			method: 'get',
			dataType: 'json',
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					//$('#import_file_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Load all the files
	 */
	function loadFiles() {
		getFiles(function(data) {
			if(data.status !== 'success') {
				return false;
			}

			$('.files').html(`<tr>
	                            <td colspan="2">
	                                Loading your files...
	                            </td>
	                        </tr>`);

			var filesHTML = '';

			for(var fileIndex in data.files) {
				var file = data.files[fileIndex];
				filesHTML += `<tr data-file-id="${file.id}">
				                <td>
				                    <span class="fa fa-file"></span>
				                    &nbsp;&nbsp;&nbsp;${file.title}
				                </td>
				                <td class="text-right text-nowrap">
				                    <a href="/files/${file.id}/build" class="btn btn-sm btn-default build-file" data-toggle="tooltip" data-placement="top" title="Edit file">
				                        <i class="fa fa-wrench"></i>
				                    </a>
				                    <button class="btn btn-sm btn-danger delete-file-modal" data-toggle="tooltip" data-placement="top" title="Delete file">
				                        <i class="fa fa-trash"></i>
				                    </button>
				                </td>
				            </tr>\n`;
			}

			if(!data.files.length) {
				filesHTML = `<tr>
		                        <td colspan="2">
		                            You have not imported any files...
		                        </td>
			                </tr>`;
			}

			$('.files').html(filesHTML);
		});
	}

	/**
	 * Import a theme settings file from Shopify
	 */
	function importFile(theme_id, cb) {
		var name = $('#name').val();
		$.ajax({
			url: '/api/v1/files/' + theme_id + '/import',
			method: 'post',
			dataType: 'json',
			data: {
				name: name
			},
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#import_file_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Delete the theme settings file
	 */
	function deleteFile(file_id, cb) {
		$.ajax({
			url: '/api/v1/files/' + file_id + '/delete',
			method: 'delete',
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#delete_file_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	return {
		getFiles: getFiles,
		loadFiles: loadFiles,
		importFile: importFile,
		deleteFile: deleteFile
	};
})();



ThemeSettingsBuilder.Builder = (function() {

	/**
	 * Set the last updated text
	 */
	function setUpdated(timestamp) {
		var dateOptions = {
			//weekday: 'long',
			year: 'numeric',
			month: 'long',
			day: '2-digit',
			hour: '2-digit',
			minute: '2-digit',
			second: '2-digit'
		};
		var date = new Date(timestamp);
		var timeString = date.toLocaleTimeString('en-us', dateOptions);

		$('.last-update').html('Last updated: ' + timeString);
	}

	/**
	 * Sync a theme settings file to Shopify
	 */
	function sync(cb) {
		var file_id = $('.file-builder').attr('data-file-id');
		$.ajax({
			url: '/api/v1/files/' + file_id + '/sync',
			method: 'post',
			dataType: 'json',
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					//$('#add_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);

					setTimeout(function() {
						//window.location.href = '/dashboard';
					}, 800);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Change the selected theme of a theme settings file
	 */
	function changeTheme(themeId, cb) {
		var file_id = $('.file-builder').attr('data-file-id');
		$.ajax({
			url: '/api/v1/files/' + file_id + '/change-theme',
			method: 'put',
			dataType: 'json',
			data: {
				theme_id: themeId
			},
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					//$('#add_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Change the name of a theme settings file
	 */
	function changeName(name, cb) {
		var file_id = $('.file-builder').attr('data-file-id');
		$.ajax({
			url: '/api/v1/files/' + file_id + '/edit',
			method: 'put',
			dataType: 'json',
			data: {
				name: name
			},
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					//$('#add_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Get all sections for theme settings file
	 */
	function getSections(file_id, cb) {
		$.ajax({
			url: '/api/v1/files/' + file_id + '/sections',
			method: 'get',
			dataType: 'json',
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					//$('#import_file_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Load sections for a theme settings file
	 */
	function loadSections() {
		var file_id = $('.file-builder').attr('data-file-id');
		this.getSections(file_id, function(data) {
			if(data.status !== 'success') {
				return false;
			}

			$('.sections').html(`<li>
	                            <span>
	                                Loading your files...
	                            </span>
	                        </li>`);

			var sectionsHTML = '';

			for(var sectionIndex in data.sections) {
				var section = data.sections[sectionIndex];
				sectionsHTML += `<li class="section" data-section-id="${section.id}">
				                    <div class="row">
	                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	                                        <div class="input-group">
	                                            <input class="form-control section-name" value="${section.title}">
	                                            <span class="input-group-btn">
	                                            	<button class="btn btn-default add-setting-modal" data-toggle="tooltip" title="Add setting"><i class="fa fa-plus"></i> Add Setting</button>
	                                                <button class="btn btn-danger delete-section-modal" data-toggle="tooltip" title="Delete section"><i class="fa fa-trash"></i></button>
	                                            </span>
	                                        </div>
	                                    </div>
	                                </div>
	                                <table class="table table-striped settings">
	                                    <thead>
	                                        <th>Type</th>
	                                        <th>Title</th>
	                                        <th>&nbsp;</th>
	                                    </thead>
	                                    <tbody>`;

				for(var settingIndex in section.settings) {
					var setting = section.settings[settingIndex];
					var settingDetails = JSON.parse(setting.json_value);

					setting.type = settingDetails.type;
					var i = parseInt(settingIndex) + 1;


					sectionsHTML += `<tr class="setting" data-setting-id="${setting.id}" data-setting-type="${setting.type}" data-setting="${escape(JSON.stringify(setting))}">
                                            <td>${setting.type}</td>
                                            <td>${setting.title}</td>
                                            <td><button class="btn btn-xs btn-info edit-setting-modal" data-toggle="tooltip" title="Edit setting"><i class="fa fa-pencil"></i></button>&nbsp;<button class="btn btn-xs btn-danger delete-setting-modal" data-toggle="tooltip" title="Delete setting"><i class="fa fa-trash"></i></button></td>
                                        </tr>`;
				}

				if(!section.settings.length) {
					sectionsHTML += `<tr>
				                        <td colspan="3">No settings</td>
				                    </li>`;
			    }

			    if(parseInt(sectionIndex) < data.sections.length - 1) {
			    	sectionsHTML += `	<hr>`;			    }

			    sectionsHTML += `	</tbody>
                                </table>`;
			}

			if(!data.sections.length) {
				sectionsHTML = `<li>
			                        <span>No sections</span>
			                    </li>`;
			}

			$('.sections').html(sectionsHTML);
		});
	}

	/**
	 * Add a new section to a theme settings file
	 */
	function addSection(title, cb) {
		var file_id = $('.file-builder').attr('data-file-id');
		$.ajax({
			url: '/api/v1/files/' + file_id + '/add-section',
			method: 'post',
			dataType: 'json',
			data: {
				title: title
			},
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#add_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Change a section name
	 */
	function changeSectionName(section_id, title, cb) {
		$.ajax({
			url: '/api/v1/files/sections/' + section_id + '/edit',
			method: 'put',
			dataType: 'json',
			data: {
				title: title
			},
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					//$('#add_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Delete a section
	 */
	function deleteSection(section_id, cb) {
		$.ajax({
			url: '/api/v1/files/sections/' + section_id + '/delete',
			method: 'delete',
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#delete_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Add setting to a section
	 */
	function addSetting(section_id, params, cb) {
		var file_id = $('.file-builder').attr('data-file-id');
		params.section_id = section_id;
		$.ajax({
			url: '/api/v1/files/settings/create',
			method: 'post',
			dataType: 'json',
			data: params,
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#change_setting_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Edit a setting of a section
	 */
	function editSetting(setting_id, params, cb) {
		$.ajax({
			url: '/api/v1/files/settings/' + setting_id + '/edit',
			method: 'put',
			dataType: 'json',
			data: params,
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#edit_section_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	/**
	 * Load the setting modal
	 */
	function loadSettingModal(modalSelector, setting) {
		var container = $(modalSelector);

		// remove active class from all fields
		container.find('.form-group[class*="setting-"].active, .row[class*="setting-"].active').removeClass('active');

		// set type
		container.find('.setting-type').val(setting.type);

		// set the fields / show or hide active fields
		var fieldKeys = settingTypeFieldMap[setting.type];
		for(var i = 0; i < fieldKeys.length; i++) {
			var settingSelector = container.find('.setting-' + fieldKeys[i] + ':not(.form-group)');

			if(typeof setting[fieldKeys[i]] !== 'undefined') {
				settingSelector.val(setting[fieldKeys[i]] ? setting[fieldKeys[i]] : '');
			}
			
			container.find('.setting-' + fieldKeys[i]).addClass('active');

			if(setting.type === 'radio' && fieldKeys[i] === 'radio-options') {
				// fill in options
				var optionsHTML = '';
				for(var optionIndex in setting.options) {
					var option = setting.options[optionIndex];
					optionsHTML += '<tr class="option">' + "\n\r";
					optionsHTML += '<td class="option-label">' + option.label + '</td>' + "\n\r";
					optionsHTML += '<td class="option-value">' + option.value + '</td>' + "\n\r";
					optionsHTML += '<td><a class="btn btn-xs btn-danger delete-option" data-toggle="tooltip" title="Delete option"><i class="fa fa-trash"></i></a></td>' + "\n\r";
					optionsHTML += '</tr>' + "\n\r";
				}

				if(setting.options) {
					container.find('.form-group.setting-' + fieldKeys[i] + ' tbody').html(optionsHTML);
				}
				else {
					container.find('.form-group.setting-' + fieldKeys[i] + ' tbody').html('<tr><td colspan="3">No options</td></tr>');
				}
			}
			else if(setting.type === 'select' && fieldKeys[i] === 'select-options') {
				// fill in options
				var optionsHTML = '';
				for(var optionIndex in setting.options) {
					var option = setting.options[optionIndex];
					optionsHTML += '<tr class="option">' + "\n\r";
					optionsHTML += '<td class="option-group" data-option-group="' + option.group + '">' + option.group + '</td>' + "\n\r";
					optionsHTML += '<td class="option-label">' + option.label + '</td>' + "\n\r";
					optionsHTML += '<td class="option-value">' + option.value + '</td>' + "\n\r";
					optionsHTML += '<td><a class="btn btn-xs btn-danger delete-option" data-toggle="tooltip" title="Delete option"><i class="fa fa-trash"></i></a></td>' + "\n\r";
					optionsHTML += '</tr>' + "\n\r";
				}

				if(setting.options) {
					container.find('.form-group.setting-' + fieldKeys[i] + ' tbody').html(optionsHTML);
				}
				else {
					container.find('.form-group.setting-' + fieldKeys[i] + ' tbody').html('<tr><td colspan="3">No options</td></tr>');
				}
			}

			//if(setting.type === 'color' && fieldKeys[i] === 'default') {
				//container.find('.form-group.setting-' + fieldKeys[i] + ' input').simplecolorpicker();
				//container.find('.form-group.setting-' + fieldKeys[i] + ' input').val('ab2567');
			//}
			//else if(setting.type !== 'color' && fieldKeys[i] === 'default') {
				//container.find('.form-group.setting-' + fieldKeys[i] + ' input').simplecolorpicker('destroy');
			//}
		}
	}

	/**
	 * Delete a setting
	 */
	function deleteSetting(setting_id, cb) {
		$.ajax({
			url: '/api/v1/files/settings/' + setting_id + '/delete',
			method: 'delete',
			cache: false,
			success: function(data) {
				if(data.status !== 'success') {
					$('#delete_setting_modal .error-msg').text(data.message);
				}
				else {
					if(typeof cb === 'function') cb(data);
				}
			},
			error: function(err) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashError(err.message);
			}
		});
	}

	return {
		setUpdated: setUpdated,
		sync: sync,
		changeTheme: changeTheme,
		changeName: changeName,
		getSections: getSections,
		loadSections: loadSections,
		addSection: addSection,
		changeSectionName: changeSectionName,
		deleteSection: deleteSection,
		addSetting: addSetting,
		editSetting: editSetting,
		loadSettingModal: loadSettingModal,
		deleteSetting: deleteSetting
	};
})();


$(function() {
	var builder = ThemeSettingsBuilder;
	builder.init();

	// if install page
	if($('body.install').length) {
		// install form submitted
		$('#install_form').on('submit', function(e) {
			// no url has been entered
			var shop_url = $('#shop_url').val();
			if(shop_url === '') {
				$('.error-container').html('');

				var msg = `<div class="alert alert-danger alert-dismissible" role="alert">
	                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;<strong>Error!</strong>&nbsp;&nbsp;&nbsp;You must enter your Shopify store's URL.
	            </div>`;

				$('.error-container').append(msg);

				e.preventDefault();
			}
			// url is not a .myshopify.com url
			else if(!/.*\.myshopify\.com/.test(shop_url)) {
				$('.error-container').html('');

				var msg = `<div class="alert alert-danger alert-dismissible" role="alert">
	                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;<strong>Error!</strong>&nbsp;&nbsp;&nbsp;Not a valid Shopify store URL.
	            </div>`;

				$('.error-container').append(msg);

				e.preventDefault();
			}
		});
	}
	// if dashboard page
	else if($('body.dashboard').length) {
		// import button clicked (in modal)
		$('.import').on('click', function(e) {
			var theme_id = $('.selected-theme').val();

			builder.Dashboard.importFile(theme_id, function(data) {
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('Theme Setting file imported successfully');

				// close modal window
				$('#import_file_modal').modal('hide');

				builder.Dashboard.loadFiles();

			});
		});

		// show delete file modal button clicked
		$(document).on('click', '.delete-file-modal', function(e) {
			var file_id = $(this).closest('tr').attr('data-file-id');
			
			var container = $('#delete_file_modal');
			container.attr('data-file-id', file_id);
			container.modal('show');
		});

		// delete file button clicked
		$('.delete-file').on('click', function(e) {
			var container = $('#delete_file_modal');
			var file_id = container.attr('data-file-id');

			builder.Dashboard.deleteFile(file_id, function(data){
				builder.Dashboard.loadFiles();

				// close modal window
				$('#delete_file_modal').modal('hide');

				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('Theme Setting file deleted successfully');
			});
		});
	}
	// if build page
	else if($('body.builder').length) {

		// theme selection change
		$(document).on('change', '.selected-theme', function(e) {
			var theme_id = $(this).val();
			builder.Builder.changeTheme(theme_id, function(data) {
				
				builder.Builder.setUpdated(data.updated_file.updated_at);

				// update last update timestamp
				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('Theme selected successfully');
			});
		});

		// file name change
		var fileNameTypingTimer;
		$(document).on('keyup', '.name', function(e) {
			var title = $(this).val();

			clearTimeout(fileNameTypingTimer);
			fileNameTypingTimer = setTimeout(function() {
				// update file name
				builder.Builder.changeName(title, function(data) {

					builder.Builder.setUpdated(data.updated_file.updated_at);

					// update last update timestamp
					if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File saved successfully');
				});
			}, 800);
		});
		$(document).on('keydown', '.name', function(e) {
			clearTimeout(fileNameTypingTimer);
		});

		// show add section modal button clicked
		$(document).on('click', '.add-section-modal', function(e) {
			e.preventDefault();

			var container = $('#add_section_modal');

			var section_id = $(this).closest('.section').attr('data-section-id');
			container.attr('data-section-id', section_id);
			container.modal('show');
		});

		// add section button clicked
		$(document).on('click', '.add-section', function(e) {
			var container = $('#add_section_modal');
			var title = container.find('input[name="name"]').val();

			builder.Builder.addSection(title, function(data){

				builder.Builder.setUpdated(data.file.updated_at);

				builder.Builder.loadSections();

				container.find('input[name="name"]').val('');
				container.modal('hide');

				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File section successfully added');
			});
		});

		// change section name
		var sectionNameTypingTimer;
		$(document).on('keyup', '.section-name', function(e) {
			var title = $(this).val();
			var section_id = $(this).closest('.section').attr('data-section-id');

			clearTimeout(sectionNameTypingTimer);
			sectionNameTypingTimer = setTimeout(function() {
				// update section name
				builder.Builder.changeSectionName(section_id, title, function(data) {

					builder.Builder.setUpdated(data.file.updated_at);

					// update last update timestamp
					if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File section saved successfully');
				});
			}, 800);
		});
		$(document).on('keydown', '.section-name', function(e) {
			clearTimeout(sectionNameTypingTimer);
		});

		// show delete section modal button clicked
		$(document).on('click', '.delete-section-modal', function(e) {
			e.preventDefault();

			var container = $('#delete_section_modal');
			var section_id = $(this).closest('.section').attr('data-section-id');
			$('#delete_section_modal').attr('data-section-id', section_id);

			container.modal('show');
		});

		// delete section button clicked
		$(document).on('click', '.delete-section', function(e) {
			var section_id = $('#delete_section_modal').attr('data-section-id');
			builder.Builder.deleteSection(section_id, function(data){

				builder.Builder.setUpdated(data.file.updated_at);

				builder.Builder.loadSections();

				$('#delete_section_modal').modal('hide');

				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File section deleted successfully');
			});
		});

		// add option to a group
		$(document).on('click', '.add-option-with-group', function(e) {
			e.preventDefault();

			var container = $('#change_setting_modal');
			
			var group = container.find('.setting-select-options-form .group-input').val();
			var label = container.find('.setting-select-options-form .label-input').val();
			var value = container.find('.setting-select-options-form .value-input').val();

			var optionsHTML = '<tr class="option">' + "\n\r";
			optionsHTML += '<td class="option-group" data-option-group="' + group + '">' + group + '</td>' + "\n\r";
			optionsHTML += '<td class="option-label">' + label + '</td>' + "\n\r";
			optionsHTML += '<td class="option-value">' + value + '</td>' + "\n\r";
			optionsHTML += '<td><a class="btn btn-xs btn-danger delete-option" data-toggle="tooltip" title="Delete option"><i class="fa fa-trash"></i></a></td>' + "\n\r";
			optionsHTML += '</tr>' + "\n\r";

			if(container.find('.setting-select-options .option').length) {
				container.find('.setting-select-options tbody').append(optionsHTML);
			}
			else {
				container.find('.setting-select-options tbody').html(optionsHTML);
			}

			container.find('.setting-select-options-form .group-input').val('');
			container.find('.setting-select-options-form .label-input').val('');
			container.find('.setting-select-options-form .value-input').val('');
		});


		// add option
		$(document).on('click', '.add-option', function(e) {
			e.preventDefault();

			var container = $('#change_setting_modal');
			
			var label = container.find('.setting-radio-options-form .label-input').val();
			var value = container.find('.setting-radio-options-form .value-input').val();

			var optionsHTML = '<tr class="option">' + "\n\r";
			optionsHTML += '<td class="option-label">' + label + '</td>' + "\n\r";
			optionsHTML += '<td class="option-value">' + value + '</td>' + "\n\r";
			optionsHTML += '<td><a class="btn btn-xs btn-danger delete-option" data-toggle="tooltip" title="Delete option"><i class="fa fa-trash"></i></a></td>' + "\n\r";
			optionsHTML += '</tr>' + "\n\r";

			if(container.find('.setting-radio-options .option').length) {
				container.find('.setting-radio-options tbody').append(optionsHTML);
			}
			else {
				container.find('.setting-radio-options tbody').html(optionsHTML);
			}

			container.find('.setting-radio-options-form .label-input').val('');
			container.find('.setting-radio-options-form .value-input').val('');
		});

		// remove option
		$(document).on('click', '.delete-option', function(e) {
			e.preventDefault();
			var container = $('#change_setting_modal');

			var option = $(this).closest('tr');
			option.remove();

			if(!container.find('.option').length) {
				container.find('.setting-radio-options tbody').html('<tr><td colspan="3">No settings</td></tr>');
				container.find('.setting-select-options tbody').html('<tr><td colspan="4">No settings</td></tr>');
			}
		});


		// show add setting modal button clicked
		$(document).on('click', '.add-setting-modal', function(e) {
			e.preventDefault();

			var container = $('#change_setting_modal');
			container.find('.modal-title').text('Add setting');
			container.find('.btn-primary').removeClass('edit-setting');
			container.find('.btn-primary').addClass('add-setting');

			var section_id = $(this).closest('.section').attr('data-section-id');
			container.attr('data-section-id', section_id);

			var settingType = container.find('.setting-type').val();

			var setting = {
				type: settingType
			};

			container.attr('data-setting', escape(JSON.stringify(setting)));

			// reset inputs
			var inputs = container.find('.form-group.active input, .form-group.active select, .form-group.active textarea');
			for(var inputIndex = 0; inputIndex < inputs.length; inputIndex++) {
				var input = $(inputs[inputIndex]);
				input.val('');
			}

			// fill in setting fields in modal
			builder.Builder.loadSettingModal('#change_setting_modal', setting);

			container.modal('show');
		});

		// add setting button clicked
		$(document).on('click', '.add-setting', function(e) {
			var container = $('#change_setting_modal');
			var section_id = container.attr('data-section-id');
			var setting = JSON.parse(unescape(container.attr('data-setting')));

			// get all inputs
			var inputs = container.find('.form-group.active input, .form-group.active select, .form-group.active textarea');

			var params = {
				type: container.find('.setting-type').val(),
			};
			var errors = [];

			for(var i = 0; i < inputs.length; i++) {
				var input = $(inputs[i]);

				if(!input.closest('label').text().indexOf('(optional)') && input.val() == '') {
					errors[input.attr('name')] = 'Field required';
				}
				else {
					params[input.attr('name')] = input.val();
				}
			}

			// add options
			if(params.type === 'radio') {
				params.options = [];
				var radioOptions = container.find('.setting-radio-options .option');
				for(var j = 0; j < radioOptions.length; j++) {
					var option = {
						label: $(radioOptions[j]).find('.option-label').text(),
						value: $(radioOptions[j]).find('.option-value').text()
					};
					params.options.push(option);
				}
			}
			// add options with groups
			else if(params.type === 'select') {
				params.options = [];
				var selectOptions = container.find('.setting-select-options .option');
				for(var j = 0; j < options.length; j++) {
					var option = {
						group: $(radioOptions[j]).find('.option-group').attr('data-option-group'),
						label: $(radioOptions[j]).find('.option-label').text(),
						value: $(radioOptions[j]).find('.option-value').text()
					};
					params.options.push(option);
				}
			}

			builder.Builder.addSetting(section_id, params, function(data){

				builder.Builder.setUpdated(data.file.updated_at);

				builder.Builder.loadSections();

				container.modal('hide');

				window.location.reload();

				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File setting successfully added');
			});
		});


		// show edit setting modal button clicked
		$(document).on('click', '.edit-setting-modal', function(e) {
			e.preventDefault();

			var container = $('#change_setting_modal');
			container.find('.modal-title').text('Edit setting');
			container.find('.btn-primary').removeClass('add-setting');
			container.find('.btn-primary').addClass('edit-setting');

			var setting = JSON.parse(unescape($(this).closest('.setting').attr('data-setting')));

			container.attr('data-setting-id', setting.id);

			// merge json_value encoded data with setting object
			var shopifySetting = JSON.parse(setting.json_value);
			for(var shopifySettingIndex in shopifySetting) {
				setting[shopifySettingIndex] = shopifySetting[shopifySettingIndex];
			}

			container.attr('data-setting', escape(JSON.stringify(setting)));

			// reset inputs
			var inputs = container.find('.form-group.active input, .form-group.active select, .form-group.active textarea');
			for(var inputIndex = 0; inputIndex < inputs.length; inputIndex++) {
				var input = $(inputs[inputIndex]);
				input.val('');
			}

			// fill in setting fields in modal
			builder.Builder.loadSettingModal('#change_setting_modal', setting);

			container.modal('show');
		});


		// on setting type change
		$(document).on('change', '.setting-type', function(e) {
			var container = $('#change_setting_modal');
			var settingType = $(this).val();

			var setting = {
				type: settingType
			};

			// fill in setting fields in modal
			builder.Builder.loadSettingModal('#change_setting_modal', setting);
		});

		// add edit button clicked
		$(document).on('click', '.edit-setting', function(e) {
			var container = $('#change_setting_modal');
			var setting = JSON.parse(unescape(container.attr('data-setting')));
			var setting_id = container.attr('data-setting-id');

			// get all inputs
			var inputs = container.find('.form-group.active input, .form-group.active select, .form-group.active textarea');

			var params = {
				type: container.find('.setting-type').val(),
			};

			var errors = [];
			for(var i = 0; i < inputs.length; i++) {
				var input = $(inputs[i]);

				if(!input.closest('label').text().indexOf('optional') && input.val() == '') {
					errors[input.attr('name')] = 'Field required';
				}
				else {
					params[input.attr('name')] = input.val();
				}
			}

			// add options
			if(params.type === 'radio') {
				params.options = [];
				var radioOptions = container.find('.setting-radio-options .option');
				for(var j = 0; j < radioOptions.length; j++) {
					params.options.push({
						label: $(radioOptions[j]).find('.option-label').text(),
						value: $(radioOptions[j]).find('.option-value').text()
					});
				}
			}
			// add options with groups
			else if(params.type === 'select') {
				params.options = [];
				var selectOptions = container.find('.setting-select-options .option');
				for(var j = 0; j < selectOptions.length; j++) {
					params.options.push({
						group: $(selectOptions[j]).find('.option-group').attr('data-option-group'),
						label: $(selectOptions[j]).find('.option-label').text(),
						value: $(selectOptions[j]).find('.option-value').text()
					});
				}
			}

			builder.Builder.editSetting(setting_id, params, function(data){

				builder.Builder.setUpdated(data.file.updated_at);

				builder.Builder.loadSections();

				container.modal('hide');

				window.location.reload();

				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File setting successfully updated');
			});
		});


		// show delete setting modal button clicked
		$(document).on('click', '.delete-setting-modal', function(e) {
			e.preventDefault();

			var container = $('#delete_setting_modal');
			var setting = JSON.parse(unescape($(this).closest('.setting').attr('data-setting')));
			$('#delete_setting_modal').attr('data-setting-id', setting.id);

			container.modal('show');
		});

		// delete setting button clicked
		$(document).on('click', '.delete-setting', function(e) {
			var setting_id = $('#delete_setting_modal').attr('data-setting-id');
			builder.Builder.deleteSetting(setting_id, function(data){

				builder.Builder.setUpdated(data.file.updated_at);

				builder.Builder.loadSections();

				$('#delete_setting_modal').modal('hide');

				if(typeof ShopifyApp !== 'undefined') ShopifyApp.flashNotice('File setting deleted successfully');
			});
		});
	}

});