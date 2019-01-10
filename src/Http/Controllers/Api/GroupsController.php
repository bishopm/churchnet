<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\GroupsRepository;
use Bishopm\Churchnet\Models\Group;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $group = Group::with('individuals')->where('id', $id)->first();
        if (in_array($group->society_id, \Illuminate\Support\Facades\Request::get('user_soc'))) {
            return $group;
        } else {
            return "Unauthorised";
        }
    }

    public function store(Request $request)
    {
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
        return Group::with('individuals')->where('id', $gid)->first();
    }

    public function update($id, Request $request)
    {
        $group = $this->group->find($id);
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
