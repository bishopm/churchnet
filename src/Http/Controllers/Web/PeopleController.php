<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\PeopleRepository;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Tags\Tag;
use Bishopm\Churchnet\Http\Requests\CreatePersonRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePersonRequest;

class PeopleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $person;

    public function __construct(PeopleRepository $person)
    {
        $this->person = $person;
    }

    public function index()
    {
        $people = $this->person->all();
        return view('churchnet::people.index', compact('people'));
    }

    public function edit($circuit, Person $person)
    {
        $data['person'] = $person;
        $ptags=array();
        $mtags=array();
        $ltags=array();
        foreach ($person->tags as $tag) {
            if ($tag->type == "preacher") {
                $ptags[]=$tag->name;
            } elseif ($tag->type == "minister") {
                $mtags[]=$tag->name;
            } else {
                $ltags[]=$tag->name;
            }
        }
        $person->ptags=$ptags;
        $person->mtags=$mtags;
        $person->ltags=$ltags;
        $data['societies'] = Circuit::find($circuit)->societies;
        $data['ministertags'] = Tag::where('type', 'minister')->get();
        $data['preachertags'] = Tag::where('type', 'preacher')->get();
        $data['leadertags'] = Tag::where('type', 'leader')->get();
        $data['roles'] = array('biblewoman','deacon','evangelist','leader','minister','preacher');
        return view('churchnet::people.edit', $data);
    }

    public function create($circuit)
    {
        $data['societies'] = Circuit::find($circuit)->societies;
        $data['ministertags'] = Tag::where('type', 'minister')->get();
        $data['preachertags'] = Tag::where('type', 'preacher')->get();
        $data['leadertags'] = Tag::where('type', 'leader')->get();
        $data['roles'] = array('biblewoman','deacon','evangelist','leader','minister','preacher');
        $data['circuit'] = $circuit;
        return view('churchnet::people.create', $data);
    }
    
    public function store($circuit, CreatePersonRequest $request)
    {
        if (isset($request->ptags)) {
            $person=$this->person->create($request->except('ptags', 'ltags'));
            $person->syncTagsWithType($request->ptags, 'preacher');
            $person->syncTagsWithType($request->ltags, 'leader');
        } elseif (isset($request->ltags)) {
            $person=$this->person->create($request->except('ltags'));
            $person->syncTagsWithType($request->ltags, 'leader');
        } else {
            $person=$this->person->create($request->except('mtags'));
            $person->syncTagsWithType($request->mtags, 'minister');
        }
        return redirect()->route('admin.people.index')
            ->withSuccess('New person added');
    }
    
    public function update($circuit, Person $person, Request $request)
    {
        if (isset($request->ptags)) {
            $this->person->update($person, $request->except('ptags', 'ltags'));
            $person->syncTagsWithType($request->ptags, 'preacher');
            $person->syncTagsWithType($request->ltags, 'leader');
            $person->syncTagsWithType([], 'minister');
        } elseif (isset($request->ltags)) {
            $this->person->update($person, $request->except('ltags'));
            $person->syncTagsWithType([], 'preacher');
            $person->syncTagsWithType($request->ltags, 'leader');
            $person->syncTagsWithType([], 'minister');
        } else {
            $this->person->update($person, $request->except('mtags'));
            $person->syncTagsWithType([], 'preacher');
            $person->syncTagsWithType([], 'leader');
            $person->syncTagsWithType($request->mtags, 'minister');
        }
        return redirect()->route('admin.people.index')->withSuccess('Person has been updated');
    }

    public function destroy(Person $person)
    {
        $this->person->destroy($person);
        return view('churchnet::people.index')->withSuccess('The ' . $person->person . ' person has been deleted');
    }
}
