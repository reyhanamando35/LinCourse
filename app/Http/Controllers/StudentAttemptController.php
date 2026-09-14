<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\StudentAttempt;
use App\Models\StudentDetail;
use Illuminate\Http\Request;

class StudentAttemptController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'nullable|string',
            'answer_file' => 'nullable|file|max:5120',
        ]);

        $user = $request->user();
        $question = Question::find($request->question_id);
        
        $enrollment = StudentDetail::where('student_id', $user->student->id)
                                    ->where('subject_id', $question->practice->module->subject_id)
                                    ->firstOrFail();

        $path = null;
        if ($request->hasFile('answer_file')) {
            $path = $request->file('answer_file')->store('attempts', 'public');
        }

        StudentAttempt::updateOrCreate(
            [
                'student_detail_id' => $enrollment->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer_text' => $request->answer_text,
                'answer_file' => $path,
            ]
        );

        return redirect()->back()->with('success', 'Your answer has been submitted!');
    }

    public function grade(Request $request, StudentAttempt $attempt)
    {
        // 1. Otorisasi: Pastikan hanya admin atau guru yang bisa mengakses
        $user = $request->user();
        if (!$user->admin && !$user->teacher) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // 2. Validasi input
        $request->validate([
            'is_correct' => 'required|boolean',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // 3. Update data attempt
        $attempt->is_correct = $request->is_correct;
        $attempt->feedback = $request->feedback;
        $attempt->save();

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Answer has been graded successfully!');
    }
}
