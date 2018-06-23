<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Cartalyst\Tags\IlluminateTag;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
        $data['roles'] = IlluminateTag::where('namespace', 'Bishopm\Churchnet\Models\Minister')->orWhere('namespace', 'Bishopm\Churchnet\Models\Person')->orwhere('namespace', 'Bishopm\Churchnet\Models\Preacher')->orderBy('name')->get();
        return view('churchnet::roles.index', $data);
    }

    public function store(Request $request)
    {
        IlluminateTag::create(['namespace'=>$request->namespace, 'slug'=>str_slug($request->position), 'name'=>$request->position ]);
        return redirect()->route('admin.roles.index')
            ->withSuccess('New role / status added');
    }

    public function edit($id)
    {
        $role = IlluminateTag::find($id);
        return view('churchnet::roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $tag = IlluminateTag::find($id);
        $tag->update(['namespace'=>$request->namespace, 'slug'=>str_slug($request->position), 'name'=>$request->position ]);
        return redirect()->route('admin.roles.index')
            ->withSuccess('Role / status updated');
    }
}
