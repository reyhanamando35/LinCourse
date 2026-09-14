{{-- Modal Add Module --}}
<div id="add-module-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Add New Module to {{ $subject->name }}</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="add-module-modal">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form action="{{ route('modules.store') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Module Title</label>
                        <input type="text" name="title" id="title" class="bg-gray-50 border @error('title') border-red-500 @enderror text-gray-900 text-sm rounded-lg block w-full p-2.5" value="{{ old('title') }}" placeholder="e.g. Introduction to Algebra" required>
                    </div>
                    <div class="col-span-2">
                        <label for="content" class="block mb-2 text-sm font-medium text-gray-900">Content</label>
                        <textarea id="content" name="content" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border @error('content') border-red-500 @enderror" placeholder="Write module content here" required>{{ old('content') }}</textarea>                    
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="pictures">Upload Pictures (Multiple)</label>
                        <input name="pictures[]" accept="image/*" class="block w-full text-sm text-gray-900 border @error('pictures.*') border-red-500 @enderror rounded-lg cursor-pointer bg-gray-50" id="pictures" type="file" multiple>
                        <p class="mt-1 text-xs text-gray-500">You can select more than one image (PNG, JPG, GIF).</p>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add Module</button>
            </form>
        </div>
    </div>
</div>

<div id="add-practice-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b"><h3 class="text-lg font-semibold text-gray-900">Add New Practice</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="add-practice-modal">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>               
            </div>
            <form id="add-practice-form" action="{{ route('practices.store') }}" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="module_id" id="practice_module_id">
                <div>
                    <label for="practice_title" class="block mb-2 text-sm font-medium">Practice Title</label>
                    <input type="text" name="title" id="practice_title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                </div>
                <div>
                    <label for="practice_description" class="block mt-4 mb-2 text-sm font-medium">Description</label>
                    <textarea name="description" id="practice_description" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300"></textarea>
                </div>
                <button type="submit" class="mt-4 text-white w-full bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Add Practice</button>
            </form>
        </div>
    </div>
</div>

<div id="add-question-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Add New Question</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="add-question-modal">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button> 
            </div>
            {{-- MODIFIED FORM TO ACCEPT FILES --}}
            <form id="add-question-form" action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
                @csrf
                <input type="hidden" name="practice_id" id="question_practice_id">
                <div>
                    <label for="question_content_text" class="block mb-2 text-sm font-medium text-gray-900">Question Text</label>
                    <textarea name="content_text" id="question_content_text" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" required></textarea>
                </div>
                <div class="mt-4">
                    <label for="question_content_file" class="block mb-2 text-sm font-medium text-gray-900">Attach Image/File (Optional)</label>
                    <input type="file" name="content_file" id="question_content_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
                    <p class="mt-1 text-xs text-gray-500">Allowed file types: JPG, PNG, PDF, etc.</p>
                </div>
                <button type="submit" class="mt-4 text-white w-full bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Add Question</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal View Users --}}
<div id="view-users-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
    <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
        <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                <h3 class="text-lg font-semibold text-gray-900">Enrolled Students in {{ $subject->name }}</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="view-users-modal">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="overflow-y-auto" style="max-height: 60vh;">
                <ul class="space-y-3">
                    @forelse($subject->students as $student)
                    <li class="p-3 bg-gray-100 rounded-lg flex items-center justify-between">
                        <span class="font-medium text-gray-800">{{ $student->user->name }}</span>
                        <span class="text-sm text-gray-500">Grade {{ $student->grade }}</span>
                    </li>
                    @empty
                    <li class="text-center text-gray-500 p-4">No students are currently enrolled in this subject.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
<div id="edit-practice-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">
                    Edit Practice
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="edit-practice-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="edit-practice-form" method="POST" class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="edit_practice_title" class="block mb-2 text-sm font-medium text-gray-900">Title</label>
                        <input type="text" name="title" id="edit_practice_title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>
                    <div>
                        <label for="edit_practice_description" class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                        <textarea name="description" id="edit_practice_description" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Update Practice
                </button>
            </form>
        </div>
    </div>
</div>

<div id="edit-question-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">
                    Edit Question
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="edit-question-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="edit-question-form" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                @csrf
                {{-- Karena route-nya POST, kita tidak perlu @method('PUT') atau spoofing lainnya --}}
                
                <div class="space-y-4">
                    <div>
                        <label for="edit_question_content_text" class="block mb-2 text-sm font-medium text-gray-900">Question</label>
                        <textarea name="content_text" id="edit_question_content_text" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    </div>
                    <div>
                        <label for="edit_question_content_file" class="block mb-2 text-sm font-medium text-gray-900">Attach New File (Optional)</label>
                        <input type="file" name="content_file" id="edit_question_content_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        <p class="mt-1 text-xs text-gray-500">Leave empty if you don't want to change the file.</p>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Update Question
                </button>
            </form>
        </div>
    </div>
</div>