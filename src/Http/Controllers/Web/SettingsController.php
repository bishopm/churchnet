<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\SettingsRepository;
use Bishopm\Churchnet\Repositories\DistrictsRepository;
use Bishopm\Churchnet\Repositories\CircuitsRepository;
use Bishopm\Churchnet\Models\Setting;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateSettingRequest;
use Bishopm\Churchnet\Http\Requests\UpdateSettingRequest;
use Mapper;

class SettingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $setting;
    private $districts;
    private $circuits;

    public function __construct(SettingsRepository $setting, DistrictsRepository $districts, CircuitsRepository $circuits)
    {
        $this->setting = $setting;
        $this->districts = $districts;
        $this->circuits = $circuits;
    }

    public function index()
    {
        $settings = $this->setting->all();
        return view('churchnet::settings.index', compact('settings'));
    }

    public function edit(Setting $setting)
    {
        $data['districts']=$this->districts->all();
        $data['circuits']=$this->circuits->all();
        $data['setting']=$setting;
        return view('churchnet::settings.edit', $data);
    }

    public function create()
    {
        $data['districts']=$this->districts->all();
        $data['circuits']=$this->circuits->all();
        return view('churchnet::settings.create', $data);
    }

    public function show($id)
    {
        $setting=Setting::with('services')->find($id);
        Mapper::map($setting->latitude, $setting->longitude, ['zoom' => 16, 'type' => 'HYBRID']);
        Mapper::marker($setting->latitude, $setting->longitude, ['title' => $setting->setting . " setting"]);
        return view('churchnet::settings.show', compact('setting'));
    }

    public function store(CreateSettingRequest $request)
    {
        $arr= array();
        $arr['setting_key']=$request->setting_key;
        $arr['setting_value']=$request->setting_value;
        if ($request->level=="Connexion") {
            $arr['level']=$request->level;
        } elseif ($request->level=="Bishopm\\Churchnet\\Models\\District") {
            $arr['level']="District";
            $arr['relatable_type']=$request->level;
            $arr['relatable_id']=$request->district_id;
        } else {
            $arr['level']="Circuit";
            $arr['relatable_type']=$request->level;
            $arr['relatable_id']=$request->circuit_id;
        }
        $setting = Setting::create($arr);
        return redirect()->route('admin.settings.index')
            ->withSuccess('New setting added');
    }
    
    public function update(Setting $setting, UpdateSettingRequest $request)
    {
        $arr= array();
        $arr['setting_key']=$request->setting_key;
        $arr['setting_value']=$request->setting_value;
        if ($request->level=="Connexion") {
            $arr['level']=$request->level;
        } elseif ($request->level=="Bishopm\\Churchnet\\Models\\District") {
            $arr['level']="District";
            $arr['relatable_type']=$request->level;
            $arr['relatable_id']=$request->district_id;
        } else {
            $arr['level']="Circuit";
            $arr['relatable_type']=$request->level;
            $arr['relatable_id']=$request->circuit_id;
        }
        $this->setting->update($setting, $arr);
        return redirect()->route('admin.settings.index')->withSuccess('Setting has been updated');
    }

    public function destroy(Setting $setting)
    {
        $this->setting->destroy($setting);
        return view('churchnet::settings.index')->withSuccess('The ' . $setting->setting . ' setting has been deleted');
    }
}
