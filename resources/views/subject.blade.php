<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Detail Subjek - {{ $subject->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            background-color: #ffffff !important; /* Background of the popup */
        }
    
        .swal2-confirm {
            background-color: #6A5ACD !important; /* Button background color */
            color: white !important; /* Button text color */
        }
        
    </style>
</head>
<body class="bg-gray-50">
    @if (Session::has('success'))
        <script>Swal.fire({ title: "Success!", text: "{{ Session::get('success') }}", icon: "success" });</script>
    @endif
    @if (Session::has('error'))
        <script>Swal.fire({ title: "Ooops!", text: "{{ Session::get('error') }}", icon: "error" });</script>
    @endif
    @if ($errors->any())
        <script>Swal.fire({ title: "Ooops!", text: "{{ $errors->first() }}", icon: "error" });</script>
    @endif

    @include('partials.navbar')
    
    <div class="container mx-auto px-6 py-12 md:py-20 text-center">
        <a href="{{ route('dashboard') }}" title="Kembali ke dashboard" class="absolute top-24 left-6 text-gray-500 bg-white p-2 rounded-full shadow-md hover:bg-gray-100 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div class="sm:mt-20">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ $subject->name }}</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-10">{{ $subject->description }}</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <p class="text-2xl font-semibold text-gray-800">Rp. {{ number_format($subject->price, 0, ',', '.') }}/month</p>
                
                @if($enrollment)
                    @if($paymentStatus == 'verified')
                        <a href="{{ route('showModule', ['id' => $subject->id]) }}" class="bg-green-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:bg-green-700">
                            Go to Module
                        </a>
                    @else
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
                            <p class="font-bold">Waiting for Verification</p>
                            <p>Your payment is being reviewed by an admin.</p>
                        </div>
                    @endif
                @else
                    <button type="button" data-modal-target="payment-modal" data-modal-toggle="payment-modal" class="bg-black text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:bg-gray-800 transition-colors duration-300">
                        Apply Now!
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran -->
    <div id="payment-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Proof of Payment: {{ $subject->name }}
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="payment-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Form Body -->
                <form class="p-4 md:p-5" method="POST" action="{{ route('enroll.subject', $subject->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Please transfer to <span class="font-bold">Bank A, 0123456789 a/n LinCourse</span> and upload the proof below.</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="payment_proof">Upload file</label>
                        <input name="payment_proof" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" aria-describedby="file_input_help" id="payment_proof" type="file" required>
                        <p class="mt-1 text-xs text-gray-500" id="file_input_help">PNG, JPG or JPEG (MAX. 2MB).</p>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-6 w-full">
                        Submit Payment
                    </button>
                </form>
            </div>
        </div>
    </div> 

</body>
</html>
