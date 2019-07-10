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
        $socs = array();
        if (isset($request->circuit)) {
            $circuit = Circuit::with('societies')->where('id', $request->circuit)->first();
            foreach ($circuit->societies as $soc) {
                $socs[] = $soc->id;
            }
        } else {
            foreach ($request->societies as $soc) {
                $socs[] = intval($soc);
            }
        }
        if ($request->scope === true) {
            return Household::with('individuals','society.circuit')->where('addressee', 'like', '%' . $request->search . '%')->get();
        } else {
            return Household::with('individuals','society')->whereIn('society_id', $socs)->where('addressee', 'like', '%' . $request->search . '%')->get();
        }
    }

    public function stickers(Request $request)
    {
        return $request->all();
        $indivs = Individual::insociety($request->society)
            ->withsearch($request->search)
            ->select('household_id')->groupBy('household_id')->get();
        return Household::with('individuals')->whereIn('id', $indivs)->get();
    }

    public function newstickers(Request $request)
    {
        $addressee = '';
        $fsize = count($request->indivs);
        foreach ($request->indivs as $ndx => $indiv) {
            $addressee = $addressee . $indiv['firstname'] . ' ' . $indiv['surname'];
            if (($fsize > 1) and ($ndx < $fsize - 2)) {
                $addressee = $addressee . ", ";
            } elseif (($fsize > 1) and ($ndx == $fsize - 2)) {
                $addressee = $addressee . " and ";
            }
        }
        $household = Household::create(['addressee' => $addressee, 'sortsurname' => $request->indivs[0]['surname']]);
        $indivs = array();
        foreach ($request->indivs as $ind) {
            if ($ind['memberstatus'] == 'adult') {
                $ind['memberstatus'] = 'non-member';
            }
            if (!$ind['cellphone']) {
                $ind['cellphone'] = '';
            }
            $newindiv = Individual::create([
                'firstname' => $ind['firstname'],
                'surname' => $ind['surname'],
                'sex' => $ind['sex'],
                'cellphone' => $ind['cellphone'],
                'household_id' => $household->id
            ]);
            $indivs[] = $newindiv;
            if ((!$household->householdcell) and (strlen($ind['cellphone'])) == 10) {
                $household->householdcell = $newindiv->id;
                $household->save();
            }
        }
        return $indivs;
    }

    public function stickerupdate(Request $request)
    {
        $household = Household::with('individuals')->find($request->id);
        $household->addressee = $request->addressee;
        $household->save();
        $indivs = array();
        foreach ($request->individuals as $individual) {
            $indiv = Individual::find($individual['id']);
            $indiv->firstname = $individual['firstname'];
            $indiv->surname = $individual['surname'];
            $indiv->sex = $individual['sex'];
            $indiv->cellphone = $individual['cellphone'];
            $indiv->save();
            $indivs[] = $indiv;
        }
        return $indivs;
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
        $household = Household::find($request->id);
        $this->household->update($household, $request->all());
        return "Household updated";
    }

    public function show($id)
    {
        $household = Household::with('individuals', 'individuals.groups', 'individuals.tags', 'pastorals.individual', 'specialdays', 'location', 'society.location')->where('id', $id)->first();
        $household->alltags = Tag::where('type', 'leader')->get();
        if (in_array($household->society->id, \Illuminate\Support\Facades\Request::get('user_soc'))) {
            return $household;
        } elseif (\Illuminate\Support\Facades\Request::get('super_admin') == 'true') {
            return $household;
        } else {
            return "Unauthorised";
        }
    }

    public function store(Request $request)
    {
        $household = Household::create($request->except('longitude', 'latitude'));
        $household->location()->create(['latitude' => $request->latitude, 'longitude' => $request->longitude]);
        $household->save();
        return $household;
    }

    public function update($id, Request $request)
    {
        $household = $this->household->find($id);
        $data = $this->household->update($household, $request->except('latitude', 'longitude'));
        $household->location->latitude = $request->latitude;
        $household->location->longitude = $request->longitude;
        $household->location->phone = $request->location->phone;
        $household->location->address = $request->location->address;
        return $data;
    }

    public function destroy(Request $request)
    {
        $household = Household::find($request->id);
        foreach ($household->individuals as $indiv) {
            $indiv->forceDelete();
        }
        foreach ($household->pastorals as $past) {
            $past->forceDelete();
        }
        foreach ($household->specialdays as $sd) {
            $sd->forceDelete();
        }
        $household->forceDelete();
        return "Household and related records deleted";
    }
}
