<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Module - {{ $subject->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .swal2-popup {
            background-color: #ffffff !important;
        }
    
        .swal2-confirm {
            background-color: #6A5ACD !important;
            color: white !important;
        }
        .content-area, .sidebar-area { height: calc(100vh - 72.8px); }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50">
     @if (Session::has('success'))
        <script>Swal.fire({ title: "Success!", text: "{{ Session::get('success') }}", icon: "success" });</script>
    @endif
    @if (Session::has('error'))
        <script>Swal.fire({ title: "Ooops!", text: "{{ Session::get('error') }}", icon: "error" });</script>
    @endif
    @if ($errors->any())
        <script>Swal.fire({ title: "Ooops!", html: `{!! implode('<br>', $errors->all()) !!}`, icon: "error" });</script>
    @endif
    <button id="hamburger" data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
        </svg>
    </button>
 
    <div class="flex">
    @include('partials.sidenav')
    @yield('body')
    </div>
    @auth
        @if(Auth::user()->admin || Auth::user()->teacher)
            @include('partials.modal')
        @endif
    @endauth
    @yield('script')
</body>
</html>