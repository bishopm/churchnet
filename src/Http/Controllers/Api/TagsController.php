<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\TagsRepository;
use Cviebrock\EloquentTaggable\Models\Tag;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;

class TagsController extends ApiController
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
        return $this->tag->all();
    }

    public function appindex()
    {
        return $this->tag->all();
    }

    public function identify($circuit, $position, $type)
    {
        return Person::withAnyTags(array(urldecode($position)), $type)->get();
    }
}
