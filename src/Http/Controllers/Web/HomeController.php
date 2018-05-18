<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function home()
    {
        return view('churchnet::home');
    }
}
