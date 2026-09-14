@extends('admin.layouts.main')

@section('body')
<div class="p-4 sm:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Income Report</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <p class="text-gray-600 mb-4">Verified monthly income for the last 12 months.</p>
        <canvas id="incomeChart"></canvas>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    
    const labels = {!! json_encode($labels) !!};
    const data = {!! json_encode($data) !!};

    const chartData = {
        labels: labels,
        datasets: [{
            label: 'Monthly Income (IDR)',
            backgroundColor: 'rgba(75, 128, 242, 0.2)',
            borderColor: 'rgba(75, 128, 242, 1)',
            borderWidth: 2,
            tension: 0.3, 
            pointBackgroundColor: 'rgba(75, 128, 242, 1)',
            data: data,
        }]
    };

    const config = {
        type: 'line', 
        data: chartData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        },
    };

    const incomeChart = new Chart(
        document.getElementById('incomeChart'),
        config
    );
</script>
@endsection