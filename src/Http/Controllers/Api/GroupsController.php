<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\GroupsRepository;
use Bishopm\Churchnet\Models\Group;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateGroupRequest;
use Bishopm\Churchnet\Http\Requests\UpdateGroupRequest;

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
        return Group::with('individuals')->whereIn('society_id', $socs)->where('groupname', 'like', '%' . $request->search . '%')->orderBy('groupname')->get();
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
        return Group::with('individuals')->where('id', $id)->first();
    }

    public function store(CreateGroupRequest $request)
    {
        $soc=$this->group->create($request->all());

        return redirect()->route('admin.groups.show', $soc->id)
            ->withSuccess('New group added');
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

    public function update(Group $group, UpdateGroupRequest $request)
    {
        $this->group->update($group, $request->all());
        return redirect()->route('admin.groups.index')->withSuccess('Group has been updated');
    }

    public function destroy(Group $group)
    {
        $this->group->destroy($group);
        return view('connexion::groups.index')->withSuccess('The ' . $group->group . ' group has been deleted');
    }
}
