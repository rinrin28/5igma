<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Survey;
use App\Models\User;
use App\Mail\SurveyInvitationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [2023, 2024];
        $baseDates = [
            [
                'start_date' => '04-01',
                'end_date' => '04-14',
            ],
            [
                'start_date' => '10-01',
                'end_date' => '10-14',
            ],
        ];

        $departments = Department::all();

        // 2023-2024年のデータを作成
        foreach ($departments as $department) {
            foreach ($years as $year) {
                foreach ($baseDates as $base) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $base['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $base['end_date']);

                    Survey::create([
                        'survey_types_id' => 1,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'department_id' => $department->id,
                    ]);
                }
            }
        }

        // 2025年上半期のデータを追加
        foreach ($departments as $department) {
            $startDate = Carbon::createFromFormat('Y-m-d', '2025-04-01');
            $endDate = Carbon::createFromFormat('Y-m-d', '2025-04-14');

            Survey::create([
                'survey_types_id' => 1,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'department_id' => $department->id,
            ]);
        }

        $today = Carbon::today();
        $surveys = Survey::where('survey_types_id', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        // foreach ($surveys as $survey) {
        //     // 全ユーザーを取得（部署による絞り込みなし）
        //     $recipients = User::whereIn('role', ['employee', 'management'])
        //         ->get();

        //     // 各ユーザーにメールを送信
        //     foreach ($recipients as $recipient) {
        //         $surveyUrl = route('survey.start', $survey->id);

        //         Mail::to($recipient->email)->send(new SurveyInvitationMail(
        //             $surveyUrl,
        //             $recipient,
        //             $survey->start_date,
        //             $survey->end_date
        //         ));
        //     }
        // }
                // パルスサーベイ（survey_types_id = 2）を半期ごとに3回繰り返して作成
foreach ($departments as $department) {
    $baseDates = [
        ['year' => 2023, 'start_date' => '04-15'], // 2023年上半期
        ['year' => 2023, 'start_date' => '10-15'], // 2023年下半期
        ['year' => 2024, 'start_date' => '04-15'], // 2024年上半期
        ['year' => 2024, 'start_date' => '10-15'], // 2024年下半期
        ['year' => 2025, 'start_date' => '04-15'], // 2025年上半期
    ];

    foreach ($baseDates as $base) {
        $baseStartDate = Carbon::createFromFormat('Y-m-d', $base['year'] . '-' . $base['start_date']);

        // 半期ごとに3回のパルスサーベイを作成
        for ($iteration = 0; $iteration < 3; $iteration++) {
            $offsetDays = $iteration * 30; // 各回で開始日を30日ずつずらす
            $pulseStartDate = $baseStartDate->copy()->addDays($offsetDays);
            $pulseEndDate = $pulseStartDate->copy()->addDays(13); // 2週間後に終了

            Survey::create([
                'survey_types_id' => 2,
                'start_date' => $pulseStartDate,
                'end_date' => $pulseEndDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'department_id' => $department->id,
            ]);
        }
    }
}
    }
}
