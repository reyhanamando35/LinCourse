<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\StudentDetail;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin mengelola semua subject; guru hanya subject yang ditugaskan kepadanya (tabel teacher_subjects)
        Gate::define('manage-subject', function (User $user, int $subjectId) {
            if ($user->admin) {
                return true;
            }

            return $user->teacher
                && $user->teacher->subjects()->where('subjects.id', $subjectId)->exists();
        });

        // Siswa hanya bisa membuka materi setelah terdaftar DAN pembayarannya diverifikasi
        Gate::define('access-subject', function (User $user, int $subjectId) {
            if (Gate::forUser($user)->allows('manage-subject', $subjectId)) {
                return true;
            }

            if (!$user->student) {
                return false;
            }

            $enrollment = StudentDetail::where('student_id', $user->student->id)
                ->where('subject_id', $subjectId)
                ->first();

            return $enrollment
                && !Payment::where('student_detail_id', $enrollment->id)->where('status', 'pending')->exists();
        });
    }
}
