<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\JsonResponse;

class BillingController extends Controller
{



    public function addCard(Request $request)
    {
        $user_id = $request->user_id;

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $token = $request->stripeToken;

        try {

            $stripeCustomer = $user->createOrGetStripeCustomer();

            $card = Stripe::cards()->create($stripeCustomer['id'], $token);

            if (!$card) {
                throw new \Exception("Failed to add card to Stripe customer.");
            }




            return response()->json(['message' => 'Card added successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
