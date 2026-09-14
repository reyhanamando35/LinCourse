<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\StudentAttempt;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class StudentAttemptController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'nullable|string|max:10000',
            'answer_file' => self::DOCUMENT_RULE,
        ]);

        $user = $request->user();
        $question = Question::findOrFail($request->question_id);
        $subjectId = $question->practice->module->subject_id;

        // Harus terdaftar dan pembayarannya sudah diverifikasi, bukan sekadar terdaftar
        Gate::authorize('access-subject', $subjectId);

        $enrollment = StudentDetail::where('student_id', $user->student->id)
                                    ->where('subject_id', $subjectId)
                                    ->firstOrFail();

        $attempt = StudentAttempt::firstOrNew([
            'student_detail_id' => $enrollment->id,
            'question_id' => $question->id,
        ]);

        $path = $attempt->answer_file;
        if ($request->hasFile('answer_file')) {
            if ($attempt->answer_file) Storage::disk('public')->delete($attempt->answer_file);
            $path = $request->file('answer_file')->store('attempts', 'public');
        }

        $attempt->fill([
            'answer_text' => $request->answer_text,
            'answer_file' => $path,
        ])->save();

        return redirect()->back()->with('success', 'Your answer has been submitted!');
    }

    public function grade(Request $request, StudentAttempt $attempt)
    {
        // 1. Otorisasi: hanya admin atau guru yang mengampu subject ini
        Gate::authorize('manage-subject', $attempt->question->practice->module->subject_id);

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
