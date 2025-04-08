<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Expenses;
use App\Models\ExpenseStatus;
use App\Models\Item;
use App\Models\PaymentType;
use App\Models\PosLocation;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpensesController extends Controller
{
    public function indexExpenses(Request $request)
    {
        $expenses = Expense::with(['expenseCategory', 'paymentType', 'vendor'])
            ->latest(); // Start with the base query

        // Apply filters based on request parameters
        if ($category = $request->get('filter_category')) {
            $expenses->where('expense_category_id', $category);
        }

        if ($reimbursable = $request->get('filter_reimbursable')) {
            $expenses->where('is_reimbursable', $reimbursable);
        }

        if ($dateRange = $request->get('filter_date_range')) {
            $dates = explode(' - ', $dateRange); // Assuming date range is in "start - end" format
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::parse($dates[0])->format('Y-m-d');
                $endDate = \Carbon\Carbon::parse($dates[1])->format('Y-m-d');
                $expenses->whereBetween('expense_date', [$startDate, $endDate]);
            }
        }

        if ($search = $request->get('filter_search')) {
            $expenses->where(function ($query) use ($search) {
                $query->where('description', 'like', '%' . $search . '%')
                    ->orWhere('reference_number', 'like', '%' . $search . '%'); // Add more fields if needed for search
            });
        }

        $expenses = $expenses->paginate(10)->appends(request()->query()); // Paginate after filters and append query params

        $expenseCategories = ExpenseCategory::all(); // Fetch all expense categories for filter and modal
        $paymentTypes = PaymentType::all(); // Fetch payment methods for edit modal
        $vendors = Vendor::all();
        $purchaseOrders = PurchaseOrder::all();
        $inventoryItems = Item::all();
        $posLocations = PosLocation::all();
        $currencies = Currency::all();

        return view('Expenses.index', compact('expenses', 'expenseCategories', 'paymentTypes', 'vendors', 'purchaseOrders', 'inventoryItems', 'posLocations', 'currencies'));
    }
    public function createExpenses()
    {
        $expenseCategories = ExpenseCategory::all();
        $paymentMethods = PaymentType::all();
        $vendors = Vendor::all();
        $purchaseOrders = PurchaseOrder::all();
        $inventoryItems = Item::with('itemBarcode')->get();
        $posLocations = PosLocation::all();
        $currencies = Currency::all();

        return view('Expenses.auto_generate.create', [
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods,
            'vendors' => $vendors,
            'purchaseOrders' => $purchaseOrders,
            'inventoryItems' => $inventoryItems,
            'posLocations' => $posLocations,
            'currencies' => $currencies,
        ]);

    }

    public function storeExpenses(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'payment_type_id' => 'required|exists:payment_types,id',
                'vendor_id' => 'nullable|exists:vendors,id',
                'expense_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:10',
                'description' => 'nullable|string',
                'reference_number' => 'nullable|string|max:255',
                'receipt_path' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'purchase_order_id' => 'nullable|exists:purchase_orders,id',
                'inventory_item_id' => 'nullable|exists:inventory_items,id',
                'pos_location_id' => 'nullable|exists:pos_locations,id',
                'is_reimbursable' => 'nullable|boolean', // Keep boolean validation
                'notes' => 'nullable|string',
            ]);

            // Handle receipt upload
            if ($request->hasFile('receipt_path')) {
                $receiptPath = $request->file('receipt_path')->store('receipts', 'public');
                $validatedData['receipt_path'] = $receiptPath;
            }

            $expense = new Expense();
            $expense->expense_category_id = $validatedData['expense_category_id'];
            $expense->payment_type_id = $validatedData['payment_type_id'];
            $expense->vendor_id = $validatedData['vendor_id'];
            $expense->expense_date = $validatedData['expense_date'];
            $expense->amount = $validatedData['amount'];
            $expense->currency = $validatedData['currency'];
            $expense->description = $validatedData['description'];
            $expense->reference_number = $validatedData['reference_number'];
            $expense->receipt_path = $validatedData['receipt_path'] ?? null;
            $expense->purchase_order_id = $validatedData['purchase_order_id'];
            $expense->item_id = $validatedData['inventory_item_id'];
            $expense->pos_location_id = $validatedData['pos_location_id'];
            $expense->user_id = Auth::user()->id;

            // Correctly handle boolean value for is_reimbursable from checkbox
            $expense->is_reimbursable = $request->has('is_reimbursable'); // Check if 'is_reimbursable' is in the request

            $expense->notes = $validatedData['notes'];
            $expense->save();

            return redirect()->route('list_expenses')->with('message', 'Expense created successfully.');

        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return redirect()->back()->withErrors($validationException->errors())->withInput();

        } catch (QueryException $queryException) {
            \Log::error('Expense Creation Database Error', [
                'message' => $queryException->getMessage(),
                'exception' => $queryException,
            ]);
            return redirect()->back()->with('error', 'Database error occurred while creating expense. Please try again.')->withInput();

        } catch (Exception $e) {
            \Log::error('Expense Creation Error', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            return redirect()->back()->with('error', 'Failed to create expense due to an unexpected error. Please try again.')->withInput();
        }
    }


    public function editExpenses(Request $request, $id)
    {
        $expense = Expense::findOrFail($id); 
        $expenseCategories = ExpenseCategory::all();
        $paymentTypes = PaymentType::all(); 
        $vendors = Vendor::all();
        $purchaseOrders = PurchaseOrder::all();
        $inventoryItems = Item::all();
        $posLocations = PosLocation::all();
        $currencies = Currency::all();

        return view('Expenses.auto_generate.edit', compact(
            'expense', // Pass the single expense to the view, renamed from 'expenses' to 'expense' for clarity
            'expenseCategories',
            'paymentTypes', // Corrected variable name
            'vendors',
            'purchaseOrders',
            'inventoryItems',
            'posLocations',
            'currencies'
        ));
    }

    public function updateExpenses(Request $request, $id)
    {
        // 1. Validate the request data
        $validatedData = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id', // Ensure category exists
            'payment_type_id' => 'required|exists:payment_types,id', // Ensure payment type exists
            'vendor_id' => 'nullable|exists:vendors,id', // Optional vendor, ensure it exists if provided
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10', // Adjust max length as needed
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255', 
            'receipt_path' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048', 
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'item_id' => 'nullable|exists:items,id',
            'pos_location_id' => 'nullable|exists:pos_locations,id',
            'is_reimbursable' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::findOrFail($id);

        if ($request->hasFile('receipt_path')) {
            
            if ($expense->receipt_path) {
                Storage::delete($expense->receipt_path);
            }
            $receiptPath = $request->file('receipt_path')->store('receipts'); 
            $validatedData['receipt_path'] = $receiptPath; 
        }

        // 4. Update the expense attributes
        $expense->update($validatedData);

        // 5. Redirect with success message
        return redirect()->route('list_expenses')->with('success', __('Expense updated successfully!'));
    }


    public function deleteExpenses($id)
    {
       
        $expense = Expense::findOrFail($id);
        if ($expense->receipt_path) {
            Storage::delete($expense->receipt_path);
        }
        $expense->delete();
        return redirect()->route('list_expenses')->with('success', __('Expense deleted successfully!'));
    }
}
