<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Individual;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    public function userdetails($id)
    {
        $data = User::with('districts', 'circuits', 'societies')->where('id', $id)->first();
        $user = array();
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
