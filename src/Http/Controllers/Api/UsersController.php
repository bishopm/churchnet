<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\User;
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
        return $user;
    }

    public function index()
    {
        return User::orderBy('name')->get();
    }
}
