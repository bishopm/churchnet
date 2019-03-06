<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Person;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateSocietyRequest;
use Bishopm\Churchnet\Http\Requests\UpdateSocietyRequest;

class SocietiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $society;

    public function __construct(SocietiesRepository $society)
    {
        $this->society = $society;
    }

    public function index()
    {
        $societies = $this->society->all();
        return $societies;
    }

    public function edit(Society $society)
    {
        return view('churchnet::societies.edit', compact('society'));
    }

    public function create()
    {
        return view('churchnet::societies.create');
    }

    public function show($circuit, $slug)
    {
        $data['society']=$this->society->findBySlugForCircuitSlug($circuit, $slug);
        $data['stewards']=$data['society']->circuit->tagged('Society steward')->get();
        return view('churchnet::societies.show', $data);
    }

    public function store(CreateSocietyRequest $request)
    {
        $soc=$this->society->create($request->all());

        return redirect()->route('admin.societies.show', $soc->id)
            ->withSuccess('New society added');
    }
    
    public function update(Society $society, UpdateSocietyRequest $request)
    {
        $this->society->update($society, $request->all());
        return redirect()->route('admin.societies.index')->withSuccess('Society has been updated');
    }

    public function destroy(Society $society)
    {
        $this->society->destroy($society);
        return view('churchnet::societies.index')->withSuccess('The ' . $society->society . ' society has been deleted');
    }
}
