<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function indexPayment()
    {
        $payments = Payment::orderBy('id', 'DESC')->paginate(10);
        $payment_types = PaymentType::orderBy('id', 'DESC')->get();
        return view('Vendor_&_Finance.Payments.index', compact('payments', 'payment_types'));
    }

    public function indexPaymentLocal()
    {
        $payments = Payment::select('id', 'short_code')->get();
        return response()->json([
            'data' => $payments,
            'message' => "success",
            'code' => 201
        ]);
    }

    public function storePayment(StorePaymentRequest $request)
    {
        $validated = $request->validate([
            'short_code' => 'required|string',
            'payment_type_id' => 'required|exists:payment_types,id', // Make sure the payment_type_id exists in the payment_types table
            'payment_method' => 'required|string',
        ]);

        // Store the validated data
        $payment = new Payment();
        $payment->short_code = $validated['short_code'];
        $payment->payment_type_id = $validated['payment_type_id'];
        $payment->payment_method = $validated['payment_method'];
        $payment->save();

        return redirect()->route('list_payment')->with('success', 'Successfully create payment');
    }





    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'short_code' => 'required|string|max:255',
            'payment_type_id' => 'required|exists:payment_types,id',
            'payment_method' => 'required|string|max:255',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->short_code = $request->short_code;
        $payment->payment_type_id = $request->payment_type_id;
        $payment->payment_method = $request->payment_method;
        $payment->save();

        return redirect()->route('list_payment')->with('success', 'Payment updated successfully.');
    }


    public function deletePayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->route('list_payment')->with('success', 'Payment deleted successfully.');
    }
}
