<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\PagesRepository;
use Bishopm\Churchnet\Models\Page;
use App\Http\Controllers\Controller;

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
        return view('churchnet::pages.edit', compact('page'));
    }

    public function create()
    {
        return view('churchnet::pages.create');
    }

    public function show($id)
    {
        $data['page'] = $this->page->find($id);
        return view('churchnet::pages.show', $data);
    }

    public function store(CreatePageRequest $request)
    {
        $page = $this->page->create($request->all());
        return redirect()->route('admin.pages.index')
            ->withSuccess('New page added');
    }
    
    public function update(Page $page, UpdatePageRequest $request)
    {
        $this->page->update($page, $request->all());
        return redirect()->route('admin.pages.index')->withSuccess('Page has been updated');
    }

    public function destroy(Page $page)
    {
        $this->page->destroy($page);
        return view('churchnet::pages.index')->withSuccess('The page has been deleted');
    }
}
