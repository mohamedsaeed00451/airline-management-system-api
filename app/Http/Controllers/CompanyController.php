<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCompanyRequest;
use App\Http\Requests\GetCompaniesRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\Company;
use App\Models\Visa;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(GetCompaniesRequest $request)
    {
        $query = Company::query();

        if ($request->search) {
            $query = $query->where('name', 'like', '%' . $request->search . '%');
        }

        $companies = $query->orderByDesc('id')->paginate(PAGINATION_NUMBER);

        return $this->responseMessage(200, true, null, $companies);
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
    public function store(AddCompanyRequest $request)
    {
        try {

            foreach ($request->companies as $company) {
                Company::query()->create([
                    'name' => $company['name']
                ]);
            }

            return $this->responseMessage(200, true, 'تم حفظ البيانات بنجاح', $request->companies);

        } catch (\Exception $exception) {
            return $this->responseMessage(400, false, ['errors' => $exception]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }

    public function reports(ReportRequest $request, $id)
    {
        $company = Company::query()->find($id);

        if (!$company) {
            return $this->responseMessage(400, false, 'company not found');
        }

        if ($request->report_type == 'comprehensive') {
            $selling_price = Visa::query()->where('from_company_id', $id)->orWhere('to_company_id', $id)->sum('selling_price');
            $execution_price = Visa::query()->where('from_company_id', $id)->orWhere('to_company_id', $id)->sum('execution_price');
        } elseif ($request->report_type == 'implement') {
            $selling_price = Visa::query()->Where('from_company_id', $id)->sum('selling_price');
            $execution_price = Visa::query()->Where('from_company_id', $id)->sum('execution_price');
        } elseif ($request->report_type == 'sale') {
            $selling_price = Visa::query()->where('to_company_id', $id)->sum('selling_price');
            $execution_price = Visa::query()->where('to_company_id', $id)->sum('execution_price');
        }

        $totalAmount = $selling_price - $execution_price;

        $visas = Visa::query()->where(function ($query) use ($id, $request) {

            $query->where(function ($query) use ($id, $request) {
                if ($request->report_type == 'comprehensive') {
                    $query->where('from_company_id', $id)->orWhere('to_company_id', $id);
                } elseif ($request->report_type == 'implement') {
                    $query->Where('from_company_id', $id);
                } elseif ($request->report_type == 'sale') {
                    $query->where('to_company_id', $id);
                }
            });

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
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

        })->with('category', 'fromCompany', 'toCompany')->paginate(PAGINATION_NUMBER);

        $data = [
            'totalAmount' => $totalAmount,
            'visas' => $visas
        ];

        return $this->responseMessage(200, true, null, $data);

    }

    public function getCompaniesToList()
    {
        $companies = Company::query()->orderByDesc('id')->get();
        return $this->responseMessage(200, true, null, $companies);
    }
}
