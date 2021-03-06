<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Denomination;
use Bishopm\Churchnet\Models\Setting;
use Bishopm\Churchnet\Models\Permissible;
use Illuminate\Http\Request;
use Auth;
use DB;

class UsersController extends ApiController
{
    public function userdetails($id, $auth='')
    {
        $denom="";
        $data = User::with('districts', 'circuits', 'societies.location','denominations')->where('id', $id)->first();
        $user = array();
        $dists = array();
        $circs = array();
        $socs = array();
        $denoms = array();
        if ($auth) {
            $auth = User::with('districts', 'circuits', 'societies','denominations.individuals')->where('id', $auth)->first();
            if (count($auth->denominations)) {
                $user['auth']['denominations'] = $auth->denominations;
                foreach ($auth->denominations as $den) {
                    $denoms[] = $den->id;
                }
                $user['auth']['districts'] = District::whereIn('denomination_id', $denoms)->get();
                foreach ($auth->districts as $dist) {
                    $dists[] = $dist->id;
                }
                $user['auth']['circuits'] = Circuit::whereIn('district_id', $dists)->get();
                foreach ($auth['circuits'] as $circ) {
                    $circs[] = $circ->id;
                }
                $user['auth']['societies'] = Society::whereIn('circuit_id', $circs)->orderBy('society')->get();
            } elseif (count($auth->districts)) {
                $user['auth']['districts'] = $auth->districts;
                foreach ($auth->districts as $dist) {
                    $dists[] = $dist->id;
                }
                $user['auth']['circuits'] = Circuit::whereIn('district_id', $dists)->get();
                foreach ($auth['circuits'] as $circ) {
                    $circs[] = $circ->id;
                }
                $user['auth']['societies'] = Society::whereIn('circuit_id', $circs)->orderBy('society')->get();
            } elseif (count($auth->circuits)) {
                $user['auth']['circuits'] = $auth->circuits;
                foreach ($auth->circuits as $circ) {
                    $circs[] = $circ->id;
                }
                $user['auth']['societies'] = Society::whereIn('circuit_id', $circs)->get();
            } elseif (count($auth->societies)) {
                $user['auth']['societies'] = $auth->societies;
            }
        }
        foreach ($data->circuits as $circuit) {
            $user['circuits'][$circuit->id]=$circuit->pivot->permission;
            $user['circuits']['keys'][]=$circuit->id;
            $user['circuits']['full'][$circuit->id]=$circuit;
        }
        foreach ($data->societies as $society) {
            $user['societies'][$society->id]=$society->pivot->permission;
            $user['societies']['keys'][]=$society->id;
            $user['societies']['full'][$society->id]=$society;
        }
        foreach ($data->districts as $district) {
            $user['districts'][$district->id]=$district->pivot->permission;
            $user['districts']['keys'][]=$district->id;
            $user['districts']['full'][$district->id]=$district;
            $denom = $district->denomination_id;
        }
        foreach ($data->denominations as $denomination) {
            $user['denominations'][$denomination->id]=$denomination->pivot->permission;
            $user['denominations']['keys'][]=$denomination->id;
            $user['denominations']['full'][$denomination->id]=$denomination;
            $user['denominations']['full'][$denomination->id]['indivs']=$denomination->individuals;
            if ($denom == '') {
                $denom = $denomination->id;
            }
        }
        $user['denomination'] = Denomination::find($denom);
        $user['id'] = $data->id;
        $user['name'] = $data->name;
        $user['level'] = $data->level;
        $user['email'] = $data->email;
        $user['version']=Setting::where('setting_key','churchnet_version')->first()->setting_value;
        $user['updatenotes']=Setting::where('setting_key','churchnet_updatenotes')->first()->setting_value;
        return $user;
    }

    public function index(Request $request)
    {
        $users = User::with('individual.household.society.circuit')->whereNotNull('individual_id')->where('level', '<>', 'user')->where('name','like','%' . $request->search . '%')->orderBy('name')->get();
        $data = array();
        foreach ($users as $user) {
            if ($user->individual){
                $dum=array();
                $dum['name'] = $user->individual->title . ' ' . $user->individual->firstname . ' ' . $user->individual->surname;
                if ($user->individual->household->society) {
                    $dum['society'] = $user->individual->household->society->society;
                    $dum['circuit'] = $user->individual->household->society->circuit->circuit;
                } else {
                    $dum['society'] = "None";
                    $dum['circuit'] = "None";
                }
                $dum['phonetoken'] = $user->phonetoken;
                $dum['id'] = $user->id;
                $data[] = $dum;
            }
        }
        return $data;
    }

    public function specialaccess($society_id, $accesstype, $token)
    {
        $checkspecial = DB::table('specialaccess')
        ->where('society_id', $society_id)
        ->where('accesstype', $accesstype)
        ->where('token', $token)->get();
        foreach ($checkspecial as $cs) {
            $data['model'] = $cs->model;
            $data['users'][]= $cs->user_id;
        }
        return $data;
    }

    public function permissibles(Request $request)
    {
        $user = $request->user_id;
        $form = $request->form;
        foreach ($form['societies'] as $society) {
            $upd = Permissible::create([
                'user_id' => $user,
                'permissible_type' => 'Bishopm\Churchnet\Models\Society',
                'permissible_id' => $society,
                'permission' => $form['societylevel']
            ]);
        }
        foreach ($form['circuits'] as $circuit) {
            $upd = Permissible::create([
                'user_id' => $user,
                'permissible_type' => 'Bishopm\Churchnet\Models\Circuit',
                'permissible_id' => $circuit,
                'permission' => $form['circuitlevel']
            ]);
        }
        foreach ($form['districts'] as $district) {
            $upd = Permissible::create([
                'user_id' => $user,
                'permissible_type' => 'Bishopm\Churchnet\Models\District',
                'permissible_id' => $district,
                'permission' => $form['districtlevel']
            ]);
        }
        return "Permissibles updated";
    }

    public function deletepermissibles(Request $request)
    {
        $perm = $request->perms;
        $del = Permissible::where('user_id', $perm['user_id'])->where('permissible_id', $perm['permissible_id'])->where('permissible_type', $perm['permissible_type'])->delete();
        return "Permission deleted";
    }

    public function store(Request $request)
    {
        $usr = $request->user;
        if ($usr['indiv']) {
            $user=User::create([
                'name' => $usr['indiv']['firstname'] . ' ' . $usr['indiv']['surname'],
                'individual_id' => $usr['indiv']['id'],
                'phone' => $usr['indiv']['cellphone'],
                'email' => $usr['indiv']['email'],
                'level' => 'editor'
            ]);
        } else {
            $indiv = Individual::create([
                'firstname' => $usr['firstname'],
                'surname' => $usr['surname'],
                'sex' => $usr['sex'],
                'title' => $usr['title'],
                'cellphone' => $usr['cellphone']
            ]);
            $user=User::create([
                'name' => $indiv->firstname . ' ' . $indiv->surname,
                'individual_id' => $indiv->id,
                'phone' => $indiv->cellphone,
                'email' => $indiv->email,
                'level' => 'editor'
            ]);
        }
        return $user;
    }
}
