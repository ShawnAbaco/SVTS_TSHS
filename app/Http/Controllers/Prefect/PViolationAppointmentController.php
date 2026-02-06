<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ViolationAnecdotal;
use App\Services\PhilSMSService;
use App\Models\Complaints;
use Barryvdh\DomPDF\Facade\Pdf;

class PViolationAppointmentController extends Controller
{
    protected $smsService;

public function __construct(PhilSMSService $smsService)
{
    $this->smsService = $smsService;
}

   // Individual appointment status update
public function updateStatus(Request $request)
{
    try {
        $request->validate([
            'appointment_id' => 'required|exists:violation_appointments,violation_app_id',
            'new_status' => 'required|in:Pending,Scheduled,Rescheduled,Completed,Cancelled',
            'status_notes' => 'nullable|string|max:500'
        ]);

        $appointment = ViolationAppointment::findOrFail($request->appointment_id);

        // Check if appointment is already completed or cancelled
        if (in_array($appointment->violation_app_status, ['Completed', 'Cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update status of a completed or cancelled appointment.'
            ], 400);
        }

        // Get the old status for comparison
        $oldStatus = $appointment->violation_app_status;

        // Update the appointment
        $appointment->violation_app_status = $request->new_status;

        // Add status notes if provided
        if ($request->filled('status_notes')) {
            $appointment->violation_app_notes .= "\n\n[Status Update: " . now()->format('Y-m-d H:i') . "]\n" . $request->status_notes;
        }

        $appointment->save();

        // Send SMS notification when status changes to "Scheduled" or "Rescheduled"
        if (($oldStatus !== $request->new_status) &&
            (in_array($request->new_status, ['Scheduled', 'Rescheduled']))) {
            $this->sendAppointmentSMSNotification($appointment);
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating status.',
            'error' => $e->getMessage()
        ], 500);
    }
}

// New method to send SMS notification for appointment status changes
private function sendAppointmentSMSNotification($appointment)
{
    try {
        Log::info("DEBUG: Preparing APPOINTMENT STATUS SMS for appointment: {$appointment->violation_app_id}");

        // Load the violation with student and parent information
        $appointment->load([
            'violation.student.parent',
            'violation.offense'
        ]);

        if (!$appointment->violation || !$appointment->violation->student) {
            Log::warning("Appointment or student not found for SMS notification: {$appointment->violation_app_id}");
            return;
        }

        $student = $appointment->violation->student;

        if (!$student->parent) {
            Log::warning("No parent found for student: {$student->student_fname} {$student->student_lname}");
            return;
        }

        // DEBUG: Check if this is YOUR number
        $parentContact = $student->parent->parent_contactinfo ?? null;
        $isMyNumber = in_array($parentContact, ['09154240619', '639154240619']);

        Log::info("DEBUG APPOINTMENT STATUS PHONE CHECK:", [
            'student_name' => $student->student_fname . ' ' . $student->student_lname,
            'parent_name' => $student->parent->parent_fname . ' ' . $student->parent->parent_lname,
            'parent_contact' => $parentContact,
            'is_my_number' => $isMyNumber
        ]);

        if (!$parentContact) {
            Log::warning("No parent contact found for student: {$student->student_fname} {$student->student_lname}");
            return;
        }

        $studentName = $student->student_fname . ' ' . $student->student_lname;
        $parentName = $student->parent->parent_fname . ' ' . $student->parent->parent_lname;
        $offenseName = $appointment->violation->offense->offense_type ?? 'Unknown Offense';

        // Build appointment status message
        $message = "Dear Parent/Guardian {$parentName},\n\n";
        $message .= "Your child {$studentName}'s appointment regarding \"{$offenseName}\" has been updated.\n\n";

        $message .= "New Appointment Details:\n";
        $message .= "Status: {$appointment->violation_app_status}\n";
        $message .= "Date: " . date('M j, Y', strtotime($appointment->violation_app_date)) . "\n";
        $message .= "Time: " . date('g:i A', strtotime($appointment->violation_app_time)) . "\n\n";

        if (!empty($appointment->violation_app_notes)) {
            // Extract only the latest notes if there are multiple entries
            $notes = $appointment->violation_app_notes;
            // Find the last status update section
            $lastUpdatePos = strrpos($notes, '[Status Update:');
            if ($lastUpdatePos !== false) {
                $notes = substr($notes, $lastUpdatePos);
            }
            $message .= "Notes: " . substr($notes, 0, 200) . "\n";
        }

        $message .= "\nPlease check with Tagoloan Senior High School for any questions.";

        // Limit message length
        if (strlen($message) > 480) {
            $message = substr($message, 0, 477) . '...';
        }

        Log::info("DEBUG: Sending appointment status SMS message", [
            'message_length' => strlen($message),
            'appointment_id' => $appointment->violation_app_id,
            'old_status' => $oldStatus ?? 'N/A',
            'new_status' => $appointment->violation_app_status,
            'student_name' => $studentName
        ]);

        // Send SMS - Assuming you have access to smsService
        if (property_exists($this, 'smsService') && $this->smsService) {
            $smsResult = $this->smsService->sendSMS($parentContact, $message);

            if ($smsResult['success'] ?? false) {
                Log::info("✅ APPOINTMENT STATUS SMS sent to parent of {$studentName}", [
                    'parent_name' => $parentName,
                    'phone' => $parentContact,
                    'appointment_id' => $appointment->violation_app_id,
                    'status' => $appointment->violation_app_status
                ]);
            } else {
                Log::error("❌ Failed to send appointment status SMS to parent of {$studentName}", [
                    'parent_name' => $parentName,
                    'phone' => $parentContact,
                    'error' => $smsResult['error'] ?? 'Unknown error'
                ]);
            }
        } else {
            Log::warning("SMS service not available in PViolationAppointmentController");
        }

    } catch (\Exception $e) {
        Log::error('Error in sendAppointmentSMSNotification: ' . $e->getMessage());
    }
}

// Also update bulkUpdateStatus to send SMS notifications
public function bulkUpdateStatus(Request $request)
{
    try {
        $request->validate([
            'appointment_ids' => 'required|array',
            'appointment_ids.*' => 'exists:tbl_violation_appointment,violation_app_id',
            'new_status' => 'required|in:Pending,Scheduled,Rescheduled,Completed,Cancelled',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'appointment_notes' => 'nullable|string|max:1000'
        ]);

        $appointmentIds = $request->input('appointment_ids');
        $newStatus = $request->input('new_status');
        $appointmentDate = $request->input('appointment_date');
        $appointmentTime = $request->input('appointment_time');
        $appointmentNotes = $request->input('appointment_notes');

        // Get appointments before update to check old statuses
        $appointments = ViolationAppointment::whereIn('violation_app_id', $appointmentIds)
            ->where('violation_app_status', '!=', 'Completed')
            ->get();

        // Update appointments
        $updatedCount = ViolationAppointment::whereIn('violation_app_id', $appointmentIds)
            ->where('violation_app_status', '!=', 'Completed')
            ->update([
                'violation_app_status' => $newStatus,
                'violation_app_date' => $appointmentDate,
                'violation_app_time' => $appointmentTime,
                'violation_app_notes' => $appointmentNotes,
                'updated_at' => now()
            ]);

        // Send SMS notifications for updated appointments
        if ($updatedCount > 0 && in_array($newStatus, ['Scheduled', 'Rescheduled'])) {
            foreach ($appointments as $appointment) {
                // Create a fresh appointment object with updated values for SMS
                $updatedAppointment = new ViolationAppointment();
                $updatedAppointment->violation_app_id = $appointment->violation_app_id;
                $updatedAppointment->violation_app_status = $newStatus;
                $updatedAppointment->violation_app_date = $appointmentDate;
                $updatedAppointment->violation_app_time = $appointmentTime;
                $updatedAppointment->violation_app_notes = $appointmentNotes;
                $updatedAppointment->violation_id = $appointment->violation_id;

                $this->sendAppointmentSMSNotification($updatedAppointment);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} appointment(s).",
            'updated_count' => $updatedCount
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update appointments: ' . $e->getMessage()
        ], 500);
    }
}

// public function bulkUpdateStatus(Request $request)
// {
//     try {
//         $request->validate([
//             'appointment_ids' => 'required|array',
//             'appointment_ids.*' => 'exists:tbl_violation_appointment,violation_app_id',
//             'new_status' => 'required|in:Pending,Scheduled,Rescheduled,Completed,Cancelled',
//             'appointment_date' => 'required|date|after_or_equal:today',
//             'appointment_time' => 'required|date_format:H:i',
//             'appointment_notes' => 'nullable|string|max:1000'
//         ]);

//         $appointmentIds = $request->input('appointment_ids');
//         $newStatus = $request->input('new_status');
//         $appointmentDate = $request->input('appointment_date');
//         $appointmentTime = $request->input('appointment_time');
//         $appointmentNotes = $request->input('appointment_notes');

//         // Update appointments
//         $updatedCount = ViolationAppointment::whereIn('violation_app_id', $appointmentIds)
//             ->where('violation_app_status', '!=', 'Completed') // Prevent updating completed appointments
//             ->update([
//                 'violation_app_status' => $newStatus,
//                 'violation_app_date' => $appointmentDate,
//                 'violation_app_time' => $appointmentTime,
//                 'violation_app_notes' => $appointmentNotes,
//                 'updated_at' => now()
//             ]);

//         return response()->json([
//             'success' => true,
//             'message' => "Successfully updated {$updatedCount} appointment(s).",
//             'updated_count' => $updatedCount
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to update appointments: ' . $e->getMessage()
//         ], 500);
//     }
// }
}
