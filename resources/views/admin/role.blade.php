@extends('admin.layouts.main')

@section('body')
<div class="p-4 sm:p-8 space-y-10">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Students List</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Grade</th>
                            <th scope="col" class="px-6 py-3">Joined On</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $student->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $student->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $student->grade }}</td>
                            <td class="px-6 py-4">
                                @if($student->studentDetails->isNotEmpty())
                                    {{ $student->studentDetails->first()->created_at->format('d F Y') }}
                                @else
                                    <span class="text-gray-400 italic">Not enrolled yet</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{-- GANTI TAMPILAN AKSI DENGAN TOMBOL --}}
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $student->user->id) }}" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition-colors">Edit</a>
                                    
                                    <form action="{{ route('admin.users.makeAdmin', $student->user->id) }}" method="POST" onsubmit="return confirm('Are you sure? Promoting this user to admin will remove their student role.');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 transition-colors">Make Admin</button>
                                    </form>

                                    <form action="{{ route('admin.users.destroy', $student->user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-6 text-gray-500">No student data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Teachers List</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Experience</th>
                            <th scope="col" class="px-6 py-3">Joined On</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $teacher->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $teacher->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $teacher->experience_years }} year(s)</td>
                            <td class="px-6 py-4">{{ $teacher->created_at->format('d F Y') }}</td>
                            <td class="px-6 py-4">
                                {{-- GANTI TAMPILAN AKSI DENGAN TOMBOL --}}
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $teacher->user->id) }}" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition-colors">Edit</a>
                                    
                                    <form action="{{ route('admin.users.makeAdmin', $teacher->user->id) }}" method="POST" onsubmit="return confirm('Are you sure? Promoting this user to admin will remove their teacher role.');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 transition-colors">Make Admin</button>
                                    </form>

                                    <form action="{{ route('admin.users.destroy', $teacher->user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-6 text-gray-500">No teacher data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection