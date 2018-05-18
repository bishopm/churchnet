<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\WeekdaysRepository;
use Bishopm\Churchnet\Models\Weekday;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateWeekdayRequest;
use Bishopm\Churchnet\Http\Requests\UpdateWeekdayRequest;

class WeekdaysController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $weekday;

    public function __construct(WeekdaysRepository $weekday)
    {
        $this->weekday = $weekday;
    }

    public function index($circuit)
    {
        return $this->weekday->all();
    }

    public function create()
    {
        $societies = $this->societies->all();
        return view('connexion::weekdays.create', compact('societies'));
    }

    public function show($circuit, $weekday)
    {
        return $this->weekday->findfordate($circuit, $weekday);
    }

    public function edit($circuit, $weekday)
    {
        return $this->weekday->find($weekday);
    }

    public function store($circuit, CreateWeekdayRequest $request)
    {
        $data=$request->except('token');
        $data['circuit_id']=$circuit;
        $this->weekday->create($data);
        return "New weekday added";
    }
    
    public function update($circuit, Weekday $weekday, UpdateWeekdayRequest $request)
    {
        $data=$request->except('token');
        $data['circuit_id']=$circuit;
        $this->weekday->update($weekday, $data);
        return "Weekday has been updated";
    }
    
    public function destroy($circuit, Weekday $weekday)
    {
        $this->weekday->destroy($weekday);
    }
}
