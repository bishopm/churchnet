<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\ResourcesRepository;
use Bishopm\Churchnet\Models\Resource;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Tagg;
use Bishopm\Churchnet\Http\Requests\CreateResourceRequest;
use Bishopm\Churchnet\Http\Requests\UpdateResourceRequest;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

class ResourcesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $resource;

    public function __construct(ResourcesRepository $resource)
    {
        $this->resource = $resource;
    }

    public function index()
    {
        $resources = $this->resource->all();
        return view('churchnet::resources.index', compact('resources'));
    }

    public function edit(Resource $resource)
    {
        $tags=Tagg::type('resource')->get();
        $rtags=array();
        foreach ($resource->tags as $tag) {
            $rtags[]=$tag->name;
        }
        return view('churchnet::resources.edit', compact('resource', 'tags', 'rtags'));
    }

    public function create()
    {
        $tags=Tagg::type('resource')->get();
        return view('churchnet::resources.create', compact('tags'));
    }

    public function addtag($id, $tag)
    {
        $resource=$this->resource->find($id);
        $resource->attachTag($tag);
    }

    public function removetag($id, $tag)
    {
        $resource=$this->resource->find($id);
        $resource->detachTag($tag);
    }

    public function show($id)
    {
        $data['resource'] = $this->resource->find($id);
        $data['comments'] = $data['resource']->comments;
        return view('churchnet::resources.show', $data);
    }

    public function store(CreateResourceRequest $request)
    {
        $resource = $this->resource->create($request->except('tags'));
        $resource->detag();
        $resource->tag($request->tags);
        foreach ($request->tags as $tag) {
            DB::table('taggable_tags')->where('name', $tag)->update(['type' => 'resource']);
        }
        return redirect()->route('admin.resources.index')
            ->withSuccess('New resource added');
    }

    public function addcomment(Resource $resource, Request $request)
    {
        $user=User::find($request->user);
        $user->comment($resource, $request->newcomment);
    }

    public function deletecomment(Request $request)
    {
        DB::table('comments')->where('id', $request->id)->delete();
        return $request->id;
    }
    
    public function update(Resource $resource, UpdateResourceRequest $request)
    {
        $resource = $this->resource->update($resource, $request->except('tags'));
        $resource->detag();
        $resource->tag($request->tags);
        foreach ($request->tags as $tag) {
            DB::table('taggable_tags')->where('name', $tag)->update(['type' => 'resource']);
        }
        return redirect()->route('admin.resources.index')->withSuccess('Resource has been updated');
    }

    public function destroy(Resource $resource)
    {
        $this->resource->destroy($resource);
        return view('churchnet::resources.index')->withSuccess('The resource has been deleted');
    }
}
