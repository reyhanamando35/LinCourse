<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LinCourse - Select Role</title>
    <!-- Link to Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Sweet Alert CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
         @keyframes scale {
            0% {
                transform: scale(1, 1) translate(-70%, -70%);
                opacity: 0.8;
            }

            20% {
                transform: scale(1.4, 1.8) translate(-50%, -50%);
                opacity: 0.5;
            }

            40% {
                transform: scale(2, 2) translate(-20%, -50%);
                opacity: 0.2;
            }

            60% {
                transform: scale(1.1, 1.2) translate(-50%, -50%);
                opacity: 0.5;
            }

            80% {
                transform: scale(1, 1) translate(-50%, -20%);
                opacity: 0.7;
            }

            100% {
                transform: scale(1, 1) translate(-70%, -70%);
                opacity: 0.8;
            }
        }

        @keyframes scale2 {
            0% {
                transform: scale(2, 2);
                opacity: 0.5;
            }

            25% {
                transform: scale(1.9, 2.1);
                opacity: 0.4;
            }

            50% {
                transform: scale(2.7, 2.9);
                opacity: 0.1;
            }

            75% {
                transform: scale(2.5, 2.1);
                opacity: 0.5;
            }

            100% {
                transform: scale(2, 2);
                opacity: 0.5;
            }
        }

        .ele-icon {
            animation: scale 4s infinite;
        }

        .ele-icon-2 {
            animation: scale2 6s infinite
        }
    </style>
</head>

<body>
    {{-- SweetAlert Notifications (optional) --}}
    @if (Session::has('success'))
        <script>
            Swal.fire({
                title: "Success!",
                text: "{{ Session::get('success') }}",
                icon: "success"
            });
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            Swal.fire({
                title: "Ooops!",
                text: "{{ Session::get('error') }}",
                icon: "error"
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({
                title: "Ooops!",
                text: "{{ $errors->first() }}",
                icon: "error"
            });
        </script>
    @endif

    <div class="w-full min-h-screen bg-blue-50">
        <div class="w-full min-h-screen flex flex-col justify-center items-center relative overflow-hidden p-2">
            <div
                class="w-[400px] h-[400px] bg-blue-100 absolute top-0 left-0 rounded-full blur ele-icon border border-black">
            </div>
            <div
                class="w-[400px] h-[400px] bg-blue-700 absolute bottom-0 right-0 rounded-full blur ele-icon-2 border border-black">
            </div>
            <h1 class="z-10 px-2 text-5xl md:text-6xl text-center font-bold mb-12">
                <span class="text-gray-800">Welcome to</span> 
                <span class="text-blue-600">LinCourse</span>
            </h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 w-full max-w-4xl z-10" id="role">
                
                <!-- Teacher Card -->
                <div class="col-span-1">
                    <a href="{{ route('loginTeacher') }}" class="w-full p-6 bg-white shadow-xl rounded-3xl border border-gray-200 hover:shadow-2xl hover:bg-gray-200 hover:scale-105 transition-all ease-in-out duration-300 flex flex-col justify-center items-center text-center">
                        <h2 class="text-3xl font-bold text-gray-800 mb-6 mt-3 ">Teacher</h2>
                        <img src="{{ asset('teacher.webp') }}"
                            class="w-full h-auto max-h-[250px] object-contain rounded-md">
                    </a>
                </div>

                <!-- Student Card -->
                <div class="col-span-1">
                    <a href="{{ route('loginStudent') }}" class="w-full p-6 bg-white shadow-xl rounded-3xl border border-gray-200 hover:shadow-2xl hover:bg-gray-200 hover:scale-105 transition-all ease-in-out duration-300 flex flex-col justify-center items-center text-center">
                        <h2 class="text-3xl font-bold text-gray-800 mb-6 mt-3 ">Student</h2>
                        <img src="{{ asset('student.webp') }}"
                            class="w-full h-auto max-h-[250px] object-contain rounded-md">
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
