<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = session()->get('cart', []);
        
        // Convertir el array de la sesión en objetos que la vista entienda
        $cartItems = collect($items)->map(function($item) {
            $book = \App\Models\Book::find($item['id']);
            if (!$book) return null; // El libro fue eliminado
            return (object)[
                'book_id'         => $item['id'],
                'book'            => $book,
                'book_title'      => $item['title'],
                'format'          => $item['format'],
                'quantity'        => $item['quantity'],
                'unit_price_cents'=> $item['price_cents'],
            ];
        })->filter(); // Elimina los null

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

        return view('cart.index', compact('cart', 'summary'));
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'format' => 'required|in:pdf,epub,physical',
            'quantity' => 'integer|min:1'
        ]);

        $book = \App\Models\Book::find($request->book_id);
        $cart = session()->get('cart', []);
        $key = $request->book_id . '_' . $request->format;

        if(isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->get('quantity', 1);
        } else {
            $cart[$key] = [
                'id' => $book->id,
                'title' => $book->title,
                'price_cents' => $book->price_cents,
                'format' => $request->format,
                'quantity' => $request->get('quantity', 1),
                'cover' => $book->cover_image
            ];
        }

        session()->put('cart', $cart);
        session()->put('cart_count', collect($cart)->sum('quantity'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'    => true, 
                'count'      => session('cart_count'),
                'item_count' => session('cart_count')
            ]);
        }

        return back()->with('success', 'Libro añadido al carrito');
    }

    public function removeItem($bookId, $format)
    {
        $cart = session()->get('cart', []);
        $key = $bookId . '_' . $format;

        if(isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            session()->put('cart_count', collect($cart)->sum('quantity'));
        }

        return back()->with('success', 'Libro removido');
    }

    public function updateQuantity(Request $request, $bookId, $format)
    {
        $cart = session()->get('cart', []);
        $key = $bookId . '_' . $format;

        if(isset($cart[$key])) {
            $cart[$key]['quantity'] = max(1, $request->quantity);
            session()->put('cart', $cart);
            session()->put('cart_count', collect($cart)->sum('quantity'));
        }

        return back();
    }

    public function applyCoupon(Request $request)
    {
        return back()->with('error', 'Cupón no válido');
    }
}
