<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lin Course</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

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
<body class="bg-gray-100">
    @if (Session::has('success'))
        <script>
            Swal.fire({
                title: "Success!",
                text: @json(Session::get('success')),
                icon: "success"
            });
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            Swal.fire({
                title: "Ooops!",
                text: @json(Session::get('error')),
                icon: "error"
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            Swal.fire({
                title: "Ooops!",
                text: @json($errors->first()),
                icon: "error"
            });
        </script>
    @endif

    @include('partials.navbar')
    <div class="px-4 sm:px-10 lg:px-20 py-6 sm:py-10">
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-10">
            <div class="flex items-center mb-4 sm:mb-0">
                <p class="text-4xl font-bold">Subjects</p>
                @auth
                    @if(Auth::user()->admin || Auth::user()->teacher)
                        <button type="button" data-modal-target="add-subject-modal" data-modal-toggle="add-subject-modal" class="ml-4 bg-blue-800 hover:bg-blue-900 text-white font-bold text-2xl w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition-colors duration-300">
                            +
                        </button>
                    @endif
                @endauth
            </div>
            <div>
                @auth
                    @if(Auth::user()->student)
                        <button type="button" id="open-my-bill-btn" data-modal-target="my-bill-modal" class="bg-gray-700 hover:bg-gray-800 text-white text-lg font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300">
                            My Bill
                        </button>
                    @endif
                    @if(Auth::user()->admin)
                        <a href="{{ route('admin.role') }}" class="bg-gray-700 hover:bg-gray-800 ml-4 text-white text-lg font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300">
                            Manage
                        </a>
                    @endif
                @endauth
            </div>
        </div>
        <div class="sm:px-6 py-4 sm:py-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @foreach ($subjects as $subject)
                    <a href="{{ route('showSubject', ['id' => $subject->id]) }}" class="group block transform transition-transform duration-300 hover:scale-105">
                        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg h-full flex flex-col">
                            <div class="p-6 flex justify-between items-start">
                                    <h3 class="font-bold text-2xl text-white">{{ $subject->name }}</h3>
                            </div>
                            
                            <div class="mt-auto">
                                <img class="w-full h-48 object-cover" src="{{ Storage::disk('public')->url($subject->picture) }}" alt="Illustration for {{ $subject->name }}">
                            </div>
                        </div>
                    </a>
                @endforeach
                
            </div>
        </div>
    </div>
    @auth
        @if(Auth::user()->student)
        <div id="my-bill-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-xl font-semibold text-gray-900">
                            My Pending Bills
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="my-bill-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-4 md:p-5 space-y-4">
                        {{-- Cek jika tidak ada tagihan sama sekali --}}
                        @if (empty($unpaidBills) && empty($processingBills))
                            <p class="text-center text-gray-500 py-4">You have no pending bills. Great job!</p>
                        @else
                            {{-- Tampilkan tagihan yang sedang diproses --}}
                            @foreach ($processingBills as $bill)
                                <div class="flex justify-between items-center p-3 bg-yellow-100 border-l-4 border-yellow-400 rounded-lg">
                                    <div>
                                        <p class="font-bold text-yellow-800">{{ $bill->subject_name }}</p>
                                        <p class="text-sm text-yellow-700">Payment for {{ $bill->due_months }} month(s) is being processed.</p>
                                    </div>
                                    <span class="text-sm font-semibold text-yellow-800">Processing</span>
                                </div>
                            @endforeach

                            {{-- Tampilkan tagihan yang belum dibayar --}}
                            @foreach ($unpaidBills as $bill)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $bill->subject_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $bill->due_months }} month(s) due - Total: Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <button type="button" 
                                        class="pay-button px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                        data-enrollment-ids="{{ $bill->student_detail_id }}"
                                        data-subject-name="{{ $bill->subject_name }}"
                                        data-total-amount="{{ $bill->total_amount }}">
                                        Pay Now
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    {{-- Tampilkan footer HANYA jika ada tagihan yang belum dibayar --}}
                    @if (!empty($unpaidBills))
                    <div class="flex items-center justify-between p-4 md:p-5 border-t border-gray-200 rounded-b">
                        <div>
                            <p class="text-lg font-bold">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                        </div>
                        <button type="button" 
                            class="pay-button text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            data-enrollment-ids="all"
                            data-subject-name="All Subjects"
                            data-total-amount="{{ $grandTotal }}">
                            Pay All
                        </button>
                    </div>
                    {{-- Tampilkan pesan ini jika SEMUA tagihan sudah dibayar dan sedang diproses --}}
                    @elseif (!empty($processingBills) && empty($unpaidBills))
                    <div class="p-4 md:p-5 border-t border-gray-200 rounded-b text-center">
                        <p class="text-gray-600 font-semibold">All payments are being processed. Please wait for admin verification.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div id="payment-proof-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900" id="payment-modal-title">Proof of Payment</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="payment-proof-modal"><svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg><span class="sr-only">Close modal</span></button>
                    </div>
                    <form class="p-4 md:p-5" method="POST" action="{{ route('payments.submit') }}" enctype="multipart/form-data">
                        @csrf
                        {{-- Input tersembunyi ini akan diisi oleh JavaScript --}}
                        <input type="hidden" name="enrollment_ids" id="payment-enrollment-ids">
                        
                        <div class="mb-4">
                            <p class="text-gray-800">You are about to pay a total of <span class="font-bold text-lg" id="payment-modal-amount"></span>.</p>
                            <p class="text-sm text-gray-600 mt-2">Please transfer to <span class="font-bold">Bank A, 0123456789 a/n LinCourse</span> and upload the proof below.</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="payment_proof">Upload file</label>
                            <input name="payment_proof" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" required>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, JPEG (MAX. 2MB).</p>
                        </div>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-6 w-full">Submit Payment Proof</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @if(Auth::user()->admin || Auth::user()->teacher)
        <div id="add-subject-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">Add New Subject</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="add-subject-modal">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('subjects.store') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Subject Name</label>
                                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="e.g. Advanced Physics" required>
                            </div>
                            <div class="col-span-2">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price (per month)</label>
                                <input type="number" name="price" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="e.g. 150000" required>
                            </div>
                            <div class="col-span-2">
                                <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                                <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" placeholder="Write subject description here" required></textarea>                    
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900" for="picture">Upload Picture</label>
                                <input name="picture" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" id="picture" type="file" required>
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, WEBP, atau AVIF (MAX. 2MB).</p>
                            </div>
                        </div>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Add Subject
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endauth
    <script>
        $(document).ready(function() {
            // Ambil elemen HTML dari kedua modal
            const myBillModalEl = document.getElementById('my-bill-modal');
            const paymentProofModalEl = document.getElementById('payment-proof-modal');

            // Pastikan elemen ada sebelum membuat instance (untuk menghindari error jika user bukan student)
            if (myBillModalEl && paymentProofModalEl) {
                const modalOptions = {
                    placement: 'center',
                    backdrop: 'dynamic',
                    closable: true,
                };
                
                // Buat instance objek Modal dari Flowbite untuk kita kontrol
                const myBillModal = new Modal(myBillModalEl, modalOptions);
                const paymentProofModal = new Modal(paymentProofModalEl, modalOptions);

                // Handler untuk tombol "My Bill" utama
                $('#open-my-bill-btn').on('click', function() {
                    myBillModal.show();
                });

                // Handler untuk tombol "Pay Now" dan "Pay All"
                $('.pay-button').on('click', function() {
                    const enrollmentIds = $(this).data('enrollment-ids');
                    const subjectName = $(this).data('subject-name');
                    const totalAmount = $(this).data('total-amount');

                    const formattedAmount = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(totalAmount);

                    $('#payment-modal-title').text('Payment for: ' + subjectName);
                    $('#payment-modal-amount').text(formattedAmount);
                    $('#payment-enrollment-ids').val(enrollmentIds);

                    myBillModal.hide();
                    paymentProofModal.show();
                });

                // ========================================================
                // === BAGIAN BARU: Handler untuk tombol close 'X' manual ===
                // ========================================================
                // Pilih semua tombol yang memiliki atribut data-modal-hide
                $('[data-modal-hide]').on('click', function() {
                    // Dapatkan ID modal target dari atribut tombol 'X'
                    const modalId = $(this).attr('data-modal-hide');

                    // Cek ID dan tutup modal yang sesuai
                    if (modalId === 'my-bill-modal') {
                        myBillModal.hide();
                    } else if (modalId === 'payment-proof-modal') {
                        paymentProofModal.hide();
                    }
                });
            }
        });
    </script>
</body>
</html>