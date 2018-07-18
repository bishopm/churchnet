<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\HouseholdsRepository;
use Bishopm\Churchnet\Models\Household;
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
        return Household::with('individuals')->where('addressee', 'like', '%' . $request->search . '%')->get();
    }

    public function query($household, Request $request)
    {
        return DB::select(DB::raw($request->sql))->toArray();
    }

    public function create()
    {
        return view('connexion::households.create');
    }

    public function show($id)
    {
        return Household::with('individuals')->where('id', $id)->first();
    }

    public function store(CreateHouseholdRequest $request)
    {
        $soc=$this->household->create($request->all());

        return redirect()->route('admin.households.show', $soc->id)
            ->withSuccess('New household added');
    }
    
    public function update(Household $household, UpdateHouseholdRequest $request)
    {
        $this->household->update($household, $request->all());
        return redirect()->route('admin.households.index')->withSuccess('Household has been updated');
    }

    public function destroy(Household $household)
    {
        $this->household->destroy($household);
        return view('connexion::households.index')->withSuccess('The ' . $household->household . ' household has been deleted');
    }
}
