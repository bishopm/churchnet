<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\DistrictsRepository;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\Person;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DistrictsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $district;

    public function __construct(DistrictsRepository $district)
    {
        $this->district = $district;
    }

    public function index()
    {
        return District::orderBy('district')->get();
    }

    public function details($id)
    {
        return District::with('denomination', 'people.tags', 'people.individual', 'location')->find($id);
    }

    public function show($id)
    {
        return Circuit::with('societies.location')->where('district_id', $id)->orderBy('circuitnumber')->get();
    }

    public function showwithmap($id)
    {
        $data['circuits'] = Circuit::with('societies.location')->where('district_id', $id)->orderBy('circuitnumber')->get();
        $data['district'] = District::with('denomination')->find($id);
        $first = true;
        foreach ($data['circuits'] as $circuit) {
            foreach ($circuit->societies as $society) {
                if ($society->location) {
                    if ($first) {
                        $data['bounds']['minlat'] = floatval($society->location->latitude);
                        $data['bounds']['maxlat'] = floatval($society->location->latitude);
                        $data['bounds']['minlng'] = floatval($society->location->longitude);
                        $data['bounds']['maxlng'] = floatval($society->location->longitude);
                    }
                    $first = false;
                    $title['society'] = $society;
                    $title['circuit'] = $society->circuit;
                    $data['markers'][] = ['title' => $title, 'lat' => $society->location->latitude, 'lng' => $society->location->longitude];
                    if (floatval($society->location->latitude) < $data['bounds']['minlat']) {
                        $data['bounds']['minlat'] = floatval($society->location->latitude);
                    }
                    if (floatval($society->location->latitude) > $data['bounds']['maxlat']) {
                        $data['bounds']['maxlat'] = floatval($society->location->latitude);
                    }
                    if (floatval($society->location->longitude) < $data['bounds']['minlng']) {
                        $data['bounds']['minlng'] = floatval($society->location->longitude);
                    }
                    if (floatval($society->location->longitude) > $data['bounds']['maxlng']) {
                        $data['bounds']['maxlng'] = floatval($society->location->longitude);
                    }
                }
            }
        }
        return $data;
    }

    public function directory(Request $request)
    {
        $data['district'] = District::with('denomination', 'people.individual')->where('id', $request->id)->first();
        $ministers = Person::districtministers($request->id)->with('tags', 'personable', 'individual')->get();
        foreach ($data['district']['people'] as $person) {
            $ministers[] = $person;
        }
        foreach ($ministers as $minister) {
            if (isset($minister->individual)) {
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['individual'] = $minister->individual;
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['id'] = $minister->individual->id;
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['circuit'] = $minister->personable;
                $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['image'] = $minister->individual->image;
                foreach ($minister->tags as $tag) {
                    $data['ministers'][$minister->individual->surname . $minister->individual->firstname]['tags'][] = $tag->name;
                }
                if (isset($data['ministers'][$minister->individual->surname . $minister->individual->firstname]['tags'])) {
                    asort($data['ministers'][$minister->individual->surname . $minister->individual->firstname]['tags']);
                }
            }
        }
        ksort($data['ministers']);
        return $data;
    }
}
