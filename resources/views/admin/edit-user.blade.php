@extends('admin.layouts.main')

@section('body')
<div class="p-4 sm:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Edit User: {{ $user->name }}</h1>

    <div class="max-w-2xl">
        <div class="bg-white p-6 sm:p-8 shadow-md rounded-lg">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>

                    @if($user->student)
                    <div>
                        <label for="grade" class="block mb-2 text-sm font-medium text-gray-900">Grade</label>
                        <input type="number" name="grade" id="grade" value="{{ old('grade', $user->student->grade) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    @endif

                    @if($user->teacher)
                    <div>
                        <label for="experience_years" class="block mb-2 text-sm font-medium text-gray-900">Years of Experience</label>
                        <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $user->teacher->experience_years) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    @endif
                </div>

                <div class="mt-8 flex items-center gap-4">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Update User</button>
                    <a href="{{ route('admin.role') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection