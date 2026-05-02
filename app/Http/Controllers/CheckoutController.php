<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    private function getCartData()
    {
        $items = session()->get('cart', []);
        
        $cartItems = collect($items)->map(function($item) {
            $book = \App\Models\Book::find($item['id']);
            if (!$book) return null;
            return (object)[
                'book_id'         => $item['id'],
                'book'            => $book,
                'book_title'      => $item['title'],
                'format'          => $item['format'],
                'quantity'        => $item['quantity'],
                'unit_price_cents'=> $item['price_cents'],
            ];
        })->filter();

        $cart = (object)[
            'items' => $cartItems
        ];

        $subtotalCents = $cartItems->sum(function($item) {
            return $item->unit_price_cents * $item->quantity;
        });

        $taxCents = $subtotalCents * 0.19;
        $totalCents = $subtotalCents + $taxCents;

        $summary = [
            'subtotal' => '$' . number_format($subtotalCents / 100, 2),
            'tax' => '$' . number_format($taxCents / 100, 2),
            'discount' => '$0.00',
            'discount_cents' => 0,
            'total' => '$' . number_format($totalCents / 100, 2)
        ];

        return [$cart, $summary];
    }

    public function address()
    {
        if (session('cart_count', 0) == 0) {
            return redirect()->route('cart.index');
        }

        [$cart, $summary] = $this->getCartData();
        return view('checkout.address', compact('cart', 'summary'));
    }

    public function storeAddress(Request $request)
    {
        // TODO: Validar y guardar dirección
        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        if (session('cart_count', 0) == 0) {
            return redirect()->route('cart.index');
        }

        [$cart, $summary] = $this->getCartData();
        $stripeKey = config('services.stripe.key') ?? 'pk_test_mock';
        
        // Simular dirección de facturación
        $billingAddress = [
            'full_name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'address' => 'Calle 123',
            'city' => 'Bogotá',
        ];

        return view('checkout.payment', compact('cart', 'summary', 'stripeKey', 'billingAddress'));
    }

    public function createIntent(Request $request)
    {
        // TODO: Crear PaymentIntent de Stripe
        return response()->json(['clientSecret' => 'mock_secret']);
    }

    public function confirm(Request $request)
    {
        // TODO: Confirmar pago y crear orden
        return redirect()->route('orders.index');
    }

    public function success($orderId)
    {
        return view('checkout.success', ['orderId' => $orderId]);
    }
}
