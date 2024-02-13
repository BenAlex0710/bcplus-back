<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;

class CronTasksController extends Controller
{
    public function update_active_packages_status()
    {
        return $orders = PackageOrder::where('status', '1')
            ->where('expiry_date', '<', now()->format('Y-m-d'))
            ->update(['status' => '2']);
    }
}
