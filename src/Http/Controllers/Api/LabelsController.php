<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\LabelsRepository;
use Bishopm\Churchnet\Models\Label;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Bishopm\Churchnet\Http\Requests\CreateLabelRequest;
use Bishopm\Churchnet\Http\Requests\UpdateLabelRequest;

class LabelsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $label;

    public function __construct(LabelsRepository $label)
    {
        $this->label = $label;
    }

    public function index($circuit)
    {
        return json_decode($this->label->allforcircuitonly($circuit));
    }

    public function show($circuit, $label)
    {
        return $this->label->findforcircuit($circuit, $label);
    }

    public function store(CreateLabelRequest $request)
    {
        $this->label->create($request->except('image', 'token'));
        return 'New label added';
    }

    public function update($circuit, Label $label, UpdateLabelRequest $request)
    {
        $this->label->update($label, $request->except('token'));
        return "Label has been updated";
    }

    public function destroy($circuit, Label $label)
    {
        $this->label->destroy($label);
    }
}
