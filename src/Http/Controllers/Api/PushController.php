<?php
namespace Bishopm\Churchnet\Http\Controllers\Api;

use Illuminate\Http\Request;
use Bishopm\Churchnet\Notifications\PushNotification;
use Bishopm\Churchnet\Models\User;
use App\Http\Controllers\Controller;
use Auth;
use Notification;

class PushController extends Controller
{
    public function store(Request $request){
        $post = json_decode($request->body);
        $endpoint = $post->endpoint;
        $token = $post->keys->auth;
        $key = $post->keys->p256dh; 
        $user = User::find(1);
        $user->updatePushSubscription($endpoint, $key, $token, null);
        return response()->json(['success' => true],200);
    }
}