<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\HouseholdsRepository;
use Bishopm\Churchnet\Models\Household;
use Cviebrock\EloquentTaggable\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
        foreach ($request->societies as $soc) {
            $socs[]=intval($soc);
        }
        return Household::with('individuals')->whereIn('society_id', $socs)->where('addressee', 'like', '%' . $request->search . '%')->get();
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
        $household = Household::with('individuals.groups','individuals.tags')->where('id', $id)->first();
        $household->alltags = Tag::where('type','leader')->get();
        return $household;
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
