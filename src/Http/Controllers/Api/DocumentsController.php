<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Synod;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class DocumentsController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        Return Document::with('synod')->where('synod_id',$request->synod)->get();
    }

    public function store(Request $request)
    {
        $file = $request->all();
        return $file;
        $file->move(public_path() . '/vendor/bishopm/docs/', $file->file_name);
        $fname = public_path() . '/vendor/bishopm/images/profile/' . $file->file_name;
        return $fname;
    }

}
