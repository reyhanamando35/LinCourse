<?php

namespace Database\Seeders;

use App\Models\AnswerKey;
use App\Models\Module;
use App\Models\Practice;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua subjek yang ada untuk diisi konten
        $subjects = Subject::all();

        if ($subjects->isEmpty()) {
            $this->command->warn('No subjects found. Skipping content seeding.');
            return;
        }

        $this->command->info('Seeding content for Modules, Practices, and Questions...');

        foreach ($subjects as $subject) {
            
            // === BUAT MODULES UNTUK SETIAP SUBJECT ===
            if ($subject->name === 'Mathematics') {
                $module1 = Module::create([
                    'subject_id' => $subject->id,
                    'title' => 'Algebra Fundamentals',
                    'content' => 'This module covers the basic principles of algebra, including variables, expressions, and linear equations. Students will learn to solve for unknowns and understand the core concepts that form the foundation of higher mathematics.',
                ]);

                // Buat Practice untuk Module Algebra
                $practice1 = Practice::create(['module_id' => $module1->id, 'title' => 'Solving Linear Equations', 'description' => 'Practice solving for x in various linear equations.']);
                
                // Buat Questions & Answer Keys untuk Practice 1
                $q1 = Question::create(['practice_id' => $practice1->id, 'content_text' => 'What is the value of x in the equation 2x + 5 = 17?']);
                AnswerKey::create(['question_id' => $q1->id, 'key_text' => 'Subtract 5 from both sides to get 2x = 12. Then, divide by 2 to get x = 6.']);

                $q2 = Question::create(['practice_id' => $practice1->id, 'content_text' => 'If 3(y - 2) = 9, what is the value of y?']);
                AnswerKey::create(['question_id' => $q2->id, 'key_text' => 'First, divide both sides by 3 to get y - 2 = 3. Then, add 2 to both sides to find y = 5.']);

            } elseif ($subject->name === 'Science') {
                $module2 = Module::create([
                    'subject_id' => $subject->id,
                    'title' => 'Introduction to Physics',
                    'content' => "Explore the fundamental laws of motion as described by Sir Isaac Newton. This module will cover concepts like inertia, force, mass, acceleration, and the principle of action-reaction. Real-world examples will be used to illustrate these foundational concepts.",
                ]);

                // Buat Practice untuk Module Physics
                $practice2 = Practice::create(['module_id' => $module2->id, 'title' => "Newton's Laws", 'description' => "Test your understanding of Newton's three laws of motion."]);
                
                $q3 = Question::create(['practice_id' => $practice2->id, 'content_text' => 'Which of Newton\'s laws is also known as the law of inertia?']);
                AnswerKey::create(['question_id' => $q3->id, 'key_text' => 'Newton\'s First Law of Motion. It states that an object will remain at rest or in uniform motion in a straight line unless acted upon by an external force.']);

                $q4 = Question::create(['practice_id' => $practice2->id, 'content_text' => 'A force of 20 N is applied to an object with a mass of 5 kg. What is the acceleration of the object?']);
                AnswerKey::create(['question_id' => $q4->id, 'key_text' => 'Using Newton\'s Second Law (F = ma), we can find the acceleration (a) by dividing the force (F) by the mass (m). So, a = 20 N / 5 kg = 4 m/s².']);

            } elseif ($subject->name === 'English') {
                $module3 = Module::create([
                    'subject_id' => $subject->id,
                    'title' => 'Grammar and Punctuation',
                    'content' => 'Master the essential rules of English grammar and punctuation to improve your writing clarity and professionalism. This module covers sentence structure, parts of speech, and the correct usage of commas, semicolons, and periods.',
                ]);
                
                $practice3 = Practice::create(['module_id' => $module3->id, 'title' => "Verb Tenses", 'description' => "Identify and use the correct verb tenses."]);

                $q5 = Question::create(['practice_id' => $practice3->id, 'content_text' => 'Choose the correct form of the verb: "Yesterday, she ______ to the store." (go/went/gone)']);
                AnswerKey::create(['question_id' => $q5->id, 'key_text' => 'The correct answer is "went," which is the simple past tense form of the verb "to go."']);
            }
            // Anda bisa tambahkan blok 'elseif' lain untuk subjek lainnya di sini
        }

        $this->command->info('Content seeding completed.');
    }
}
