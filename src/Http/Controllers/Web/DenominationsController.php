<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Denomination;
use App\Http\Controllers\Controller;

class DenominationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
        $denominations = Denomination::orderBy('slug')->get();
        return view('churchnet::denominations.index', compact('denominations'));
    }

    public function show($slug)
    {
        $denomination=Denomination::where('slug', $slug)->with('individuals', 'districts', 'circuits.societies.location', 'location')->first();
        $data['markers'] = array();
        foreach ($denomination->circuits as $circuit) {
            foreach ($circuit->societies as $society) {
                if (isset($society->location)) {
                    $title="<b><a href=\"" . url('/circuits/' . $circuit->slug . '/' . $society->slug) . "\">" . $society->society . "</a></b> - <a href=\"" . url('/circuits/' . $circuit->slug) . "\">" . $society->circuit->circuitnumber . " " . $society->circuit->circuit . "</a>";
                    $title=str_replace('\'', '\\\'', $title);
                    $data['markers'][]=['title'=>$title, 'lat'=>$society->location->latitude, 'lng'=>$society->location->longitude];
                }
            }
        }
        $data['denomination']=$denomination;
        $data['title']=$denomination->denomination . " - " . str_plural($denomination->provincial);
        return view('churchnet::denominations.show', $data);
    }
}
