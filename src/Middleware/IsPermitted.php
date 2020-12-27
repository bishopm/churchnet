<?php

namespace Bishopm\ChurchNet\Middleware;

use Closure;
use Auth;
use Bishopm\Churchnet\Models\User;

class IsPermitted
{
    public function handle($request, Closure $next)
    {
        $request->route()->parameters();
        $socs = array();
        $user = User::with('societies','circuits','districts')->where('id',Auth::user()->id)->first()->toArray();
        $user_soc=array();
        $user_cir=array();
        $user_dis=array();
        foreach ($user['societies'] as $society){
            $user_soc[]=$society['id'];
        }
        foreach ($user['circuits'] as $circuit){
            $user_cir[]=$circuit['id'];
        }
        foreach ($user['districts'] as $district){
            $user_dis[]=$district['id'];
        }
        $request->attributes->add(['user_soc' => $user_soc]);
        $request->attributes->add(['user_cir' => $user_cir]);
        $request->attributes->add(['user_dis' => $user_dis]);
        if ($user['level'] == 1) {
            $request->attributes->add(['super_admin' => 'true']);
        }
        return $next($request);
    }
}
