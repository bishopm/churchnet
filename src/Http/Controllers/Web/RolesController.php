<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Cartalyst\Tags\IlluminateTag;
use Spatie\Tags\Tag;
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
        $data['tags'] =Tag::where('type', 'minister')->orWhere('type', 'leader')->orWhere('type', 'preacher')->get();
        return view('churchnet::roles.index', $data);
    }

    public function store(Request $request)
    {
        $tag = Tag::findOrCreate($request->tag, $request->type);
        return redirect()->route('admin.roles.index')
            ->withSuccess('New role / status added');
    }

    public function edit($id)
    {
        $tag = Tag::find($id);
        return view('churchnet::roles.edit', compact('tag'));
    }

    public function update(Request $request, $id)
    {
        $tag = IlluminateTag::find($id);
        $tag->update(['namespace'=>$request->namespace, 'slug'=>str_slug($request->position), 'name'=>$request->position ]);
        return redirect()->route('admin.roles.index')
            ->withSuccess('Role / status updated');
    }
}
