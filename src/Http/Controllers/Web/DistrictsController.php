<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\DistrictsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Denomination;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateDistrictRequest;
use Bishopm\Churchnet\Http\Requests\UpdateDistrictRequest;
use Mapper;

class DistrictsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	private $district, $society;

	public function __construct(DistrictsRepository $district, SocietiesRepository $society)
    {
        $this->district = $district;
        $this->society = $society;
    }

	public function edit(District $district)
    {
        return view('churchnet::districts.edit', compact('district'));
    }

    public function create()
    {
        return view('churchnet::districts.create');
    }

	public function show($districtnum)
	{
        $district=District::with('circuits','individuals','location')->find($districtnum);
        $first=true;
        foreach ($district->circuits as $circuit){
            foreach ($circuit->societies as $society){
                if ($society->location) {
                    $title=$society->society . " (" . $society->circuit->circuitnumber . " " . $society->circuit->circuit . ")";
                    if ($first){
                        Mapper::map($society->location->latitude, $society->location->longitude, ['cluster' => false, 'type' => 'HYBRID', 'center'=>false, 'markers' => ['title' => $title]]);
                        $first=false;
                    }
                    Mapper::marker($society->location->latitude, $society->location->longitude, ['title' => $title]);
                }
            }
        }
        if ($district->location) {
            Mapper::map($district->location->latitude, $district->location->longitude, ['zoom' => 15, 'center' => true, 'markers' => ['title' => $district->location->description]]);
            Mapper::marker($district->location->latitude, $district->location->longitude, ['title' => $district->location->description]);
        }
        $data['district']=$district;
        $data['title']=$district->district . " " . $district->denomination->provincial;
        return view('churchnet::districts.show', $data);
	}

    public function store(CreateDistrictRequest $request)
    {
        $soc=$this->district->create($request->all());

        return redirect()->route('admin.districts.show',$soc->id)
            ->withSuccess('New district added');
    }
	
    public function update(District $district, UpdateDistrictRequest $request)
    {
        $this->district->update($district, $request->all());
        return redirect()->route('admin.districts.index')->withSuccess('District has been updated');
    }

    public function destroy(District $district)
    {
        $this->district->destroy($district);
        return view('churchnet::districts.index')->withSuccess('The ' . $district->district . ' district has been deleted');
    }

}