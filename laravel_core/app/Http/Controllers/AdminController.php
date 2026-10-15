<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'title' => 'ADMIN',
            'pendingCount' => Order::where('status', OrderStatus::Pending)->count(),
            'paidCount' => Order::where('status', OrderStatus::Paid)->count(),
            'processingCount' => Order::where('status', OrderStatus::Processing)->count(),
            'productCount' => Product::count(),
        ]);
    }
}
