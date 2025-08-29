<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adviser Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <link rel="stylesheet" href="{{ asset('css/adviser/dashboard.css') }}">
    
</head>
<body>
    <nav class="sidebar" role="navigation">
        <div style="text-align: center; margin-bottom: 1rem; margin-top: -1rem;">
            <img src="/images/Logo.png" alt="Logo">
            <p>ADVISER</p>
        </div>
        <ul style="list-style: none; padding: 0; margin: 0;">
          <li><a href="{{ route('adviser.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
<li><a href="{{ route('student.list') }}"><i class="fas fa-users"></i> Student List</a></li>
<li><a href="{{ route('parent.list') }}"><i class="fas fa-user-friends"></i> Parent List</a></li>
<li><a href="{{ route('violation.record') }}"><i class="fas fa-exclamation-triangle"></i> Violation Record</a></li>
<li><a href="{{ route('violation.appointment') }}"><i class="fas fa-calendar-check"></i> Violation Appointment</a></li>
<li><a href="{{ route('violation.anecdotal') }}"><i class="fas fa-clipboard-list"></i> Violation Anecdotal</a></li>
<li><a href="{{ route('complaints.all') }}"><i class="fas fa-comments"></i> Complaints</a></li>
<li><a href="{{ route('complaints.anecdotal') }}"><i class="fas fa-clipboard"></i> Complaints Anecdotal</a></li>
<li><a href="{{ route('complaints.appointment') }}"><i class="fas fa-calendar-alt"></i> Complaints Appointment</a></li>
<li><a href="{{ route('offense.sanction') }}"><i class="fas fa-gavel"></i> Offense & Sanction</a></li>
<li><a href="{{ route('adviser.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
<li><a href="{{ route('profile.settings') }}"><i class="fas fa-cog"></i> Profile Settings</a></li>
<li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>


        </ul>
    </nav>

    <main class="main-content">
        <section id="overview" class="section">
            <h2>Dashboard Overview</h2>
            <div class="dashboard-container">
                <div class="card">
                    <h3>Violation</h3>
                    <p>Details about recent violations can go here.</p>
                </div>
                <div class="card">
                    <h3>Student</h3>
                    <p>Details about student activities or updates.</p>
                </div>
                <div class="card">
                    <h3>Notification</h3>
                    <p>Recent notifications for parents or students.</p>
                </div>
                <div class="card">
                    <h3>Report</h3>
                    <p>Overview of recent reports or analytics.</p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </section>
    </main>

    <script>
        function logout() {
            const confirmation = confirm('Are you sure you want to log out?');
            if (confirmation) {
                window.location.href = '/adviser/login';
            }
        }

        // Set active link when clicked
        const links = document.querySelectorAll('.sidebar a');
        links.forEach(link => {
            link.addEventListener('click', function () {
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        const ctx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Monthly Violations',
                    data: [5, 10, 8, 15, 6, 12],
                    borderColor: '#FF0000', /* Red line */
                    backgroundColor: 'rgba(255, 0, 0, 0.1)',
                    borderWidth: 3,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { color: '#000', font: { weight: 'bold' } }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                            color: '#000',
                            font: { weight: 'bold' }
                        },
                        ticks: { color: '#000', font: { weight: 'bold' } },
                        grid: { color: '#ccc' }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Violations',
                            color: '#000',
                            font: { weight: 'bold' }
                        },
                        ticks: { color: '#000', font: { weight: 'bold' } },
                        grid: { color: '#ccc' }
                    }
                }
            }
        });
    </script>
</body>
</html>
