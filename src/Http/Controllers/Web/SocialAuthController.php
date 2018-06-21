<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App;
use Bishopm\Churchnet\Models\User;

class SocialAuthController extends Controller
{
    public function redirect($service)
    {
        if (App::environment('local')) {
            $user=User::find(3);
            Auth::login($user);
            return redirect()->route('home');
        } else {
            return Socialite::driver($service)->redirect();
        }
    }

    public function callback($service)
    {
        $socialuser = Socialite::with($service)->user();
        $avatar = $socialuser->getAvatar();
        $id = $socialuser->getId();
        if ($service == "google") {
            $user = User::where('google_id', $id)->first();
        } else {
            $user = User::where('facebook_id', $id)->first();
        }
        if (!$user) {
            // Check if already registered with that email address
            $user = User::where('email', $socialuser->email)->first();
            if ($user) {
                if ($service == "google") {
                    $user->update(['google_id'=>$id]);
                } else {
                    $user->update(['facebook_id'=>$id]);
                }
            } else {
                if ($service == "google") {
                    $user = User::create(['name'=>$socialuser->name, 'email'=>$socialuser->email, 'avatar'=>$avatar, 'google_id'=>$id]);
                } else {
                    $user = User::create(['name'=>$socialuser->name, 'email'=>$socialuser->email, 'avatar'=>$avatar, 'facebook_id'=>$id]);
                }
            }
        }
        Auth::login($user);
        return redirect()->route('home');
    }
}
