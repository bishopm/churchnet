<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Repositories\ReadingsRepository;
use Bishopm\Churchnet\Models\Reading;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateReadingRequest;
use Bishopm\Churchnet\Http\Requests\UpdateReadingRequest;

class ReadingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $reading;

    public function __construct(ReadingsRepository $reading)
    {
        $this->reading = $reading;
    }

    public function index()
    {
        $readings = $this->reading->all();
        return view('churchnet::readings.index', compact('readings'));
    }

    public function edit(Reading $reading)
    {
        return view('churchnet::readings.edit', compact('reading'));
    }

    public function create()
    {
        return view('churchnet::readings.create');
    }
    
    public function store(CreateReadingRequest $request)
    {
        $this->reading->create($request->all());
        return redirect()->route('admin.readings.index')
            ->withSuccess('New reading added');
    }
    
    public function update(Reading $reading, UpdateReadingRequest $request)
    {
        $this->reading->update($reading, $request->all());
        return redirect()->route('admin.readings.index')->withSuccess('Reading has been updated');
    }

    public function destroy(Reading $reading)
    {
        $this->reading->destroy($reading);
        return view('churchnet::readings.index')->withSuccess('The ' . $reading->reading . ' reading has been deleted');
    }
}
