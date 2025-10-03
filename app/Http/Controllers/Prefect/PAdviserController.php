<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Adviser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PAdviserController extends Controller
{

    // AdviserController.php (index)
public function index(Request $request)
{

    $totalAdvisers = DB::table('tbl_adviser')->count();

        // Count advisers per grade level
        $grade11Advisers = DB::table('tbl_adviser')
            ->where('adviser_gradelevel', '11')
            ->count();

        $grade12Advisers = DB::table('tbl_adviser')
            ->where('adviser_gradelevel', '12')
            ->count();
    $advisers = Adviser::orderBy('updated_at')
                ->paginate(15)           // <-- paginate instead of get()/all()
                ->appends($request->query()); // keep query string (useful if you later add server search)

    return view('prefect.adviser', compact('advisers', 'totalAdvisers', 'grade11Advisers', 'grade12Advisers'));

}
 public function createAdviser()
    {
        return view('prefect.create-adviser'); // Blade file
    }

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

    public function update(Request $request)
{
    $adviser = Adviser::findOrFail($request->adviser_id);

    $adviser->update([
        'adviser_fname' => $request->adviser_fname,
        'adviser_lname' => $request->adviser_lname,
        'adviser_section' => $request->adviser_section,
        'adviser_gradelevel' => $request->adviser_gradelevel,
        'adviser_email' => $request->adviser_email,
        'adviser_contactinfo' => $request->adviser_contactinfo,
    ]);

    return redirect()->back()->with('success', 'Adviser updated successfully!');
}


}
