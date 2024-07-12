<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Payout;
use Stripe\BalanceTransaction;
use Stripe\Issuing\Cardholder;
use Stripe\Issuing\Card;
use Stripe\Transfer;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Issuing\CardDetails;

class PaymentController extends Controller
{
    /**
     * Process user payment with Stripe.
     */




     public function createPayout(Request $request)
     {
         $user = $request->user();
         $amount = $request->input('amount'); // Amount the user wants to withdraw
         $stripeToken = $request->input('stripeToken'); // Token from Stripe

         if ($user->money_amount < $amount) {
             // The user does not have enough money in their account
             return response()->json(['error' => 'Insufficient funds'], 400);
         }



         try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
             // Convert the amount to cents
             $transfer = Transfer::create([
                'amount' => $amount * 100, // Convert amount to cents
                'currency' => 'usd',
                'destination' => '{CONNECTED_ACCOUNT_ID}', // Replace with the connected account ID
                'source_transaction' => $stripeToken,
            ]);

            // Perform any additional actions or update the user's balance
            $user->money_amount = $user->money_amount - $amount;
            return response()->json(['message' => 'Payout successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
     }

     public function redirectToStripePayment()
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    // Create a Stripe session for the payment
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],

        'mode' => 'setup',
        'success_url' => 'http://localhost:8000/payment/success',
        'cancel_url' => 'http://localhost:8000/payment/cancel',
    ]);

    // Redirect the user to the Stripe hosted payment page
    return redirect()->to($session->url);
}



public function createPayment(Request $request)
{
    $user = $request->user();
    $amount = $request->input('amount');
    Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $amount * 100,
                    'product_data' => [
                        'name' => 'Payment',
                        'description' => 'Payment for account recharge',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkPaymentStatus') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cancel'),
            'client_reference_id' => $user->id,
        ]);


        return response()->json(['url' => $session->url]); // Redirect the user to this URL
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function checkPaymentStatus(Request $request)
{
    $sessionId = $request->input('session_id');
    Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($session && $session->payment_status === 'paid') {
            // Payment is successful

            $user = User::find($session->client_reference_id); // Assuming you store user ID as client_reference_id in your session

            if ($session && $session->payment_status === 'paid') {
                $user = User::find($session->client_reference_id);
                if ($user) {
                    $amount = $session->amount_total / 100;
                    $user->money_amount += $amount;
                    $user->save();
                }
                return redirect()->route('success');
            } else {
                return redirect()->route('failure');
            }}

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}







public function paymentSuccess()
{
    // Payment was successful, update the money_amount for the user or perform any required actions
    return view('payment.success');
}

public function paymentCancel()
{
    // Payment was canceled, handle it accordingly
    return view('payment.cancel');
}



public function showPayoutForm(Request $request)
{
    $user = $request->user();

    if ($user->money_amount <= 0) {
        return redirect()->back()->with('error', 'You do not have enough funds to make a withdrawal.');
    }

    return view('payout');
}






public function linkAccount(Request $request)
{
    // Get authenticated user
    $user = $request->user();
    $withdrawAmount = $request->input('amount');

    // Check if user has enough balance
    if ($user->money_amount < $withdrawAmount) {
        return response()->json([
            'message' => 'Insufficient balance.'
        ], 400);
    }

    Stripe::setApiKey(env('STRIPE_SECRET'));

    $account = Account::create([
        'type' => 'standard',
    ]);

    $accountLink = AccountLink::create([
        'account' => $account->id,
        'refresh_url' => 'your_refresh_url',
        'return_url' => 'your_return_url',
        'type' => 'account_onboarding',
    ]);

    // Decrease user balance
    $user->money_amount -= $withdrawAmount;
    $user->save();

    return response()->json([
        'url' => $accountLink->url
    ]);
}
public function createAccount()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $account = Account::create([
            'type' => 'custom',
            'country' => 'US',
            'email' => 'jenny.rosen@example.com',
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ]
        ]);

        $account->external_accounts->create([
            'external_account' => 'btok_us_verified',
        ]);

        return response()->json($account);
    }






    public function createCardholder(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $cardholder = Cardholder::create([
            'type' => 'individual',
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phoneNumber,
            'billing' => [
                'address' => [
                    'line1' => $request->addressLine1,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'postal_code' => $request->postalCode,
                ],
            ],
        ]);

        return response()->json($cardholder);
    }

    public function createCard(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $card = Card::create([
            'cardholder' => $request->cardholderId,
            'currency' => $request->currency,
            'type' => 'virtual',
        ]);

        return response()->json($card);
    }
    public function createCardholderAndCard(Request $request)
{
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $cardholder = Cardholder::create([
        'type' => 'individual',
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phoneNumber,
        'billing' => [
            'address' => [
                'line1' => $request->addressLine1,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postalCode,
            ],
        ],
    ]);

    $card = Card::create([
        'cardholder' => $cardholder->id,
        'currency' => $request->currency,
        'type' => 'virtual',
    ]);

    return response()->json(['cardholder' => $cardholder, 'card' => $card,$cardId = $card->id]);
}

}










