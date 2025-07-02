<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function payWithRazorpay(Request $request)
    {
        $input = $request->all();

        // Ensure required Razorpay fields are present
        if (!isset($input['razorpay_payment_id']) || !isset($input['razorpay_order_id']) || !isset($input['razorpay_signature'])) {
            $request->session()->put('error', 'Missing Razorpay payment details.');
            return redirect()->back();
        }

        // Initialize Razorpay API
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            // Verify payment signature
            $attributes = [
                'razorpay_order_id'    => $input['razorpay_order_id'],
                'razorpay_payment_id'  => $input['razorpay_payment_id'],
                'razorpay_signature'   => $input['razorpay_signature']
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Fetch and capture payment
            $paymentData = $api->payment->fetch($input['razorpay_payment_id']);
            $captured = $paymentData->capture(['amount' => $paymentData['amount']]);

            // Store payment in database
            $payment = new Payment();
            $payment->amount = $captured['amount'] / 100; // Convert from paisa to INR
            $payment->txn_id = $captured['id'];
            $payment->pay_method = 'razorpay';
            $payment->status = $captured['status'] ?? 'captured';
            $payment->email = $captured['email'] ?? null;
            $payment->contact = $captured['contact'] ?? null;
            $payment->save();

            $request->session()->put('success', 'Payment successful!');
            return redirect()->back();

        } catch (Exception $e) {
            // Log error and redirect with message
            Log::error('Razorpay Payment Failed: ' . $e->getMessage());
            $request->session()->put('error', 'Payment Failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
