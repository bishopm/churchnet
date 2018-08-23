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
            $user['circuits'][$circuit->pivot->permission][]=$circuit->id;
        }
        foreach ($data->societies as $society) {
            $user['societies'][$society->pivot->permission][]=$society->id;
        }
        foreach ($data->districts as $district) {
            $user['districts'][$district->pivot->permission][]=$district->id;
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
