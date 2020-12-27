<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Payment;
use Bishopm\Churchnet\Models\Society;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index($society)
    {
        return Payment::where('society_id', $society)->orderBy('paymentdate', 'DESC')->get();
    }

    public function upcoming($circuit)
    {
        $now = time();
        $upcomings = Payment::with('society')->where('paymentdatetime', '>', $now)->where('circuit_id', $circuit)->orderBy('paymentdatetime')->get();
        $data = array();
        foreach ($upcomings as $upcoming) {
            $dum['start'] = date("j F Y (H:i)", $upcoming->paymentdatetime);
            $dum['details'] = $upcoming->description;
            $dum['society'] = $upcoming->society->society;
            $dum['society_id'] = $upcoming->society->id;
            $data[]=$dum;
        }
        return $data;
    }

    public function edit($id)
    {
        return Payment::find($id);
    }

    public function stats(Request $request)
    {
        $rows=array();
        $payments = Payment::where('society_id', $request->society)->orderBy('paymentdate', 'ASC')->get();
        $rows['01']=array('id'=>1, 'Month'=>'Jan');
        $rows['02']=array('id'=>2, 'Month'=>'Feb');
        $rows['03']=array('id'=>3, 'Month'=>'Mar');
        $rows['04']=array('id'=>4, 'Month'=>'Apr');
        $rows['05']=array('id'=>5, 'Month'=>'May');
        $rows['06']=array('id'=>6, 'Month'=>'Jun');
        $rows['07']=array('id'=>7, 'Month'=>'Jul');
        $rows['08']=array('id'=>8, 'Month'=>'Aug');
        $rows['09']=array('id'=>9, 'Month'=>'Sep');
        $rows['10']=array('id'=>10, 'Month'=>'Oct');
        $rows['11']=array('id'=>11, 'Month'=>'Nov');
        $rows['12']=array('id'=>12, 'Month'=>'Dec');
        $stats['columns'] = array();
        $stats['society'] = Society::find($request->society)->society;
        $stats['columns'][]=array('name'=>'id', 'required'=>'true', 'label'=>'', 'align'=>'left', 'field'=>'id');
        $stats['columns'][]=array('name'=>'Month', 'required'=>'true', 'label'=>'Month', 'align'=>'left', 'field'=>'Month');
        $years = array();
        foreach ($payments as $payment) {
            $yr = substr($payment->paymentdate, 0, 4);
            if (!in_array($yr, $years)) {
                $years[]=$yr;
                foreach ($rows as $key=>$row) {
                    $rows[$key][$yr]=0;
                }
            }
            $mth = substr($payment->paymentdate, 5, 2);
            $rows[$mth][$yr]=$rows[$mth][$yr] + floatval($payment->amount);
        }
        $stats['rows'] = $rows;
        asort($years);
        $stats['yrs'] = $years;
        foreach ($years as $yy) {
            $stats['columns'][]=array('name'=>$yy, 'required'=>'true', 'label'=>$yy, 'align'=>'center', 'field'=>$yy);
        }
        return $stats;
    }

    public function store(Request $request)
    {
        $payment = Payment::create(['society_id'=>$request->society_id, 'paymentdate'=>substr($request->paymentdate, 0, 10), 'pgnumber'=>$request->pgnumber, 'amount'=>$request->amount]);
        return "New payment added";
    }
    
    public function update($id, Request $request)
    {
        $payment = Payment::find($id);
        $payment->update($request->all());
        return "Payment has been updated";
    }

    public function destroy($id)
    {
        $payment=Payment::find($id);
        $payment->delete();
        return "Payment has been deleted";
    }
}
