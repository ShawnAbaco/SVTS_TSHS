@extends('adviser.layout')

@section('content')
<div class="main-container">
    <style>
    .toolbar {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 80px;
    }

    .toolbar h3 {
        font-size: 2rem;
        color: #4b0000;
        font-weight: 600;
        text-align: center;
    }
    </style>

    <!-- Toolbar -->
    <div class="toolbar">
        <h3>Dashboard Overview</h3>
    </div>

    <!-- Stats Cards -->
    <div class="cards">
        <div class="card">
            <div>
                <h3>Total Students</h3>
                <p>{{ $totalStudents ?? 0 }}</p>
            </div>
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="card">
            <div>
                <h3>Violations</h3>
                <p>{{ $totalViolations ?? 0 }}</p>
            </div>
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="card">
            <div>
                <h3>Complaints</h3>
                <p>{{ $totalComplaints ?? 0 }}</p>
            </div>
            <i class="fas fa-comments"></i>
        </div>
    </div>

    <!-- Chart + Table -->
    <div class="grid">
        <div class="card">
            <div class="card-header">
                <h3>Violation Types</h3>
            </div>
            <div class="chart-container">
                @if(isset($violationTypes) && $violationTypes->where('count', '>', 0)->count() > 0)
                <canvas id="violationChart"></canvas>
                @else
                <div class="no-data-message">
                    <i class="fas fa-chart-pie" style="font-size: 48px; color: #bdc3c7; margin-bottom: 15px;"></i>
                    <p style="color: #7f8c8d; font-size: 16px; text-align: center;">No violation data available</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Recent Activity (Last 7 Days)</h3>
                <a href="#">View All</a>
            </div>
            <div class="chart-container">
                <canvas id="recentChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Chart.js Doughnut - Violation Types (only if there's data)
    @if(isset($violationTypes) && $violationTypes->where('count', '>', 0)->count() > 0)
    const ctx = document.getElementById('violationChart').getContext('2d');

    const violationLabels = {!! json_encode($violationTypes->where('count', '>', 0)->pluck('offense_type')) !!};
    const violationData = {!! json_encode($violationTypes->where('count', '>', 0)->pluck('count')) !!};

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: violationLabels,
            datasets: [{
                data: violationData,
                backgroundColor: [
                    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4',
                    '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F',
                    '#BB8FCE', '#85C1E9', '#F8C471', '#82E0AA'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '50%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 10,
                            weight: '200'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(44, 62, 80, 0.9)',
                    titleFont: { size: 12, weight: 'normal' },
                    bodyFont: { size: 13, weight: '600' },
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} violations (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
    @endif

    // Recent Violations Line Chart
    const recentCtx = document.getElementById('recentChart').getContext('2d');

    // Safe data access with defaults
    const recentDates = {!! isset($recentActivity['dates']) ? json_encode($recentActivity['dates']) : '[]' !!};
    const violationData = {!! isset($recentActivity['violations']) ? json_encode($recentActivity['violations']) : '[]' !!};
    const complaintData = {!! isset($recentActivity['complaints']) ? json_encode($recentActivity['complaints']) : '[]' !!};

    new Chart(recentCtx, {
        type: 'line',
        data: {
            labels: recentDates,
            datasets: [
                {
                    label: 'Violations',
                    data: violationData,
                    borderColor: '#FF6B6B',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#FF6B6B',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Complaints',
                    data: complaintData,
                    borderColor: '#4ECDC4',
                    backgroundColor: 'rgba(78, 205, 196, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#4ECDC4',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(44, 62, 80, 0.9)',
                    titleFont: { size: 12 },
                    bodyFont: { size: 13, weight: '600' },
                    padding: 12
                }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection