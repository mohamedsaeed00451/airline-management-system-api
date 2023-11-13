<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDepositRequest;
use App\Http\Requests\BankTransactionRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{

    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = BankAccount::all();
        return $this->responseMessage(200, true, null, $banks);
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
    public function store(AddDepositRequest $request)
    {
        try {

            $bank = BankAccount::query()->find($request->bank_id);
            if (!$bank) {
                return $this->responseMessage(400, false, 'bank not found');
            }

            $bank->amount += $request->amount;
            $bank->save();

            return $this->responseMessage(201, true, 'تم إضافة الإيداع بنجاح', ['amount' => intval($bank->amount)]);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BankTransactionRequest $request, $id)
    {
        $bank = BankAccount::query()->find($id);
        if (!$bank) {
            return $this->responseMessage(400, false, 'bank not found');
        }

        $query = BankTransaction::query()->where('bank_id', $id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->get();

        $data = [
            'bank' => $bank,
            'transactions' => $transactions
        ];

        return $this->responseMessage(200, true, null, $data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddDepositRequest $request, $id)
    {
        try {

            $bank = BankAccount::query()->find($id);
            if (!$bank) {
                return $this->responseMessage(400, false, 'bank not found');
            }

            $bank->amount = $request->amount;
            $bank->save();

            return $this->responseMessage(201, true, 'تم تحديث الإيداع بنجاح', ['amount' => intval($bank->amount)]);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
    }
}
