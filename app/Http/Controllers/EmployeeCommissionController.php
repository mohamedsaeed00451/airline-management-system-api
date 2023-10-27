<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommissionRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\Employee;
use App\Models\EmployeeCommission;

class EmployeeCommissionController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(CommissionRequest $request)
    {
        try {

            $employee = Employee::query()->find($request->employee_id);

            if (!$employee) {
                return $this->responseMessage(400, false, 'employee not found');
            }

            $employeeCommissionInMonth = $employee->commissions()->whereMonth('created_at', date('m'))->first();

            if ($employeeCommissionInMonth) {
                return $this->responseMessage(400, false, 'تم وضع عمولة الشهر لهذا الموظف من قبل', ['commission' => $employeeCommissionInMonth->commission]);
            }

            $commission = EmployeeCommission::query()->create([
                'commission' => $request->commission,
                'employee_id' => $request->employee_id
            ]);

            return $this->responseMessage(201, true, 'تم حفظ البيانات بنجاح', $commission);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommissionRequest $request, $id)
    {
        try {

            $commission = EmployeeCommission::query()->find($id);

            if (!$commission) {
                return $this->responseMessage(400, false, 'commission not found');
            }

            $commission->update([
                'commission' => $request->commission,
            ]);

            return $this->responseMessage(201, true, 'تم تحديث البيانات بنجاح', $commission);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
