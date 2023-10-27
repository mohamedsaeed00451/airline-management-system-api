<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->delete();

        $categories = [
            [
                'name' => 'حجز الطيران',
                'icon' => url('icons/7.png')
            ], [
                'name' => 'تأشيرة عمره',
                'icon' => url('icons/6.png')
            ], [
                'name' => 'تأشيرة الإمارات',
                'icon' => url('icons/5.png')
            ], [
                'name' => 'تأشيرة مسقط',
                'icon' => url('icons/10.png')
            ], [
                'name' => 'تأشيرة البحرين',
                'icon' => url('icons/9.png')
            ], [
                'name' => 'تأشيرات متنوعة',
                'icon' => url('icons/1.png')
            ], [
                'name' => 'تأشيرة شخصية',
                'icon' => url('icons/6.png')
            ], [
                'name' => 'باركود عمره',
                'icon' => url('icons/6.png')
            ], [
                'name' => 'باركود شخصى',
                'icon' => url('icons/8.png')
            ], [
                'name' => 'تساهيل',
                'icon' => url('icons/3.png')
            ], [
                'name' => 'إنجاز',
                'icon' => url('icons/1.png')
            ], [
                'name' => 'برامج عمره',
                'icon' => url('icons/6.png')
            ], [
                'name' => 'حجوزات فنادق',
                'icon' => url('icons/4.png')
            ], [
                'name' => 'السياحة الداخلية',
                'icon' => url('icons/2.png')
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}
