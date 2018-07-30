<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\IndividualsRepository;
use Bishopm\Churchnet\Models\Individual;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateIndividualRequest;
use Bishopm\Churchnet\Http\Requests\UpdateIndividualRequest;

class IndividualsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $individual;

    public function __construct(IndividualsRepository $individual)
    {
        $this->individual = $individual;
    }

    public function index()
    {
        return Individual::all();
    }
    
    public function phone(Request $request)
    {
        return Individual::with('household.individuals','groups')->where('cellphone', $request->phone)->first();
    }
    
    public function search(Request $request)
    {
        return Individual::with('individuals')->where('addressee', 'like', '%' . $request->search . '%')->get();
    }

    public function query($individual, Request $request)
    {
        return DB::select(DB::raw($request->sql))->toArray();
    }

    public function edit(Individual $individual)
    {
        return view('connexion::individuals.edit', compact('individual'));
    }

    public function create()
    {
        return view('connexion::individuals.create');
    }

    public function show($no)
    {
        return $this->individual->find($no);
    }

    public function store(CreateIndividualRequest $request)
    {
        $soc=$this->individual->create($request->all());

        return redirect()->route('admin.individuals.show', $soc->id)
            ->withSuccess('New individual added');
    }
    
    public function update(Individual $individual, UpdateIndividualRequest $request)
    {
        $this->individual->update($individual, $request->all());
        return redirect()->route('admin.individuals.index')->withSuccess('Individual has been updated');
    }

    public function destroy(Individual $individual)
    {
        $this->individual->destroy($individual);
        return view('connexion::individuals.index')->withSuccess('The ' . $individual->individual . ' individual has been deleted');
    }
}
