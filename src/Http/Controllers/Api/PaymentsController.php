<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Models\Payment;
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

    public function show($payment)
    {
        $data['payment']=$payment;
        return view('connexion::payments.show', $data);
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
