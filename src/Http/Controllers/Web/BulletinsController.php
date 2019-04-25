<?php

namespace Bishopm\Churchnet\Http\Controllers\Web;

use Bishopm\Churchnet\Models\Bulletin;
use Bishopm\Churchnet\Models\Bulletintemplate;
use Bishopm\Churchnet\Libraries\Fpdf\Fpdf;
use App\Http\Controllers\Controller;

class BulletinsController extends Controller
{

    public function index()
    {
        $bulletins = $this->bulletin->all();
        return view('churchnet::bulletins.index', compact('bulletins'));
    }

    public function show($id)
    {
        $bulletin = Bulletin::with('bulletinitems')->find($id);
        $template = Bulletintemplate::with('bulletintemplateitems')->find(1);
        $columns = json_decode($template->columncount, true);
        $pdf = new Fpdf();
        $pdf->AddPage('L');
        $logopath = base_path() . '/public/vendor/bishopm/images/mcsa.jpg';
        $pdf->Image($logopath, 5, 5, 0, 21);
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->text(10, 10, "No preachers have been added yet");
        $pdf->Output();
    }
}
