<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\HouseholdsRepository;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Circuit;
use Cviebrock\EloquentTaggable\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateHouseholdRequest;
use Bishopm\Churchnet\Http\Requests\UpdateHouseholdRequest;

class HouseholdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $household;

    public function __construct(HouseholdsRepository $household)
    {
        $this->household = $household;
    }

    public function index()
    {
        return Household::all();
    }
    
    public function search(Request $request)
    {
        $socs=array();
        if (isset($request->circuit)) {
            $circuit = Circuit::with('societies')->where('id', $request->circuit)->first();
            foreach ($circuit->societies as $soc) {
                $socs[]=$soc->id;
            }
        } else {
            foreach ($request->societies as $soc) {
                $socs[]=intval($soc);
            }
        }
        return Household::with('individuals')->whereIn('society_id', $socs)->where('addressee', 'like', '%' . $request->search . '%')->get();
    }

    public function stickers(Request $request)
    {
        $indivs = Individual::insociety($request->society)
                        ->where('surname', 'like', '%' . $request->search . '%')
                        ->orWhere('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('cellphone', 'like', '%' . $request->search . '%')->select('household_id')->groupBy('household_id')->get();
        return Household::with('individuals')->whereIn('id', $indivs)->get();
    }

    public function newstickers(Request $request)
    {
        $addressee='';
        $fsize = count($request->indivs);
        foreach ($request->indivs as $ndx=>$indiv) {
            $addressee = $addressee . $indiv['firstname'] . ' ' . $indiv['surname'];
            if (($fsize > 1) and ($ndx < $fsize-2)) {
                $addressee = $addressee . ", ";
            } elseif (($fsize > 1) and ($ndx == $fsize-2)) {
                $addressee = $addressee . " and ";
            }
        }
        $household = Household::create(['addressee'=>$addressee, 'sortsurname'=>$request->indivs[0]['surname']]);
        foreach ($request->indivs as $ind) {
            if ($ind['memberstatus']=='adult') {
                $ind['memberstatus']='non-member';
            }
            $newindiv = Individual::create(['firstname'->$ind['firstname'], 'surname'=>$ind['surname'], 'sex'=>$ind['sex'], 'cellphone']);
        }
        return $addressee;
    }

    public function query($household, Request $request)
    {
        return DB::select(DB::raw($request->sql))->toArray();
    }

    public function create()
    {
        return view('connexion::households.create');
    }

    public function journeyedit(Request $request)
    {
        $household=Household::find($request->id);
        $this->household->update($household, $request->all());
        return "Household updated";
    }

    public function show($id)
    {
        $household = Household::with('individuals.groups', 'individuals.tags', 'pastorals.individual', 'location')->where('id', $id)->first();
        $household->alltags = Tag::where('type', 'leader')->get();
        if (in_array($household->society->id, \Illuminate\Support\Facades\Request::get('user_soc'))) {
            return $household;
        } else {
            return "Unauthorised";
        }
    }

    public function store(Request $request)
    {
        return $this->household->create($request->all());
    }
    
    public function update($id, Request $request)
    {
        $household = $this->household->find($id);
        $data = $this->household->update($household, $request->all());
        return $data;
    }

    public function destroy(Household $household)
    {
        $this->household->destroy($household);
        return view('connexion::households.index')->withSuccess('The ' . $household->household . ' household has been deleted');
    }
}
