<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\User;
use Auth;

class UsersController extends Controller
{
    public function statistics() 
    {
        $users = User::has('individual')->with('individual.household.society.circuit.district')->get();
        $data = array();
        foreach ($users as $user) {
            if ($user->individual->household->society) {
                $dd = $user->individual->household->society->circuit->district->district;
                $cc = $user->individual->household->society->circuit->circuitnumber . " " . $user->individual->household->society->circuit->circuit;
                $ss = $user->individual->household->society->society;
            }
            if (!$user->last_access) {
                $data[$dd][$cc][$ss]['registered'][] = $user->name;
            } elseif (time() - strtotime($user->last_access) < 60*60*24) {
                $data[$dd][$cc][$ss]['today'][] = $user->name;
            } elseif (time() - strtotime($user->last_access) < 60*60*24*7) {
                $data[$dd][$cc][$ss]['thisweek'][] = $user->name;
            } elseif (time() - strtotime($user->last_access) < 60*60*24*30) {
                $data[$dd][$cc][$ss]['thismonth'][] = time()- strtotime($user->last_access);//$user->name;
            } else {
                $data[$dd][$cc][$ss]['ever'][] = $user->name;
            }
        }
        foreach ($data as $s=>$dd) {
            ksort($data[$s]);
            foreach ($dd as $d=>$cc) {
                ksort($data[$s][$d]);
                foreach ($cc as $c=>$ss) {
                    ksort($data[$s][$d][$c]);
                }
            }
        }
        return view('churchnet::people.stats', compact('data'));
    }

}
