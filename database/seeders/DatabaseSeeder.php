<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentDetail;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Data demo memakai password 'password'; jangan pernah ada di server produksi
        if (app()->isProduction()) {
            $this->command->error('DatabaseSeeder berisi akun demo dan tidak boleh dijalankan di production.');
            return;
        }

        $this->command->info('Seeding Users...');
        $adminUser = User::create(['name' => 'Fransisco', 'email' => 'admin@gmail.com', 'password' => Hash::make('password')]);
        
        $teachersData = [
            ['name' => 'Ms. Lane', 'email' => 'teacher1@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Mr. David', 'email' => 'teacher2@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Mrs. Carol', 'email' => 'teacher3@gmail.com', 'password' => Hash::make('password')],
        ];
        foreach($teachersData as $data) { Teacher::create(['user_id' => User::create($data)->id, 'experience_years' => rand(2, 10)]); }

        $studentsData = [
            ['name' => 'Owen', 'email' => 'student1@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Marlin', 'email' => 'student2@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Budi', 'email' => 'student3@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Citra', 'email' => 'student4@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Dewi', 'email' => 'student5@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Eka', 'email' => 'student6@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Fajar', 'email' => 'student7@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Gita', 'email' => 'student8@gmail.com', 'password' => Hash::make('password')],
        ];
        foreach($studentsData as $data) { Student::create(['user_id' => User::create($data)->id, 'grade' => rand(10, 12)]); }
        
        Admin::create(['user_id' => $adminUser->id]);
        $this->command->call('lincourse:demo-admin');

        // === 2. BUAT SUBJECTS ===
        $this->command->info('Seeding Subjects...');
        $subjectsData = [
            ['name' => 'Mathematics', 'description' => 'Advanced algebra, calculus, and statistics.', 'picture' => 'math.webp', 'price' => 150000.00],
            ['name' => 'Science', 'description' => 'Physics, chemistry, and biology fundamentals.', 'picture' => 'science.avif', 'price' => 200000.00],
            ['name' => 'English', 'description' => 'Literature, grammar, and composition.', 'picture' => 'english.jpg', 'price' => 125000.00],
            ['name' => 'History', 'description' => 'World history from ancient civilizations to modern times.', 'picture' => 'history.webp', 'price' => 100000.00],
            ['name' => 'Art & Design', 'description' => 'Creative drawing, painting, and digital design.', 'picture' => 'images.png', 'price' => 175000.00],
        ];
        foreach($subjectsData as $data) { Subject::create($data); }

        // Tiap guru mengampu subject tertentu (tanpa ini guru tidak bisa mengelola modul apa pun)
        $allSubjects = Subject::all();
        foreach (Teacher::all() as $i => $teacher) {
            $teacher->subjects()->attach($allSubjects->slice($i * 2, 2)->pluck('id'));
        }

        // === 3. DAFTARKAN SISWA KE SUBJECT (ENROLLMENT) ===
        $this->command->info('Seeding Enrollments...');
        $students = Student::all();
        $subjects = Subject::all();
        $enrollments = [];
        foreach ($students as $student) {
            // Setiap siswa akan didaftarkan ke 1 sampai 3 subjek secara acak
            $subjectsToEnroll = $subjects->random(rand(1, 3));
            foreach ($subjectsToEnroll as $subject) {
                $enrollmentDate = Carbon::now()->subMonths(rand(0, 12));
                $enrollments[] = StudentDetail::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'status' => 'active',
                    'created_at' => $enrollmentDate,
                    'updated_at' => $enrollmentDate,
                ]);
            }
        }
        
        // === 4. BUAT RIWAYAT PEMBAYARAN 12 BULAN ===
        $this->command->info('Seeding 12-Month Payment History...');
        foreach ($enrollments as $enrollment) {
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);

                if ($enrollment->created_at->startOfMonth()->lte($date->startOfMonth())) {
                    $status = 'pending';
                    $proof = null;
                    
                    // Logika acak untuk status pembayaran
                    $rand = rand(1, 100);
                    if ($rand <= 80) { // 80% kemungkinan sudah diverifikasi (ini akan mengisi grafik income)
                        $status = 'verified';
                        $proof = 'bukti-tf.jpg';
                    } elseif ($rand <= 90) { // 10% kemungkinan ditolak
                        $status = 'rejected';
                        $proof = 'bukti-tf2.jpeg';
                    }
                    // 10% sisanya akan tetap 'pending' dan menjadi tagihan di modal "My Bill"

                    Payment::create([
                        'student_detail_id' => $enrollment->id,
                        'amount'            => $enrollment->subject->price,
                        'month_year'        => $date->format('Y-m'),
                        'payment_proof'     => $proof,
                        'status'            => $status,
                        'verified_by'       => ($status != 'pending') ? 1 : null, // Admin ID
                        'payment_date'      => $date,
                        'created_at'        => $date,
                        'updated_at'        => $date,
                    ]);
                }
            }
        }
        $this->call([
            ContentSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully.');
    }
}