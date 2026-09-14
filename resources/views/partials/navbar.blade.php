<div class="bg-gray-800 text-white">
    <div class="flex border-b border-gray-500 items-center" style="min-height: 72.8px">
        <div class="flex w-5/6 items-center">
            <img src="{{ asset('pfp.jpg') }}" alt="User Image" width="50" height="50" class="ml-4 hidden sm:block rounded-full">
            
            {{-- Gunakan helper Auth untuk mendapatkan nama user yang login --}}
            @auth
                <p class="ml-3 font-bold text-2xl">Welcome, {{ Auth::user()->name }}</p>
            @else
                <p class="ml-3 font-bold text-2xl">Welcome, Guest</p>
            @endauth
        </div>
        <div class="flex w-1/6 items-center justify-end">
            
            {{-- @guest directive untuk menampilkan konten jika user belum login --}}
            @guest
                <a href="{{ route('index') }}" type="button" class="flex items-center mr-4 focus:outline-none text-white focus:ring-2 
                font-medium rounded-lg text-sm px-4 py-3 sm:px-4 sm:py-1 bg-blue-600 hover:bg-blue-700 
                focus:ring-blue-900">
                    {{-- Ganti dengan SVG atau icon login jika perlu --}}
                    <svg class="w-5 h-5 mr-2 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    <p>Login</p>
                </a>
            @endguest

            {{-- @auth directive untuk menampilkan konten jika user sudah login --}}
            @auth
                {{-- Logout harus menggunakan form dengan method POST untuk keamanan (CSRF) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center mr-4 focus:outline-none text-white focus:ring-2 
                    font-medium rounded-lg text-sm px-4 py-2 bg-red-600 hover:bg-red-700 
                    focus:ring-red-900">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right mr-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                        <p>Logout</p>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>