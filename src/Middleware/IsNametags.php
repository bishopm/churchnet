<?php

namespace Bishopm\ChurchNet\Middleware;

use Closure;
use DB;

class IsNametags
{
    public function handle($request, Closure $next)
    {
        $request->route()->parameters();
        $user_soc=array($request->society_id);
        $checknames = DB::table('specialaccess')
        ->where('society_id', $request->society_id)
        ->where('accesstype', $request->accesstype)
        ->where('token', $request->token)->get();
        if (count($checknames)) {
            $request->attributes->remove('society_id');
            $request->attributes->remove('accesstype');
            $request->attributes->remove('token');
            $request->attributes->add(['user_soc' => $user_soc]);
            return $next($request);
        } else {
            abort(200, 'Not permitted to edit');
        }
    }
}
