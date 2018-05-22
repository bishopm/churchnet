<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ResourcesRepository;
use Bishopm\Churchnet\Repositories\PagesRepository;
use Bishopm\Churchnet\Models\Resource;
use Bishopm\Churchnet\Models\Page;
use Cartalyst\Tags\IlluminateTag;
use LithiumDev\TagCloud\TagCloud;

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
                $cloud->addTag(array('tag' => $tag->name, 'url' => $tag->slug));
            }
        }
        $baseUrl=url('/');
        $cloud->setOrder('tag', 'ASC');
        $cloud->setHtmlizeTagFunction(function ($tag, $size) use ($baseUrl) {
            $link = '<a href="'.$baseUrl.'/tag/'.$tag['url'].'">'.$tag['tag'].'</a>';
            return "<span class='tag size{$size}'>{$link}</span> ";
        });
        $data['cloud'] = $cloud;
        $data['recentresources'] = $this->resource->recents(5);
        $data['recentpages'] = $this->page->recents(5);
        return view('churchnet::home', $data);
    }

    public function tag($tag)
    {
        $data['resources'] = Resource::whereTag($tag)->get();
        $data['pages'] = Page::whereTag($tag)->get();
        $data['tag'] = strtoupper(IlluminateTag::where('slug', $tag)->first()->name);
        return view('churchnet::tag', $data);
    }
}
