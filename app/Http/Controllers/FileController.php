<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;

use StdClass;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use ThemeSettingBuilder\ShopifyData;

use App\File;
use App\Section;
use App\Setting;

class FileController extends Controller
{

    // get all imported files
    public function index() {
        $shop = $this->shop;

        $files = File::where('shop_id', $shop->id)->orderBy('updated_at', 'DESC')->get();

        return ['status' => 'success', 'files' => $files];

    }

    // import a theme's settings file
    public function import(Request $req, $theme_id) {
        $shop = $this->shop;
        
        // basic input validation, will use built in validator later
        $name = $req->input('name');

        if(!$name || !$theme_id) {
            return ['status' => 'error', 'message' => 'You must give a name and a valid theme ID.'];
        }

        // check if file already exist with the same name
        $file = File::where('title', $name)->where('shop_id', $shop->id)->first();
        if(!$file) {
            $file = new File;
            $file->shop_id = $shop->id;
            $file->shopify_theme_id = $theme_id;
        }
        $file->title = $name;
        $file->save();

        $settingsFile = $this->shopifyData->getThemeSettingsFile($theme_id);
        if(!$settingsFile) {
            return ['status' => 'error', 'message' => 'An error occured when trying to sync the file to Shopify.'];
        }


        $sections = json_decode($settingsFile->value);

        // loop through all sections to import into the database
        foreach($sections as $s) {

            $section = Section::where('title', $s->name)->first();
            if(!$section) {
                $section = new Section;
                $section->file_id = $file->id;
            }
            $section->title = $s->name;
            $section->save();

            // loop through all settings with each section
            foreach($s->settings as $set) {
                $title = isset($set->id) ? $set->id : $set->type;
                $setting = Setting::where('title', $title)->first();
                if(!$setting) {
                    $setting = new Setting;
                    $setting->section_id = $section->id;
                }
                $setting->title = $title;
                $setting->json_value = json_encode($set);
                $setting->save();
            }
        }

        $file = File::where('title', $name)->where('shop_id', $shop->id)->with('sections.settings')->first();

        return ['status' => 'success', 'file' => $file];
    }

    // sync a file to shopify
    public function sync(Request $req, $file_id) {
        $shop = $this->shop;

        // check if file exist
        $file = File::where('id', $file_id)->where('shop_id', $shop->id)->with('sections.settings')->first();
        if(!$file) {
            return ['status' => 'error', 'message' => 'File doesn\'t exist.'];
        }

        $data = [];

        // loop through sections
        foreach($file->sections as $section) {
            $s = [
                'name' => $section->title,
                'settings' => []
            ];

            // loop through settings
            foreach($section->settings as $setting) {
                $settingValue = json_decode($setting->json_value);
                unset($settingValue->title);
                $s['settings'][] = $settingValue;
            }

            // skip sections that have not settings
            if(!count($s['settings'])) continue;

            $data[] = $s;
        }

        $result = $this->shopifyData->setThemeSettingsFile($file->shopify_theme_id, $data);

        return ['status' => 'success', 'message' => 'File synced to Shopify successfully!'];
    }

    // change the select shopify theme for a file
    public function changeTheme(Request $req, $id) {
        // check that file id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a file id.'];
        }

        // check that file exists
        $file = File::where('id', $id)->first();
        if(!$file) {
            return ['status' => 'error', 'message' => 'File doesn\'t exist.'];
        }

        // check for permission
        if($this->shop->id != $file->shop_id) {
            return ['status' => 'error', 'message' => 'You do not have permission to change the select of this file.'];
        }

        // validate input
        $theme_id = $req->input('theme_id');
        if(!$theme_id) {
            return ['status' => 'error', 'message' => 'You must provide a theme id for the file.'];
        }

        $file->shopify_theme_id = $theme_id;
        $file->save();

        return ['status' => 'success', 'updated_file' => $file];
    }

    // edit a file
    public function edit(Request $req, $id) {
        // check that file id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a file id.'];
        }

        // check that file exists
        $file = File::where('id', $id)->first();
        if(!$file) {
            return ['status' => 'error', 'message' => 'File doesn\'t exist.'];
        }

        // check for permission
        if($this->shop->id != $file->shop_id) {
            return ['status' => 'error', 'message' => 'You do not have permission to edit this file.'];
        }

        // validate input
        $name = $req->input('name');
        if(!$name) {
            return ['status' => 'error', 'message' => 'You must provide a name for the file.'];
        }

        $file->title = $name;
        $file->save();

        return ['status' => 'success', 'updated_file' => $file];
    }

    // delete an imported file
    public function delete(Request $req, $id) {
        // check that file id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a file id.'];
        }

        // check that file exists
        $file = File::where('id', $id)->first();
        if(!$file) {
            return ['status' => 'error', 'message' => 'File doesn\'t exist.'];
        }

        // check for permission
        if($this->shop->id != $file->shop_id) {
            return ['status' => 'error', 'message' => 'You do not have permission to delete this file.'];
        }

        $file->delete();

        return ['status' => 'success', 'deleted_file' => $file];
    }

    // get all sections for a file by file id
    public function getSections($id) {
        $file = File::where('id', $id)->with('sections.settings')->first();

        if(!$file) {
            return ['status' => 'error', 'message' => 'File doesn\'t exist.'];
        }

        return ['status' => 'success', 'sections' => $file->sections];
    }

    // add a file section
    public function addSection(Request $req, $id) {
        // check that file id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a file id.'];
        }

        // check that file exists
        $file = File::where('id', $id)->first();
        if(!$file) {
            return ['status' => 'error', 'message' => 'File doesn\'t exist.'];
        }

        // validate input
        $title = $req->input('title');
        if(!$title) {
            return ['status' => 'error', 'message' => 'You must provide a title for the section.'];
        }

        // check that section exists
        $section = Section::where('title', $title)->first();
        if($section) {
            return ['status' => 'error', 'message' => 'Section already exists.'];
        }

        // create a new section
        $section = new Section;
        $section->file_id = $file->id;
        $section->title = $title;
        $section->save();

        return ['status' => 'success', 'created_section' => $section];
    }

    // edit a file section
    public function editSection(Request $req, $id) {
        // check that section id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a section id.'];
        }

        // check that section exists
        $section = Section::where('id', $id)->first();
        if(!$section) {
            return ['status' => 'error', 'message' => 'Section doesn\'t exist.'];
        }

        // validate input
        $title = $req->input('title');
        if(!$title) {
            return ['status' => 'error', 'message' => 'You must provide a title for the section.'];
        }

        // create a new section
        $section->title = $title;
        $section->save();

        return ['status' => 'success', 'updated_section' => $section];
    }

    // delete a file section
    public function deleteSection(Request $req, $id) {
        // check that section id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a section id.'];
        }

        // check that section exists
        $section = Section::where('id', $id)->first();
        if(!$section) {
            return ['status' => 'error', 'message' => 'Section doesn\'t exist.'];
        }

        // check for permission
        if($this->shop->id != $section->file->shop_id) {
            return ['status' => 'error', 'message' => 'You do not have permission to delete this section.'];
        }

        $section->delete();

        return ['status' => 'success', 'deleted_section' => $section];
    }

    // add a setting
    public function addSetting(Request $req) {
        // validate input
        $params = $req->all();
        if(!count($params)) {
            return ['status' => 'error', 'message' => 'You must provide fill in all required fields.'];
        }

        // loop through each field and remove empty ones
        foreach($params as $key => $value) {
            if(!$value) unset($params[$key]);
        }

        // parse the int for the max image sizes
        if(isset($params['max-height'])) {
            $params['max-height'] = intval($params['max-height']);
        }
        if(isset($params['max-width'])) {
            $params['max-width'] = intval($params['max-width']);
        }

        // parse the boolean value for default when field is a checkbox
        if($params['type'] == 'checkbox' && isset($params['default'])) {
            $params['default'] = $params['default'] == 'true' ? true : false;
        }

        // decode the options json array when field is a radio or select type
        /*if(($params['type'] == 'radio' || $params['type'] == 'select') && isset($params['options'])) {
            $params['options'] = json_decode($params['options']);
        }*/
        
        // check that section id was given
        if(!isset($params['section_id'])) {
            return ['status' => 'error', 'message' => 'You got to give a section id.'];
        }

        $title = $params['title'];

        // check that section exists
        $section = Section::where('id', $params['section_id'])->first();
        if(!$section) {
            return ['status' => 'error', 'message' => 'Section doesn\'t exist.'];
        }

        // check that setting exists
        $setting = Setting::where('section_id', $section->id)->where('title', $title)->first();
        if($setting) {
            return ['status' => 'error', 'message' => 'Setting already exists.'];
        }

        // create a new setting
        $setting = new Setting;
        $setting->section_id = $params['section_id'];
        $setting->title = $title;
        $setting->json_value = json_encode(array_except($params, ['section_id']));
        $setting->save();

        return ['status' => 'success', 'created_setting' => $setting];
    }

    // edit a setting
    public function editSetting(Request $req, $id) {
        // check that setting id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a setting id.'];
        }

        // check that section exists
        $setting = Setting::where('id', $id)->first();
        if(!$setting) {
            return ['status' => 'error', 'message' => 'Setting doesn\'t exist.'];
        }

        // validate input
        $params = $req->all();
        if(!count($params)) {
            return ['status' => 'error', 'message' => 'You must provide fill in all required fields.'];
        }

        // loop through each field and remove empty ones
        foreach($params as $key => $value) {
            if(!is_array($value) && !$value) unset($params[$key]);
        }

        // parse the int for the max image sizes
        if(isset($params['max-height'])) {
            $params['max-height'] = intval($params['max-height']);
        }
        if(isset($params['max-width'])) {
            $params['max-width'] = intval($params['max-width']);
        }

        // parse the boolean value for default when field is a checkbox
        if($params['type'] == 'checkbox' && isset($params['default'])) {
            $params['default'] = $params['default'] == 'true' ? true : false;
        }

        // decode the options json array when field is a radio or select type
        /*if(($params['type'] == 'radio' || $params['type'] == 'select') && isset($params['options'])) {
            $params['options'] = json_decode($params['options']);
        }*/

        Log::info(json_encode($params));

        // edit a setting
        $setting->title = $params['title'];
        $setting->json_value = json_encode($params);
        $setting->save();

        return ['status' => 'success', 'updated_setting' => $setting];
    }

    // delete a file setting
    public function deleteSetting(Request $req, $id) {
        // check that setting id was given
        if(!$id) {
            return ['status' => 'error', 'message' => 'You got to give a setting id.'];
        }

        // check that setting exists
        $setting = Setting::where('id', $id)->first();
        if(!$setting) {
            return ['status' => 'error', 'message' => 'Setting doesn\'t exist.'];
        }

        // check for permission
        if($this->shop->id != $setting->section->file->shop_id) {
            return ['status' => 'error', 'message' => 'You do not have permission to delete this setting.'];
        }

        $setting->delete();

        return ['status' => 'success', 'deleted_setting' => $setting];
    }
}
