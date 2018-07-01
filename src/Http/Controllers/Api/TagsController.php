<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\TagsRepository;
use Spatie\Tags\Tag;
use Bishopm\Churchnet\Models\Person;
use App\Http\Controllers\Controller;

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
        return $this->tag->all();
    }

    public function identify($circuit, $position, $type)
    {
        return Person::withAnyTags(array(urldecode($position)), $type)->get();
    }
}
