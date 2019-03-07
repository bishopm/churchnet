<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Reminder;
use Bishopm\Churchnet\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RemindersController extends Controller
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

    public function store(CreateReminderRequest $request)
    {
        $this->reminder->create($request->except('image', 'token'));
        return 'New reminder added';
    }
    
    public function destroy($circuit, Reminder $reminder)
    {
        $this->reminder->destroy($reminder);
    }
}
