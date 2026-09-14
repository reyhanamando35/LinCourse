<!-- Hamburger Style -->
<style>
    .ham {
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        transition: transform 400ms;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .hamRotate.active {
        transform: rotate(45deg);
    }

    .hamRotate180.active {
        transform: rotate(180deg);
    }

    .line {
        fill: none;
        transition: stroke-dasharray 400ms, stroke-dashoffset 400ms;
        stroke: #000;
        stroke-width: 5.5;
        stroke-linecap: round;
    }

    .ham8 .top {
        stroke-dasharray: 40 160;
    }

    .ham8 .middle {
        stroke-dasharray: 40 142;
        transform-origin: 50%;
        transition: transform 400ms;
    }

    .ham8 .bottom {
        stroke-dasharray: 40 85;
        transform-origin: 50%;
        transition: transform 400ms, stroke-dashoffset 400ms;
    }

    .ham8.active .top {
        stroke-dashoffset: -64px;
    }

    .ham8.active .middle {
        transform: rotate(90deg);
    }

    .ham8.active .bottom {
        stroke-dashoffset: -64px;
    }
</style>

<!-- Toggle Navbar Script -->
<script>
    $(document).ready(function() {
        $('#toggle').on('click', function() {
            if ($('#smallNav').hasClass('hidden')) {
                $('#smallNav').removeClass('hidden');
            } else {
                $('#smallNav').addClass('hidden');
            }
        })
    })
</script>

<!-- Side Navigation Bar (> 1024px) -->
<aside class="w-64 h-screen border-e-2 bg-white fixed hidden lg:block">
    <div class="mx-6 h-full flex flex-col">
        <!-- List Navigation -->
        <div class="mt-6">
            <ul>
                <a href="{{ route('admin.role') }}" class="text-md">
                    <li class="flex flex-row items-center p-3 my-2 hover:bg-gray-200 rounded-lg transition">
                        <i class="fa-solid fa-user mr-2"></i> Users
                    </li>
                </a>
                <a href="{{ route('admin.subject') }}" class="text-md">
                    <li class="flex flex-row items-center p-3 my-2 hover:bg-gray-200 rounded-lg transition">
                        <i class="fa-solid fa-palette mr-2"></i> Subjects
                    </li>
                </a>
                <a href="{{ route('admin.verify') }}" class="text-md">
                    <li class="flex flex-row items-center p-3 my-2 hover:bg-gray-200 rounded-lg transition">
                        <i class="fa-solid fa-cart-shopping mr-2"></i>Verify Payments
                    </li>
                </a>
                <a href="{{ route('admin.income') }}" class="text-md">
                    <li class="flex flex-row items-center p-3 my-2 hover:bg-gray-200 rounded-lg transition">
                        <i class="fa-solid fa-chart-line mr-2"></i>Income Report
                    </li>
                </a>
            </ul>
        </div>

        <!-- Admin & Logout -->
        <div class="mt-auto mb-6 p-3 hover:bg-rose-500 rounded-lg transition">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <div>
                    <p><i class="fa-solid fa-right-from-bracket mr-2"></i>Admin</p>
                    <div class="flex flex-row items-center">
                        <p class="me-2">{{ Auth::user()->name }}</p>
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</aside>

<!-- Toggle Navbar (< 1024px)-->
<!-- Navbar & Toggler -->
<div class="h-16 w-screen bg-white border-b-2 flex lg:hidden">
    <div class="ms-4 my-auto text-center my-4 w-fit flex flex-row items-center">
        <p class="font-bold me-2">{{ Auth::user()->name }}</p>
        <span class="relative flex items-center h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
    </div>
    <div id="toggle" class="ms-auto me-4 self-center active:bg-gray-200 rounded-full transition">
        <svg class="ham hamRotate ham8" viewBox="0 0 100 100" width="44" onclick="this.classList.toggle('active')">
            <path class="line top"
                d="m 30,33 h 40 c 3.722839,0 7.5,3.126468 7.5,8.578427 0,5.451959 -2.727029,8.421573 -7.5,8.421573 h -20" />
            <path class="line middle" d="m 30,50 h 40" />
            <path class="line bottom"
                d="m 70,67 h -40 c 0,0 -7.5,-0.802118 -7.5,-8.365747 0,-7.563629 7.5,-8.634253 7.5,-8.634253 h 20" />
        </svg>
    </div>
</div>

<!-- Navbar Content -->
<div id="smallNav" class="bg-white border-b-2 absolute w-full hidden z-[999] lg:hidden">
    <ul class="mx-2">
        <li class="p-4 my-2 hover:bg-gray-200 rounded-lg transition">
            <a href="{{ route('admin.role') }}" class="flex flex-row text-md items-center justify-center">
                <i class="fa-solid fa-user mr-2"></i>Users
            </a>
        </li>

        <li class="p-4 my-2 hover:bg-gray-200 rounded-lg transition">
            <a href="{{ route('admin.subject') }}" class="flex flex-row text-md items-center justify-center">
                <i class="fa-solid fa-palette mr-2"></i>Subjects
            </a>
        </li>

        <li class="p-4 my-2 hover:bg-gray-200 rounded-lg transition">
            <a href="{{ route('admin.verify') }}" class="flex flex-row text-md items-center justify-center">
                <i class="fa-solid fa-cart-shopping mr-2"></i>Verify Payments
            </a>
        </li>
        <li class="p-4 my-2 hover:bg-gray-200 rounded-lg transition">
            <a href="{{ route('admin.income') }}" class="flex flex-row text-md items-center justify-center">
                <i class="fa-solid fa-palette mr-2"></i>Income Report
            </a>
        </li>

        <li class="p-4 my-2 hover:bg-rose-500 rounded-lg transition">
            <a href="{{ route('dashboard') }}" class="flex flex-row text-md items-center justify-center">
                <i class="fa-solid fa-right-from-bracket mr-2"></i> Back to Dashboard
            </a>
        </li>
    </ul>
</div>
