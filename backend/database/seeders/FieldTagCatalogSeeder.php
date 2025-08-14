<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldTagCatalogSeeder extends Seeder
{
    public function run()
    {
        // Catalogs - danh mục khóa học chung
        DB::table('catalogs')->insert([
            ['name' => 'Programming'],
            ['name' => 'Networking'],
            ['name' => 'Data'],
            ['name' => 'Cloud & DevOps'],
            ['name' => 'Cybersecurity'],
        ]);

        // Tags - ngành lớn trong IT
        DB::table('tags')->insert([
            ['name' => 'Web Development'],       // 1
            ['name' => 'Mobile Development'],    // 2
            ['name' => 'Game Development'],      // 3
            ['name' => 'Data Science & AI'],     // 4
            ['name' => 'Cloud Computing'],       // 5
            ['name' => 'Cybersecurity'],         // 6
            ['name' => 'IT Support & Networking'], // 7
            ['name' => 'DevOps'],                // 8
        ]);

        // Fields - vị trí công việc trong ngành
        DB::table('fields')->insert([
            // Web Development
            ['tag_id' => 1, 'name' => 'Frontend Developer'],
            ['tag_id' => 1, 'name' => 'Backend Developer'],
            ['tag_id' => 1, 'name' => 'Fullstack Developer'],
            ['tag_id' => 1, 'name' => 'Web UI/UX Designer'],

            // Mobile Development
            ['tag_id' => 2, 'name' => 'iOS Developer'],
            ['tag_id' => 2, 'name' => 'Android Developer'],
            ['tag_id' => 2, 'name' => 'Flutter Developer'],
            ['tag_id' => 2, 'name' => 'React Native Developer'],

            // Game Development
            ['tag_id' => 3, 'name' => 'Game Programmer'],
            ['tag_id' => 3, 'name' => 'Game Designer'],
            ['tag_id' => 3, 'name' => '3D Artist'],
            ['tag_id' => 3, 'name' => 'Game Tester'],

            // Data Science & AI
            ['tag_id' => 4, 'name' => 'Data Analyst'],
            ['tag_id' => 4, 'name' => 'Data Engineer'],
            ['tag_id' => 4, 'name' => 'Machine Learning Engineer'],
            ['tag_id' => 4, 'name' => 'AI Researcher'],

            // Cloud Computing 
            ['tag_id' => 5, 'name' => 'Cloud Engineer'],
            ['tag_id' => 5, 'name' => 'Cloud Solutions Architect'],
            ['tag_id' => 5, 'name' => 'Cloud Administrator'],
            ['tag_id' => 5, 'name' => 'AWS Engineer'],
            ['tag_id' => 5, 'name' => 'Azure Engineer'],
            ['tag_id' => 5, 'name' => 'Google Cloud Engineer'],
            ['tag_id' => 5, 'name' => 'Cloud Security Engineer'],
            ['tag_id' => 5, 'name' => 'Multi-Cloud Specialist'],

            // Cybersecurity
            ['tag_id' => 6, 'name' => 'Security Analyst'],
            ['tag_id' => 6, 'name' => 'Penetration Tester'],
            ['tag_id' => 6, 'name' => 'Security Engineer'],
            ['tag_id' => 6, 'name' => 'Incident Responder'],

            // IT Support & Networking
            ['tag_id' => 7, 'name' => 'IT Support Specialist'],
            ['tag_id' => 7, 'name' => 'Network Engineer'],
            ['tag_id' => 7, 'name' => 'System Administrator'],
            ['tag_id' => 7, 'name' => 'Helpdesk Technician'],

            // DevOps
            ['tag_id' => 8, 'name' => 'DevOps Engineer'],
            ['tag_id' => 8, 'name' => 'Site Reliability Engineer'],
            ['tag_id' => 8, 'name' => 'Build & Release Engineer'],
        ]);
    }
}
