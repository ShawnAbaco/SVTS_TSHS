@extends('adviser.NewAdviser.layout')

@section('content')
<div class="main-container">

  <!-- Simplified Header -->
  <div class="dashboard-header">
    <div class="header-content">
      <h1><i class="fas fa-chart-line"></i> Analytics Dashboard</h1>
      <p>Violation tracking and student behavior insights</p>
    </div>
    <div class="header-actions">
      <div class="date-display">
        <i class="fas fa-calendar-alt"></i>
        <span id="currentPeriod">This Week</span>
      </div>
      <button class="btn-refresh" onclick="refreshData()">
        <i class="fas fa-sync-alt"></i>
      </button>
    </div>
  </div>

  <!-- Time Period Toggle -->
  <div class="period-toggle">
    <button class="period-btn active" data-period="weekly">Weekly</button>
    <button class="period-btn" data-period="monthly">Monthly</button>
    <button class="period-btn" data-period="yearly">Yearly</button>
  </div>

  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>{{ $totalStudents }}</h3>
        <p>Total Students</p>
        <span class="stat-change">+{{ $weeklyStudents ?? 0 }} this week</span>
      </div>
    </div>

    <div class="stat-card warning">
      <div class="stat-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="stat-info">
        <h3>{{ $totalViolations }}</h3>
        <p>Active Violations</p>
        <span class="stat-change">+{{ $weeklyViolations ?? 0 }} this week</span>
      </div>
    </div>

    <div class="stat-card info">
      <div class="stat-icon">
        <i class="fas fa-clock"></i>
      </div>
      <div class="stat-info">
        <h3>{{ $pendingViolations ?? 0 }}</h3>
        <p>Pending</p>
        <span class="stat-change">Awaiting action</span>
      </div>
    </div>

    <div class="stat-card success">
      <div class="stat-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="stat-info">
        <h3>{{ $resolvedViolations ?? 0 }}</h3>
        <p>Resolved</p>
        <span class="stat-change">Completed cases</span>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="charts-section">
    <div class="chart-container">
      <div class="chart-header">
        <h3><i class="fas fa-chart-line"></i> Violation Trends</h3>
        <div class="chart-tabs">
          <button class="chart-tab active" data-trend="weekly">Week</button>
          <button class="chart-tab" data-trend="monthly">Month</button>
          <button class="chart-tab" data-trend="yearly">Year</button>
        </div>
      </div>
      <div class="chart-wrapper">
        <canvas id="trendChart"></canvas>
      </div>
    </div>

    <div class="chart-container">
      <div class="chart-header">
        <h3><i class="fas fa-chart-pie"></i> Violation Types</h3>
        <select class="chart-select" onchange="updateViolationChart(this.value)">
          <option value="weekly">This Week</option>
          <option value="monthly">This Month</option>
        </select>
      </div>
      <div class="chart-wrapper">
        <canvas id="violationChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Violations Table -->
  <div class="table-section">
    <div class="table-header">
      <div>
        <h3><i class="fas fa-clipboard-list"></i> Recent Violations</h3>
        <p class="table-subtitle">Latest reported violations</p>
      </div>
      <div class="table-actions">
        <div class="table-count">
          <span class="count-badge">{{ is_countable($violations) ? count($violations) : 0 }}</span>
          total violations
        </div>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="violations-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Type</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="violationsTableBody">
          @php
            $violationsArray = $violations instanceof \Illuminate\Support\Collection ? $violations->toArray() : (array)$violations;
            $violationsCount = count($violationsArray);
            $itemsPerPage = 5;
            $currentPage = request()->get('page', 1);
            $startIndex = ($currentPage - 1) * $itemsPerPage;
            $paginatedViolations = array_slice($violationsArray, $startIndex, $itemsPerPage);
          @endphp

          @if($violationsCount > 0)
            @foreach($paginatedViolations as $index => $violation)
            @php
              if (is_object($violation)) {
                $studentName = $violation->student_name ?? $violation['student_name'] ?? 'N/A';
                $gradeLevel = $violation->grade_level ?? $violation['grade_level'] ?? 'N/A';
                $violationType = $violation->violation_type ?? $violation['violation_type'] ?? 'N/A';
                $date = $violation->date ?? $violation['date'] ?? now();
                $status = $violation->status ?? $violation['status'] ?? 'pending';
                $violationId = $violation->violation_id ?? $violation['violation_id'] ?? null;
              } else {
                $studentName = $violation['student_name'] ?? 'N/A';
                $gradeLevel = $violation['grade_level'] ?? 'N/A';
                $violationType = $violation['violation_type'] ?? 'N/A';
                $date = $violation['date'] ?? now();
                $status = $violation['status'] ?? 'pending';
                $violationId = $violation['violation_id'] ?? null;
              }
            @endphp
            <tr>
              <td>
                <div class="student-info">
                  <div class="student-avatar">{{ substr($studentName, 0, 1) }}</div>
                  <div>
                    <div class="student-name">{{ \Illuminate\Support\Str::limit($studentName, 20) }}</div>
                    <div class="student-grade">Grade {{ $gradeLevel }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="violation-type">{{ \Illuminate\Support\Str::limit($violationType, 15) }}</span>
              </td>
              <td>
                <div class="violation-date">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
              </td>
              <td>
                @if($status == 'pending')
                  <span class="status-badge pending">
                    <i class="fas fa-clock"></i> Pending
                  </span>
                @elseif($status == 'in_progress')
                  <span class="status-badge in-progress">
                    <i class="fas fa-spinner"></i> In Progress
                  </span>
                @elseif($status == 'resolved')
                  <span class="status-badge resolved">
                    <i class="fas fa-check"></i> Resolved
                  </span>
                @else
                  <span class="status-badge pending">
                    {{ ucfirst($status) }}
                  </span>
                @endif
              </td>
              <td>
                <div class="action-buttons">
                  <button class="btn-action view" onclick="viewViolationDetails({{ $index + $startIndex }})" title="View Details">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5">
                <div class="empty-state">
                  <i class="fas fa-check-circle"></i>
                  <p>No active violations</p>
                  <small>All caught up!</small>
                </div>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    @if($violationsCount > $itemsPerPage)
    <div class="pagination-controls">
      <div class="pagination-info">
        Showing
        <strong>{{ min($startIndex + 1, $violationsCount) }}-{{ min($startIndex + $itemsPerPage, $violationsCount) }}</strong>
        of <strong>{{ $violationsCount }}</strong> violations
      </div>
      <div class="pagination-buttons">
        @if($currentPage > 1)
          <a href="?page={{ $currentPage - 1 }}" class="pagination-btn prev">
            <i class="fas fa-chevron-left"></i> Previous
          </a>
        @endif

        <div class="page-numbers">
          @php
            $totalPages = ceil($violationsCount / $itemsPerPage);
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $startPage + 4);
          @endphp

          @for($i = $startPage; $i <= $endPage; $i++)
            @if($i == $currentPage)
              <span class="page-number active">{{ $i }}</span>
            @else
              <a href="?page={{ $i }}" class="page-number">{{ $i }}</a>
            @endif
          @endfor
        </div>

        @if($currentPage < $totalPages)
          <a href="?page={{ $currentPage + 1 }}" class="pagination-btn next">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        @endif
      </div>
    </div>
    @endif
  </div>

</div>

<!-- Violation Details Modal -->
<div id="violationDetailsModal" class="modal-overlay" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-info-circle"></i> Violation Details</h3>
      <button class="modal-close" onclick="closeViolationModal()">&times;</button>
    </div>
    <div class="modal-body" id="violationDetailsContent">
      <!-- Content will be populated by JavaScript -->
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeViolationModal()">Close</button>
    </div>
  </div>
</div>

<style>
/* Main Container */
.main-container {
  padding: 20px;
  background: #f8fafc;
  min-height: 100vh;
}

/* Dashboard Header */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content h1 {
  margin: 0;
  font-size: 24px;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-content p {
  margin: 5px 0 0 0;
  color: #64748b;
  font-size: 14px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.date-display {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #f1f5f9;
  border-radius: 8px;
  color: #475569;
  font-size: 14px;
  font-weight: 500;
}

.btn-refresh {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: white;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-refresh:hover {
  background: #f1f5f9;
  color: #6366f1;
  border-color: #c7d2fe;
}

/* Period Toggle */
.period-toggle {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  padding: 6px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  width: fit-content;
}

.period-btn {
  padding: 8px 20px;
  border: none;
  background: transparent;
  color: #64748b;
  font-weight: 500;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 14px;
}

.period-btn.active {
  background: #6366f1;
  color: white;
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
}

.period-btn:hover:not(.active) {
  background: #f1f5f9;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 20px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 15px;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-card.warning {
  border-left: 4px solid #ef4444;
}

.stat-card.info {
  border-left: 4px solid #3b82f6;
}

.stat-card.success {
  border-left: 4px solid #10b981;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: white;
}

.stat-card .stat-icon {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

.stat-card.warning .stat-icon {
  background: linear-gradient(135deg, #ef4444, #f97316);
}

.stat-card.info .stat-icon {
  background: linear-gradient(135deg, #3b82f6, #60a5fa);
}

.stat-card.success .stat-icon {
  background: linear-gradient(135deg, #10b981, #34d399);
}

.stat-info h3 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
}

.stat-info p {
  margin: 4px 0;
  color: #64748b;
  font-size: 14px;
}

.stat-change {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}

/* Charts Section */
.charts-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 20px;
}

.chart-container {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.chart-header h3 {
  margin: 0;
  font-size: 18px;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.chart-tabs {
  display: flex;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 8px;
}

.chart-tab {
  padding: 6px 12px;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 12px;
  font-weight: 500;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.chart-tab.active {
  background: white;
  color: #6366f1;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-select {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  color: #475569;
  font-size: 12px;
  cursor: pointer;
  outline: none;
}

.chart-wrapper {
  height: 200px;
  position: relative;
}

/* Table Section */
.table-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e2e8f0;
}

.table-header h3 {
  margin: 0;
  font-size: 18px;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.table-subtitle {
  margin: 4px 0 0 0;
  color: #64748b;
  font-size: 14px;
}

.table-actions {
  display: flex;
  align-items: center;
  gap: 15px;
  color: #64748b;
  font-size: 14px;
}

.count-badge {
  background: #6366f1;
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-right: 8px;
}

.table-wrapper {
  padding: 20px;
}

.violations-table {
  width: 100%;
  border-collapse: collapse;
}

.violations-table thead {
  background: #f8fafc;
}

.violations-table th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: #475569;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e2e8f0;
}

.violations-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.violations-table tbody tr:hover {
  background: #f8fafc;
}

/* Student Info */
.student-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.student-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
}

.student-name {
  font-weight: 600;
  color: #1e293b;
  font-size: 14px;
}

.student-grade {
  font-size: 12px;
  color: #64748b;
}

/* Violation Type */
.violation-type {
  display: inline-block;
  padding: 4px 8px;
  background: #f1f5f9;
  color: #475569;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}

/* Violation Date */
.violation-date {
  font-size: 14px;
  color: #475569;
  font-weight: 500;
}

/* Status Badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
  border: 1px solid #fbbf24;
}

.status-badge.in-progress {
  background: #e0f2fe;
  color: #0284c7;
  border: 1px solid #7dd3fc;
}

.status-badge.resolved {
  background: #d1fae5;
  color: #059669;
  border: 1px solid #a7f3d0;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-action {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  background: white;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-action.view:hover {
  background: #e0e7ff;
  color: #4f46e5;
  border-color: #c7d2fe;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-state p {
  margin: 0;
  font-size: 16px;
  margin-bottom: 4px;
}

.empty-state small {
  font-size: 14px;
}

/* Pagination Controls */
.pagination-controls {
  padding: 20px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
}

.pagination-info {
  font-size: 14px;
  color: #64748b;
}

.pagination-info strong {
  color: #1e293b;
}

.pagination-buttons {
  display: flex;
  align-items: center;
  gap: 16px;
}

.pagination-btn {
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  background: white;
  color: #475569;
  border-radius: 6px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}

.pagination-btn:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.pagination-btn.prev {
  color: #64748b;
}

.pagination-btn.next {
  color: #6366f1;
}

.page-numbers {
  display: flex;
  gap: 4px;
}

.page-number {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  text-decoration: none;
  font-size: 14px;
  color: #475569;
  transition: all 0.2s ease;
}

.page-number:hover:not(.active) {
  background: #f1f5f9;
  color: #6366f1;
}

.page-number.active {
  background: #6366f1;
  color: white;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.modal-close {
  background: none;
  border: none;
  color: #64748b;
  font-size: 24px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: #f1f5f9;
  color: #ef4444;
}

.modal-body {
  padding: 20px;
  max-height: 60vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 20px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .charts-section {
    grid-template-columns: 1fr;
  }

  .pagination-controls {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .pagination-buttons {
    justify-content: center;
  }

  .table-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .table-actions {
    align-self: flex-end;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .dashboard-header {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }

  .header-actions {
    flex-direction: column;
    width: 100%;
  }

  .date-display {
    width: 100%;
    justify-content: center;
  }
}
</style>

<script>
// Initialize Charts
let trendChart, violationChart;

document.addEventListener('DOMContentLoaded', function() {
  initializeCharts();
  setupEventListeners();
  updateCurrentPeriod();
});

function initializeCharts() {
  // Trend Chart
  const trendCtx = document.getElementById('trendChart');
  if (trendCtx) {
    trendChart = new Chart(trendCtx.getContext('2d'), {
      type: 'line',
      data: {
        labels: {!! json_encode($recentActivity['dates'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']) !!},
        datasets: [{
          label: 'Violations',
          data: {!! json_encode($recentActivity['violations'] ?? [5, 8, 3, 6, 9]) !!},
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99, 102, 241, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointBackgroundColor: '#6366f1',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  }

  // Violation Chart
  const violationCtx = document.getElementById('violationChart');
  if (violationCtx) {
    const violationTypes = {!! json_encode($violationTypes->where('count', '>', 0)) !!};
    const labels = violationTypes.map(v => v.offense_type);
    const data = violationTypes.map(v => v.count);

    violationChart = new Chart(violationCtx.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: [
            '#FF6B6B', '#4ECDC4', '#FFD166', '#06D6A0',
            '#118AB2', '#EF476F', '#7B68EE', '#FF9A76'
          ],
          borderWidth: 0,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { display: false }
        }
      }
    });
  }
}

function setupEventListeners() {
  // Period toggle buttons
  document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      const period = this.dataset.period;
      updatePeriodData(period);
    });
  });

  // Chart tabs
  document.querySelectorAll('.chart-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      const trend = this.dataset.trend;
      updateTrendChart(trend);
    });
  });
}

function updateCurrentPeriod() {
  const activePeriod = document.querySelector('.period-btn.active');
  if (activePeriod) {
    const periodText = activePeriod.textContent;
    document.getElementById('currentPeriod').textContent = periodText;
  }
}

function updatePeriodData(period) {
  console.log(`Updating data for ${period} period`);
  updateCurrentPeriod();

  // Update chart data based on period
  const mockData = {
    weekly: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
      values: [5, 8, 3, 6, 9]
    },
    monthly: {
      labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
      values: [25, 32, 28, 35]
    },
    yearly: {
      labels: ['Q1', 'Q2', 'Q3', 'Q4'],
      values: [45, 52, 48, 55]
    }
  };

  if (trendChart && mockData[period]) {
    trendChart.data.labels = mockData[period].labels;
    trendChart.data.datasets[0].data = mockData[period].values;
    trendChart.update();
  }
}

function updateTrendChart(trend) {
  console.log(`Updating trend chart for ${trend}`);

  const mockData = {
    weekly: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
      values: [5, 8, 3, 6, 9]
    },
    monthly: {
      labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
      values: [25, 32, 28, 35]
    },
    yearly: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      values: [45, 52, 48, 55, 60, 58]
    }
  };

  if (trendChart && mockData[trend]) {
    trendChart.data.labels = mockData[trend].labels;
    trendChart.data.datasets[0].data = mockData[trend].values;
    trendChart.update();
  }
}

function updateViolationChart(range) {
  console.log(`Updating violation chart for ${range}`);
  // In a real app, this would fetch new data via AJAX
}

function refreshData() {
  console.log('Refreshing data...');
  // Show loading state
  const refreshBtn = document.querySelector('.btn-refresh');
  const originalHTML = refreshBtn.innerHTML;
  refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  refreshBtn.disabled = true;

  // Simulate API call
  setTimeout(() => {
    location.reload(); // In real app, update data without reload
  }, 1000);
}

function viewViolationDetails(index) {
  const allViolations = {!! json_encode($violationsArray) !!};

  if (index >= 0 && index < allViolations.length) {
    const violation = allViolations[index];

    // Format date
    const violationDate = new Date(violation.date);
    const formattedDate = violationDate.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });

    // Get status info
    const statusClass = violation.status.toLowerCase().replace('_', '-');
    const statusIcon = statusClass === 'pending' ? 'fa-clock' :
                      statusClass === 'in-progress' ? 'fa-spinner' : 'fa-check-circle';

    // Create modal content
    const content = `
      <div class="violation-details">
        <div class="detail-group">
          <h4>Student Information</h4>
          <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">${violation.student_name}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Grade Level:</span>
            <span class="detail-value">${violation.grade_level}</span>
          </div>
        </div>

        <div class="detail-group">
          <h4>Violation Details</h4>
          <div class="detail-row">
            <span class="detail-label">Type:</span>
            <span class="detail-value violation-type-tag">${violation.violation_type}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date:</span>
            <span class="detail-value">${formattedDate}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Description:</span>
            <span class="detail-value">${violation.description || 'No description provided'}</span>
          </div>
        </div>

        <div class="detail-group">
          <h4>Status</h4>
          <div class="status-display ${statusClass}">
            <i class="fas ${statusIcon}"></i>
            ${violation.status.charAt(0).toUpperCase() + violation.status.slice(1).replace('_', ' ')}
          </div>
        </div>
      </div>
    `;

    // Add styles for modal content
    const style = document.createElement('style');
    style.textContent = `
      .violation-details {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .detail-group {
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
      }

      .detail-group h4 {
        margin: 0 0 12px 0;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
      }

      .detail-row {
        display: flex;
        margin-bottom: 8px;
        font-size: 14px;
      }

      .detail-row:last-child {
        margin-bottom: 0;
      }

      .detail-label {
        width: 100px;
        color: #64748b;
        font-weight: 500;
      }

      .detail-value {
        flex: 1;
        color: #1e293b;
      }

      .violation-type-tag {
        display: inline-block;
        padding: 4px 8px;
        background: #e0e7ff;
        color: #4f46e5;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
      }

      .status-display {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
      }

      .status-display.pending {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fbbf24;
      }

      .status-display.in-progress {
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #7dd3fc;
      }

      .status-display.resolved {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
      }
    `;

    // Remove existing style if any
    const existingStyle = document.querySelector('#modalContentStyle');
    if (existingStyle) existingStyle.remove();
    style.id = 'modalContentStyle';
    document.head.appendChild(style);

    // Set content and show modal
    document.getElementById('violationDetailsContent').innerHTML = content;
    document.getElementById('violationDetailsModal').style.display = 'flex';
  }
}

function closeViolationModal() {
  document.getElementById('violationDetailsModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('violationDetailsModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeViolationModal();
  }
});

// Keyboard support for modal
document.addEventListener('keydown', function(e) {
  const modal = document.getElementById('violationDetailsModal');
  if (modal.style.display === 'flex' && e.key === 'Escape') {
    closeViolationModal();
  }
});
</script>
@endsection
