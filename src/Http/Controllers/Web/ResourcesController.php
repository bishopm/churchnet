<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\ResourcesRepository;
use Bishopm\Churchnet\Models\Resource;
use Bishopm\Churchnet\Http\Requests\CreateResourceRequest;
use Bishopm\Churchnet\Http\Requests\UpdateResourceRequest;
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
        return view('churchnet::resources.edit', compact('resource'));
    }

    public function create()
    {
        return view('churchnet::resources.create');
    }

    public function show($id)
    {
        $data['resource'] = $this->resource->find($id);
        return view('churchnet::resources.show', $data);
    }

    public function store(CreateResourceRequest $request)
    {
        $resource = $this->resource->create($request->all());
        return redirect()->route('admin.resources.index')
            ->withSuccess('New resource added');
    }
    
    public function update(Resource $resource, UpdateResourceRequest $request)
    {
        $this->resource->update($resource, $request->all());
        return redirect()->route('admin.resources.index')->withSuccess('Resource has been updated');
    }

    public function destroy(Resource $resource)
    {
        $this->resource->destroy($resource);
        return view('churchnet::resources.index')->withSuccess('The resource has been deleted');
    }
}
