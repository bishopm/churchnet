<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\HouseholdsRepository;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Circuit;
use Cviebrock\EloquentTaggable\Models\Tag;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateHouseholdRequest;
use Bishopm\Churchnet\Http\Requests\UpdateHouseholdRequest;
use Bishopm\Churchnet\Models\Location;
use Bishopm\Churchnet\Models\Society;

class HouseholdsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $household;

    public function __construct(HouseholdsRepository $household)
    {
        parent::__construct();
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
            return Individual::with('household.Location','household.society.circuit')
            ->where(function ($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                ->orWhere('surname', 'like', '%' . $request->search . '%')
                ->orWhere('cellphone', 'like', '%' . $request->search . '%');
            })->orderBy('surname')->get();
        } else {
            return Individual::societymember($socs)->with('household.Location','household.society')
            ->where(function ($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                ->orWhere('surname', 'like', '%' . $request->search . '%')
                ->orWhere('cellphone', 'like', '%' . $request->search . '%');
            })->orderBy('surname')->get();
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
        if (in_array($household->society->id, $this->user_soc)) {
            return $household;
        } elseif ($this->super_admin == 'true') {
            return $household;
        } else {
            return "Unauthorised";
        }
    }

    public function store(Request $request)
    {
        $household = Household::create(['addressee' => $request->addressee, 'society_id' => $request->society_id, 'householdcell' => $request->householdcell]);
        $household->location()->create(['latitude' => $request->location['latitude'], 'longitude' => $request->location['longitude'], 'phone' => $request->location['phone'], 'address' => $request->location['address']]);
        if (($household->location->latitude == null) or ($household->location->longitude == null)){
            $society = Society::find($request->society_id);
            if ($society->location) {
                $household->location->latitude = $society->location->latitude;
                $household->location->longitude = $society->location->longitude;
            } else {
                $household->location->latitude = 0;
                $household->location->longitude = 0;
            }
            $household->location->save();
        }
        $household->save();
        return $household;
    }

    public function update($id, Request $request)
    {
        $household = $this->household->find($id);
        $household->addressee = $request->addressee;
        $household->householdcell = $request->householdcell;
        $household->location->latitude = $request->location['latitude'];
        $household->location->longitude = $request->location['longitude'];
        $household->location->phone = $request->location['phone'];
        $household->location->address = $request->location['address'];
        $household->location->save();
        $household->save();
        return $household;
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
