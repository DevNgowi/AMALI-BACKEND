<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CityLedger;
use App\Models\Customer;
use App\Models\CustomerType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function indexCustomer()
    {
        $customers = Customer::with(['customerType', 'city'])->get();
        return view('Customers.index', compact('customers'));
    }

    public function indexCustomerLocal()
    {
        $customers = Customer::all();

        return response()->json([
            'data' => $customers,
            'message' => 'Successfull get customers data',
            'code' => 201
        ]);
    }


    public function createCustomer()
    {
        $cities = City::all();
        $customer_types = CustomerType::all();
        $customer_code = $this->generateCustomerCode();

        return view('Customers.auto_generate.create', compact('cities', 'customer_types', 'customer_code'));
    }

    private function generateCustomerCode()
    {
        $prefix = 'CUST';
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        // Get the last customer code for the current month
        $lastCustomer = Customer::where('customer_code', 'like', "{$prefix}{$year}{$month}{$day}%")
            ->orderBy('customer_code', 'desc')
            ->first();

        if ($lastCustomer) {
            // Extract the numeric part and increment
            $lastNumber = intval(substr($lastCustomer->customer_code, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: CUST2024020001
        return sprintf("%s%s%s%s%04d", $prefix, $year, $month, $day, $newNumber);
    }

    public function storeCustomer(Request $request)
    {
        Log::info('Starting storeCustomer method with request data: ' . json_encode($request->all()));

        // Validate the request
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'customer_type_id' => 'required|exists:customer_types,id',
            'city_id' => 'required|exists:cities,id',
            'opening_balance' => 'nullable|numeric',
            'dr_cr' => 'required|in:Dr,Cr',
            'mobile_sms_no' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'credit_period' => 'nullable|integer',
            'active' => 'boolean',
            'account_ledger_creation' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::warning('Customer validation failed: ' . json_encode($validator->errors()->toArray()));
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        Log::info('Customer validation passed.');

        DB::beginTransaction();
        try {
            // Generate customer code
            $customerCode = $this->generateCustomerCode();
            $validated['customer_code'] = $customerCode;
            Log::info('Generated customer code: ' . $customerCode);

            // Create customer
            $customer = Customer::create($validated);
            Log::info('Customer created successfully. Customer ID: ' . $customer->id);

            // Create city ledger if requested
            if ($request->account_ledger_creation) {
                CityLedger::create([
                    'customer_id' => $customer->id,
                    'ledger_code' => 'CL-' . $customer->customer_code,
                    'opening_balance' => $request->opening_balance ?? 0.00,
                    'balance_type' => $request->dr_cr,
                    'current_balance' => $request->opening_balance ?? 0.00,
                    'active' => true
                ]);
                Log::info('City Ledger created for Customer ID: ' . $customer->id);
            } else {
                Log::info('City Ledger creation skipped as requested.');
            }

            DB::commit();
            Log::info('Transaction committed successfully for Customer ID: ' . $customer->id);
            return redirect()->route('list_customer')->with('success', 'Customer created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating customer. Transaction rolled back. Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Error creating customer: ' . $e->getMessage())->withInput();
        }
    }

    public function editCustomer(Request $request, $id)
    {
        $customers = Customer::findOrFail($id);

        $customer_types = CustomerType::all();
        $cities = City::all();

        return view('Customers.auto_generate.edit', compact('customer_types', 'cities', 'customers'));
    }

    public function updateCustomer(Request $request, $id)
    {
        Log::info('Starting updateCustomer method for customer ID: ' . $id . ' with request data: ' . json_encode($request->all()));

        // Validate the request
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'customer_type_id' => 'required|exists:customer_types,id',
            'city_id' => 'required|exists:cities,id',
            'opening_balance' => 'nullable|numeric',
            'dr_cr' => 'required|in:Dr,Cr',
            'mobile_sms_no' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'credit_period' => 'nullable|integer',
            'active' => 'nullable|boolean', // Ensure this is nullable and validated correctly
            'account_ledger_creation' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::warning('Customer validation failed: ' . json_encode($validator->errors()->toArray()));
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        Log::info('Customer validation passed.');

        // Ensure the `active` field is set to `false` if it's not present in the request
        if (!array_key_exists('active', $validated)) {
            $validated['active'] = false; // Set to false if the checkbox is unchecked
        }

        DB::beginTransaction();
        try {
            // Find the existing customer by ID
            $customer = Customer::findOrFail($id);
            Log::info('Customer found for update. Customer ID: ' . $customer->id);

            // Update customer data
            $customer->update($validated);
            Log::info('Customer updated successfully. Customer ID: ' . $customer->id);

            // Update city ledger if account_ledger_creation is checked
            if ($request->account_ledger_creation) {
                // Check if the customer already has a city ledger, and update it if it exists, or create a new one
                $cityLedger = CityLedger::where('customer_id', $customer->id)->first();
                if ($cityLedger) {
                    $cityLedger->update([
                        'ledger_code' => 'CL-' . $customer->customer_code,
                        'opening_balance' => $request->opening_balance ?? 0.00,
                        'balance_type' => $request->dr_cr,
                        'current_balance' => $request->opening_balance ?? 0.00,
                        'active' => true
                    ]);
                    Log::info('City Ledger updated for Customer ID: ' . $customer->id);
                } else {
                    CityLedger::create([
                        'customer_id' => $customer->id,
                        'ledger_code' => 'CL-' . $customer->customer_code,
                        'opening_balance' => $request->opening_balance ?? 0.00,
                        'balance_type' => $request->dr_cr,
                        'current_balance' => $request->opening_balance ?? 0.00,
                        'active' => true
                    ]);
                    Log::info('City Ledger created for Customer ID: ' . $customer->id);
                }
            } else {
                Log::info('City Ledger creation skipped as requested.');
            }

            DB::commit();
            Log::info('Transaction committed successfully for Customer ID: ' . $customer->id);
            return redirect()->route('list_customer')->with('success', 'Customer updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating customer. Transaction rolled back. Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Error updating customer: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->back()->with('message', 'Successfully delete Customer Information');
    }


}
