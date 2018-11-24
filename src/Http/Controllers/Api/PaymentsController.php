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
        return Payment::where('society_id', $society)->get();
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

    public function edit($payment)
    {
        $mtg = $this->payment->find($payment);
        $mtg->datestr = date('Y-m-d H:i', $mtg->paymentdatetime);
        return $mtg;
    }

    public function show(Payment $payment)
    {
        $data['payment']=$payment;
        return view('connexion::payments.show', $data);
    }

    public function store(Request $request)
    {
        $request->merge(array('paymentdatetime' => strtotime(substr($request->paymentdatetime, 0, 19))));
        $this->payment->create($request->all());
        return "New payment added";
    }
    
    public function update($id, Request $request)
    {
        $payment = Payment::find($id);
        $request->merge(array('paymentdatetime' => strtotime(substr($request->paymentdatetime, 0, 19))));
        $payment->update($request->all());
        return "Payment has been updated";
    }

    public function destroy($id)
    {
        $mtg=Payment::find($id);
        $this->payment->destroy($mtg);
        return "Payment has been deleted";
    }
}
