<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentGateway::create([
            'form_id' => 20,
            'code' => 1000,
            'name' => 'Bank Transfer',
            'alias' => 'bank_transfer',
            'status' => true,
            'gateway_parameters' => null,
            'support_currencies' => null,
            'extra_data' => null,
            'description' => 'Bank Transfer',
        ]);

        PaymentGateway::create([
            'form_id' => 0,
            'code' => 101,
            'name' => 'PayPal',
            'alias' => 'paypal',
            'status' => true,
            'gateway_parameters' => '{"paypal_email":{"title":"PayPal Email","global":true,"value":"sb-owud61543012@business.example.com"}}',
            'support_currencies' => '{"AUD":"AUD","BRL":"BRL","CAD":"CAD","CZK":"CZK","DKK":"DKK","EUR":"EUR","HKD":"HKD","HUF":"HUF","INR":"INR","ILS":"ILS","JPY":"JPY","MYR":"MYR","MXN":"MXN","TWD":"TWD","NZD":"NZD","NOK":"NOK","PHP":"PHP","PLN":"PLN","GBP":"GBP","RUB":"RUB","SGD":"SGD","SEK":"SEK","CHF":"CHF","THB":"THB","USD":"$"}',
            'extra_data' => '',
            'description' => 'PayPal',
        ]);

        PaymentGateway::create([
            'form_id' => 0,
            'code' => 102,
            'name' => 'Stripe',
            'alias' => 'stripe',
            'status' => true,
            'gateway_parameters' => '{"stripe_api_key":{"title":"Stripe API Key","global":true,"value":""}}',
            'support_currencies' => '{"AUD":"AUD","BRL":"BRL","CAD":"CAD","CZK":"CZK","DKK":"DKK","EUR":"EUR","HKD":"HKD","HUF":"HUF","INR":"INR","ILS":"ILS","JPY":"JPY","MYR":"MYR","MXN":"MXN","TWD":"TWD","NZD":"NZD","NOK":"NOK","PHP":"PHP","PLN":"PLN","GBP":"GBP","RUB":"RUB","SGD":"SGD","SEK":"SEK","CHF":"CHF","THB":"THB","USD":"$"}',
            'extra_data' => '',
            'description' => 'Stripe',
        ]);

        PaymentGateway::create([
            'form_id' => 0,
            'code' => 103,
            'name' => 'Midtrans',
            'alias' => 'midtrans',
            'status' => true,
            'gateway_parameters' => '{"midtrans_api_key":{"title":"Midtrans API Key","global":true,"value":""}}',
            'support_currencies' => '{"AUD":"AUD","BRL":"BRL","CAD":"CAD","CZK":"CZK","DKK":"DKK","EUR":"EUR","HKD":"HKD","HUF":"HUF","INR":"INR","ILS":"ILS","JPY":"JPY","MYR":"MYR","MXN":"MXN","TWD":"TWD","NZD":"NZD","NOK":"NOK","PHP":"PHP","PLN":"PLN","GBP":"GBP","RUB":"RUB","SGD":"SGD","SEK":"SEK","CHF":"CHF","THB":"THB","USD":"$"}',
            'extra_data' => '',
            'description' => 'Midtrans',
        ]);

        //xendit
        PaymentGateway::create([
            'form_id' => 0,
            'code' => 104,
            'name' => 'Xendit',
            'alias' => 'xendit',
            'status' => true,
            'gateway_parameters' => '{"xendit_api_key":{"title":"Xendit API Key","global":true,"value":""}}',
            'support_currencies' => '{"AUD":"AUD","BRL":"BRL","CAD":"CAD","CZK":"CZK","DKK":"DKK","EUR":"EUR","HKD":"HKD","HUF":"HUF","INR":"INR","ILS":"ILS","JPY":"JPY","MYR":"MYR","MXN":"MXN","TWD":"TWD","NZD":"NZD","NOK":"NOK","PHP":"PHP","PLN":"PLN","GBP":"GBP","RUB":"RUB","SGD":"SGD","SEK":"SEK","CHF":"CHF","THB":"THB","USD":"$"}',
            'extra_data' => '',
            'description' => 'Xendit',
        ]);

        PaymentGateway::create([
            'form_id' => 0,
            'code' => 105,
            'name' => 'Tripay',
            'alias' => 'tripay',
            'status' => true,
            'gateway_parameters' => '{"tripay_api_key":{"title":"Tripay API Key","global":true,"value":""}}',
            'support_currencies' => '{"AUD":"AUD","BRL":"BRL","CAD":"CAD","CZK":"CZK","DKK":"DKK","EUR":"EUR","HKD":"HKD","HUF":"HUF","INR":"INR","ILS":"ILS","JPY":"JPY","MYR":"MYR","MXN":"MXN","TWD":"TWD","NZD":"NZD","NOK":"NOK","PHP":"PHP","PLN":"PLN","GBP":"GBP","RUB":"RUB","SGD":"SGD","SEK":"SEK","CHF":"CHF","THB":"THB","USD":"$"}',
            'extra_data' => '',
            'description' => 'Tripay',
        ]);
    }
}
