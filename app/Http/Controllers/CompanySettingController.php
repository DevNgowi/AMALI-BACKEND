<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyFiscalYear;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function indexCompanySetting()
    {
        $company_details = Company::with(['fiscalYears', 'country'])->get();
        return view('settings.company_settings.index', compact('company_details'));
    }

    public function indexCompanySettingLocal()
    {
        $company_details = Company::first();

        if (!$company_details) {
            return response()->json([
                'message' => "Company details not found",
                'code' => 404
            ], 404);
        }

        return response()->json([
            'data' => $company_details,
            'message' => "Success",
            'code' => 200
        ], 200);
    }


    public function createCompanySetting()
    {
        $countries = Country::all();
        return view('settings.company_settings.auto_generate.create', compact('countries'));
    }

    public function editCompanySetting($id)
    {
        $company = Company::with('fiscalYears')->findOrFail($id);
        $countries = Country::all();
        return view('settings.company_settings.auto_generate.edit', compact('company', 'countries'));
    }




    public function storeCompanySetting(Request $request)
    {
        Log::info('Starting company registration process', [
            'request_data' => $request->except(['company_logo'])
        ]);

        try {
            // Validate request data
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'website' => 'nullable|url|max:255',
                'email' => 'required|email|max:255|unique:companies,email',
                'state' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'tin_no' => 'nullable|string|max:50',
                'post_code' => 'nullable|string|max:50',
                'vrn_no' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:500',
                'financial_year_from' => 'required|date',
                'financial_year_to' => 'required|date|after:financial_year_from',
                'is_active' => 'required|boolean',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'nullable|string|max:1000'
            ]);

            Log::info('Request data validated successfully');

            DB::beginTransaction();
            Log::info('Database transaction started');

            $logoPath = null;
            if ($request->hasFile('company_logo')) {
                Log::info('Processing company logo upload');
                try {
                    $logoPath = $request->file('company_logo')->store('company_logos', 'public');
                    Log::info('Company logo uploaded successfully', ['path' => $logoPath]);
                } catch (\Exception $e) {
                    Log::error('Failed to upload company logo', [
                        'error' => $e->getMessage(),
                        'file' => $request->file('company_logo')
                    ]);
                    throw $e;
                }
            }

            // Create company record
            Log::info('Creating company record', [
                'company_data' => array_merge(
                    $validatedData,
                    ['company_logo' => $logoPath]
                )
            ]);

            $company = Company::create([
                'company_name' => $validatedData['company_name'],
                'country_id' => $validatedData['country_id'],
                'website' => $validatedData['website'],
                'email' => $validatedData['email'],
                'state' => $validatedData['state'],
                'phone' => $validatedData['phone'],
                'tin_no' => $validatedData['tin_no'],
                'post_code' => $validatedData['post_code'],
                'vrn_no' => $validatedData['vrn_no'],
                'address' => $validatedData['address'],
                'company_logo' => $logoPath,
            ]);

            Log::info('Company record created successfully', ['company_id' => $company->id]);

            // Create fiscal year record
            Log::info('Creating fiscal year record', [
                'fiscal_year_data' => [
                    'company_id' => $company->id,
                    'financial_year_from' => $validatedData['financial_year_from'],
                    'financial_year_to' => $validatedData['financial_year_to'],
                ]
            ]);

            CompanyFiscalYear::create([
                'company_id' => $company->id,
                'financial_year_from' => $validatedData['financial_year_from'],
                'financial_year_to' => $validatedData['financial_year_to'],
                'description' => $validatedData['description'],
                'is_active' => $validatedData['is_active'],
            ]);

            Log::info('Fiscal year record created successfully');

            DB::commit();
            Log::info('Database transaction committed successfully');

            return redirect()->route('list_company_details')
                ->with('success', 'Company details saved successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['company_logo'])
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('Error during company registration', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['company_logo'])
            ]);

            DB::rollBack();
            Log::info('Database transaction rolled back');

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving company details: ' . $e->getMessage());
        }
    }

    public function updateCompanySetting(Request $request, $id)
    {
        Log::info('Starting company update process', [
            'request_data' => $request->except(['company_logo'])
        ]);

        try {
            // Validate request data
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'website' => 'nullable|url|max:255',
                'email' => 'required|email|max:255|unique:companies,email,' . $id,
                'state' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'tin_no' => 'nullable|string|max:50',
                'post_code' => 'nullable|string|max:50',
                'vrn_no' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:500',
                'financial_year_from' => 'required|date',
                'financial_year_to' => 'required|date|after:financial_year_from',
                'is_active' => 'required|boolean',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'nullable|string|max:1000'
            ]);

            Log::info('Request data validated successfully');

            DB::beginTransaction();
            Log::info('Database transaction started');

            // Find the company record
            $company = Company::findOrFail($id);

            // Handle logo upload
            $logoPath = $company->company_logo; // Keep the existing logo by default
            if ($request->hasFile('company_logo')) {
                Log::info('Processing company logo upload');
                try {
                    // Delete the old logo if it exists
                    if ($logoPath) {
                        Storage::disk('public')->delete($logoPath);
                    }

                    // Store the new logo
                    $logoPath = $request->file('company_logo')->store('company_logos', 'public');
                    Log::info('Company logo uploaded successfully', ['path' => $logoPath]);
                } catch (\Exception $e) {
                    Log::error('Failed to upload company logo', [
                        'error' => $e->getMessage(),
                        'file' => $request->file('company_logo')
                    ]);
                    throw $e;
                }
            }

            // Update company record
            Log::info('Updating company record', [
                'company_data' => array_merge(
                    $validatedData,
                    ['company_logo' => $logoPath]
                )
            ]);

            $company->update([
                'company_name' => $validatedData['company_name'],
                'country_id' => $validatedData['country_id'],
                'website' => $validatedData['website'],
                'email' => $validatedData['email'],
                'state' => $validatedData['state'],
                'phone' => $validatedData['phone'],
                'tin_no' => $validatedData['tin_no'],
                'post_code' => $validatedData['post_code'],
                'vrn_no' => $validatedData['vrn_no'],
                'address' => $validatedData['address'],
                'company_logo' => $logoPath,
            ]);

            Log::info('Company record updated successfully', ['company_id' => $company->id]);

            // Update fiscal year record
            $fiscalYear = $company->fiscalYears->first();
            if ($fiscalYear) {
                Log::info('Updating fiscal year record', [
                    'fiscal_year_data' => [
                        'financial_year_from' => $validatedData['financial_year_from'],
                        'financial_year_to' => $validatedData['financial_year_to'],
                    ]
                ]);

                $fiscalYear->update([
                    'financial_year_from' => $validatedData['financial_year_from'],
                    'financial_year_to' => $validatedData['financial_year_to'],
                    'description' => $validatedData['description'],
                    'is_active' => $validatedData['is_active'],
                ]);
            } else {
                // If no fiscal year exists, create a new one (optional)
                CompanyFiscalYear::create([
                    'company_id' => $company->id,
                    'financial_year_from' => $validatedData['financial_year_from'],
                    'financial_year_to' => $validatedData['financial_year_to'],
                    'description' => $validatedData['description'],
                    'is_active' => $validatedData['is_active'],
                ]);
            }

            Log::info('Fiscal year record updated successfully');

            DB::commit();
            Log::info('Database transaction committed successfully');

            return redirect()->route('list_company_details')
                ->with('success', 'Company details updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['company_logo'])
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('Error during company update', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['company_logo'])
            ]);

            DB::rollBack();
            Log::info('Database transaction rolled back');

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating company details: ' . $e->getMessage());
        }
    }

    public function deleteCompanySetting($id)
    {
        Log::info('Starting company deletion process', ['company_id' => $id]);

        try {
            // Find the company record
            $company = Company::findOrFail($id);

            // Optionally delete the company logo from storage
            if ($company->company_logo) {
                Storage::disk('public')->delete($company->company_logo);
                Log::info('Company logo deleted from storage', ['path' => $company->company_logo]);
            }

            // Delete associated fiscal years
            $company->fiscalYears()->delete();
            Log::info('Associated fiscal years deleted for company', ['company_id' => $id]);

            // Delete the company record
            $company->delete();
            Log::info('Company record deleted successfully', ['company_id' => $id]);

            return redirect()->route('list_company_details')
                ->with('success', 'Company details deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error during company deletion', [
                'error_message' => $e->getMessage(),
                'company_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while deleting company details: ' . $e->getMessage());
        }
    }
}
