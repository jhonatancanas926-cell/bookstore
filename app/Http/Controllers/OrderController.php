<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OrderController extends Controller
{
    public function index()
    {
        // Mientras no exista el modelo Order, devolvemos colección vacía paginada
        if (class_exists(\App\Models\Order::class)) {
            try {
                $orders = auth()->user()->orders()
                    ->with('items')
                    ->latest()
                    ->paginate(10);
            } catch (\Exception $e) {
                $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect(), 0, 10
                );
            }
        } else {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 10
            );
        }

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = null;

        if (class_exists(\App\Models\Order::class)) {
            try {
                $order = auth()->user()->orders()
                    ->with('items.book')
                    ->findOrFail($id);
            } catch (\Exception $e) {
                abort(404);
            }
        } else {
            abort(404);
        }

        return view('orders.show', compact('order'));
    }
}
