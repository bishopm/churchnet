<?php

namespace Bishopm\Churchnet\Http\Controllers\Auth;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Circuit;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('name', 'password');
        $user=User::where('name', $request->input('name'))->first();
        $fullname=$user->individual->firstname . " " . $user->individual->surname;
        $indiv_id=$user->individual_id;
        //Log::info('API login attempt: ' . json_encode($credentials));

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token', 'fullname', 'indiv_id'));
    }

    public function journeylogin(Request $request)
    {
        $user=User::where('phone', $request->name)->first();
        try {
            if (!$token=JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $existing = User::where('circuit_id', '=', $request->circuit)->get();
        if (count($existing)) {
            return response('Already taken: ' . json_encode($existing));
        } else {
            $user= new User;
            $user->name = $request->username;
            $user->app_secret = $request->app_secret;
            $user->app_name = $request->app_name;
            $user->circuit_id = $request->circuit;
            $user->email = $request->email;
            $user->app_url = $request->app_url;
            $user->save();
            $token = JWTAuth::fromUser($user);
            return response($token);
        }
    }

    public function check()
    {
        // This will return the userid. We need the circuit id
        $userid=JWTAuth::getPayload()['sub'];
        return User::with('circuit')->find($userid);
    }
}
