<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ResourcesRepository;
use Bishopm\Churchnet\Repositories\PagesRepository;
use Bishopm\Churchnet\Models\Resource;
use Bishopm\Churchnet\Models\Denomination;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Page;
use Cviebrock\EloquentTaggable\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $resource;
    private $page;

    public function __construct(ResourcesRepository $resource, PagesRepository $page)
    {
        $this->resource = $resource;
        $this->page = $page;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function home()
    {
        $data['words']=array();
        $dummy = array();
        $resources=$this->resource->all();
        foreach ($resources as $thisresource) {
            foreach ($thisresource->tags as $tag) {
                if (array_key_exists($tag->name, $dummy)) {
                    $dummy[$tag->name]++;
                } else {
                    $dummy[$tag->name]=1;
                }
            }
        }
        foreach ($dummy as $key=>$dum) {
            $data['words'][] = array($key,9+$dum*2);
        }
        $data['denominations'] = Denomination::orderBy('slug')->get();
        $data['recentresources'] = $this->resource->recents(15);
        $data['users'] = User::orderBy('created_at', 'DESC')->get()->take(5);
        return view('churchnet::home', $data);
    }

    public function search(Request $request)
    {
        $data['resources'] = Resource::with('tags')->where('title', 'like', '%' . $request->search . '%')->orWhere('description', 'like', '%' . $request->search . '%')->orderBy('title')->get();
        $data['pages'] = Page::where('title', 'like', '%' . $request->search . '%')->orWhere('body', 'like', '%' . $request->search . '%')->orderBy('title')->get();
        $data['tags'] = Tag::where('name', 'like', '%' . $request->search . '%')->get();
        $data['search'] = $request->search;
        return view('churchnet::search', $data);
    }

    public function tag($tag)
    {
        $data['resources'] = Resource::withAllTags([$tag])->get();
        $data['pages'] = Page::withAllTags([$tag])->get();
        $data['tag'] = strtoupper($tag);
        return view('churchnet::tag', $data);
    }
}
