<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bank_accounts')->delete();

        $accounts = [
            [
                'name' => 'حساب محمد حبيب بالجنية CIB',
            ], [
                'name' => 'حساب محمد حبيب بالدولار CIB',
            ], [
                'name' => 'حساب محمد حبيب بالريال CIB',
            ], [
                'name' => 'حساب محمد حبيب البنك الأهلى',
            ], [
                'name' => 'حساب محمد حبيب البريد المصرى',
            ], [
                'name' => 'حساب محمد حبيب بنك مصر',
            ], [
                'name' => 'حساب نورهان الغراوى بالجنية CIB',
            ], [
                'name' => 'كاش 01094447779',
            ], [
                'name' => 'كاش 01142368989',
            ], [
                'name' => 'كاش 01067924032',
            ], [
                'name' => 'كاش 01022897054',
            ], [
                'name' => 'كاش المكتب',
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::query()->create($account);
        }
    }
}
