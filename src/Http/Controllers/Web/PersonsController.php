<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\PersonsRepository;
use Bishopm\Churchnet\Models\Person;
use Bishopm\Churchnet\Models\Preacher;
use Bishopm\Churchnet\Models\Minister;
use Bishopm\Churchnet\Models\Circuit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreatePersonRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePersonRequest;

class PersonsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $person;

    public function __construct(PersonsRepository $person)
    {
        $this->person = $person;
    }

    public function index()
    {
        $persons = $this->person->all();
        return view('churchnet::persons.index', compact('persons'));
    }

    public function edit($circuit, Person $person)
    {
        $data['person'] = $person;
        $data['societies'] = Circuit::find($circuit)->societies;
        $data['ministertags'] = Minister::allTags()->get();
        $data['preachertags'] = Preacher::allTags()->get();
        $data['leadertags'] = Person::allTags()->get();
        if ($person->minister) {
            $data['status']="minister";
        } elseif ($person->preacher) {
            $data['status']="preacher";
        } else {
            $data['status']="leader";
        }
        return view('churchnet::persons.edit', $data);
    }

    public function create()
    {
        return view('churchnet::persons.create');
    }
    
    public function store(CreatePersonRequest $request)
    {
        $this->person->create($request->all());
        return redirect()->route('admin.persons.index')
            ->withSuccess('New person added');
    }
    
    public function update($circuit, Person $person, Request $request)
    {
        dd($request->all());
        //$this->person->update($person, $request->all());
        return redirect()->route('admin.persons.index')->withSuccess('Person has been updated');
    }

    public function destroy(Person $person)
    {
        $this->person->destroy($person);
        return view('churchnet::persons.index')->withSuccess('The ' . $person->person . ' person has been deleted');
    }
}
