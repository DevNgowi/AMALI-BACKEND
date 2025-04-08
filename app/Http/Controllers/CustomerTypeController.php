<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    public function indexCustomerTypeLocal()
    {
        $customer_type = CustomerType::select(
            'name', 'id'
        )->get();

        return response()->json([
            'data' => $customer_type,
            'message' => 'Successfull get customer type',
            'code' => 201
        ]);
    }
}
