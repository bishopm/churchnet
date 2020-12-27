<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\SettingsRepository;
use Bishopm\Churchnet\Models\Setting;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateSettingRequest;
use Bishopm\Churchnet\Http\Requests\UpdateSettingRequest;

class SettingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $setting;

    public function __construct(SettingsRepository $setting)
    {
        $this->setting = $setting;
    }

    public function index($circuit)
    {
        $setting=$this->setting->allforcircuit($circuit);
        return json_decode($this->setting->allforcircuit($circuit));
    }

    public function edit($circuit, Setting $setting)
    {
        $data['setting'] = $setting;
        return view('connexion::settings.edit', $data);
    }

    public function create()
    {
        $data['individuals'] = $this->individuals->all();
        return view('connexion::settings.create', $data);
    }

    public function show($circuit, $setting)
    {
        return $this->setting->find($setting);
    }

    public function store(CreateSettingRequest $request)
    {
        $this->setting->create($request->except('image', 'token'));

        return redirect()->route('admin.settings.index')
            ->withSuccess('New setting added');
    }
    
    public function update($circuit, Setting $setting, UpdateSettingRequest $request)
    {
        $this->setting->update($setting, $request->except('token'));
        return "Setting has been updated";
    }
}
