@extends('admin.layouts.main')

@section('body')

<div class="p-4 sm:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Subjects Management</h1>

    @if($subjects->isEmpty())
        <div class="text-center p-12 bg-white rounded-lg shadow-md">
            <p class="text-gray-500">No subjects have been created yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($subjects as $subject)
            <div class="bg-white rounded-xl overflow-hidden shadow-lg h-full flex flex-col">
                <a href="{{ route('showModule', $subject->id) }}">
                    <img class="w-full h-40 object-cover" src="{{ Storage::disk('public')->url($subject->picture) }}" alt="Illustration for {{ $subject->name }}">
                </a>
                
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $subject->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4 flex-grow">{{ Str::limit($subject->description, 80) }}</p>
                    
                    <div class="flex justify-between items-center text-sm text-gray-800 pt-3 border-t">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                            <span>{{ $subject->student_details_count }} Students</span>
                        </div>
                        <a href="{{ route('showModule', $subject->id) }}" class="font-semibold text-blue-600 hover:text-blue-800">
                            Modules &rarr;
                        </a>
                    </div>
                </div>

                {{-- === BAGIAN BARU: Tombol Aksi Admin === --}}
                <div class="flex items-center justify-end space-x-2 bg-gray-50 p-3 border-t">
                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="px-3 py-1.5 text-xs font-medium text-white bg-yellow-500 rounded-lg shadow-sm hover:bg-yellow-600 transition-colors">Edit</a>
                    <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('WARNING: Deleting this subject will also delete ALL its modules, questions, enrollments, and payments. This action cannot be undone. Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 transition-colors">Delete</button>
                    </form>
                </div>
                {{-- === AKHIR BAGIAN BARU === --}}
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection