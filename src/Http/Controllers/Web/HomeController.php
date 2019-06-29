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
use LithiumDev\TagCloud\TagCloud;
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
        $cloud = new TagCloud();
        $resources=$this->resource->all();
        $data['resourcecount'] = count($resources);
        foreach ($resources as $thisresource) {
            foreach ($thisresource->tags as $tag) {
                $cloud->addTag($tag->name);
                $cloud->addTag(array('tag' => $tag->name, 'url' => $tag->normalized));
            }
        }
        $baseUrl=url('/');
        $cloud->setOrder('tag', 'ASC');
        $cloud->setHtmlizeTagFunction(function ($tag, $size) use ($baseUrl) {
            $size = intval($size) + 10;
            $link = '<a size="'.$size.'" href="'.$baseUrl.'/tag/'.$tag['url'].'">'.$tag['tag'].'</a>';
            return "{$link} ";
        });
        $data['denominations'] = Denomination::orderBy('slug')->get();
        $data['cloud'] = $cloud->render();
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
