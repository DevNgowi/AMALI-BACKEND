<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
=======
use App\Models\ExpenseCategory;
use Dotenv\Validator;
use Exception;
use Illuminate\Database\QueryException;
>>>>>>> main
use Illuminate\Http\Request;

class ExpensesCategoryController extends Controller
{
<<<<<<< HEAD
    //
=======
 
        public function storeExpensesCategory(Request $request)
        {
            try {
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ]);
    
                $category = new ExpenseCategory();
                $category->name = $validatedData['name'];
                $category->description = $validatedData['description'];
                $category->save();
    
                return redirect()->back()->with('message', 'Successfully created expenses category');
    
            } catch (\Illuminate\Validation\ValidationException $validationException) {
                // Catch validation exceptions (e.g., required fields missing)
                return redirect()->back()->withErrors($validationException->errors())->withInput();
    
            } catch (QueryException $queryException) {
                // Catch database query exceptions (like Integrity constraint violations)
                if ($queryException instanceof \PDOException && $queryException->getCode() == '23000') {
                    // Specifically check for unique constraint violation (SQLSTATE 23000 and code 1062 for MySQL, might vary by DB)
                    if (strpos(strtolower($queryException->getMessage()), 'duplicate entry') !== false) {
                        $errorMessage = 'Category name already exists. Please choose a different name.';
                    } else {
                        $errorMessage = 'Database error occurred while creating category. Please try again.'; // General DB error if not unique violation
                    }
                } else {
                    $errorMessage = 'Database error occurred while creating category. Please try again.'; // General DB error for other QueryExceptions
                }
    
                // Log the database exception for debugging
                \Log::error('Expense Category Creation Database Error', [
                    'message' => $queryException->getMessage(),
                    'exception' => $queryException,
                ]);
    
                return redirect()->back()->with('error', $errorMessage)->withInput(); // Redirect back with user-friendly error message
    
            } catch (Exception $e) {
                // Catch any other unexpected exceptions
                \Log::error('Unexpected Error Creating Expense Category', [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]);
                return redirect()->back()->with('error', 'Failed to create expenses category due to an unexpected error. Please try again.');
            }
        }
>>>>>>> main
}
