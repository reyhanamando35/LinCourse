<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function role()
    {
        $students = Student::with([
            'user', 
            // Ambil relasi studentDetails, urutkan berdasarkan tanggal, dan ambil hanya 1 (yang pertama)
            'studentDetails' => function($query) {
                $query->orderBy('created_at', 'asc')->limit(1);
            }
        ])->get();

        $teachers = Teacher::with('user')->get();

        return view('admin.role', [
            'students' => $students,
            'teachers' => $teachers,
        ]);
    }

    public function subject()
    {
        $subjects = Subject::withCount('studentDetails')->get();

        return view('admin.subject', compact('subjects'));
    }

    public function showVerify()
    {
        $payments = Payment::latest()->with(['studentDetail.student.user', 'studentDetail.subject'])->get();
        
        return view('admin.verify', ['payments' => $payments]);
    }

    public function verifyPayment(Request $request, Payment $payment)
    {
        $payment->update(['status' => 'verified', 'verified_by' => $request->user()->admin->id]);

        $payment->studentDetail()->update(['status' => 'active']);

        return redirect()->route('admin.verify')->with('success', 'Payment has been successfully verified.');
    }

    public function rejectPayment(Payment $payment)
    {
        $proofPath = $payment->payment_proof;

        $payment->studentDetail()->delete();

        if ($proofPath) {
            Storage::disk('local')->delete($proofPath);
            Storage::disk('public')->delete($proofPath);
        }

        return redirect()->route('admin.verify')->with('success', 'Payment has been rejected and the enrollment has been deleted.');
    }

    public function editUser(User $user)
    {
        $user->load(['student', 'teacher']);
        
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), 
            ],
            'grade' => 'nullable|integer|min:7|max:12',
            'experience_years' => 'nullable|integer|min:0', 
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->student) {
            $user->student->update(['grade' => $request->grade]);
        }
        
        if ($user->teacher) {
            $user->teacher->update(['experience_years' => $request->experience_years]);
        }

        return redirect()->route('admin.role')->with('success', 'User has been updated successfully.');
    }

    public function destroyUser(Request $request, User $user)
    {
        // Mencegah admin terakhir mengunci dirinya sendiri keluar dari panel admin
        if ($user->is($request->user())) {
            return redirect()->route('admin.role')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.role')->with('success', 'User has been deleted successfully.');
    }

    public function makeAdmin(User $user)
    {
        if ($user->admin) {
            return redirect()->route('admin.role')->with('error', 'This user is already an admin.');
        }

        if ($user->student) {
            $user->student->delete();
        }
        if ($user->teacher) {
            $user->teacher->delete();
        }

        Admin::create([
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.role')->with('success', $user->name . ' has been promoted to Admin.');
    }

    public function editSubject(Subject $subject)
    {
        $teachers = Teacher::with('user')->get();
        $assignedTeacherIds = $subject->teachers()->pluck('teachers.id')->all();

        return view('admin.edit-subject', compact('subject', 'teachers', 'assignedTeacherIds'));
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('subjects')->ignore($subject->id)],
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:2048', // Nullable: gambar tidak wajib diubah
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'integer|exists:teachers,id',
        ]);

        // Guru yang dicentang = guru yang boleh mengelola modul, soal, dan menilai jawaban subject ini
        $subject->teachers()->sync($request->input('teacher_ids', []));

        $path = $subject->picture;

        if ($request->hasFile('picture')) {
            if ($subject->picture) {
                Storage::disk('public')->delete($subject->picture);
            }
            $path = $request->file('picture')->store('subjects', 'public');
        }

        $subject->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'picture' => $path,
        ]);

        return redirect()->route('admin.subject')->with('success', 'Subject has been updated successfully.');
    }

    public function destroySubject(Subject $subject)
    {
        if ($subject->picture) {
            Storage::disk('public')->delete($subject->picture);
        }

        $subject->delete();

        return redirect()->route('admin.subject')->with('success', 'Subject has been deleted successfully.');
    }

    public function showIncome()
    {
        // Dikelompokkan per bulan di PHP, bukan DATE_FORMAT, supaya jalan di MySQL maupun PostgreSQL
        $incomeData = Payment::where('status', 'verified')
            ->where('payment_date', '>=', Carbon::now()->subMonths(12))
            ->get(['payment_date', 'amount'])
            ->groupBy(fn ($payment) => Carbon::parse($payment->payment_date)->format('Y-m'))
            ->sortKeys()
            ->map(fn ($payments) => $payments->sum('amount'));

        $labels = $incomeData->keys()->map(fn ($month) => Carbon::createFromFormat('Y-m', $month)->format('M Y'))->values();

        $data = $incomeData->values();

        return view('admin.income', compact('labels', 'data'));
    }
}
