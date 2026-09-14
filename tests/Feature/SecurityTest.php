<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Module;
use App\Models\Payment;
use App\Models\Practice;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAttempt;
use App\Models\StudentDetail;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Setiap tes di sini mencoba satu celah yang ditemukan saat audit keamanan.
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private Subject $subject;
    private Module $module;
    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');

        $this->subject = Subject::create(['name' => 'Math', 'description' => 'd', 'picture' => 'm.png', 'price' => 100000]);
        $this->module = Module::create(['subject_id' => $this->subject->id, 'title' => 'M1', 'content' => 'c', 'pictures' => []]);
        $practice = Practice::create(['module_id' => $this->module->id, 'title' => 'P1']);
        $this->question = Question::create(['practice_id' => $practice->id, 'content_text' => 'Q1']);
    }

    // Tabel users project ini tidak punya kolom email_verified_at, jadi UserFactory bawaan tidak bisa dipakai
    private function user(): User
    {
        static $n = 0;
        return User::create(['name' => 'User'.++$n, 'email' => "user{$n}@example.com", 'password' => 'password']);
    }

    // PNG 1x1 asli; UploadedFile::fake()->image() butuh ekstensi GD yang tidak ada di PHP CLI XAMPP
    private function png(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAMAASsJTYQAAAAASUVORK5CYII='));
    }

    private function student(): User
    {
        $user = $this->user();
        Student::create(['user_id' => $user->id, 'grade' => 10]);
        return $user->refresh();
    }

    private function teacher(bool $assigned): User
    {
        $user = $this->user();
        $teacher = Teacher::create(['user_id' => $user->id, 'experience_years' => 3]);
        if ($assigned) {
            $teacher->subjects()->attach($this->subject->id);
        }
        return $user->refresh();
    }

    private function admin(): User
    {
        $user = $this->user();
        Admin::create(['user_id' => $user->id]);
        return $user->refresh();
    }

    private function enroll(User $student, string $status): Payment
    {
        $detail = StudentDetail::create(['student_id' => $student->student->id, 'subject_id' => $this->subject->id]);
        return Payment::create([
            'student_detail_id' => $detail->id, 'amount' => 100000, 'month_year' => now()->format('Y-m'),
            'status' => $status, 'payment_proof' => 'proofs/p.jpg',
        ]);
    }

    public function test_student_cannot_manage_content(): void
    {
        $student = $this->student();

        $this->actingAs($student)->post(route('modules.store'), ['subject_id' => $this->subject->id, 'title' => 'x', 'content' => 'x'])->assertForbidden();
        $this->actingAs($student)->delete(route('modules.destroy', $this->module))->assertForbidden();
        $this->actingAs($student)->post(route('answerkeys.store'), ['question_id' => $this->question->id, 'key_text' => 'x'])->assertForbidden();

        $this->assertModelExists($this->module);
    }

    public function test_teacher_can_only_manage_assigned_subjects(): void
    {
        $this->actingAs($this->teacher(assigned: false))
            ->put(route('modules.update', $this->module), ['title' => 'Hacked', 'content' => 'x'])->assertForbidden();

        $this->actingAs($this->teacher(assigned: true))
            ->put(route('modules.update', $this->module), ['title' => 'Updated', 'content' => 'x'])->assertRedirect();

        $this->assertSame('Updated', $this->module->fresh()->title);
    }

    public function test_teacher_creating_subject_is_assigned_to_it(): void
    {
        $teacher = $this->teacher(assigned: false);

        $this->actingAs($teacher)->post(route('subjects.store'), [
            'name' => 'Physics', 'description' => 'd', 'price' => 1000,
            'picture' => $this->png('p.png'),
        ])->assertRedirect();

        $this->assertTrue($teacher->teacher->subjects()->where('name', 'Physics')->exists());
    }

    public function test_module_page_requires_verified_payment(): void
    {
        $this->withoutVite();
        $outsider = $this->student();
        $pending = $this->student();
        $this->enroll($pending, 'pending');
        $paid = $this->student();
        $this->enroll($paid, 'verified');

        $this->actingAs($outsider)->get(route('showModule', $this->subject->id))->assertRedirect(route('showSubject', $this->subject->id));
        $this->actingAs($pending)->get(route('showModule', $this->subject->id))->assertRedirect(route('showSubject', $this->subject->id));
        $this->actingAs($paid)->get(route('showModule', $this->subject->id))->assertOk();
    }

    public function test_student_cannot_overwrite_other_students_payment_proof(): void
    {
        $victimPayment = $this->enroll($this->student(), 'pending');
        $attacker = $this->student();

        $this->actingAs($attacker)->post(route('payments.submit'), [
            'enrollment_ids' => (string) $victimPayment->student_detail_id,
            'payment_proof' => $this->png('fake.png'),
        ]);

        $this->assertSame('proofs/p.jpg', $victimPayment->fresh()->payment_proof);
    }

    public function test_php_file_cannot_be_uploaded_as_answer(): void
    {
        $student = $this->student();
        $this->enroll($student, 'verified');

        $this->actingAs($student)->post(route('attempts.store'), [
            'question_id' => $this->question->id,
            'answer_file' => UploadedFile::fake()->createWithContent('shell.php', '<?php system($_GET["c"]); ?>'),
        ])->assertSessionHasErrors('answer_file');

        $this->assertDatabaseCount('student_attempts', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles('attempts'));
    }

    public function test_unpaid_student_cannot_submit_answers(): void
    {
        $student = $this->student();
        $this->enroll($student, 'pending');

        $this->actingAs($student)->post(route('attempts.store'), ['question_id' => $this->question->id, 'answer_text' => 'x'])->assertForbidden();
    }

    public function test_only_assigned_teacher_can_grade(): void
    {
        $student = $this->student();
        $payment = $this->enroll($student, 'verified');
        $attempt = StudentAttempt::create(['student_detail_id' => $payment->student_detail_id, 'question_id' => $this->question->id, 'answer_text' => 'x']);

        $this->actingAs($this->teacher(assigned: false))->post(route('attempts.grade', $attempt), ['is_correct' => 1])->assertForbidden();
        $this->actingAs($this->teacher(assigned: true))->post(route('attempts.grade', $attempt), ['is_correct' => 1])->assertRedirect();

        $this->assertEquals(1, $attempt->fresh()->is_correct);
    }

    public function test_payment_proof_is_private(): void
    {
        $owner = $this->student();
        $payment = $this->enroll($owner, 'pending');
        Storage::disk('local')->put('proofs/p.jpg', 'image-bytes');

        $this->get(route('payments.proof', $payment))->assertRedirect();
        $this->actingAs($this->student())->get(route('payments.proof', $payment))->assertForbidden();
        $this->actingAs($owner)->get(route('payments.proof', $payment))->assertOk();
        $this->actingAs($this->admin())->get(route('payments.proof', $payment))->assertOk();
    }

    public function test_admin_verification_records_verifier(): void
    {
        $admin = $this->admin();
        $payment = $this->enroll($this->student(), 'pending');

        $this->actingAs($admin)->put(route('admin.payments.verify', $payment))->assertRedirect();

        $this->assertSame($admin->admin->id, $payment->fresh()->verified_by);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $this->assertModelExists($admin);
    }

    public function test_login_is_rate_limited(): void
    {
        $this->student();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('loginStudent'), ['email' => 'nobody@example.com', 'password' => 'wrong']);
        }

        $this->post(route('loginStudent'), ['email' => 'nobody@example.com', 'password' => 'wrong'])->assertStatus(429);
    }
}
