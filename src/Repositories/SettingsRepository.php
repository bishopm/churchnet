<?php namespace Bishopm\Churchnet\Repositories;

use Bishopm\Churchnet\Repositories\EloquentBaseRepository;
use Bishopm\Churchnet\Models\Circuit;
use Illuminate\Support\Facades\DB;
use Bishopm\Churchnet\Models\Setting;

class SettingsRepository extends EloquentBaseRepository
{
    public function allforrelatable($relatable, $relatable_id)
    {
        if ($relatable=="Connexion") {
            return $this->model->where('relatable', '=', "Connexion")->get();
        } else {
            return $this->model->where('relatable', $relatable)->where('relatable_id', $relatable_id)->get();
        }
    }

    public function allforcircuit($circuitnumber)
    {
        $this->circuit=Circuit::find($circuitnumber);
        $allkeys=array('Connexion*presiding_bishop','Connexion*general_secretary','Circuit*superintendent','Circuit*circuit_secretary','Circuit*supervisor_of_studies','Circuit*local_preachers_secretary','District*district_bishop','Circuit*circuit_stewards','Circuit*circuit_treasurer');
        $settings=DB::table('settings')->where('level', '=', 'Connexion')
        ->orWhere(function ($query) {
            $query->where('level', '=', 'District')
                  ->where('relatable_id', '=', $this->circuit->district_id);
        })
        ->orWhere(function ($query) {
            $query->where('level', '=', 'Circuit')
                  ->where('relatable_id', '=', $this->circuit->id);
        })->get();
        foreach ($allkeys as $key) {
            $data=explode('*', $key);
            if ($data[0]=="Connexion") {
                $relid=0;
            } elseif ($data[0]=="District") {
                $relid=$this->circuit->district_id;
            } else {
                $relid=$this->circuit->id;
            }
            if (!$settings->contains('setting_key', $data[1])) {
                $newset=Setting::create(['level'=>$data[0], 'relatable_id'=>$relid, 'relatable_type'=>'Bishopm\\Churchnet\\Models\\' . $data[0], 'setting_key'=>$data[1], 'setting_value'=>'', 'description'=>str_replace('_', ' ', ucfirst($data[1]))]);
            }
        }
        return $settings;
    }
}
