<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ResourcesRepository;
use Bishopm\Churchnet\Models\Resource;
use Cartalyst\Tags\IlluminateTag;

class HomeController extends Controller
{
    private $resource;
    
    public function __construct(ResourcesRepository $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function home()
    {
        $data['recents'] = $this->resource->recents(5);
        return view('churchnet::home', $data);
    }

    public function tag($tag)
    {
        $data['resources'] = Resource::whereTag($tag)->get();
        $data['tag'] = strtoupper(IlluminateTag::where('slug', $tag)->first()->name);
        return view('churchnet::tag', $data);
    }
}
