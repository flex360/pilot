<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\Setting;
use Illuminate\Support\Facades\Request;

class SettingController extends AdminController
{
    public static $namespace = '\\Flex360\\Pilot\\Pilot\\';
    public static $model = 'Setting';
    public static $viewFolder = 'settings';

    //cast the value field in database as an array instead of Json
    protected $casts = [
        'value' => 'array',
    ];

    public function index()
    {
        $settings = config('settings');
        $keys = array_keys($settings);
        $firstKey = array_shift($keys);

        if (empty($firstKey)) {
            abort(404, 'No settings defined.');
        }

        return redirect()->route('admin.setting.default', $firstKey);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function settings($key)
    {
        $site = Site::getCurrent();

        $configSetting = config('settings.' . $key);

        if (empty($configSetting)) {
            abort(404, 'Page not found.');
        }

        $configSetting['key'] = $key;

        $setting = Setting::withoutGlobalScopes()
            ->firstOrCreate([
                'key' => $key,
                'value' => '',
                'name' => $configSetting['label'],
                'site_id' => $site->id,
            ]);

        $formOptions = [
            'route' => ['admin.setting.update', $setting->id],
            'method' => 'put',
        ];

        mimic('Settings: ' . $setting->name);

        return view('pilot::admin.settings.index', compact(
            'configSetting',
            'setting',
            'formOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $data = Request::except('_method', '_token');

        $setting = Setting::withoutGlobalScopes()->find($id);

        $setting->update(['fields' => json_encode($data)]);

        return redirect()->route('admin.setting.default', [
            'setting' => $setting->key
        ])
        ->with('alert-success', 'Setting saved successfully!');
    }
}
