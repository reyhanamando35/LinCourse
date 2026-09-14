@extends('partials.layout') 

@section('body')
<div class="sm:ml-80 container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6">Edit Module: {{ $module->title }}</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('modules.update', $module->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Module Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $module->title) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>
                <div>
                    <label for="content" class="block mb-2 text-sm font-medium text-gray-900">Content</label>
                    <textarea id="content" name="content" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>{{ old('content', $module->content) }}</textarea>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Current Pictures</label>
                    <div class="flex flex-wrap gap-4 mb-4">
                        @foreach($module->pictures as $picture)
                            <img src="{{ asset('storage/' . $picture) }}" class="h-24 w-auto rounded">
                        @endforeach
                    </div>
                    <label class="block mb-2 text-sm font-medium text-gray-900" for="pictures">Upload New Pictures (Optional)</label>
                    <input name="pictures[]" type="file" multiple class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
                    <p class="mt-1 text-xs text-gray-500">Uploading new pictures will replace all old ones.</p>
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Update Module</button>
                <a href="{{ route('showModule', $module->subject_id) }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    </script>
@endsection