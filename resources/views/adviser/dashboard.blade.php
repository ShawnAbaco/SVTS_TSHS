@extends('adviser.layout')

@section('title', 'Dashboard Overview')

@section('content')
<section id="overview">
  <div class="dashboard-overview">
    <div class="overview-card total-students">
      <h3>Total Students</h3>
      <p>1,247</p>
      <small>+12% from last month</small>
    </div>
    <div class="overview-card active-violations">
      <h3>Active Violations</h3>
      <p>23</p>
      <small>+5% from last week</small>
    </div>
    <div class="overview-card pending-complaints">
      <h3>Pending Complaints</h3>
      <p>8</p>
      <small>-3% from last week</small>
    </div>
    <div class="overview-card appointments-today">
      <h3>Appointments Today</h3>
      <p>12</p>
      <small>+8% from yesterday</small>
    </div>
  </div>

  <div class="chart-section">
    <div class="chart-card">
      <h4>Violation Types Distribution</h4>
      <canvas id="pieChart"></canvas>
    </div>
    <div class="chart-card">
      <h4>Monthly Violations Trend</h4>
      <canvas id="barChart"></canvas>
    </div>
  </div>

  <div class="recent-activities">
    <h4>Recent Activities</h4>
    <div class="activity"><i class="fas fa-exclamation-triangle"></i><p>New violation reported - Student John Doe - Behavioral violation (2 hours ago)</p></div>
    <div class="activity"><i class="fas fa-comments"></i><p>New complaint filed - Parent complaint about cafeteria service (4 hours ago)</p></div>
    <div class="activity"><i class="fas fa-calendar-check"></i><p>Appointment scheduled - Meeting with Sarah Johnson's parents (6 hours ago)</p></div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  // Pie Chart
  new Chart(document.getElementById('pieChart').getContext('2d'), {
    type:'doughnut',
    data:{
      labels:['overall','Complaints','Violations'],
      datasets:[{
        data:[12,8,3],
        backgroundColor:['#FFD700','#1E3A8A','#EF4444'],
        borderColor:'#fff',
        borderWidth:2,
        hoverOffset:10
      }]
    },
    options:{
      responsive:true,
      plugins:{ legend:{ position:'bottom', labels:{ font:{ size:12 }, padding:10 } } },
      cutout:'40%'
    }
  });

  // Bar Chart
  new Chart(document.getElementById('barChart').getContext('2d'), {
    type:'bar',
    data:{
      labels:['Jan','Feb','Mar','Apr','May','Jun'],
      datasets:[{
        label:'Monthly Violations',
        data:[15,18,12,20,25,32],
        backgroundColor:'#000',
        borderRadius:3,
        barPercentage:0.45
      }]
    },
    options:{
      responsive:true,
      plugins:{ legend:{ display:false } },
      scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true, ticks:{ stepSize:5 } } }
    }
  });
</script>
@endsection
