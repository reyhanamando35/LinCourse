<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Practice;
use App\Models\Question;
use App\Models\AnswerKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    /**
     * Menyimpan Modul baru.
     */
    public function storeModule(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        Gate::authorize('manage-subject', (int) $request->subject_id);

        try {
            $picturePaths = [];
            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $picture) {
                    $path = $picture->store('modules', 'public');
                    $picturePaths[] = $path;
                }
            }

            Module::create([
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'content' => $request->content,
                'pictures' => $picturePaths,
            ]);

            return redirect()->back()->with('success', 'Module has been successfully added!');
        } catch (\Throwable $th) {
            Log::error('Module creation failed: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to add module. Please try again.');
        }
    }

    public function editModule(Module $module)
    {
        Gate::authorize('manage-subject', $module->subject_id);

        $subject = $module->subject;

        return view('edit-module', compact('module', 'subject'));
    }

    public function updateModule(Request $request, Module $module)
    {
        Gate::authorize('manage-subject', $module->subject_id);

        $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('modules')->ignore($module->id)],
            'content' => 'required|string',
            'pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $picturePaths = $module->pictures;
        if ($request->hasFile('pictures')) {
            // Hapus gambar lama jika ada
            foreach ($module->pictures ?? [] as $oldPicture) {
                Storage::disk('public')->delete($oldPicture);
            }
            // Simpan gambar baru
            $picturePaths = [];
            foreach ($request->file('pictures') as $picture) {
                $path = $picture->store('modules', 'public');
                $picturePaths[] = $path;
            }
        }

        $module->update([
            'title' => $request->title,
            'content' => $request->content,
            'pictures' => $picturePaths,
        ]);

        return redirect()->route('showModule', $module->subject_id)->with('success', 'Module updated successfully!');
    }

    public function destroyModule(Module $module)
    {
        Gate::authorize('manage-subject', $module->subject_id);

        foreach ($module->pictures ?? [] as $picture) {
            Storage::disk('public')->delete($picture);
        }
        $subjectId = $module->subject_id;
        $module->delete();
        return redirect()->route('showModule', $subjectId)->with('success', 'Module has been deleted.');
    }

    public function storePractice(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
        ]);

        Gate::authorize('manage-subject', Module::findOrFail($validated['module_id'])->subject_id);

        Practice::create($validated);
        return redirect()->back()->with('success', 'New practice has been successfully added!');
    }

    public function updatePractice(Request $request, Practice $practice)
    {
        Gate::authorize('manage-subject', $practice->module->subject_id);

        // Hanya title & description: module_id tidak boleh diubah (bisa memindahkan practice ke subject lain)
        $validated = $request->validate(['title' => 'required|string|max:255', 'description' => 'nullable|string']);
        $practice->update($validated);
        return back()->with('success', 'Practice updated successfully!');
    }

    public function destroyPractice(Practice $practice)
    {
        Gate::authorize('manage-subject', $practice->module->subject_id);

        $practice->delete();
        return back()->with('success', 'Practice has been deleted.');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'content_text' => 'required|string',
            'practice_id' => 'required|exists:practices,id',
            'content_file' => self::DOCUMENT_RULE,
        ]);

        Gate::authorize('manage-subject', Practice::findOrFail($request->practice_id)->module->subject_id);

        $path = null;
        if ($request->hasFile('content_file')) {
            $path = $request->file('content_file')->store('questions', 'public');
        }

        Question::create([
            'practice_id' => $request->practice_id,
            'content_text' => $request->content_text,
            'content_file' => $path,
        ]);

        return redirect()->back()->with('success', 'New question has been successfully added!');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        Gate::authorize('manage-subject', $question->practice->module->subject_id);

        $request->validate(['content_text' => 'required|string', 'content_file' => self::DOCUMENT_RULE]);
        $path = $question->content_file;
        if ($request->hasFile('content_file')) {
            if ($question->content_file) Storage::disk('public')->delete($question->content_file);
            $path = $request->file('content_file')->store('questions', 'public');
        }
        $question->update(['content_text' => $request->content_text, 'content_file' => $path]);
        return back()->with('success', 'Question updated successfully!');
    }

    public function destroyQuestion(Question $question)
    {
        Gate::authorize('manage-subject', $question->practice->module->subject_id);

        if ($question->content_file) Storage::disk('public')->delete($question->content_file);
        $question->delete();
        return back()->with('success', 'Question has been deleted.');
    }

    public function storeAnswerKey(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'key_text' => 'required|string',
            'key_file' => self::DOCUMENT_RULE,
        ]);

        Gate::authorize('manage-subject', Question::findOrFail($request->question_id)->practice->module->subject_id);

        $path = null;
        if ($request->hasFile('key_file')) {
            $path = $request->file('key_file')->store('answer_keys', 'public');
        }

        AnswerKey::updateOrCreate(
            ['question_id' => $request->question_id],
            [
                'key_text' => $request->key_text,
                'key_file' => $path ?? AnswerKey::where('question_id', $request->question_id)->value('key_file'),
            ]
        );

        return redirect()->back()->with('success', 'Answer key has been saved!');
    }

    public function destroyAnswerKey(AnswerKey $answer_key)
    {
        Gate::authorize('manage-subject', $answer_key->question->practice->module->subject_id);

        if ($answer_key->key_file) Storage::disk('public')->delete($answer_key->key_file);
        $answer_key->delete();
        return back()->with('success', 'Answer key has been deleted.');
    }
}
