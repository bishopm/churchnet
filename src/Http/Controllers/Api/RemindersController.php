<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Reminder;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class RemindersController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function show(Request $request)
    {
        $user = User::where('individual_id', $request->individual)->first();
        if ($user) {
            return Reminder::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
        } else {
            return null;
        }
    }

    public function destroy(Request $request)
    {
        Reminder::find($request->id)->delete();
        return "reminder " . $request->id . " deleted";
    }
}
