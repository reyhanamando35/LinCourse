<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-80 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
            <div class="h-full flex flex-col px-3 py-4 overflow-y-auto bg-gray-800 pt-20 sm:pt-4">
                <div class="p-4 mb-4 border-b border-gray-600">
                    <h1 class="text-2xl font-bold text-white">{{ $subject->name }}</h1>
                    <p class="text-sm text-gray-400">Daftar Modul & Latihan</p>
                </div>
                
                <ul class="space-y-2 font-medium flex-1">
                    @foreach($subject->modules as $module)
                    <li class="group/module">
                        {{-- Module Item --}}
                        <div class="flex items-center p-2 rounded-lg text-white hover:bg-gray-700">
                            <a href="#" onclick="showContent('module-{{ $module->id }}')" class="flex-grow"><span class="ms-3 font-semibold">{{ $loop->iteration }}. {{ $module->title }}</span></a>
                            @if(auth()->user()?->can('manage-subject', $subject->id))
                            <div class="flex items-center sm:opacity-0 sm:group-hover/module:opacity-100 transition-opacity">
                                <a href="{{ route('modules.edit', $module) }}" class="p-1 text-blue-400 hover:text-white" title="Edit Module"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg></a>
                                <form action="{{ route('modules.destroy', $module) }}" method="POST" onsubmit="return confirm('Delete this module and all its contents?')">@csrf @method('DELETE')<button type="submit" class="p-1 text-red-400 hover:text-white" title="Delete Module"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg></button></form>
                            </div>
                            @endif
                        </div>
                        {{-- Practice Dropdown --}}
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-300 transition rounded-lg group hover:bg-gray-700" data-collapse-toggle="dropdown-module-{{ $module->id }}">
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Latihan</span>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" d="m1 1 4 4 4-4"/></svg>
                        </button>
                        <ul id="dropdown-module-{{ $module->id }}" class="hidden py-2 space-y-2">
                            @foreach($module->practices as $practice)
                            <li class="group/practice flex items-center pr-2">
                                <a href="#" onclick="showContent('practice-{{ $practice->id }}')" class="flex-grow flex items-center w-full p-2 text-gray-300 transition pl-11 rounded-lg group hover:bg-gray-700">{{ $practice->title }}</a>
                                @if(auth()->user()?->can('manage-subject', $subject->id))
                                <div class="flex items-center sm:opacity-0 sm:group-hover/practice:opacity-100 transition-opacity">
                                    <button type="button" class="edit-practice-btn p-1 text-blue-400 hover:text-white" title="Edit Practice" data-practice-id="{{ $practice->id }}" data-practice-title="{{ $practice->title }}" data-practice-description="{{ $practice->description }}"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg></button>
                                    <form action="{{ route('practices.destroy', $practice) }}" method="POST" onsubmit="return confirm('Delete this practice and all its questions?')">@csrf @method('DELETE')<button type="submit" class="p-1 text-red-400 hover:text-white" title="Delete Practice"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg></button></form>
                                </div>
                                @endif
                            </li>
                            @endforeach
                            @if(auth()->user()?->can('manage-subject', $subject->id))
                            <li><button type="button" data-module-id="{{ $module->id }}" data-modal-target="add-practice-modal" data-modal-toggle="add-practice-modal" class="add-practice-btn flex items-center w-full p-2 text-blue-400 transition pl-11 rounded-lg hover:bg-gray-700 hover:text-white font-semibold">+ Add Practice</button></li>
                            @endif
                        </ul>
                    </li>
                    @endforeach
                </ul>

                @auth
                @if(auth()->user()?->can('manage-subject', $subject->id))
                <div class="pt-4 mt-4 border-t border-gray-600">
                    <button type="button" data-modal-target="add-module-modal" data-modal-toggle="add-module-modal" class="w-full flex items-center justify-center p-2 text-base text-white bg-blue-600 rounded-lg hover:bg-blue-700 group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span class="ms-2">Add Module</span>
                    </button>
                </div>
                @endif
                @endauth

                <div class="pt-4 mt-4 border-t border-gray-600 space-y-2">
                    @auth
                    @if(auth()->user()?->can('manage-subject', $subject->id))
                    <button type="button" data-modal-target="view-users-modal" data-modal-toggle="view-users-modal" class="flex items-center p-2 text-base text-white rounded-lg hover:bg-gray-700 group w-full">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 18"><path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/></svg>
                        <span class="ms-3">Users</span>
                    </button>
                    @endif
                    @endauth
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-base text-white rounded-lg hover:bg-gray-700 group">
                         <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="ms-3">Kembali ke Dashboard</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-2 text-base text-red-400 rounded-lg hover:bg-red-500 hover:text-white group">
                           <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/></svg>
                            <span class="ms-3">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>