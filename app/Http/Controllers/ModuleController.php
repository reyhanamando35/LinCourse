<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Practice;
use App\Models\Question;
use App\Models\AnswerKey;
use Illuminate\Http\Request;
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
         $subject = $module->subject; 

        return view('edit-module', compact('module', 'subject'));
    }

    public function updateModule(Request $request, Module $module)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('modules')->ignore($module->id)],
            'content' => 'required|string',
            'pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        $picturePaths = $module->pictures;
        if ($request->hasFile('pictures')) {
            // Hapus gambar lama jika ada
            foreach ($module->pictures as $oldPicture) {
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
        foreach ($module->pictures as $picture) {
            Storage::disk('public')->delete($picture);
        }
        $subjectId = $module->subject_id;
        $module->delete(); 
        return redirect()->route('showModule', $subjectId)->with('success', 'Module has been deleted.');
    }

    public function storePractice(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
        ]);

        Practice::create($request->all());
        return redirect()->back()->with('success', 'New practice has been successfully added!');
    }

    public function updatePractice(Request $request, Practice $practice)
    {
        $request->validate(['title' => 'required|string|max:255', 'description' => 'nullable|string']);
        $practice->update($request->all());
        return back()->with('success', 'Practice updated successfully!');
    }

    public function destroyPractice(Practice $practice)
    {
        $practice->delete();
        return back()->with('success', 'Practice has been deleted.');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'content_text' => 'required|string',
            'practice_id' => 'required|exists:practices,id',
            'content_file' => 'nullable|file|max:5120',
        ]);

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
        $request->validate(['content_text' => 'required|string', 'content_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048']);
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
        if ($question->content_file) Storage::disk('public')->delete($question->content_file);
        $question->delete();
        return back()->with('success', 'Question has been deleted.');
    }

    public function storeAnswerKey(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'key_text' => 'required|string',
            'key_file' => 'nullable|file|max:5120',
        ]);

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
        if ($answer_key->key_file) Storage::disk('public')->delete($answer_key->key_file);
        $answer_key->delete();
        return back()->with('success', 'Answer key has been deleted.');
    }
}
