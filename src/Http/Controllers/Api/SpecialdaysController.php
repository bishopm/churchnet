<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Specialday;
use Bishopm\Churchnet\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class SpecialdaysController extends ApiController
{
    public function update(Request $request)
    {
        $data = array();
        if ($request->specialday['id']) {
            $specialday = Specialday::find($request->specialday['id']);
            $specialday->anniversarydate = str_replace('/', '-', substr($request->specialday['anniversarydate'], 0, 10));
            $specialday->details = $request->specialday['details'];
            $specialday->anniversarytype = $request->specialday['anniversarytype'];
            $specialday->household_id = $request->specialday['household_id'];
            $specialday->save();
            $data['specialday'] = $specialday;
            $data['message'] = "Anniversary has been updated";
            return $data;
        } else {
            $specialday = Specialday::create([
                    'anniversarydate' => str_replace('/', '-', substr($request->specialday['anniversarydate'], 0, 10)),
                    'details' => $request->specialday['details'],
                    'anniversarytype' => $request->specialday['anniversarytype'],
                    'household_id' => $request->specialday['household_id']
                ]);
            $data['specialday'] = Specialday::find($specialday->id);
            $data['message'] = "Anniversary has been created";
            return $data;
        }
    }

    public function destroy($id)
    {
        $specialday=Specialday::find($id);
        $specialday->delete();
        return "Anniversary has been deleted";
    }
}
