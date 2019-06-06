<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\GroupsRepository;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Bishopm\Churchnet\Mail\GenericMail;
use Illuminate\Support\Facades\Mail;

class GroupsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $group;

    public function __construct(GroupsRepository $group)
    {
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
        //$leader = User::with('individual')->where('individual_id',$group->leader)->first();
        //$person = User::with('individual')->where('individual_id',$request->person)->first();
        $leader = User::with('individual')->where('individual_id',570)->first();
        $person = User::with('individual')->where('individual_id',570)->first();
        // Email to group leader
        Mail::to($leader)->queue(new GenericMail([
            'title'=>'Potential new member (' . $group->groupname . ')',
            'body'=>'Dear ' . $leader->individual->firstname . '\n\nThis is just to let you know that ' . $person->individual->firstname . ' ' . $person->individual->surname . ' has sent you a message via the Journey App, expressing interest in joining the group (' . $group->groupname . ') that you lead.\n\n',
            'society'=>$group->society->society,
            'website'=>$group->society->website,
            'sender'=>$group->society->email
        ]));
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
        if (in_array($data['group']->society_id, \Illuminate\Support\Facades\Request::get('user_soc'))) {
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
