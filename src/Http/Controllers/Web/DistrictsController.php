<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\DistrictsRepository;
use Bishopm\Churchnet\Repositories\SocietiesRepository;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Denomination;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateDistrictRequest;
use Bishopm\Churchnet\Http\Requests\UpdateDistrictRequest;

class DistrictsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $district;
    private $society;

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
        $district=District::with('circuits.societies.location', 'people.individual', 'location')->find($districtnum);
        $first=true;
        foreach ($district->circuits as $circuit) {
            foreach ($circuit->societies as $society) {
                if ($society->location) {
                    $title="<b><a href=\"" . url('/circuits/' . $circuit->slug . '/' . $society->slug) . "\">" . $society->society . "</a></b> - <a href=\"" . url('/circuits/' . $circuit->slug) . "\">" . $society->circuit->circuitnumber . " " . $society->circuit->circuit . "</a>";
                    $title=str_replace('\'', '\\\'', $title);
                    $data['markers'][]=['title'=>$title, 'lat'=>$society->location->latitude, 'lng'=>$society->location->longitude];
                }
            }
        }
        $data['district']=$district;
        $data['title']=$district->district . " " . $district->denomination->provincial;
        return view('churchnet::districts.show', $data);
    }

    public function ministers($districtnum)
    {
        $data['district']=District::with('denomination','people.individual')->where('id',$districtnum)->first();
        $ministers=Person::districtministers($districtnum)->with('tags','circuit','individual')->get();
        $data['ministers']=array();
        foreach ($data['district']->people as $dminister) {
            if (isset($dminister->individual)){
                $data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['name']=$dminister->individual->title . ' ' . $dminister->individual->firstname . ' <b>' . $dminister->individual->surname . '</b>';
                $data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['id']=$dminister->individual->id;
                $data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['district']=$data['district'];
                $data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['image']=$dminister->individual->image;
                foreach ($dminister->tags as $tag){
                    $data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['tags'][]=$tag->name;
                }
                if (isset($data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['tags'])){
                    asort($data['ministers'][$dminister->individual->surname . $dminister->individual->firstname]['tags']);
                }
            }
        }
        foreach ($ministers as $minister) {
            if (isset($minister->individual)){
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['name']=$minister->individual->title . ' ' . $minister->individual->firstname . ' <b>' . $minister->individual->surname . '</b>';
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['id']=$minister->individual->id;
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['circuit']['name']=$minister->individual->household->society->circuit;
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['circuit']['id']=$minister->individual->household->society->circuit_id;
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['image']=$minister->individual->image;
                foreach ($minister->tags as $tag){
                    $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['tags'][]=$tag->name;
                }
                if (isset($data['ministers'][$minister->individual->surname . $minister->individual->firstname]['tags'])){
                    asort($data['ministers'][$minister->individual->surname . $minister->individual->firstname]['tags']);
                }
            }
        }
        ksort($data['ministers']);
        $data['title']=$data['district']->district . " " . $data['district']->denomination->provincial . " Ministers";
        return view('churchnet::districts.ministers', $data);
    }

    public function store(CreateDistrictRequest $request)
    {
        $soc=$this->district->create($request->all());

        return redirect()->route('admin.districts.show', $soc->id)
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
