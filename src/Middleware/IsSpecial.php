<?php

namespace Bishopm\ChurchNet\Middleware;

use Closure;
use Auth;
use DB;
use Bishopm\Churchnet\Models\User;

class IsSpecial
{
    public function handle($request, Closure $next)
    {
        $request->route()->parameters();
        $user_soc=array($request->society_id);
        $checkspecial = DB::table('specialaccess')
        ->where('society_id', $request->society_id)
        ->where('user_id', $request->user_id)
        ->where('accesstype', $request->accesstype)
        ->where('token', $request->token)->get();
        if (count($checkspecial)) {
            $request->attributes->remove('society_id');
            $request->attributes->remove('user_id');
            $request->attributes->remove('accesstype');
            $request->attributes->remove('token');
            $request->attributes->add(['user_soc' => $user_soc]);
            return $next($request);
        } else {
            return "Nothing to see";
        }
    }
}
