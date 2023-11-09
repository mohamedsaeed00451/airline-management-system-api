<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\Expense;

class ExpenseController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::query()->orderByDesc('id')->paginate(PAGINATION_NUMBER);
        $total_expenses = Expense::query()->sum('price');
        $data = [
            'total_expenses' => $total_expenses,
            'expenses' => $expenses
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
    public function store(ExpenseRequest $request)
    {
        try {

            $expense = Expense::query()->create([
                'name' => $request->name,
                'price' => $request->price,
                'notes' => $request->notes
            ]);

            return $this->responseMessage(201, true, 'تم حفظ البيانات بنجاح', $expense);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseRequest $request, $id)
    {
        try {

            $expense = Expense::query()->find($id);

            if (!$expense) {
                return $this->responseMessage(400, false, 'expense not found');
            }

            $expense->update([
                'name' => $request->name,
                'price' => $request->price,
                'notes' => $request->notes
            ]);

            return $this->responseMessage(201, true, 'تم تحديث البيانات بنجاح', $expense);

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

            $expense = Expense::query()->find($id);

            if (!$expense) {
                return $this->responseMessage(400, false, 'expense not found');
            }

            $expense->delete();

            return $this->responseMessage(200, true, 'تم حذف البيانات بنجاح');

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }
}
