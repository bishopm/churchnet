<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\PagesRepository;
use Bishopm\Churchnet\Models\Page;
use Spatie\Tags\Tag;
use Bishopm\Churchnet\Models\Resource;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreatePageRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePageRequest;

class PagesController extends Controller
{

    /**
     * Display a listing of the page.
     *
     * @return Response
     */

    private $page;

    public function __construct(PagesRepository $page)
    {
        $this->page = $page;
    }

    public function index()
    {
        $pages = $this->page->all();
        return view('churchnet::pages.index', compact('pages'));
    }

    public function edit(Page $page)
    {
        $tags=Tag::where('type','resource')->get();
        $rtags=array();
        foreach ($page->tags as $tag) {
            $rtags[]=$tag->name;
        }
        return view('churchnet::pages.edit', compact('page', 'tags', 'rtags'));
    }

    public function create()
    {
        $tags=Tag::where('type','resource')->get();
        return view('churchnet::pages.create', compact('tags'));
    }

    public function addtag($id, $tag)
    {
        $page=$this->page->find($id);
        $page->tag($tag);
    }

    public function removetag($id, $tag)
    {
        $page=$this->page->find($id);
        $page->untag($tag);
    }

    public function show($id)
    {
        $data['page'] = $this->page->find($id);
        return view('churchnet::pages.show', $data);
    }

    public function store(CreatePageRequest $request)
    {
        $page = $this->page->create($request->except('tags'));
        $page->syncTagsWithType($request->tags,'resource');
        return redirect()->route('admin.pages.index')
            ->withSuccess('New page added');
    }
    
    public function update(Page $page, UpdatePageRequest $request)
    {
        $this->page->update($page, $request->except('tags'));
        $page->syncTagsWithType($request->tags,'resource');
        return redirect()->route('admin.pages.index')->withSuccess('Page has been updated');
    }

    public function destroy(Page $page)
    {
        $this->page->destroy($page);
        return view('churchnet::pages.index')->withSuccess('The page has been deleted');
    }
}
