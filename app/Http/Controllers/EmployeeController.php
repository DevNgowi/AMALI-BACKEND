<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\UtilController;
use App\Models\City;
use App\Models\Country;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmployeeController extends Controller
{
    private const VALIDATION_RULES = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'position_id' => 'required|exists:positions,id',
        'date_of_joining' => 'required|date',
        'dob' => 'required|date',
        'gender_id' => 'required|exists:genders,id',
        // 'salary' => 'required|numeric',
        'city_id' => 'required|exists:cities,id',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:15|unique:employees',
        'email' => 'nullable|string|email|max:255|unique:employees',
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $employeesQuery = Employee::query();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        if($search){
            $employeesQuery->where('name', 'like', "%$search%");
            $employeesQuery->where('email', 'like', "%$search%");
            $employeesQuery->where('phone', 'like', "%$search%");
            $employeesQuery->where('address', 'like', "%$search%");
            $employeesQuery->whereHas('city', function($query) use ($search){
                $query->where('name', 'like', "%$search%");
            });
            $employeesQuery->whereHas('position', callback: function($query) use ($search){
                $query->where('name', 'like', "%$search%");
            });
        }
        $employees = $employeesQuery->paginate($perPage);

        $employeeArray = $employees->map(fn($employee) => self::serializeEmployee($employee));
        $pagination =  UtilController::serializePagination($employees);
        return response()->json(['data' => $employeeArray,'pagination' => $pagination ], ResponseAlias::HTTP_OK);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $positions = Position::select(['id', 'title'])->get()->map(function ($position) {
            return [
                'id' => $position->id,
                'name' => $position->title,
            ];
        });
        $country = Country::select(['id','name'])->get()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
            ];
        });

        $cities = City::select(['id','name'])->get()->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
            ];
        });

        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });

        $permissions = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        });
        return response()->json(array(
            'data' => [
                'positions' => $positions,
                'countries' => $country,
                'cities' => $cities,
                'roles' => $roles,
                'permissions' => $permissions,
            ]
        ),ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

       $response = $this->validateRequest($request, self::VALIDATION_RULES);
       if($response)return $response;

       $employeeData = $this->prepareEmployeeData($request);

       $employee = Employee::create($employeeData);
       return response()->json(['message' => 'Employee created successful', 'employee' => self::serializeEmployee($employee)], ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return   response()->json(['error' => $validator->errors()->all()], ResponseAlias::HTTP_BAD_REQUEST);

        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareEmployeeData(Request $request): array
    {
        return [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'position_id' => $request->input('position_id'),
            'date_of_joining' => $request->input('date_of_joining'),
            'dob' => $request->input('dob'),
            'status' => $request->input('status'),
            'gender_id' => $request->input('gender_id'),
            'salary' => $request->input('salary'),
            'city_id' => $request->input('city_id'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
        ];
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:employees,id',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], ResponseAlias::HTTP_BAD_REQUEST);
        }

        $employee = Employee::find($request->id);
        if(!$employee){
            return response()->json(['error' => 'Employee not found'], ResponseAlias::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => self::serializeEmployee($employee)], ResponseAlias::HTTP_OK);

    }

    /**
     * @param Employee $employee
     * @return array
     */
    public function serializeEmployee(Employee $employee): array
    {
        return [
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position->title,
            'data_of_joining' => $employee->date_of_joining,
            'dob' => $employee->dob,
            'status' => $employee->status,
            'gender' => $employee->gender->name,
            'salary' => $employee->salary,
            'city' => $employee->city->name ?? '',
            'address' => $employee->address,
            'phone' => $employee->phone,
            'email' => $employee->email,
            'username' => $employee->user->username ?? '',
        ];
    }
}
