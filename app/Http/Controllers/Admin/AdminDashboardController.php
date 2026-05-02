<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function books()
    {
        return view('admin.books.index');
    }

    public function updateBookStock(Request $request, $id)
    {
        return back()->with('success', 'Stock actualizado');
    }

    public function orders()
    {
        return view('admin.orders.index');
    }

    public function showOrder($id)
    {
        return view('admin.orders.show');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        return back()->with('success', 'Estado de orden actualizado');
    }

    public function users()
    {
        return view('admin.users.index');
    }
}
