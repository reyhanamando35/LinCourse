<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentAttempt;
use App\Models\StudentDetail;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $subjects = Subject::all();
        $user = $request->user(); 

        $unpaidBills = [];
        $processingBills = []; // <-- Array baru untuk tagihan yang sedang diproses
        $grandTotal = 0;

        if ($user && $user->student) {
            $student = $user->student;
            $enrollmentIds = $student->studentDetails()->pluck('id');

            // Ambil semua pembayaran yang statusnya 'pending'
            $pendingPayments = Payment::whereIn('student_detail_id', $enrollmentIds)
                                      ->where('status', 'pending')
                                      ->with('studentDetail.subject')
                                      ->get();

            // =================================================================
            // === PERUBAHAN LOGIKA UTAMA: Pisahkan tagihan jadi dua grup ===
            // =================================================================
            
            // Gunakan metode 'partition' untuk membagi koleksi menjadi dua:
            // 1. $processing: pembayaran yang SUDAH punya bukti bayar
            // 2. $unpaid: pembayaran yang BELUM punya bukti bayar (tagihan murni)
            [$processing, $unpaid] = $pendingPayments->partition(function ($payment) {
                return !is_null($payment->payment_proof);
            });

            // Proses tagihan yang benar-benar BELUM DIBAYAR
            $groupedUnpaid = $unpaid->groupBy('student_detail_id');
            foreach ($groupedUnpaid as $studentDetailId => $payments) {
                $firstPayment = $payments->first();
                if (!$firstPayment) continue;

                $totalAmount = $payments->sum('amount');
                $grandTotal += $totalAmount;
                
                $unpaidBills[] = (object)[
                    'student_detail_id' => $studentDetailId, 
                    'subject_name'      => $firstPayment->studentDetail->subject->name,
                    'due_months'        => $payments->count(),
                    'total_amount'      => $totalAmount,
                ];
            }

            // Proses tagihan yang SEDANG DIPROSES
            $groupedProcessing = $processing->groupBy('student_detail_id');
            foreach ($groupedProcessing as $studentDetailId => $payments) {
                $firstPayment = $payments->first();
                if (!$firstPayment) continue;
                
                $processingBills[] = (object)[
                    'subject_name' => $firstPayment->studentDetail->subject->name,
                    'due_months'   => $payments->count() // kita tetap simpan jumlah bulan
                ];
            }
        }

        return view('dashboard', [
            'subjects' => $subjects,
            'unpaidBills' => $unpaidBills,
            'processingBills' => $processingBills, // <-- Kirim data baru ke view
            'grandTotal' => $grandTotal,
        ]);
    }

    public function showSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $enrollment = null;
        $paymentStatus = null;
        
        $user = $request->user(); // <-- Ganti Auth::check() dan Auth::user()

        if ($user) {
            // Admin, atau guru yang mengampu subject ini, selalu punya akses
            if (Gate::allows('manage-subject', $subject->id)) {
                $enrollment = true; // Dianggap sudah terdaftar
                $paymentStatus = 'verified'; // Dianggap sudah terverifikasi
            } 
            // Jika user adalah siswa, cek pendaftaran dan pembayaran mereka
            elseif ($user->student) {
                $studentId = $user->student->id;
                $enrollment = StudentDetail::where('student_id', $studentId)
                                           ->where('subject_id', $id)
                                           ->first();
                
                if ($enrollment) {
                    $hasPendingPayment = Payment::where('student_detail_id', $enrollment->id)
                                              ->where('status', 'pending')
                                              ->exists(); // ->exists() lebih efisien, langsung return true/false

                    $paymentStatus = $hasPendingPayment ? 'pending' : 'verified';
                }
            }
        }
        
        return view('subject', [
            'subject' => $subject,
            'enrollment' => $enrollment,
            'paymentStatus' => $paymentStatus,
        ]);
    }

    public function enroll(Request $request, $subjectId)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user(); // <-- Ganti Auth::user()
        $student = $user->student;
        $subject = Subject::findOrFail($subjectId);

        // Pastikan siswa belum terdaftar
        $isEnrolled = StudentDetail::where('student_id', $student->id)
                                   ->where('subject_id', $subject->id)
                                   ->exists();
        if($isEnrolled) {
            return redirect()->back()->with('error', 'You are already enrolled in this subject.');
        }

        try {
            DB::transaction(function () use ($request, $student, $subject) {
                // 1. Daftarkan siswa ke subjek
                $enrollment = StudentDetail::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'enrollment_date' => now(),
                ]);

                // 2. Simpan bukti pembayaran di disk privat (dibuka lewat route paymentProof yang dicek aksesnya)
                $filePath = $request->file('payment_proof')->store('proofs', 'local');

                // 3. Buat catatan pembayaran pertama
                Payment::create([
                    'student_detail_id' => $enrollment->id,
                    'amount' => $subject->price,
                    'month_year' => now()->format('Y-m'),
                    'payment_proof' => $filePath,
                    'status' => 'pending',
                ]);
            });
        } catch (\Throwable $th) {
            // Catat error jika perlu: \Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Enrollment failed. Please try again.');
        }

        return redirect()->route('showSubject', $subjectId)->with('success', 'Upload success! Please wait for admin verification.');
    }
    
    public function showModule(Request $request, $id)
    {
        $user = $request->user();
        $subject = Subject::with([
            'modules.practices.questions.answerKey'
        ])->findOrFail($id);

        // Tanpa ini siswa bisa membuka /module/{id} langsung tanpa mendaftar atau membayar
        if (Gate::denies('access-subject', $subject->id)) {
            return redirect()->route('showSubject', $subject->id)
                ->with('error', 'You need a verified enrollment to access this subject.');
        }

        $studentAttempts = collect();
        $allStudentAttempts = collect(); // Variabel baru untuk admin/guru

        if ($user) {
            if ($user->student) {
                // Logika untuk siswa tetap sama
                $enrollment = $user->student->studentDetails()->where('subject_id', $id)->first();
                if ($enrollment) {
                    $studentAttempts = StudentAttempt::where('student_detail_id', $enrollment->id)
                                                    ->get()
                                                    ->keyBy('question_id');
                }
            } elseif (Gate::allows('manage-subject', $subject->id)) {
                // --- LOGIKA BARU UNTUK GURU/ADMIN ---
                // 1. Dapatkan semua ID pertanyaan dalam subjek ini
                $questionIds = $subject->modules->flatMap(function ($module) {
                    return $module->practices->flatMap(function ($practice) {
                        return $practice->questions->pluck('id');
                    });
                });

                // 2. Ambil semua 'attempts' yang terkait dengan pertanyaan-pertanyaan tersebut
                // dan eager-load relasi untuk mendapatkan nama siswa
                $allStudentAttempts = StudentAttempt::whereIn('question_id', $questionIds)
                    ->with('studentDetail.student.user') // Eager load untuk performa
                    ->get()
                    ->groupBy('question_id'); // Kelompokkan berdasarkan ID pertanyaan
            }
        }
        
        return view('module', [
            'subject' => $subject,
            'studentAttempts' => $studentAttempts,
            'allStudentAttempts' => $allStudentAttempts, // Kirim data baru ke view
        ]);

    }

    public function storeSubject(Request $request)
    {
        $user = $request->user();
        if (!$user->admin && !$user->teacher) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        // 2. Validasi: Pastikan semua data yang dikirim valid
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'picture' => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:2048', // maks 2MB
        ]);

        // 3. Proses Unggah Gambar
        $path = null;
        if ($request->hasFile('picture')) {
            // Simpan gambar di dalam 'storage/app/public/subjects'
            $path = $request->file('picture')->store('subjects', 'public');
        }

        // 4. Simpan ke Database
        $subject = Subject::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'picture' => $path,
        ]);

        // Guru pembuat otomatis mengampu subject-nya, supaya bisa langsung mengisi modul
        if ($user->teacher) {
            $user->teacher->subjects()->attach($subject->id);
        }

        // 5. Redirect kembali ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'New subject has been added successfully!');
    }

    public function submitProof(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'enrollment_ids' => 'required|string', // Berisi ID pendaftaran atau 'all'
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:2048', // maks 2MB
        ]);

        $student = $request->user()->student;

        // 2. Tentukan ID pendaftaran mana yang akan dibayar, selalu dibatasi ke milik siswa ini
        //    (tanpa filter ini siswa bisa menimpa bukti bayar tagihan siswa lain lewat ID sembarang)
        $ownEnrollmentIds = $student->studentDetails()->pluck('id');
        $enrollmentIdsToUpdate = $request->enrollment_ids === 'all'
            ? $ownEnrollmentIds->all()
            : $ownEnrollmentIds->intersect(array_map('intval', explode(',', $request->enrollment_ids)))->all();

        if (empty($enrollmentIdsToUpdate)) {
            return redirect()->route('dashboard')->with('error', 'No bill found to pay.');
        }

        // 3. Simpan file bukti pembayaran di disk privat
        $filePath = $request->file('payment_proof')->store('payment_proofs', 'local');

        // 4. Update semua tagihan 'pending' yang relevan
        Payment::whereIn('student_detail_id', $enrollmentIdsToUpdate)
            ->where('status', 'pending')
            ->update([
                'payment_proof' => $filePath,
                'status' => 'pending', // Status tetap pending untuk diverifikasi admin
                'updated_at' => now()
            ]);

        return redirect()->route('dashboard')->with('success', 'Payment proof submitted! Please wait for admin verification.');
    }

    /**
     * Tampilkan bukti pembayaran hanya untuk admin atau siswa pemiliknya.
     */
    public function paymentProof(Request $request, Payment $payment)
    {
        $user = $request->user();
        $isOwner = $user->student && $payment->studentDetail?->student_id === $user->student->id;
        abort_unless($user->admin || $isOwner, 403);

        // Bukti lama tersimpan di disk public sebelum dipindah ke disk privat
        $disk = collect(['local', 'public'])->first(
            fn ($disk) => $payment->payment_proof && Storage::disk($disk)->exists($payment->payment_proof)
        );
        abort_unless($disk, 404);

        return Storage::disk($disk)->response($payment->payment_proof);
    }
}
