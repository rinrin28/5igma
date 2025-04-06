<?php

namespace Database\Seeders;

use App\Models\SurveyTypes;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SurveyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surveyTypes = [
            [
                'title'       => '全社組織改善アンケート',
                'description' => '全社組織改善アンケートです。',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'パルスサーベイ',
                'description' => 'パルスサーベイです。',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]
        ];

        foreach ($surveyTypes as $surveyType) {
            SurveyTypes::create($surveyType);
        }
    }

}
