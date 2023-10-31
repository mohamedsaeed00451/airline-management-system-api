<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDepositRequest;
use App\Http\Requests\GetVisaRequest;
use App\Http\Requests\VisaRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Company;
use App\Models\PlatformDeposit;
use App\Models\Visa;

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

            return $this->responseMessage(201, true, 'تم حفظ البيانات بنجاح', $visa);

        } catch (\Exception $exception) {
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

                if ($visa->execution_price != $request->execution_price) {

                    $platformDeposit->amount += $visa->execution_price;
                    $platformDeposit->save();

                    if ($platformDeposit->amount < $request->execution_price) {
                        return $this->responseMessage(400, false, 'يجب الإيداع على المنصة لإستكمال الطلب');
                    } else {
                        $platformDeposit->amount -= $request->execution_price;
                        $platformDeposit->save();
                    }

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

            return $this->responseMessage(201, true, 'تم تحديث البيانات بنجاح', $visa);

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

            $visa = Visa::query()->find($id);
            if (!$visa) {
                return $this->responseMessage(400, false, 'visa not found');
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
        return $this->responseMessage(200, true, null, $amount);
    }
}
