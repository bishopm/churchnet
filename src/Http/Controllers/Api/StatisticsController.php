<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\StatisticsRepository;
use Bishopm\Churchnet\Models\Statistic;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Service;
use App\Http\Controllers\Controller;
use DB;
use Bishopm\Churchnet\Http\Requests\CreateStatisticRequest;
use Bishopm\Churchnet\Http\Requests\UpdateStatisticRequest;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{

    /*
    Weekly stats: Grow - use Journey, Give - planned giving, Connect - small groups, Worship - attendance, Serve - teams
     */

    private $statistic;

    public function __construct(StatisticsRepository $statistic)
    {
        $this->statistic = $statistic;
    }

    public function getfordate($society,$statdate){
        $serv = Service::where('society_id',$society)->pluck('id')->toArray();
        $stats = Statistic::whereIn('service_id',$serv)->where('statdate',$statdate)->get();
        $data=array();
        foreach ($stats as $stat) {
            $data[$stat->service_id]=$stat->attendance;
        }
        return $data;
    }

    public function index($society,$yr)
    {
        $soc = Society::with('services')->find($society);
        $data['society'] = $soc;
        $labels=array();
        foreach ($soc->services as $service){
            $stats = Statistic::whereRaw('SUBSTRING(statdate, 1,  4) = ' . $yr)->where('service_id',$service->id)->orderBy('statdate')->get();
            foreach ($stats as $stat) {
                $label=substr($stat->statdate,5);
                if (!in_array($label, $labels)) {
                    $labels[]=$label;
                }
                $times[$service->id]=$service->servicetime;
                $dum[$label][$service->servicetime]=$stat->attendance;
            }
        }
        asort($labels);
        $data['labels']=array_values($labels);
        if (isset($times)) {
            foreach ($times as $ss=>$tt){
                $average[$tt] = round(Statistic::whereRaw('SUBSTRING(statdate, 1,  4) = ' . $yr)->where('service_id',$ss)->avg('attendance'),2);
            }
            foreach ($labels as $label) {
                foreach ($times as $stime) {
                    if (!array_key_exists($stime,$dum[$label])) {
                        $datasets[$stime . ' (' . $average[$stime] . ')'][$label]=0;
                    } else {
                        $datasets[$stime . ' (' . $average[$stime] . ')'][$label]=$dum[$label][$stime];
                    }
                }
            }
            foreach ($datasets as $kk=>$ds) {
                ksort($ds);
                $data['datasets'][$kk] = array_values($ds);
            }
        }
        $ayrs=array();
        foreach ($soc->services as $sss){
            $yrs=Statistic::where('service_id',$sss->id)->get();
            foreach ($yrs as $yr) {
                if (!in_array(substr($yr->statdate,0,4),$ayrs)){
                    $ayrs[]=intval(substr($yr->statdate,0,4));
                }
            }
        }
        $ayrs=array_values($ayrs);
        sort($ayrs);
        $data['years']=$ayrs;
        return $data;
    }

    public function discipleship($society,$yr)
    {
        $soc = Society::with('services')->find($society);
        $data['society'] = $soc;
        return $data;
    }

    public function store(Request $request)
    {
        $statdate = $request->statdate;
        foreach ($request->attendance as $stat){
            if (isset($stat['service_id'])){
                $deletion = Statistic::where('service_id',$stat['service_id'])->where('statdate',$statdate)->delete();
            } else {
                return $stat;
            }
            if (isset($stat['attendance'])) {
                $statistic = Statistic::create(['service_id'=>$stat['service_id'], 'attendance'=>$stat['attendance'], 'statdate'=>$statdate]);
            }
        }
    }
    
}
