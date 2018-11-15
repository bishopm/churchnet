<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\Permissible;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    public function userdetails($id, $auth='')
    {
        $data = User::with('districts', 'circuits', 'societies')->where('id', $id)->first();
        $user = array();
        $dists = array();
        $circs = array();
        $socs = array();
        if ($auth) {
            $auth = User::with('districts', 'circuits', 'societies')->where('id', $auth)->first();
            if (count($auth->districts)) {
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
        }
        $user['id'] = $data->id;
        $user['name'] = $data->name;
        $user['level'] = $data->level;
        $user['email'] = $data->email;
        return $user;
    }

    public function index()
    {
        return User::where('level', '<>', 'user')->orderBy('name')->get();
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
