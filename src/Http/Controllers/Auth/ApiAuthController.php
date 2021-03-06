<?php

namespace Bishopm\Churchnet\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Circuit;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $user=User::where('phone', $request->phone)->where('phonetoken', $request->phonetoken)->first();
        if (!$user) {
            $indiv=Individual::where('cellphone', $request->phone)->first();
            if ($indiv) {
                $user = User::create(['name'=>$indiv->firstname . ' ' . $indiv->surname, 'email'=>$indiv->email, 'phone'=>$request->phone, 'phonetoken'=>$request->phonetoken, 'individual_id'=>$indiv->id, 'level'=>'user']);
            } else {
                $user = User::create(['name'=>$request->phone, 'phone'=>$request->phone, 'phonetoken'=>$request->phonetoken, 'level'=>'user']);
            }
        }
        return [
            'token' => $user->createToken('churchnet-token')->plainTextToken,
            'userid' => $user->id
        ];
    }

    public function synodlogin(Request $request)
    {
        $user=User::with('districts')->where('phone', $request->phone)->where('phonetoken', $request->phonetoken)->first();
        if (!$user) {
            $indiv=Individual::where('cellphone', $request->phone)->first();
            if ($indiv) {
                $user = User::create(['name'=>$indiv->firstname . ' ' . $indiv->surname, 'email'=>$indiv->email, 'phone'=>$request->phone, 'phonetoken'=>$request->phonetoken, 'individual_id'=>$indiv->id, 'level'=>'user']);
            } else {
                $user = User::create(['name'=>$request->phone, 'phone'=>$request->phone, 'phonetoken'=>$request->phonetoken, 'level'=>'user']);
            }
        }
        try {
            if (!$token=JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $data['user'] = $user;
        if ($user->districts[0]['pivot']->permission == 'admin') {
            $data['admin'] = true;
        } else {
            $data['admin'] = false;
        }
        $data['person'] = Person::where('individual_id',$user->individual_id)->first();
        $data['token'] = $token;
        return $data;
    }

    public function register(Request $request)
    {
        $existing = User::where('phone', '=', $request->cellphone)->first();
        if ($existing) {
            $user = $existing;
        } else {
            $user= new User;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->cellphone;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = JWTAuth::fromUser($user);
        return response($token);
    }

    public function check()
    {
        // This will return the userid. We need the circuit id
        $userid=JWTAuth::getPayload()['sub'];
        return User::with('circuit')->find($userid);
    }
}
