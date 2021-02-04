<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\User;
use Auth;

class ApiController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check()){
                $user = User::with('societies','circuits','districts')->where('id',Auth::user()->id)->first()->toArray();
                $this->user_soc=array();
                $this->user_cir=array();
                $this->user_dis=array();
                foreach ($user['societies'] as $society){
                    $this->user_soc[]=$society['id'];
                }
                foreach ($user['circuits'] as $circuit){
                    $this->user_cir[]=$circuit['id'];
                }
                foreach ($user['districts'] as $district){
                    $this->user_dis[]=$district['id'];
                }
                if ($user['level'] == 1) {
                    $this->super_admin = 'true';
                } else {
                    $this->super_admin = 'false';
                }
            }
            return $next($request);
        });
    }

}
