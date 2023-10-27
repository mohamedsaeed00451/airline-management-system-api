<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCompanyRequest;
use App\Http\Traits\GeneralTrait;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::query()->paginate(PAGINATION_NUMBER);
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

    public function reports(Request $request, $id)
    {
        $company = Company::query()->find($id);

        if (!$company) {
            return $this->responseMessage(400, false, 'company not found');
        }

        $query = Company::query()->where('id', $id)->with('sellingVisas.category', 'executionVisas.category');

        $data = $query->first();

        return $this->responseMessage(200, true, null, $data);

    }
}
