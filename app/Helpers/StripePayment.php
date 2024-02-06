<?php

namespace App\Helpers;

use Stripe\StripeClient;

/**
 * 
 */
class StripePayment
{
    public $api;

    public function __construct()
    {
        // $stripe_test_secret_key = get_setting('stripe_settings');
        $this->api = new StripeClient('sk_test_y6sWTsaDUd3xXBm3MpkRcQjI00xYhRPmOK');
    }

    public function createCustomer($customerData)
    {
        return $this->api->customers->create([
            'email' => $customerData['email'],
            'name' => $customerData['name'],
            "address" => [
                "city" => 'test',
                "country" => 'test',
                "line1" => 'test',
                "line2" => 'test',
                "postal_code" => 'test',
                "state" => 'test'
            ],

        ]);
    }

    public function createCharge($stripe_source, $order)
    {

        $customerData = [
            // 'email' => 'pankajsoni.letscms@gmail.com',
            'name' => 'pankaj soni',
            // 'source' => $stripe_source,
            "address" => [
                "city" => 'test',
                "country" => 'US',
                "line1" => 'test',
                "line2" => 'test',
                "postal_code" => 'test',
                "state" => 'test'
            ],
        ];
        // $cutomer =  $this->createCustomer($customerData);
        $chargeData = $this->api->charges->create([
            // "customer" => $cutomer->id,
            "shipping" => $customerData,
            "amount" => ($order->amount) * 100,
            "currency" => strtolower(get_currency()),
            "source" => $stripe_source, // obtained with Stripe.js
            "description" => get_setting('website_title') . '- Order ID : ' . $order->id,
            "metadata" => ["order_id" => $order->id]
        ]);

        // $balanceTransaction = $this->api->balanceTransactions->retrieve($chargeData->balance_transaction);

        // update_post_meta($order_id, '_paid_date', date('Y-m-d H:i:s', $chargeData->created));
        // update_post_meta($order_id, '_transaction_id', $chargeData->id);
        // update_post_meta($order_id, '_stripe_source_id', $chargeData->source->id);
        // update_post_meta($order_id, '_stripe_intent_id', $chargeData->source->id);
        // update_post_meta($order_id, '_stripe_net', number_format($balanceTransaction->net / 100, 2, '.', ''));
        // update_post_meta($order_id, '_stripe_fee', number_format($balanceTransaction->fee / 100, 2, '.', ''));
        // update_post_meta($order_id, '_stripe_charge_captured', 'yes');
        // update_post_meta($order_id, '_stripe_currency', 'INR');
        return $chargeData;
    }
}
