@extends('partials.layout')

@section('body')
    {{-- Tombol Hamburger untuk Mobile --}}
    
        

        <div class="sm:ml-80 flex-1 min-w-0 p-4 md:p-8 overflow-y-auto">
            <div id="content-display">
                <div id="welcome-content" class="content-block">
                    <h1 class="text-4xl font-bold text-gray-800">Selamat Datang!</h1>
                    <p class="mt-2 text-lg text-gray-600">Pilih modul atau latihan dari menu di sebelah kiri untuk memulai.</p>
                </div>
                @foreach($subject->modules as $module)
                    <div id="module-{{ $module->id }}" class="hidden content-block">
                        <h1 class="text-3xl font-bold text-gray-800 mb-4"> {{ $module->title }}</h1>
                        @if(!empty($module->pictures) && count($module->pictures) > 0)
                            <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($module->pictures as $picture)
                                    <div>
                                        <img class="h-auto max-w-full rounded-lg shadow-md" src="{{ asset('storage/' . $picture) }}" alt="Gambar Modul">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="prose max-w-none p-6 bg-white rounded-lg shadow">{!! nl2br(e($module->content)) !!}</div>
                    </div>
                    @foreach($module->practices as $practice)
                        <div id="practice-{{ $practice->id }}" class="hidden content-block">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-800">{{ $practice->title }}</h1>
                                    
                                    {{-- BAGIAN BARU: Tampilkan deskripsi jika tidak kosong --}}
                                    @if(!empty($practice->description))
                                        <p class="mt-1 text-md text-gray-600">{{ $practice->description }}</p>
                                    @endif
                                </div>
                                @auth
                                    @if(auth()->user()?->can('manage-subject', $subject->id))
                                        <button type="button" data-practice-id="{{ $practice->id }}" data-modal-target="add-question-modal" data-modal-toggle="add-question-modal" class="add-question-btn text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 flex-shrink-0">
                                            Add Question
                                        </button>
                                    @endif
                                @endauth
                            </div>
                            <div class="space-y-8">
                                @forelse($practice->questions as $question)
                                    <div class="p-6 bg-white rounded-lg shadow-md border border-gray-200">
                                        <div class="flex justify-between items-start mb-3">
                                            <h3 class="font-bold text-xl text-gray-800">Question {{ $loop->iteration }}</h3>
                                            @if(auth()->user()?->can('manage-subject', $subject->id))
                                            <div class="flex items-center space-x-2">
                                                <button type="button" class="edit-question-btn p-1 text-blue-500 hover:text-blue-700" title="Edit Question" data-question-id="{{ $question->id }}" data-question-text="{{ $question->content_text }}"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg></button>
                                                <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?')">@csrf @method('DELETE')<button type="submit" class="p-1 text-red-500 hover:text-red-700" title="Delete Question"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg></button></form>
                                            </div>
                                            @endif
                                        </div>
                                        @if($question->content_file)
                                            @php
                                                $fileExtension = pathinfo($question->content_file, PATHINFO_EXTENSION);
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            @endphp
                                            @if(in_array($fileExtension, $imageExtensions))
                                                <div class="mb-4">
                                                    <img src="{{ asset('storage/' . $question->content_file) }}" alt="Question Image" class="w-full max-w-lg h-auto rounded-lg border">
                                                </div>
                                            @endif
                                        @endif
                                        
                                        <div class="prose max-w-none mb-4 text-gray-700">
                                            {!! nl2br(e($question->content_text)) !!}
                                        </div>

                                        @if($question->content_file)
                                            <a href="{{ asset('storage/' . $question->content_file) }}" download class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mb-4 text-sm">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download
                                            </a>
                                        @endif

                                    @if(Auth::check() && Auth::user()->student)
                                        <div class="mt-6 border-t pt-4">
                                            <h4 class="block mb-2 text-sm font-medium text-gray-900">Your Answer</h4>

                                            @if($studentAttempts->has($question->id))
                                                @php
                                                    $attempt = $studentAttempts->get($question->id);
                                                @endphp
                                                
                                                <div class="bg-gray-100 p-4 rounded-lg border">
                                                    <div class="mb-4 p-3 rounded-lg border-2 @if($attempt->is_correct === 1) border-green-500 bg-green-100 @elseif($attempt->is_correct === 0) border-red-500 bg-red-100 @else border-yellow-500 bg-yellow-100 @endif">
                                                        <h5 class="font-bold text-sm @if($attempt->is_correct === 1) text-green-800 @elseif($attempt->is_correct === 0) text-red-800 @else text-yellow-800 @endif">
                                                            @if($attempt->is_correct === 1) Status: Correct
                                                            @elseif($attempt->is_correct === 0) Status: Incorrect
                                                            @else Status: Awaiting Grade
                                                            @endif
                                                        </h5>
                                                        @if($attempt->feedback)
                                                        <div class="mt-2 pt-2 border-t @if($attempt->is_correct === 1) border-green-300 @elseif($attempt->is_correct === 0) border-red-300 @else border-yellow-300 @endif">
                                                            <p class="text-sm font-semibold text-gray-800">Teacher's Feedback:</p>
                                                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $attempt->feedback }}</p>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    <p class="text-sm font-semibold text-gray-700 mb-2">You submitted this answer on {{ $attempt->updated_at->format('d M Y, H:i') }}.</p>
                                                    <textarea disabled rows="5" class="w-full p-2.5 text-sm text-gray-700 bg-gray-200 rounded-lg border border-gray-300 cursor-not-allowed">{{ $attempt->answer_text }}</textarea>
                                                    @if($attempt->answer_file)
                                                        <div class="mt-4"><p class="block mb-2 text-sm font-medium text-gray-900">Submitted File:</p><a href="{{ asset('storage/' . $attempt->answer_file) }}" target="_blank" class="inline-flex items-center max-w-full break-all bg-blue-100 text-blue-800 text-sm font-medium me-2 px-3 py-1.5 rounded-lg border border-blue-400 hover:bg-blue-200"><svg class="w-4 h-4 me-2 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M15.5 11.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/><path d="M19.293 12.707a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414-1.414L16.586 14H5a1 1 0 0 1 0-2h11.586l-2.707-2.707a1 1 0 0 1 1.414-1.414l4 4Z"/></svg>{{ basename($attempt->answer_file) }}</a></div>
                                                    @else
                                                        <p class="mt-4 text-sm text-gray-500 italic">No file was submitted with this answer.</p>
                                                    @endif
                                                </div>

                                                
                                                @if($question->answerKey)
                                                    <div class="mt-6">
                                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Answer Key</h4>
                                                        <div class="p-4 bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg">
                                                            <div class="prose prose-sm max-w-none text-gray-800">
                                                                {!! nl2br(e($question->answerKey->key_text)) !!}
                                                            </div>
                                                            @if($question->answerKey->key_file)
                                                                <a href="{{ asset('storage/' . $question->answerKey->key_file) }}" target="_blank" class="mt-4 inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                                    View Answer Key File
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                {{-- === END BAGIAN BARU === --}}

                                            @else
                                                {{-- Form Pengumpulan Jawaban (Fitur yang sudah ada) --}}
                                                <form action="{{ route('attempts.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                                                    <div class="bg-gray-100 p-4 rounded-lg border">
                                                        <label for="answer_text_{{ $question->id }}" class="sr-only">Your Answer</label>
                                                        <textarea name="answer_text" id="answer_text_{{ $question->id }}" rows="5" class="w-full p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Write your answer here..."></textarea>
                                                        <label for="answer_file_{{ $question->id }}" class="block mt-4 mb-2 text-sm font-medium text-gray-900">Attach File</label>
                                                        <input type="file" name="answer_file" id="answer_file_{{ $question->id }}" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                                        <button type="submit" class="mt-4 text-white bg-blue-600 hover:bg-blue-700 font-bold py-2 px-4 rounded">Submit Answer</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                    {{-- === END Student Answer Area === --}}

                                    {{-- Teacher/Admin Area --}}
                                    @if(auth()->user()?->can('manage-subject', $subject->id))
                                        <div class="mt-6 space-y-8">
                                            {{-- Form untuk SAVE/UPDATE sekarang punya ID unik --}}
                                            <div class="mt-6 border-t-2 border-dashed pt-4">
                                                <h4 class="font-semibold mb-2 text-gray-700">Set Answer Key</h4>
                                                <div class="bg-gray-200 p-4 rounded-lg">
                                                    {{-- Form untuk SAVE/UPDATE sekarang punya ID unik --}}
                                                    <form id="save-key-form-{{ $question->id }}" action="{{ route('answerkeys.store') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                                                        <textarea name="key_text" rows="5" class="w-full p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Write the answer key here...">{{ $question->answerKey->key_text ?? '' }}</textarea>
                                                        <label class="block mt-4 mb-2 text-sm font-medium">Attach Key File</label>
                                                        <input type="file" name="key_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                                        @if($question->answerKey && $question->answerKey->key_file)
                                                            <p class="mt-1 text-xs text-gray-600">Current file: <a href="{{ asset('storage/' . $question->answerKey->key_file) }}" target="_blank" class="text-blue-600 hover:underline break-all">{{ basename($question->answerKey->key_file) }}</a></p>
                                                        @endif
                                                    </form>

                                                    {{-- Wadah untuk kedua tombol aksi --}}
                                                    <div class="flex items-center gap-2 mt-4">
                                                        {{-- Tombol ini secara eksplisit men-submit form dengan ID yang sesuai --}}
                                                        <button type="submit" form="save-key-form-{{ $question->id }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-900">Save Answer Key</button>
                                                        
                                                        @if($question->answerKey)
                                                            {{-- Form untuk DELETE sekarang terpisah dan hanya membungkus tombolnya sendiri --}}
                                                            <form action="{{ route('answerkeys.destroy', $question->answerKey) }}" method="POST" onsubmit="return confirm('Delete this answer key?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700">Delete Key</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Tampilan Penilaian Siswa (Fitur yang sudah ada) --}}
                                            <div class="mt-8 pt-6 border-t-4 border-gray-300">
                                                <h4 class="text-xl font-bold text-gray-800 mb-4">Student Submissions</h4>
                                                @if($allStudentAttempts->has($question->id) && $allStudentAttempts->get($question->id)->isNotEmpty())
                                                    <div class="space-y-6">
                                                        @foreach($allStudentAttempts->get($question->id) as $studentAttempt)
                                                            <div class="p-4 border rounded-lg 
                                                                @if($studentAttempt->is_correct === 1) bg-green-50 border-green-300
                                                                @elseif($studentAttempt->is_correct === 0) bg-red-50 border-red-300
                                                                @else bg-gray-50 border-gray-300 @endif">

                                                                <div class="flex justify-between items-center mb-2">
                                                                    <p class="font-bold text-gray-700">{{ $studentAttempt->studentDetail->student->user->name ?? 'Unknown Student' }}</p>
                                                                    
                                                                    {{-- Status Penilaian --}}
                                                                    @if($studentAttempt->is_correct === 1)
                                                                        <span class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded-full">Correct</span>
                                                                    @elseif($studentAttempt->is_correct === 0)
                                                                        <span class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-full">Incorrect</span>
                                                                    @else
                                                                        <span class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-200 rounded-full">Not Graded</span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <p class="text-xs text-gray-500 mb-4">Submitted on: {{ $studentAttempt->created_at->format('d M Y, H:i') }}</p>

                                                                {{-- Jawaban Siswa --}}
                                                                <div class="mb-4 p-3 bg-white rounded border border-gray-200">
                                                                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $studentAttempt->answer_text ?: 'No text answer provided.' }}</p>
                                                                    @if($studentAttempt->answer_file)
                                                                        <a href="{{ asset('storage/' . $studentAttempt->answer_file) }}" target="_blank" class="mt-3 inline-block text-sm text-blue-600 hover:underline">View Submitted File</a>
                                                                    @endif
                                                                </div>
                                                                
                                                                {{-- Form Penilaian --}}
                                                                <form action="{{ route('attempts.grade', $studentAttempt->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="space-y-3">
                                                                        <div>
                                                                            <label for="feedback_{{ $studentAttempt->id }}" class="block mb-1 text-sm font-medium text-gray-900">Feedback (Optional)</label>
                                                                            <textarea name="feedback" id="feedback_{{ $studentAttempt->id }}" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ $studentAttempt->feedback }}</textarea>
                                                                        </div>
                                                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-3">
                                                                            <label class="text-sm font-medium text-gray-900">Mark as:</label>
                                                                            <div class="flex items-center">
                                                                                <input @if($studentAttempt->is_correct === 1) checked @endif type="radio" id="correct_{{ $studentAttempt->id }}" name="is_correct" value="1" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500">
                                                                                <label for="correct_{{ $studentAttempt->id }}" class="ms-2 text-sm font-medium text-gray-900">Correct</label>
                                                                            </div>
                                                                            <div class="flex items-center">
                                                                                <input @if($studentAttempt->is_correct === 0) checked @endif type="radio" id="incorrect_{{ $studentAttempt->id }}" name="is_correct" value="0" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500">
                                                                                <label for="incorrect_{{ $studentAttempt->id }}" class="ms-2 text-sm font-medium text-gray-900">Incorrect</label>
                                                                            </div>
                                                                            <button type="submit" class="w-full sm:w-auto sm:ml-auto px-4 py-2 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Save Grade</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-sm text-center text-gray-500 py-4">No student submissions for this question yet.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                @empty
                                    <div class="p-6 bg-white rounded-lg shadow text-center text-gray-500">
                                        <p>No questions have been added to this practice yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
@endsection

@section('script')
    <script>
        function showContent(id) {
            document.querySelectorAll('.content-block').forEach(block => block.classList.add('hidden'));
            const targetBlock = document.getElementById(id);
            if (targetBlock) {
                targetBlock.classList.remove('hidden');
                // Jika elemen content-display ada, scroll ke atas
                const contentDisplayParent = document.getElementById('content-display')?.parentElement;
                if (contentDisplayParent) {
                    contentDisplayParent.scrollTop = 0;
                }
            }
        }
        
        $(document).ready(function() {
            const modals = {
                'edit-practice-modal': new Modal(document.getElementById('edit-practice-modal')),
                'edit-question-modal': new Modal(document.getElementById('edit-question-modal')),
                // Anda bisa tambahkan modal lain di sini jika perlu dikontrol manual
            };
            $('.edit-practice-btn').on('click', function() {
                const practiceId = $(this).data('practice-id');
                const title = $(this).data('practice-title');
                const description = $(this).data('practice-description');
                
                $('#edit_practice_title').val(title);
                $('#edit_practice_description').val(description);
                $('#edit-practice-form').attr('action', '/practices/' + practiceId);

                modals['edit-practice-modal'].show();
            });

            // Handler untuk tombol edit question
            $('.edit-question-btn').on('click', function() {
                const questionId = $(this).data('question-id');
                const text = $(this).data('question-text');

                $('#edit_question_content_text').val(text);
                $('#edit-question-form').attr('action', '/questions/' + questionId);
                
                modals['edit-question-modal'].show();
            });

            // --- INI BAGIAN BARU UNTUK TOMBOL CLOSE (X) ---
            // Handler general untuk semua tombol close modal
            $('[data-modal-hide]').on('click', function() {
                const modalId = $(this).attr('data-modal-hide');
                if (modals[modalId]) {
                    modals[modalId].hide();
                }
            });
            
            // --- Skrip lama Anda yang lain ---
            $('.add-practice-btn').on('click', function() {
                var moduleId = $(this).data('module-id');
                $('#add-practice-modal #practice_module_id').val(moduleId);
            });

            $('.add-question-btn').on('click', function() {
                var practiceId = $(this).data('practice-id');
                $('#add-question-modal #question_practice_id').val(practiceId);
            });

            $('#hamburger').on('click', function() {
                $('#default-sidebar').toggleClass('-translate-x-full'); 
            });
        });
    </script>

@endsection