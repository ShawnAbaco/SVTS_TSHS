<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SanctionSeeder extends Seeder
{
    public function run(): void
    {
        $sanctions = [
            [
                'sanction_consequences' => 'Verbal Warning',
                'sanction_description' => 'A verbal reminder to the student about expected behavior and potential consequences of further violations.',
            ],
            [
                'sanction_consequences' => 'Detention',
                'sanction_description' => 'The student stays after school for a specified period as a consequence for their actions. During detention, they may be required to complete assignments, reflect on their behavior, or engage in constructive activities.',
            ],
            [
                'sanction_consequences' => 'Parent/Guardian Notification',
                'sanction_description' => 'Parents/guardians are informed about the incident and the corresponding consequences to keep them involved and informed in the resolution process.',
            ],
            [
                'sanction_consequences' => 'Referral to Prefect',
                'sanction_description' => 'The case is referred to the school prefect or discipline committee for further review and potential escalation of consequences based on the severity and frequency of the offense.',
            ],
            [
                'sanction_consequences' => 'Restorative Actions',
                'sanction_description' => 'Actions aimed at helping students understand the impact of their behavior and make amends. This may include community service, mediation, conflict resolution, or character development workshops.',
            ],
            [
                'sanction_consequences' => 'Counseling and Intervention',
                'sanction_description' => 'Referral to the guidance and counseling department for additional support and intervention to address underlying issues, develop coping mechanisms, and make positive changes in behavior.',
            ],
            [
                'sanction_consequences' => 'Suspension',
                'sanction_description' => 'Temporary removal of a student from school for a designated period, during which they are not allowed to attend classes or participate in school activities.',
            ],
            [
                'sanction_consequences' => 'Expulsion',
                'sanction_description' => 'Permanent removal of a student from the school due to serious or repeated violations of the school\'s code of conduct. Expulsion results in the student no longer being allowed to attend Tagoloan Senior High School.',
            ]
        ];

        foreach ($sanctions as $sanction) {
            DB::table('tbl_sanction')->insert([
                'sanction_consequences' => $sanction['sanction_consequences'],
                'sanction_description' => $sanction['sanction_description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
