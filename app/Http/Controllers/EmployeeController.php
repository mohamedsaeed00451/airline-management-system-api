<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\Employee;
use App\Models\EmployeeCommission;
use function PHPUnit\Framework\isNull;

class EmployeeController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::query()->orderByDesc('id')->paginate(PAGINATION_NUMBER);

        if (!$employees) {
            return $this->responseMessage(204, false, 'لا يوجد موظفين');
        }

        $employees_total_salary = Employee::query()->sum('salary');
        $employees_total_commission = EmployeeCommission::query()->whereMonth('created_at', date('m'))->sum('commission');
        $employees_total_salary_commission = $employees_total_salary + $employees_total_commission;

        foreach ($employees as $employee) {

            $commission = $employee->commissions()->whereMonth('created_at', date('m'))->first();
            if (!$commission) {
                $employee->commission = 0;
                $employee->commission_id = null;
            } else {
                $employee->commission = $commission->commission;
                $employee->commission_id = $commission->id;
            }
            $employee->total_salary = $employee->commission + $employee->salary;
        }

        $data = [
            'employees_total_salary' => $employees_total_salary,
            'employees_total_commission' => $employees_total_commission,
            'employees_total_salary_commission' => $employees_total_salary_commission,
            'employees' => $employees
        ];

        return $this->responseMessage(200, true, null, $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        try {

            $employee = Employee::query()->create([
                'name' => $request->name,
                'salary' => $request->salary
            ]);

            return $this->responseMessage(201, true, 'تم حفظ البيانات بنجاح', $employee);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, $id)
    {
        try {

            $employee = Employee::query()->find($id);

            if (!$employee) {
                return $this->responseMessage(400, false, 'employee not found');
            }

            $employee->update([
                'name' => $request->name,
                'salary' => $request->salary
            ]);

            return $this->responseMessage(201, true, 'تم تحديث البيانات بنجاح', $employee);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $employee = Employee::query()->find($id);

            if (!$employee) {
                return $this->responseMessage(400, false, 'employee not found');
            }

            $employee->delete();

            return $this->responseMessage(200, true, 'تم حذف البيانات بنجاح');

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }
}
