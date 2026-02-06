<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Carbon\Carbon;

class ViolationAndComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $violation_incidents = [
            'Arrived 15 minutes late to Mathematics class without valid excuse',
            'Wore colored shirt instead of prescribed white uniform with school logo',
            'Consistently talking and distracting classmates during Science lesson',
            'Failed to submit History project deadline for 3 consecutive days',
            'Came to class without required textbooks and learning materials',
            'Using mobile phone during examination period',
            'Eating snacks inside the classroom during instructional time',
            'Sleeping during English literature discussion',
            'Wearing unauthorized footwear with school uniform',
            'Drawing graffiti on classroom desk with permanent marker'
        ];

        $complaint_incidents = [
            'Pushing and name-calling in the cafeteria during lunch break',
            'Heated argument over borrowed calculator that was not returned',
            'Copying answers from seatmate during quarterly Mathematics exam',
            'Dispute over basketball court usage during physical education class',
            'Intentionally damaging classmate\'s scientific calculator',
            'Spreading false rumors about student through social media',
            'Stealing lunch money from younger student\'s backpack',
            'Cyberbullying through anonymous messaging app',
            'Physical altercation in school hallway between classes',
            'Vandalizing library books by tearing out important pages'
        ];

        $students = Student::with(['adviser', 'parent'])->get();

        if ($students->isEmpty()) {
            throw new \Exception('No students found. Please run Student seeder first.');
        }

        // Get all offenses from tbl_offense
        $allOffenses = DB::table('tbl_offense')->get();
        if ($allOffenses->isEmpty()) {
            throw new \Exception('No offenses found in tbl_offense');
        }

        // Categorize offenses
        $tardinessOffenses = [];
        $uniformOffenses = [];
        $disruptiveOffenses = [];
        $academicOffenses = [];
        $bullyingOffenses = [];
        $cheatingOffenses = [];
        $theftOffenses = [];
        $vandalismOffenses = [];
        $otherOffenses = [];

        foreach ($allOffenses as $offense) {
            $type = strtolower($offense->offense_type);

            if (str_contains($type, 'tardiness') || str_contains($type, 'late')) {
                $tardinessOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'dress') || str_contains($type, 'uniform')) {
                $uniformOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'disruptive') || str_contains($type, 'disturbance')) {
                $disruptiveOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'homework') || str_contains($type, 'academic')) {
                $academicOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'bullying') || str_contains($type, 'harassment')) {
                $bullyingOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'cheating') || str_contains($type, 'plagiarism')) {
                $cheatingOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'theft') || str_contains($type, 'stealing')) {
                $theftOffenses[] = $offense->offense_id;
            } elseif (str_contains($type, 'vandalism') || str_contains($type, 'damage')) {
                $vandalismOffenses[] = $offense->offense_id;
            } else {
                $otherOffenses[] = $offense->offense_id;
            }
        }

        $allOffenseIds = $allOffenses->pluck('offense_id')->toArray();

        // Get sanctions from tbl_sanction - ONLY Verbal Warning and Parent/Guardian Notification
        $allowedSanctions = DB::table('tbl_sanction')
            ->whereIn('sanction_consequences', ['Verbal Warning', 'Parent/Guardian Notification'])
            ->pluck('sanction_id')
            ->toArray();

        if (empty($allowedSanctions)) {
            throw new \Exception('No allowed sanctions found. Please ensure "Verbal Warning" and "Parent/Guardian Notification" exist in tbl_sanction.');
        }

        // Get prefect ID
        $prefect = DB::table('tbl_prefect_of_discipline')->first();
        if (!$prefect) {
            throw new \Exception('No prefect found. Please run Prefect seeder first.');
        }
        $prefectId = $prefect->prefect_id;

        $violationCount = 0;
        $complaintCount = 0;

        // Divide students into adviser groups
        $adviserGroups = $students->groupBy('adviser_id');

        foreach ($adviserGroups as $adviserId => $groupStudents) {

            $studentIds = $groupStudents->pluck('student_id')->toArray();
            $studentDetails = $groupStudents->keyBy('student_id');

            // --------------------
            // VIOLATIONS (Minor offenses - typically handled by adviser)
            // --------------------
            foreach ($studentIds as $index => $studentId) {
                $student = $studentDetails[$studentId];
                $violationCount++;

                // Determine violation type based on student index for variety
                $violationType = $index % 4;
                switch ($violationType) {
                    case 0: // Tardiness
                        $offenseId = $this->getRandomOffenseId($tardinessOffenses, $allOffenseIds);
                        $incident = $violation_incidents[0];
                        $status = 'resolved';
                        $handledBy = 'adviser';
                        break;
                    case 1: // Uniform violation
                        $offenseId = $this->getRandomOffenseId($uniformOffenses, $allOffenseIds);
                        $incident = $violation_incidents[1];
                        $status = rand(0, 1) ? 'pending' : 'resolved';
                        $handledBy = 'adviser';
                        break;
                    case 2: // Disruptive behavior
                        $offenseId = $this->getRandomOffenseId($disruptiveOffenses, $allOffenseIds);
                        $incident = $violation_incidents[2];
                        $status = 'in_progress';
                        $handledBy = rand(0, 1) ? 'adviser' : 'prefect';
                        break;
                    case 3: // Academic violation
                        $offenseId = $this->getRandomOffenseId($academicOffenses, $allOffenseIds);
                        $incident = $violation_incidents[3];
                        $status = 'resolved';
                        $handledBy = 'adviser';
                        break;
                }

                $violationDate = Carbon::now()->subDays(rand(1, 30));
                $violationTime = $this->generateSchoolHoursTime();

                // Select only from allowed sanctions
                $sanctionId = $allowedSanctions[array_rand($allowedSanctions)];

                // Generate data for new columns
                $witnesses = rand(0, 1) ? $this->generateWitnesses($studentDetails, $studentId) : null;
                $complainant = rand(0, 3) === 0 ? $this->generateComplainant($studentDetails, $studentId) : null; // 25% chance
                $evidenceDescription = rand(0, 4) === 0 ? $this->generateEvidenceDescription($incident) : null; // 20% chance
                $evidenceFiles = $evidenceDescription ? $this->generateEvidenceFiles() : null;

                // Generate sanction timing - simplified for minor sanctions
                $sanctionData = $this->generateSanctionTiming($violationDate, $status);
                $sanctionStartAt = $sanctionData['start'];
                $sanctionEndAt = $sanctionData['end'];
                $sanctionStatus = $sanctionData['status'];

                $violation_id = DB::table('tbl_violation_record')->insertGetId([
                    'violator_id' => $studentId,
                    'prefect_id' => $prefectId,
                    'offense_id' => $offenseId,
                    'sanction_id' => $sanctionId,
                    'violation_incident' => $incident,
                    'violation_date' => $violationDate->toDateString(),
                    'violation_time' => $violationTime,
                    'status' => $status,
                    'handled_by' => $handledBy,
                    'escalated_at' => $handledBy === 'prefect' ? $violationDate->copy()->addHours(rand(1, 24)) : null,
                    'witnesses' => $witnesses,
                    'complainant' => $complainant,
                    'evidence_description' => $evidenceDescription,
                    'evidence_files' => $evidenceFiles ? json_encode($evidenceFiles) : null,
                    'sanction_start_at' => $sanctionStartAt,
                    'sanction_end_at' => $sanctionEndAt,
                    'sanction_status' => $sanctionStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // VIOLATION APPOINTMENT - Only for pending and under_review cases
                if (in_array($status, ['pending', 'in_progress'])) {
                    $appointmentDate = $violationDate->copy()->addDays(rand(1, 7));
                    DB::table('tbl_violation_appointment')->insert([
                        'violation_id' => $violation_id,
                        'handled_by' => $handledBy,
                        'violation_app_date' => $appointmentDate->toDateString(),
                        'violation_app_time' => $this->generateOfficeHoursTime(),
                        'violation_app_notes' => $this->getViolationAppointmentNotes($incident, $student->student_fname),
                        'violation_app_status' => $status === 'pending' ? 'Scheduled' : 'Rescheduled',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // VIOLATION ANECDOTAL - Only for resolved cases
                if ($status === 'resolved') {
                    DB::table('tbl_violation_anecdotal')->insert([
                        'violation_id' => $violation_id,
                        'handled_by' => $handledBy,
                        'violation_anec_solution' => $this->getViolationSolution($incident, $student->student_fname),
                        'violation_anec_recommendation' => $this->getViolationRecommendation($incident),
                        'violation_anec_date' => $violationDate->copy()->addDays(rand(1, 3))->toDateString(),
                        'violation_anec_time' => $this->generateOfficeHoursTime(),
                        'status' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // --------------------
            // COMPLAINTS (Serious offenses - typically handled by prefect)
            // --------------------
            $groupSize = count($studentIds);
            for ($i = 0; $i < min(3, $groupSize - 1); $i++) { // Limit to 3 complaints per group
                $violatorId = $studentIds[$i];
                $complaintantId = $studentIds[($i + 1) % $groupSize];
                $complaintCount++;

                $complaintType = $i % 4;
                switch ($complaintType) {
                    case 0: // Bullying
                        $offenseId = $this->getRandomOffenseId($bullyingOffenses, $allOffenseIds);
                        $incident = $complaint_incidents[0];
                        $status = rand(0, 1) ? 'in_progress' : 'resolved';
                        $handledBy = 'prefect'; // Serious offenses handled by prefect
                        break;
                    case 1: // Cheating
                        $offenseId = $this->getRandomOffenseId($cheatingOffenses, $allOffenseIds);
                        $incident = $complaint_incidents[2];
                        $status = 'resolved';
                        $handledBy = rand(0, 1) ? 'adviser' : 'prefect';
                        break;
                    case 2: // Theft
                        $offenseId = $this->getRandomOffenseId($theftOffenses, $allOffenseIds);
                        $incident = $complaint_incidents[6];
                        $status = 'in_progress';
                        $handledBy = 'prefect'; // Serious offenses handled by prefect
                        break;
                    case 3: // Vandalism
                        $offenseId = $this->getRandomOffenseId($vandalismOffenses, $allOffenseIds);
                        $incident = $complaint_incidents[9];
                        $status = 'resolved';
                        $handledBy = 'prefect'; // Serious offenses handled by prefect
                        break;
                }

                $violationDate = Carbon::now()->subDays(rand(1, 20));
                $violationTime = $this->generateSchoolHoursTime();

                // Select only from allowed sanctions - Parent/Guardian Notification for serious offenses
                $sanctionId = $allowedSanctions[array_rand($allowedSanctions)];

                // Generate data for new columns (more detailed for complaints)
                $witnesses = $this->generateWitnesses($studentDetails, $violatorId, 2); // More witnesses for complaints
                $complainant = $studentDetails[$complaintantId]->student_fname . ' ' . $studentDetails[$complaintantId]->student_lname;
                $evidenceDescription = $this->generateEvidenceDescription($incident, true); // Always evidence for complaints
                $evidenceFiles = $this->generateEvidenceFiles(2); // More evidence for complaints

                // Generate sanction timing
                $sanctionData = $this->generateSanctionTiming($violationDate, $status, true);
                $sanctionStartAt = $sanctionData['start'];
                $sanctionEndAt = $sanctionData['end'];
                $sanctionStatus = $sanctionData['status'];

                $violation_id = DB::table('tbl_violation_record')->insertGetId([
                    'violator_id' => $violatorId,
                    'prefect_id' => $prefectId,
                    'offense_id' => $offenseId,
                    'sanction_id' => $sanctionId,
                    'violation_incident' => $incident,
                    'violation_date' => $violationDate->toDateString(),
                    'violation_time' => $violationTime,
                    'status' => $status,
                    'handled_by' => $handledBy,
                    'escalated_at' => $handledBy === 'prefect' ? $violationDate->copy()->addHours(rand(1, 24)) : null,
                    'witnesses' => $witnesses,
                    'complainant' => $complainant,
                    'evidence_description' => $evidenceDescription,
                    'evidence_files' => json_encode($evidenceFiles),
                    'sanction_start_at' => $sanctionStartAt,
                    'sanction_end_at' => $sanctionEndAt,
                    'sanction_status' => $sanctionStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // VIOLATION APPOINTMENT - For complaints that need meetings
                if (in_array($status, ['in_progress']) || rand(0, 1)) {
                    $appointmentDate = $violationDate->copy()->addDays(rand(2, 5));
                    DB::table('tbl_violation_appointment')->insert([
                        'violation_id' => $violation_id,
                        'handled_by' => $handledBy,
                        'violation_app_date' => $appointmentDate->toDateString(),
                        'violation_app_time' => $this->generateOfficeHoursTime(),
                        'violation_app_notes' => $this->getComplaintAppointmentNotes($incident),
                        'violation_app_status' => 'Scheduled',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // VIOLATION ANECDOTAL - For resolved complaints
                if ($status === 'resolved') {
                    DB::table('tbl_violation_anecdotal')->insert([
                        'violation_id' => $violation_id,
                        'handled_by' => $handledBy,
                        'violation_anec_solution' => $this->getComplaintSolution($incident),
                        'violation_anec_recommendation' => $this->getComplaintRecommendation($incident),
                        'violation_anec_date' => $violationDate->copy()->addDays(rand(1, 3))->toDateString(),
                        'violation_anec_time' => $this->generateOfficeHoursTime(),
                        'status' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Successfully seeded violations data (including complaints).');
        $this->command->info('Total violations created: ' . $violationCount);
        $this->command->info('Total complaint-type violations created: ' . $complaintCount);
        $this->command->info('Sanctions limited to: Verbal Warning and Parent/Guardian Notification only');
    }

    private function getRandomOffenseId(array $specificOffenses, array $fallbackOffenses): int
    {
        if (!empty($specificOffenses)) {
            return $specificOffenses[array_rand($specificOffenses)];
        }
        return $fallbackOffenses[array_rand($fallbackOffenses)];
    }

    private function generateSchoolHoursTime(): string
    {
        $hours = rand(7, 15); // School hours from 7 AM to 3 PM
        $minutes = rand(0, 3) * 15; // 00, 15, 30, or 45 minutes
        return sprintf('%02d:%02d:00', $hours, $minutes);
    }

    private function generateOfficeHoursTime(): string
    {
        $hours = rand(8, 16); // Office hours from 8 AM to 4 PM
        $minutes = rand(0, 3) * 15;
        return sprintf('%02d:%02d:00', $hours, $minutes);
    }

    private function generateWitnesses($studentDetails, $excludeId, $max = 1): string
    {
        $witnesses = [];
        $availableStudents = array_diff_key($studentDetails->toArray(), [$excludeId => null]);

        if (empty($availableStudents)) {
            return 'No witnesses identified';
        }

        $count = min($max, count($availableStudents));
        $randomStudents = array_rand($availableStudents, $count);

        if (!is_array($randomStudents)) {
            $randomStudents = [$randomStudents];
        }

        foreach ($randomStudents as $studentId) {
            $student = $studentDetails[$studentId];
            $witnesses[] = $student->student_fname . ' ' . $student->student_lname;
        }

        return implode(', ', $witnesses);
    }

    private function generateComplainant($studentDetails, $excludeId): string
    {
        $availableStudents = array_diff_key($studentDetails->toArray(), [$excludeId => null]);

        if (empty($availableStudents)) {
            return 'Anonymous';
        }

        $studentId = array_rand($availableStudents);
        $student = $studentDetails[$studentId];
        return $student->student_fname . ' ' . $student->student_lname;
    }

    private function generateEvidenceDescription(string $incident, bool $detailed = false): string
    {
        if ($detailed) {
            $descriptions = [
                "Photographic evidence of the incident showing clear violation",
                "Written statement from witness describing the event in detail",
                "Digital evidence including screenshots and timestamps",
                "Physical evidence collected and documented by school staff",
                "Video recording showing the sequence of events"
            ];
        } else {
            $descriptions = [
                "Teacher's written observation of the incident",
                "Classroom monitoring report",
                "Attendance record showing tardiness",
                "Assignment submission history"
            ];
        }

        return $descriptions[array_rand($descriptions)];
    }

    private function generateEvidenceFiles(int $count = 1): array
    {
        $fileTypes = ['jpg', 'png', 'pdf', 'docx', 'mp4'];
        $files = [];

        for ($i = 0; $i < $count; $i++) {
            $type = $fileTypes[array_rand($fileTypes)];
            $files[] = "evidence_" . uniqid() . "." . $type;
        }

        return $files;
    }

    private function generateSanctionTiming(Carbon $violationDate, string $status, bool $serious = false): array
    {
        $result = [
            'start' => null,
            'end' => null,
            'status' => 'pending'
        ];

        if ($status === 'resolved') {
            // For Verbal Warning and Parent/Guardian Notification, sanctions are typically completed immediately
            $result['status'] = 'completed';

            if ($serious) {
                // For serious complaints with parent notification, schedule a meeting
                $result['start'] = $violationDate->copy()->addDays(rand(1, 3))->setTime(8, 0);
                $result['end'] = $result['start']->copy()->addHours(1); // 1-hour meeting
            } else {
                // For minor violations, verbal warning happens immediately
                $result['start'] = $violationDate->setTime(8, 0);
                $result['end'] = $result['start']->copy()->addMinutes(15); // Brief conversation
            }
        }

        return $result;
    }

    private function getWeightedRandom(array $options, array $weights)
    {
        $total = array_sum($weights);
        $random = mt_rand(1, $total);
        $current = 0;

        foreach ($options as $index => $option) {
            $current += $weights[$index];
            if ($random <= $current) {
                return $option;
            }
        }

        return $options[0];
    }

    private function getViolationAppointmentNotes(string $incident, string $studentName): string
    {
        $notes = [
            "Schedule meeting with $studentName and parents to discuss $incident",
            "Follow-up conference required regarding $incident. Invite class adviser.",
            "Behavior intervention meeting scheduled for $studentName concerning $incident",
            "Academic consultation meeting to address $incident and create improvement plan"
        ];
        return $notes[array_rand($notes)];
    }

    private function getViolationSolution(string $incident, string $studentName): string
    {
        $solutions = [
            "Student $studentName acknowledged the behavior and committed to improvement. Written apology submitted.",
            "Parent-teacher conference conducted. Agreement reached on monitoring system for $studentName.",
            "Behavior contract established with clear expectations and consequences for future incidents.",
            "Academic support plan implemented with weekly progress checks and mentoring sessions."
        ];
        return $solutions[array_rand($solutions)];
    }

    private function getViolationRecommendation(string $incident): string
    {
        $recommendations = [
            "Monitor academic performance and behavior for 4 weeks with bi-weekly check-ins",
            "Implement positive behavior support plan with reward system for improvement",
            "Schedule follow-up meeting in 2 weeks to assess progress and adjust intervention",
            "Coordinate with guidance counselor for ongoing social-emotional support"
        ];
        return $recommendations[array_rand($recommendations)];
    }

    private function getComplaintAppointmentNotes(string $incident): string
    {
        $notes = [
            "Mediation session scheduled to resolve conflict: $incident. Both parties and parents invited.",
            "Conflict resolution meeting to address $incident. School counselor will facilitate.",
            "Restorative justice circle scheduled for students involved in $incident",
            "Peer mediation session to resolve interpersonal conflict regarding $incident"
        ];
        return $notes[array_rand($notes)];
    }

    private function getComplaintSolution(string $incident): string
    {
        $solutions = [
            "Successful mediation conducted. Both students agreed to mutual respect and conflict resolution strategies.",
            "Restorative conference completed. Students developed understanding and created behavior agreement.",
            "Conflict resolved through guided dialogue. Apology accepted and reconciliation achieved.",
            "Peer mediation successful. Students committed to improved communication and boundary respect."
        ];
        return $solutions[array_rand($solutions)];
    }

    private function getComplaintRecommendation(string $incident): string
    {
        $recommendations = [
            "Monitor student interactions for 3 weeks with daily check-ins from class adviser",
            "Implement peer support buddy system to prevent future conflicts",
            "Schedule follow-up mediation session in 2 weeks to reinforce positive behaviors",
            "Provide social skills training sessions for both students through guidance office"
        ];
        return $recommendations[array_rand($recommendations)];
    }
}
