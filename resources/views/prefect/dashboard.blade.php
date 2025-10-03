@extends('prefect.layout')

@section('content')
<div class="main-container">

<style>
/* ======== TOOLBAR ======== */
.toolbar {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 80px;
  background: linear-gradient(90deg, #1e3a8a, #152a64);
  border-radius: 8px;
  margin-bottom: 25px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.toolbar h3 {
  font-size: 2rem;
  color: #fff;
  font-weight: 700;
  text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* ======== CARDS ======== */
.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin: 0 20px 30px 20px;
}
.card {
  border-radius: 12px;
  padding: 20px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  cursor: pointer;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.25);
}
.card h3 {
  font-size: 15px;
  margin-bottom: 6px;
  font-weight: 500;
}
.card p {
  font-size: 24px;
  font-weight: 700;
}
.card i {
  font-size: 28px;
  opacity: 0.9;
}
.card:nth-child(1) { background: linear-gradient(135deg, #0077ff, #005fcc); }
.card:nth-child(2) { background: linear-gradient(135deg, #ff3b3b, #c62828); }
.card:nth-child(3) { background: linear-gradient(135deg, #00c851, #009d3c); }

/* ======== CHART GRID ======== */
.grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 20px;
  margin: 0 20px 30px 20px;
}
.grid .card {
  flex-direction: column;
  align-items: flex-start;
  background: #fff;
  color: #111;
  border: 1px solid #eee;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  height: 300px;
}
.card-header {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}
.card-header h3 {
  font-size: 16px;
  color: #111;
}
.card-header a {
  font-size: 13px;
  text-decoration: none;
  color: #1e3a8a;
  font-weight: 600;
}
.card-header a:hover {
  text-decoration: underline;
}
#violationChart {
  max-width: 230px;
  max-height: 230px;
  margin: 0 auto;
}

/* ======== UPCOMING APPOINTMENTS ======== */
h2.section-title {
  margin: 25px 0 15px 25px;
  font-size: 18px;
  color: #111;
  font-weight: 600;
}
.cards.upcoming {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
  margin: 0 20px 40px 20px;
}
.cards.upcoming .card {
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  padding: 18px;
  border-radius: 10px;
  height: 150px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}
.cards.upcoming .card h3 {
  font-size: 16px;
  margin-bottom: 5px;
}
.cards.upcoming .card p {
  font-size: 14px;
  font-weight: 400;
}
.cards.upcoming .card i {
  align-self: flex-end;
  opacity: 0.9;
}
.cards.upcoming .card:nth-child(1) { background: linear-gradient(135deg, #00aaff, #0077cc); }
.cards.upcoming .card:nth-child(2) { background: linear-gradient(135deg, #ff9900, #e67e00); }
.cards.upcoming .card:nth-child(3) { background: linear-gradient(135deg, #ff3366, #cc0044); }
.cards.upcoming .card:nth-child(4) { background: linear-gradient(135deg, #33cc33, #249624); }

/* ======== RESPONSIVE ======== */
@media (max-width: 1100px) {
  .cards { grid-template-columns: repeat(2, 1fr); }
  .grid { grid-template-columns: 1fr; }
  .cards.upcoming { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 700px) {
  .cards { grid-template-columns: 1fr; }
  .cards.upcoming { grid-template-columns: 1fr; }
  .toolbar h3 { font-size: 1.5rem; }
}
</style>

  <!-- Toolbar -->
  <div class="toolbar">
    <h3>📊 Dashboard Overview</h3>
  </div>

  <!-- Stats Cards -->
  <div class="cards">
    <div class="card">
      <div>
        <h3>👩‍🎓 Total Students</h3>
        <p>1,248</p>
      </div>
      <i class="fas fa-user-graduate"></i>
    </div>
    <div class="card">
      <div>
        <h3>⚠️ Violations</h3>
        <p>42</p>
      </div>
      <i class="fas fa-exclamation-circle"></i>
    </div>
    <div class="card">
      <div>
        <h3>💬 Complaints</h3>
        <p>18</p>
      </div>
      <i class="fas fa-comments"></i>
    </div>
  </div>

  <!-- Chart + Table -->
  <div class="grid">
    <div class="card">
      <div class="card-header">
        <h3>📈 Violation Types</h3>
      </div>
      <canvas id="violationChart"></canvas>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>🕓 Recent Violations & Complaints</h3>
        <a href="#">View All</a>
      </div>
      <canvas id="recentChart" style="width:100%; max-width: 100%; height: 250px;"></canvas>
    </div>
  </div>

  <!-- Upcoming Appointments -->
  <h2 class="section-title">📅 Upcoming Appointments</h2>
  <div class="cards upcoming">
    <div class="card">
      <div><h3>John Doe</h3><p>Sep 25, 10:00 AM</p></div>
      <i class="fas fa-calendar-alt"></i>
    </div>
    <div class="card">
      <div><h3>Jane Smith</h3><p>Sep 26, 1:30 PM</p></div>
      <i class="fas fa-calendar-alt"></i>
    </div>
    <div class="card">
      <div><h3>Michael Lee</h3><p>Sep 27, 9:00 AM</p></div>
      <i class="fas fa-calendar-alt"></i>
    </div>
    <div class="card">
      <div><h3>Sarah Brown</h3><p>Sep 28, 11:00 AM</p></div>
      <i class="fas fa-calendar-alt"></i>
    </div>
  </div>

</div>
@endsection
