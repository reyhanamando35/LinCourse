@extends('admin.layouts.main')

@section('body')

<div class="p-4 sm:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Payment Verification</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase">
                    <tr>
                        <th scope="col" class="px-3 md:px-6 py-3">Student Name</th>
                        <th scope="col" class="px-3 md:px-6 py-3">Subject</th>
                        <th scope="col" class="hidden md:table-cell px-3 md:px-6 py-3">Month</th>
                        <th scope="col" class="px-3 md:px-6 py-3">Status</th>
                        <th scope="col" class="px-3 md:px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop melalui setiap data pembayaran dari controller --}}
                    @forelse ($payments as $payment)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 md:px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $payment->studentDetail->student->user->name }}
                        </td>
                        <td class="px-3 md:px-6 py-4">
                            {{ $payment->studentDetail->subject->name }}
                            <span class="block md:hidden text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->month_year)->format('M Y') }}</span>
                        </td>
                        <td class="hidden md:table-cell px-3 md:px-6 py-4">
                            {{ \Carbon\Carbon::parse($payment->month_year)->format('F Y') }}
                        </td>
                        <td class="px-3 md:px-6 py-4">
                            @if ($payment->status == 'pending')
                                <span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                            @elseif ($payment->status == 'verified')
                                <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Verified</span>
                            @else
                                <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Rejected</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-4 text-center">
                            @if ($payment->status == 'pending' && !empty($payment->payment_proof))
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-2">
                                    <a href="{{ route('payments.proof', $payment) }}" target="_blank" class="inline-block whitespace-nowrap text-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        Proof
                                    </a>
                                    
                                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="w-full sm:w-auto whitespace-nowrap text-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">Verify</button>
                                    </form>

                                    <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="w-full sm:w-auto whitespace-nowrap text-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">Reject</button>
                                    </form>
                                </div>
                            @elseif ($payment->status == 'pending' && empty($payment->payment_proof))
                                <span class="text-xs text-gray-500 italic">No proof uploaded yet.</span>

                            @else
                                <span class="text-gray-400 italic">No action required</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-6 text-gray-500">
                            No payment records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
