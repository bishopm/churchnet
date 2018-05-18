<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\TagsRepository;
use Bishopm\Churchnet\Models\Tag;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateTagRequest;
use Bishopm\Churchnet\Http\Requests\UpdateTagRequest;

class TagsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $tag;

    public function __construct(TagsRepository $tag)
    {
        $this->tag = $tag;
    }

    public function index($circuit)
    {
        return json_decode($this->tag->allforcircuitonly($circuit));
    }

    public function show($circuit, $tag)
    {
        return $this->tag->findforcircuit($circuit, $tag);
    }

    public function store(CreateTagRequest $request)
    {
        $this->tag->create($request->except('image', 'token'));
        return 'New tag added';
    }
    
    public function update($circuit, Tag $tag, UpdateTagRequest $request)
    {
        $this->tag->update($tag, $request->except('token'));
        return "Tag has been updated";
    }

    public function destroy($circuit, Tag $tag)
    {
        $this->tag->destroy($tag);
    }
}
