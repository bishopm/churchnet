<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\GroupsRepository;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Bishopm\Churchnet\Mail\GenericMail;
use Illuminate\Support\Facades\Mail;

class GroupsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $group;

    public function __construct(GroupsRepository $group)
    {
        parent::__construct();
        $this->group = $group;
    }

    public function index()
    {
        return Group::orderBy('groupname')->get();
    }

    public function search(Request $request)
    {
        $socs=array();
        foreach ($request->societies as $soc) {
            $socs[]=intval($soc);
        }
        return Group::whereIn('society_id', $socs)->where('groupname', 'like', '%' . $request->search . '%')->orderBy('groupname')->get();
    }

    public function signups($society) {
        $data=array();
        $data['fellowship'] = Group::where('society_id',$society)->where('grouptype','fellowship')->where('signup',1)->orderBy('groupname')->get();
        $data['service'] = Group::where('society_id',$society)->where('grouptype','service')->where('signup',1)->orderBy('groupname')->get();
        return $data;
    }

    public function signupmessage(Request $request) {
        $group = Group::with('society')->find($request->group);
        if ($group->grouptype == 'service') {
            $gtype = "team";
        } else {
            $gtype = "group";
        }
        //$person = User::with('individual')->where('individual_id',$request->person)->first();
        $person = User::with('individual')->where('individual_id',570)->first();
        if ($group->leader) {
            //$leader = User::with('individual')->where('individual_id',$group->leader)->first();
            $leader = User::with('individual')->where('individual_id',570)->first();
            // Email to group leader
            $leadermessage =  'Dear ' . $leader->individual->firstname . '<br><br>This is to let you know that <b>' .
                        $person->individual->firstname . ' ' . $person->individual->surname . '</b> has sent you a
                        message via the Journey App, expressing interest in joining the ' . $gtype . ' (' . $group->groupname .
                        ') that you co-ordinate.<br><br>Please could you get in touch with ' . $person->individual->firstname .
                        ' (' . $person->individual->cellphone . ').<br><br>Thank you!';
            if (!$leader->individual->email) {
                $leadermessage = $leadermessage . "<br><br><b>Note to the church office: The group leader does not have an email address!</b>";
                Mail::to($group->society->email)->queue(new GenericMail([
                    'title'=>'Potential new ' . $gtype . ' member (' . $group->groupname . ')',
                    'body'=>$leadermessage,
                    'society'=>$group->society->society,
                    'website'=>$group->society->website,
                    'sender'=>$group->society->email
                ]));
            } else {
                Mail::to($leader->individual->email)->cc($group->society->email)->queue(new GenericMail([
                    'title'=>'Potential new ' . $gtype . ' member (' . $group->groupname . ')',
                    'body'=>$leadermessage,
                    'society'=>$group->society->society,
                    'website'=>$group->society->website,
                    'sender'=>$group->society->email
                ]));
            }
        } else {
            // Email to church office
            $leadermessage =  'This is to let you know that <b>' . $person->individual->firstname . ' ' . $person->individual->surname . '</b> has sent a
                        message via the Journey App, expressing interest in joining the ' . $gtype . ' (' . $group->groupname . ').<br><br>The group does not have a leader / co-ordinator. Please could you get in touch with ' . $person->individual->firstname .
                        ' (' . $person->individual->cellphone . ').<br><br>Thank you!';
            Mail::to($group->society->email)->queue(new GenericMail([
                'title'=>'Potential new ' . $gtype . ' member (' . $group->groupname . ')',
                'body'=>$leadermessage,
                'society'=>$group->society->society,
                'website'=>$group->society->website,
                'sender'=>$group->society->email
            ]));
        }
        // Email to person
        if ($person->individual->email) {
            if ($group->leader){
                $personmessage =  'Dear ' . $person->individual->firstname . '<br><br>Thanks for your interest in one of our ' . $gtype . 's (' .
                        $group->groupname . '). We have sent your contact details to ' . $leader->individual->firstname . ' ' . $leader->individual->surname . ', who co-ordinates that  ' . $gtype
                        . '. If you don\'t hear from ' . $leader->individual->firstname . ' soon, you are welcome to make contact directly on ' . $leader->individual->cellphone . ' or get in touch with the church office by replying to this email.<br><br>Thank you!';
            } else {
                $personmessage =  'Dear ' . $person->individual->firstname . '<br><br>Thanks for your interest in one of our ' . $gtype . 's (' .
                        $group->groupname . '). We have sent your contact details to the church office and you should hear from the office soon. ' .
                        'You are also welcome to contact the church office directly by replying to this email.<br><br>Thank you!';
            }
            Mail::to($person->individual->email)->queue(new GenericMail([
                'title'=>$group->groupname,
                'body'=>$personmessage,
                'society'=>$group->society->society,
                'website'=>$group->society->website,
                'sender'=>$group->society->email
            ]));
        }
        return "Mail sent";
    }

    public function query($group, Request $request)
    {
        return DB::select(DB::raw($request->sql))->toArray();
    }

    public function create()
    {
        return view('connexion::groups.create');
    }

    public function show($id)
    {
        $data['members'] = DB::select('SELECT individuals.id, individuals.email, individuals.firstname, individuals.surname, individuals.cellphone  FROM group_individual,individuals WHERE individuals.id = group_individual.individual_id AND group_individual.deleted_at IS NULL AND group_individual.group_id = ? ORDER BY individuals.surname, individuals.firstname', [$id]);
        $group = Group::find($id);
        $group->datestr = date('Y-m-d H:i', $group->eventdatetime);
        $group->till = Carbon::parse($group->eventdatetime)->diffForHumans();
        $data['group'] = $group;
        if ($group->leader) {
            $data['leader'] = Individual::find($group->leader);
        }
        if (in_array($data['group']->society_id, $this->user_soc)) {
            return $data;
        } elseif ($this->super_admin == 'true') {
            return $data;
        } else {
            return "Unauthorised";
        }
    }

    public function store(Request $request)
    {
        $request->merge(array('eventdatetime' => strtotime(substr($request->eventdatetime, 0, 19))));
        $grp=$this->group->create(array_merge($request->all(), ['slug' => str_slug($request->groupname)]));
        return $grp->id;
    }

    public function remove($gid, Request $request)
    {
        DB::table('group_individual')->where('group_id', $gid)->where('individual_id', $request->id)->update(array('deleted_at' => DB::raw('NOW()')));
        return Group::with('individuals')->where('id', $gid)->first();
    }

    public function add($gid, Request $request)
    {
        $indiv = DB::table('group_individual')->where('group_id', $gid)->where('individual_id', $request->id)->get();
        if (count($indiv)) {
            DB::table('group_individual')->where('group_id', $gid)->where('individual_id', $request->id)->update(array('deleted_at' => null));
        } else {
            $newmem = DB::table('group_individual')->insert(['group_id' => $gid, 'individual_id' => $request->id]);
        }
        return Individual::where('id', $request->id)->select('firstname', 'surname', 'id', 'cellphone', 'email')->first();
    }

    public function sync($gid, Request $request)
    {
        $group = Group::find($gid);
        $group->individuals()->sync($request->members);
    }

    public function update($id, Request $request)
    {
        $group = $this->group->find($id);
        $request->merge(array('eventdatetime' => strtotime(substr($request->eventdatetime, 0, 19))));
        $data = $this->group->update($group, $request->all());
        return $data;
    }

    public function destroy($id)
    {
        $group = $this->group->find($id);
        $group->delete();
        return 'Group has been deleted';
    }
}
