<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Pastoral;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class PastoralsController extends ApiController
{

    public function update(Request $request)
    {
        $data = array();
        if ($request->pastoral['id']) {
            $pastoral = Pastoral::find($request->pastoral['id']);
            $pastoral->pastoraldate = str_replace('/','-',substr($request->pastoral['pastoraldate'],0,10));
            $pastoral->details = $request->pastoral['details'];
            $pastoral->individual_id = $request->pastoral['individual_id'];
            $pastoral->household_id = $request->pastoral['household_id'];
            $pastoral->save();
            $data['pastoral'] = $pastoral;
            $data['message'] = "Pastoral note has been updated";
            return $data;
        } else {
            $pastoral = Pastoral::create([
                    'pastoraldate' => str_replace('/','-',substr($request->pastoral['pastoraldate'],0,10)),
                    'details' => $request->pastoral['details'],
                    'individual_id' => $request->pastoral['individual_id'],
                    'household_id' => $request->pastoral['household_id']
                ]);
            $data['pastoral'] = Pastoral::with('individual')->find($pastoral->id);
            $data['message'] = "Pastoral note has been created";
            return $data;
        }
    }

    public function destroy($id)
    {
        $pastoral=Pastoral::find($id);
        $pastoral->delete();
        return "Pastoral note has been deleted";
    }
}
