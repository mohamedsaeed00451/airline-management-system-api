<!doctype html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Reports PDF</title>

    <style>

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td, h2 {
            text-align: right;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .flex-container {
            display: flex;
            flex-direction: row-reverse;
        }

        .flex-container div {
            margin-left: 10px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: right
        }

    </style>
</head>
<body>


<h2 style="text-align: center">المَــــلك للســـياحة</h2>
<p style="text-align: right;padding: 0;margin: 0">الشركة : {{ $company_name }} </p>
<p style="text-align: right;padding: 0;margin: 0">تاريخ الإصدار : {{ date('d/m/Y') }}</p>


{{------------------------------------------------- Comprehensive -------------------------------------------------}}


@if($type_report == 'comprehensive')

    <p style="text-align: right;padding: 0;margin: 0">نوع التقرير : شامل</p>

    <hr style="text-align: center;text-size-adjust: auto;margin-bottom: 3px">

    <table>
        <tr>
            <th><p><span> إجمالى المكسب</span>
                    : {{ $total_selling_price - $total_execution_price }}ج</p></th>
            <th><p><span> إجمالى سعر التنفيذ</span>
                    : {{ $total_execution_price }}ج</p></th>
            <th><p><span> إجمالى سعر البيع</span>
                    : {{ $total_selling_price }}ج</p></th>
        </tr>
    </table>

    @forelse($filal_data as $filal)
        <hr style="text-align: center;text-size-adjust: auto;margin-bottom: 3px">
        <h3> -: {{ $filal['type'] }}</h3>
        <table>
            <tr>
                <th>الملاحظات</th>
                <th>التاريخ</th>
                <th>التحويل</th>
                <th>الإيداع</th>
                <th>إلى شركة</th>
                <th>من شركة</th>
                <th>سعر التنفيذ</th>
                <th>سعر البيع</th>
            </tr>
            @php
                $selling_price = 0;
                $execution_price = 0;
            @endphp
            @forelse($filal['data'] as $dat)
                @php
                    $selling_price += $dat['selling_price'];
                    $execution_price += $dat['execution_price'];
                @endphp
                <tr>
                    <td>{{ $dat['notes'] }}</td>
                    <td>{{ $dat['created_at'] }}</td>
                    <td>{{ $dat['is_transfer'] == 0 ? 'لم يتم' : 'تم' }}</td>
                    <td>{{ $dat['is_deposit'] == 0 ? 'لم يتم' : 'تم' }}</td>
                    <td>{{ $dat['toCompany']['name'] }}</td>
                    <td>{{ $dat['fromCompany']['name'] }}</td>
                    <td>{{ $dat['execution_price'] }}</td>
                    <td>{{ $dat['selling_price'] }}</td>
                </tr>
            @empty
                <h3 style="text-align: center;">لا يوجد بيانات</h3>
            @endforelse

        </table>
        <br>
        <table>
            <tr>
                <th><p style="color: blue;"><span style="color: red;">المكسب</span>
                        : {{ $selling_price - $execution_price }}ج</p></th>
                <th><p style="color: blue;"><span style="color: red;">إجمالى سعر التنفيذ</span>
                        : {{ $execution_price }}ج</p></th>
                <th><p style="color: blue;"><span style="color: red;">إجمالى سعر البيع</span>
                        : {{ $selling_price }}ج</p></th>
            </tr>
        </table>
    @empty
        <h3 style="text-align: center;">لا يوجد بيانات</h3>
    @endforelse

@endif


{{----------------------------------------------- Implement ---------------------------------------------------}}


@if($type_report == 'implement')

    <p style="text-align: right;padding: 0;margin: 0">نوع التقرير : تنفيذ</p>

    <hr style="text-align: center;text-size-adjust: auto;margin-bottom: 3px">

    <table>
        <tr>
            <th><p><span> إجمالى سعر التنفيذ</span>
                    : {{ $total_execution_price }}ج</p></th>
        </tr>
    </table>

    @forelse($filal_data as $filal)
        <hr style="text-align: center;text-size-adjust: auto;margin-bottom: 3px">
        <h3> -: {{ $filal['type'] }}</h3>
        <table>
            <tr>
                <th>التاريخ</th>
                <th>التحويل</th>
                <th>الشركة</th>
                <th>السعر</th>
            </tr>
            @php
                $execution_price = 0;
            @endphp
            @forelse($filal['data'] as $dat)
                @php
                    $execution_price += $dat['execution_price'];
                @endphp
                <tr>
                    <td>{{ $dat['created_at'] }}</td>
                    <td>{{ $dat['is_transfer'] == 0 ? 'لم يتم' : 'تم' }}</td>
                    <td>{{ $dat['toCompany']['name'] }}</td>
                    <td>{{ $dat['execution_price'] }}</td>
                </tr>
            @empty
                <h3 style="text-align: center;">لا يوجد بيانات</h3>
            @endforelse


        </table>
        <div class="flex-container">
            <h4 style="color: blue;margin-left: 20px;"><span style="color: red;">إجمالى سعر التنفيذ</span>
                : {{ $execution_price }}ج</h4>
        </div>
    @empty
        <h3 style="text-align: center;">لا يوجد بيانات</h3>
    @endforelse

@endif


{{------------------------------------------------- Sale -------------------------------------------------}}


@if($type_report == 'sale')

    <p style="text-align: right;padding: 0;margin: 0">نوع التقرير : بيع</p>

    <hr style="text-align: center;text-size-adjust: auto;margin-bottom: 3px">

    <table>
        <tr>
            <th><p><span> إجمالى سعر البيع</span>
                    : {{ $total_selling_price }}ج</p></th>
        </tr>
    </table>

    @forelse($filal_data as $filal)
        <hr style="text-align: center;text-size-adjust: auto;margin-bottom: 3px">
        <h3> -: {{ $filal['type'] }}</h3>
        <table>
            <tr>
                <th>التاريخ</th>
                <th>الإيداع</th>
                <th>الشركة</th>
                <th>السعر</th>
            </tr>
            @php
                $selling_price = 0;
            @endphp
            @forelse($filal['data'] as $dat)
                @php
                    $selling_price += $dat['selling_price'];
                @endphp
                <tr>
                    <td>{{ $dat['created_at'] }}</td>
                    <td>{{ $dat['is_deposit'] == 0 ? 'لم يتم' : 'تم' }}</td>
                    <td>{{ $dat['fromCompany']['name'] }}</td>
                    <td>{{ $dat['selling_price'] }}</td>
                </tr>
            @empty
                <h3 style="text-align: center;">لا يوجد بيانات</h3>
            @endforelse


        </table>
        <div class="flex-container">
            <h4 style="color: blue;margin-left: 20px;"><span style="color: red;">إجمالى سعر البيع</span>
                : {{ $selling_price }}ج</h4>
        </div>
    @empty
        <h3 style="text-align: center;">لا يوجد بيانات</h3>
    @endforelse

@endif

</body>
</html>



