<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\ServicesRepository;
use Bishopm\Churchnet\Models\Service;
use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Http\Requests\CreateServiceRequest;
use Bishopm\Churchnet\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;

class ServicesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $service;

    public function __construct(ServicesRepository $service)
    {
        $this->service = $service;
    }

    public function index($society)
    {
        return Service::where('society_id',$society)->orderBy('servicetime')->get();
    }

    public function edit($circuit, $service)
    {
        return $this->service->find($service);
    }

    public function create($society)
    {
        //return view('connexion::services.create',compact('society'));
    }

    public function show($circuit, $service)
    {
        return $this->service->find($service);
    }

    public function store($society, CreateServiceRequest $request)
    {
        $this->service->create($request->except('token'));

        return "New service added";
    }
    
    public function update($circuit, $service, Request $request)
    {
        $serv = $this->service->find($service);
        $this->service->update($serv, $request->all());
        return $service;
    }
}
