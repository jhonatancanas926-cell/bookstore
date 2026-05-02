<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // TODO: Procesar webhooks de Stripe
        return response('Webhook Handled', 200);
    }
}
