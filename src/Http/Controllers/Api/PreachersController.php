<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\PreachersRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Preacher;
use Bishopm\Churchnet\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreatePreacherRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePreacherRequest;

class PreachersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $preacher;
    private $individuals;
    private $societies;

    public function __construct(PreachersRepository $preacher, SocietiesRepository $societies)
    {
        $this->preacher = $preacher;
        $this->societies = $societies;
    }

    public function index($circuit)
    {
        return json_decode($this->preacher->allforcircuit($circuit));
    }

    public function phone($circuit, Request $request)
    {
        $preacher = Preacher::with('society')->where('phone', $request->phone)->where('circuit_id', $circuit)->first();
        $yy=date('Y');
        $mm=date('n');
        $dd=date('j');
        $plans = Plan::with('society', 'service')->where('preacher_id', $preacher->id)->where('planyear', '>=', $yy)->where('planmonth', '>=', $mm)->where('planday', '>=', $dd)->orderBy('planyear', 'planmonth', 'planday')->get()->take(12);
        foreach ($plans as $plan) {
            $dum['id'] = $plan->id;
            $dum['society'] = $plan->society->society;
            $dum['servicetime'] = $plan->service->servicetime;
            $dum['servicedate'] = date('Y-m-d', strtotime($plan->planmonth . "/" . $plan->planday . "/" . $plan->planyear));
            $data[]=$dum;
        }
        $preacher->upcoming=$data;
        return $preacher;
    }

    public function edit($circuit, Preacher $preacher)
    {
        $data['societies'] = $this->societies->all();
        $data['preacher'] = $preacher;
        return view('connexion::preachers.edit', $data);
    }

    public function create()
    {
        $data['individuals'] = $this->individuals->all();
        $data['societies'] = $this->societies->all();
        if (count($data['societies'])) {
            return view('connexion::preachers.create', $data);
        } else {
            return redirect()->route('admin.societies.create')->with('notice', 'At least one society must be added before adding a preacher');
        }
    }

    public function show($circuit, $preacher)
    {
        return $this->preacher->findforcircuit($circuit, $preacher);
    }

    public function store(CreatePreacherRequest $request)
    {
        $this->preacher->create($request->except('image', 'token'));

        return "New preacher added";
    }
    
    public function update($circuit, Preacher $preacher, UpdatePreacherRequest $request)
    {
        $this->preacher->update($preacher, $request->except('token'));
        return "Preacher has been updated";
    }

    public function destroy($circuit, Preacher $preacher)
    {
        $this->preacher->destroy($preacher);
    }
}
