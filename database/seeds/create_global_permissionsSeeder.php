<?php

namespace Database\Seeders;

use App\Setting;
use Illuminate\Database\Seeder;

class create_global_permissionsSeeder extends Seeder
{
    public function run()
    {

        if(!Setting::where("key", "GlobalReadPermissions")->exists()){
            $setting = new Setting();
            $setting->key = "GlobalReadPermissions";
            $setting->value = false;
            $setting->save();
        }
        if(!Setting::where("key", "GlobalWritePermissions")->exists()){
            $setting = new Setting();
            $setting->key = "GlobalWritePermissions";
            $setting->value = false;
            $setting->save();
        }
        if(!Setting::where("key", "GlobalDownloadPermissions")->exists()){
            $setting = new Setting();
            $setting->key = "GlobalDownloadPermissions";
            $setting->value = false;
            $setting->save();
        }

        if(!Setting::where("key", "DefaultView")->exists()){
            $setting = new Setting();
            $setting->key = "DefaultView";
            $setting->value = "grid";
            $setting->save();
        }

    }
}
