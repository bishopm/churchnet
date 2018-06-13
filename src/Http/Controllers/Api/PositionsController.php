<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\PositionsRepository;
use Bishopm\Churchnet\Repositories\PersonsRepository;
use Bishopm\Churchnet\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreatePositionRequest;
use Bishopm\Churchnet\Http\Requests\UpdatePositionRequest;

class PositionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $position;
    private $person;

    public function __construct(PositionsRepository $position, PersonsRepository $person)
    {
        $this->person = $person;
        $this->position = $position;
    }

    public function index($circuit)
    {
        return json_decode($this->position->all());
    }

    public function show($circuit, $position)
    {
        return $this->person->find($position);
    }

    public function store(CreatePositionRequest $request)
    {
        $this->position->create($request->except('image', 'token'));

        return "New position added";
    }
    
    public function update($circuit, Position $position, UpdatePositionRequest $request)
    {
        $this->position->update($position, $request->except('token'));
        return "Position has been updated";
    }

    public function destroy($circuit, Position $position)
    {
        $this->position->destroy($position);
    }
}
