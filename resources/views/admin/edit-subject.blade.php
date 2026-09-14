@extends('admin.layouts.main')

@section('body')
<div class="p-4 sm:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Edit Subject: {{ $subject->name }}</h1>

    <div class="max-w-2xl">
        <div class="bg-white p-6 sm:p-8 shadow-md rounded-lg">
            <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Menggunakan space-y-6 untuk jarak vertikal yang konsisten antar elemen form --}}
                <div class="space-y-6">
                    
                    {{-- Subject Name --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Subject Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price (per month)</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $subject->price) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                        {{-- 
                            !!! PERBAIKAN UTAMA DI SINI !!!
                            Nilai untuk <textarea> diletakkan di antara tag, bukan menggunakan atribut 'value'.
                        --}}
                        <textarea id="description" name="description" rows="5" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>{{ old('description', $subject->description) }}</textarea>
                    </div>

                    {{-- Picture --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Current Picture</label>
                        <img src="{{ asset('storage/' . $subject->picture) }}" alt="Current Picture" class="w-48 h-auto rounded-lg border mb-4">

                        <label class="block mb-2 text-sm font-medium text-gray-900" for="picture">Upload New Picture (Optional)</label>
                        <input name="picture" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" id="picture" type="file">
                        <p class="mt-1 text-xs text-gray-500">Leave empty if you don't want to change the picture.</p>
                    </div>
                </div>

                {{-- Bagian tombol aksi dirapikan dengan flexbox --}}
                <div class="mt-8 flex items-center gap-4">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Update Subject</button>
                    <a href="{{ route('admin.subject') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection