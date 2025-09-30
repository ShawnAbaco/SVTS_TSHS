<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Adviser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PAdviserController extends Controller
{
 public function store(Request $request)
    {
        // ✅ Validate the outer array
        $request->validate([
            'advisers' => 'required|array|min:1',
            'advisers.*.adviser_fname' => 'required|string|max:255',
            'advisers.*.adviser_lname' => 'required|string|max:255',
            'advisers.*.adviser_sex' => 'nullable|in:male,female,other',
            'advisers.*.adviser_email' => 'required|email|max:255|unique:tbl_adviser,adviser_email',
            'advisers.*.adviser_password' => 'required|string|min:6',
            'advisers.*.adviser_contactinfo' => 'required|string|max:255',
            'advisers.*.adviser_section' => 'required|string|max:255',
            'advisers.*.adviser_gradelevel' => 'required|string|max:50',
        ]);

        $messages = [];

        foreach ($request->advisers as $index => $adviserData) {
            try {
                Adviser::create([
                    'adviser_fname' => $adviserData['adviser_fname'],
                    'adviser_lname' => $adviserData['adviser_lname'],
                    'adviser_sex' => $adviserData['adviser_sex'] ?? null,
                    'adviser_email' => $adviserData['adviser_email'],
                    'adviser_password' => Hash::make($adviserData['adviser_password']),
                    'adviser_contactinfo' => $adviserData['adviser_contactinfo'],
                    'adviser_section' => $adviserData['adviser_section'],
                    'adviser_gradelevel' => $adviserData['adviser_gradelevel'],
                    'status' => 'active',
                ]);

                $messages[] = "✅ Adviser " . ($index + 1) . " (" . $adviserData['adviser_fname'] . " " . $adviserData['adviser_lname'] . ") created successfully.";
            } catch (\Exception $e) {
                $messages[] = "⚠️ Failed to create Adviser " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return redirect()->back()->with('messages', $messages);
    }


}
