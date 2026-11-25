<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $medicines = Medicine::count();
        $lowStock = Medicine::where('stock', '<=', MedicineController::LOW_STOCK)->count();
        $customers = Customer::count();
        $sales = Sale::count();

        return view('principal.dashboard.index', compact('medicines', 'lowStock', 'customers', 'sales'));
    }
}
