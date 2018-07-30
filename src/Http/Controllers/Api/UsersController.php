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
        return User::with('districts', 'circuits', 'societies')->where('id', $id)->first();
    }
}
