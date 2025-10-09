<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('module_types')->updateOrInsert(
            ['name' => 'course_syllabus'],
            ['menu_order' => 1, 'description' => 'Đề cương học phần']
        );
    }
}
