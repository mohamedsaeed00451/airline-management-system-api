<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDepositRequest;
use App\Http\Requests\GetVisaRequest;
use App\Http\Requests\VisaRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Category;
use App\Models\Company;
use App\Models\PlatformDeposit;
use App\Models\Visa;
use Illuminate\Support\Facades\DB;

class VisaController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(GetVisaRequest $request)
    {
        $category = Category::query()->find($request->category_id);
        if (!$category) {
            return $this->responseMessage(400, false, 'category not found');
        }

        $visas = Visa::query()->where('category_id', $request->category_id)->where(function ($query) use ($request) {

            if ($request->search) {
                $companyIds = Company::query()->where('name', 'like', '%' . $request->search . '%')->pluck('id')->toArray();
                $query->where(function ($query) use ($companyIds, $request) {
                    $query->whereIn('from_company_id', $companyIds)->orWhereIn('to_company_id', $companyIds);
                });
            }

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            if ($request->is_deposit) {
                $query->where('is_deposit', $request->is_deposit);
            }

            if ($request->is_transfer) {
                $query->where('is_transfer', $request->is_transfer);
            }

        })->with('fromCompany', 'toCompany')->paginate(PAGINATION_NUMBER);

        $data = [
            'category' => $category,
            'visas' => $visas
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
    public function store(VisaRequest $request)
    {
        try {

            DB::beginTransaction();

            $category = Category::query()->find($request->category_id);
            if (!$category) {
                return $this->responseMessage(400, false, 'category not found');
            }

            if ($category->name == 'باركود شخصى') {

                $platformDeposit = PlatformDeposit::query()->first();

                if (!$platformDeposit) {
                    return $this->responseMessage(400, false, 'يجب الإيداع على المنصة لإستكمال الطلب');
                }

                if ($platformDeposit->amount < $request->execution_price) {
                    return $this->responseMessage(400, false, 'يجب الإيداع على المنصة لإستكمال الطلب');
                }

                $platformDeposit->amount -= $request->execution_price;
                $platformDeposit->save();

            }

            $visa = Visa::query()->create([
                'selling_price' => $request->selling_price,
                'execution_price' => $request->execution_price,
                'category_id' => $request->category_id,
                'from_company_id' => $request->from_company_id,
                'to_company_id' => $request->to_company_id,
                'is_deposit' => $request->is_deposit,
                'is_transfer' => $request->is_transfer,
                'notes' => $request->notes,
            ]);


            if ($request->is_deposit == 1) {

                if ($request->to_bank_id) {

                    $bank_deposit = BankAccount::query()->find($request->to_bank_id);
                    $bank_deposit->amount += $request->selling_price;
                    $bank_deposit->save();

                    BankTransaction::query()->create([
                        'bank_id' => $bank_deposit->id,
                        'amount' => $request->selling_price,
                        'type' => 'DEPOSIT',
                        'visa_id' => $visa->id
                    ]);
                }

            }

            if ($request->is_transfer == 1) {

                if ($request->from_bank_id) {

                    $bank_transfer = BankAccount::query()->find($request->from_bank_id);
                    if ($bank_transfer->amount < $request->execution_price) {
                        return $this->responseMessage(400, false, 'لا يوجد مبلغ كافى لاكمال التحويل');
                    }

                    $bank_transfer->amount -= $request->execution_price;
                    $bank_transfer->save();

                    BankTransaction::query()->create([
                        'bank_id' => $bank_transfer->id,
                        'amount' => $request->execution_price,
                        'type' => 'TRANSFER',
                        'visa_id' => $visa->id
                    ]);
                }
            }

            DB::commit();

            return $this->responseMessage(201, true, 'تم حفظ البيانات بنجاح', $visa);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Visa $visa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visa $visa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VisaRequest $request, $id)
    {
        try {

            DB::beginTransaction();

            $visa = Visa::query()->find($id);
            if (!$visa) {
                return $this->responseMessage(400, false, 'visa not found');
            }

            if ($request->is_deposit == 1) {

                if ($request->to_bank_id) {

                    $bank_deposit_transaction = BankTransaction::query()->where('visa_id',$id)
                        ->where('type','DEPOSIT')
                        ->first();

                    if (!$bank_deposit_transaction) {

                        $bank_deposit = BankAccount::query()->find($request->to_bank_id);
                        $bank_deposit->amount += $request->selling_price;
                        $bank_deposit->save();

                        BankTransaction::query()->create([
                            'bank_id' => $bank_deposit->id,
                            'amount' => $request->selling_price,
                            'type' => 'DEPOSIT',
                            'visa_id' => $visa->id
                        ]);

                    } else {

                        $old_bank_deposit = BankAccount::query()->find($bank_deposit_transaction->bank_id);
                        $old_bank_deposit->amount -= $visa->selling_price;
                        $old_bank_deposit->save();

                        $new_bank_deposit = BankAccount::query()->find($request->to_bank_id);
                        $new_bank_deposit->amount += $request->selling_price;
                        $new_bank_deposit->save();

                        $bank_deposit_transaction->amount = $request->selling_price;
                        $bank_deposit_transaction->bank_id = $request->to_bank_id;
                        $bank_deposit_transaction->save();

                    }

                }

            } else {

                $bank_deposit_transaction = BankTransaction::query()->where('visa_id',$id)
                    ->where('type','DEPOSIT')
                    ->first();

                if ($bank_deposit_transaction) {

                    $old_bank_deposit = BankAccount::query()->find($bank_deposit_transaction->bank_id);
                    $old_bank_deposit->amount -= $visa->selling_price;
                    $old_bank_deposit->save();

                    $bank_deposit_transaction->delete();

                }

            }


            if ($request->is_transfer == 1) {

                if ($request->from_bank_id) {

                    $bank_transfer_transaction = BankTransaction::query()->where('visa_id',$id)
                        ->where('type','TRANSFER')
                        ->first();

                    if (!$bank_transfer_transaction) {

                        $bank_transfer = BankAccount::query()->find($request->from_bank_id);
                        if ($bank_transfer->amount < $request->execution_price) {
                            return $this->responseMessage(400, false, 'لا يوجد مبلغ كافى لاكمال التحويل');
                        }

                        $bank_transfer->amount -= $request->execution_price;
                        $bank_transfer->save();

                        BankTransaction::query()->create([
                            'bank_id' => $bank_transfer->id,
                            'amount' => $request->execution_price,
                            'type' => 'TRANSFER',
                            'visa_id' => $visa->id
                        ]);

                    } else {

                        $old_bank_transfer = BankAccount::query()->find($bank_transfer_transaction->bank_id);
                        $old_bank_transfer->amount += $visa->execution_price;
                        $old_bank_transfer->save();

                        $new_bank_transfer = BankAccount::query()->find($request->from_bank_id);
                        if ($new_bank_transfer->amount < $request->execution_price) {
                            return $this->responseMessage(400, false, 'لا يوجد مبلغ كافى لاكمال التحويل');
                        }
                        $new_bank_transfer->amount -= $request->execution_price;
                        $new_bank_transfer->save();

                        $bank_transfer_transaction->amount = $request->execution_price;
                        $bank_transfer_transaction->bank_id = $request->from_bank_id;
                        $bank_transfer_transaction->save();

                    }

                }

            } else {

                $bank_transfer_transaction = BankTransaction::query()->where('visa_id',$id)
                    ->where('type','TRANSFER')
                    ->first();

                if ($bank_transfer_transaction) {

                    $old_bank_transfer = BankAccount::query()->find($bank_transfer_transaction->bank_id);
                    $old_bank_transfer->amount += $visa->execution_price;
                    $old_bank_transfer->save();

                    $bank_transfer_transaction->delete();

                }

            }

            $visa->update([
                'selling_price' => $request->selling_price,
                'execution_price' => $request->execution_price,
                'from_company_id' => $request->from_company_id,
                'to_company_id' => $request->to_company_id,
                'is_deposit' => $request->is_deposit,
                'is_transfer' => $request->is_transfer,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return $this->responseMessage(201, true, 'تم تحديث البيانات بنجاح', $visa);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $visa = Visa::query()->find($id);

            if (!$visa) {
                return $this->responseMessage(400, false, 'visa not found');
            }

            $category = Category::query()->find($visa->category_id);

            if (!$category) {
                return $this->responseMessage(400, false, 'category not found');
            }

            if ($category->name == 'باركود شخصى') {

                $platformDeposit = PlatformDeposit::query()->first();
                $platformDeposit->amount += $visa->execution_price;
                $platformDeposit->save();

            }

            $bank_deposit_transaction = BankTransaction::query()->where('visa_id',$id)
                ->where('type','DEPOSIT')
                ->first();

            if ($bank_deposit_transaction) {
                $bank_deposit = BankAccount::query()->find($bank_deposit_transaction->bank_id);
                $bank_deposit->amount -= $visa->selling_price;
                $bank_deposit->save();

                $bank_deposit_transaction->delete();
            }

            $bank_transfer_transaction = BankTransaction::query()->where('visa_id',$id)
                ->where('type','TRANSFER')
                ->first();
            if ($bank_transfer_transaction) {
                $bank_transfer = BankAccount::query()->find($bank_transfer_transaction->bank_id);
                $bank_transfer->amount += $visa->execution_price;
                $bank_transfer->save();

                $bank_transfer_transaction->delete();
            }

            $visa->delete();

            return $this->responseMessage(200, true, 'تم حذف البيانات بنجاح');

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    public function depositToPlatform(AddDepositRequest $request)
    {
        try {

            $getAmount = PlatformDeposit::query()->first();

            if (!$getAmount) {
                PlatformDeposit::query()->create([
                    'amount' => $request->amount
                ]);
                $amount = $request->amount;
            } else {
                $amount = $request->amount + $getAmount->amount;
                $getAmount->amount += $request->amount;
                $getAmount->save();
            }

            return $this->responseMessage(201, true, 'تم حفظ البيانات بنجاح', ['amount' => intval($amount)]);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    public function getPlatformAmount()
    {
        $amount = PlatformDeposit::query()->first();
        if (!$amount) {
            $amount = [
                'amount' => 0
            ];
        }
        return $this->responseMessage(200, true, null, $amount);
    }

    public function updatePlatformAmount(AddDepositRequest $request)
    {
        try {

            $getAmount = PlatformDeposit::query()->first();

            if (!$getAmount) {
                PlatformDeposit::query()->create([
                    'amount' => $request->amount
                ]);
            } else {
                $getAmount->amount = $request->amount;
                $getAmount->save();
            }

            return $this->responseMessage(201, true, 'تم تحديث البيانات بنجاح', ['amount' => intval($request->amount)]);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

}
