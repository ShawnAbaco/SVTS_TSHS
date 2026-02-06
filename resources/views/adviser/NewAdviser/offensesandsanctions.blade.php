@extends('adviser.NewAdviser.layout')

@section('content')
    <div class="offenses-sanctions-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar for Offenses -->
        <div class="offenses-toolbar">
            <h2>Offense and Sanctions Management</h2>
        </div>

        <!-- Offense Summary Dashboard -->
        <div class="offenses-summary-dashboard">
            <div class="offenses-summary-header">
                <h3 class="offenses-summary-title">📊 Offense Statistics & Analytics</h3>
                <div class="offenses-summary-actions">
                    <button class="offenses-export-btn" id="exportPdfBtn" onclick="exportCurrentTabToPDFNewTab()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Overall Statistics Cards -->
            <div class="offenses-stats-grid">
                <div class="offenses-stat-item">
                    <div class="offenses-stat-label">Total Violations</div>
                    <div class="offenses-stat-value">{{ $overallStats['totalViolations'] }}</div>
                    <div class="offenses-stat-subtext">All-time recorded violations</div>
                </div>

                <div class="offenses-stat-item">
                    <div class="offenses-stat-label">Students with Violations</div>
                    <div class="offenses-stat-value">{{ $overallStats['studentsWithViolations'] }}</div>
                    <div class="offenses-stat-subtext">{{ $overallStats['violationRate'] }}% of total students</div>
                </div>

                <div class="offenses-stat-item">
                    <div class="offenses-stat-label">Total Students</div>
                    <div class="offenses-stat-value">{{ $overallStats['totalStudents'] }}</div>
                    <div class="offenses-stat-subtext">All registered students</div>
                </div>

                <div class="offenses-stat-item">
                    <div class="offenses-stat-label">Offense Types</div>
                    <div class="offenses-stat-value">{{ $overallStats['totalOffenseTypes'] }}</div>
                    <div class="offenses-stat-subtext">Different types of offenses</div>
                </div>
            </div>

            <!-- Statistics Tabs -->
            <div class="offenses-stats-tabs">
                <button class="offenses-stats-tab active" data-tab="top-offenses">
                    Top Offenses
                </button>
                <button class="offenses-stats-tab" data-tab="top-violators">
                    Top Violators
                </button>
                <button class="offenses-stats-tab" data-tab="all-offenses">
                    All Offenses
                </button>
                <button class="offenses-stats-tab" data-tab="offense-details">
                    Details
                </button>
            </div>

            <!-- Top Offenses Panel -->
            <div id="top-offenses-panel" class="offenses-tab-panel active">
                <div class="offenses-chart-container">
                    <div class="offenses-chart-title">
                        <i class="fas fa-chart-bar"></i> Most Frequent Offenses
                    </div>
                    @if ($topOffenses->count() > 0)
                        <div class="offenses-top-offenses-container">
                            @foreach ($topOffenses as $index => $offense)
                                <div class="offenses-top-offense-card">
                                    <div class="offenses-top-offense-rank">
                                        <span class="offenses-rank-number">#{{ $index + 1 }}</span>
                                    </div>
                                    <div class="offenses-top-offense-content">
                                        <div class="offenses-top-offense-name">{{ $offense->offense_type }}</div>
                                        <div class="offenses-top-offense-stats">
                                            <span class="offenses-stat-item-sm">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $offense->count }} violations
                                            </span>
                                            <span class="offenses-stat-item-sm">
                                                <i class="fas fa-users"></i>
                                                {{ $offense->students_affected }} students
                                            </span>
                                        </div>
                                    </div>
                                    <div class="offenses-top-offense-action">
                                        <button class="offenses-btn-view-details"
                                            onclick="showOffenseModal('{{ addslashes($offense->offense_type) }}')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="offenses-empty-state">
                            <i class="fas fa-chart-line fa-3x"></i>
                            <h4>No violation data available</h4>
                            <p>No offenses have been recorded yet. Start tracking violations to see statistics here.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Violators Panel -->
            <div id="top-violators-panel" class="offenses-tab-panel">
                <div class="offenses-chart-container">
                    <div class="offenses-chart-title">
                        <i class="fas fa-user-graduate"></i> Students with Most Violations
                    </div>
                    @if ($topViolators->count() > 0)
                        <div class="offenses-top-violators-container">
                            @foreach ($topViolators as $index => $violator)
                                <div class="offenses-top-violator-card">
                                    <div class="offenses-top-violator-rank">
                                        <span class="offenses-rank-number">#{{ $index + 1 }}</span>
                                    </div>
                                    <div class="offenses-top-violator-content">
                                        <div class="offenses-top-violator-name">
                                            {{ $violator->student_fname }} {{ $violator->student_lname }}
                                            @if ($violator->adviser_gradelevel)
                                                <span class="offenses-violator-grade">Grade
                                                    {{ $violator->adviser_gradelevel }}, Section
                                                    {{ $violator->adviser_section }}</span>
                                            @endif
                                        </div>
                                        <div class="offenses-top-violator-stats">
                                            <span class="offenses-stat-item-sm">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $violator->violation_count }} violations
                                            </span>
                                            <span class="offenses-stat-item-sm">
                                                <i class="fas fa-clock"></i>
                                                {{ $violator->pending_count ?? 0 }} pending
                                            </span>
                                            <span class="offenses-stat-item-sm">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $violator->resolved_count ?? 0 }} resolved
                                            </span>
                                        </div>
                                        <div class="offenses-top-violator-info">
                                            <span class="offenses-info-item">
                                                <i class="fas fa-calendar"></i>
                                                Last offense:
                                                {{ $violator->last_offense ? \Carbon\Carbon::parse($violator->last_offense)->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="offenses-top-violator-action">
                                        <button class="offenses-btn-view-details"
                                            onclick="showStudentViolations('{{ $violator->student_id }}')">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="offenses-empty-state">
                            <i class="fas fa-user-graduate fa-3x"></i>
                            <h4>No violator data available</h4>
                            <p>No student violation records found. Statistics will appear when violations are recorded.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- All Offenses Panel -->
            <div id="all-offenses-panel" class="offenses-tab-panel">
                <div class="offenses-chart-container">
                    <div class="offenses-chart-header">
                        <div class="offenses-chart-title">
                            <i class="fas fa-list-alt"></i> Offense Statistics
                        </div>
                    </div>
                    @if ($offenseStats->count() > 0)
                        <div class="offenses-table-responsive">
                            <table class="offenses-stats-table">
                                <thead>
                                    <tr>
                                        <th>Offense Type</th>
                                        <th>Frequency</th>
                                        <th>Students Affected</th>
                                        <th>First Occurrence</th>
                                        <th>Last Occurrence</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offenseStats as $stat)
                                        <tr>
                                            <td>
                                                <div class="offenses-type-cell">
                                                    <div class="offenses-type-name">{{ $stat->offense_type }}</div>
                                                    <div class="offenses-type-desc">
                                                        {{ Str::limit($stat->offense_description, 50) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="offenses-frequency-badge">
                                                    <span class="offenses-badge-count">{{ $stat->violation_count }}</span>
                                                    <span class="offenses-badge-label">violations</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="offenses-student-count">
                                                    <i class="fas fa-user-graduate"></i>
                                                    <span>{{ $stat->unique_students }} students</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="offenses-date-cell">
                                                    @if ($stat->first_occurrence)
                                                        <div class="offenses-date-value">
                                                            {{ \Carbon\Carbon::parse($stat->first_occurrence)->format('F d, Y') }}
                                                        </div>
                                                        <div class="offenses-date-diff">
                                                            {{ \Carbon\Carbon::parse($stat->first_occurrence)->diffForHumans() }}
                                                        </div>
                                                    @else
                                                        <span class="offenses-na-text">N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="offenses-date-cell">
                                                    @if ($stat->last_occurrence)
                                                        <div class="offenses-date-value">
                                                            {{ \Carbon\Carbon::parse($stat->last_occurrence)->format('F d, Y') }}
                                                        </div>
                                                        <div class="offenses-date-diff">
                                                            {{ \Carbon\Carbon::parse($stat->last_occurrence)->diffForHumans() }}
                                                        </div>
                                                    @else
                                                        <span class="offenses-na-text">N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="offenses-action-buttons">
                                                    <button class="offenses-btn-action offenses-btn-view"
                                                        onclick="showOffenseModalFull('{{ addslashes($stat->offense_type) }}')">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button class="offenses-btn-action offenses-btn-analyze"
                                                        onclick="viewOffenseDetails('{{ addslashes($stat->offense_type) }}')">
                                                        <i class="fas fa-chart-bar"></i> Analyze
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="offenses-empty-state">
                            <i class="fas fa-database fa-3x"></i>
                            <h4>No offense statistics available</h4>
                            <p>No violation records found. Statistics will appear when violations are recorded.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Offense Details Panel -->
            <div id="offense-details-panel" class="offenses-tab-panel">
                <div class="offenses-chart-container">
                    <div class="offenses-chart-header">
                        <div class="offenses-chart-title">
                            <i class="fas fa-search"></i> Offense Details
                        </div>
                        <div class="offenses-chart-actions">
                            <div class="offenses-select-wrapper">
                                <select id="offenseSelect" onchange="viewOffenseDetails(this.value)"
                                    class="offenses-custom-select">
                                    <option value="">Select an offense...</option>
                                    @foreach ($allOffenses as $offense)
                                        <option value="{{ $offense->offense_type }}">{{ $offense->offense_type }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div id="offenseDetailsContent" class="offenses-details-container">
                        <div class="offenses-empty-state">
                            <i class="fas fa-search fa-3x"></i>
                            <h4>Select an offense to analyze</h4>
                            <p>Choose an offense type from the dropdown above to view detailed statistics and analytics.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offense & Sanction Table -->
        <div class="offenses-table-section">
            <div class="offenses-section-header">
                <div class="offenses-section-title">
                    <h3><i class="fas fa-gavel"></i> Offense List</h3>
                </div>
                <div class="offenses-section-actions">
                    <button class="offenses-export-btn"
                        onclick="window.open('{{ asset('pdf/POLICIES-AND-SANCTION.pdf') }}', '_blank')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
            <table id="offenseTable" class="offenses-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Offense Type</th>
                        <th>Offense Description</th>
                        <th>Sanction(s)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="offensesTableBody">
                    @forelse ($offenses as $offense)
                        <tr
                            data-details="{{ $offense->offense_type }}|{{ $offense->offense_description }}|{{ $offense->sanctions }}">
                            <td>{{ ($offenses->currentPage() - 1) * $offenses->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="offenses-type-cell">
                                    <div class="offenses-type-name">{{ $offense->offense_type }}</div>
                                </div>
                            </td>
                            <td>{{ Str::limit($offense->offense_description, 100) }}</td>
                            <td>
                                <div class="offenses-sanctions-list">
                                    @foreach (explode(', ', $offense->sanctions) as $sanction)
                                        <span class="offenses-sanction-tag">{{ $sanction }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="offenses-action-buttons">
                                    <button class="offenses-btn-action offenses-btn-view"
                                        onclick="showOffenseModalFull('{{ addslashes($offense->offense_type) }}')">
                                        <i class="fas fa-users"></i> View
                                    </button>
                                    <button class="offenses-btn-action offenses-btn-analyze"
                                        onclick="viewOffenseDetails('{{ addslashes($offense->offense_type) }}')">
                                        <i class="fas fa-chart-bar"></i> Analyze
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="offenses-no-data">
                                <div class="offenses-empty-state-sm">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>No offenses found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Offenses -->
            <div class="offenses-pagination-container">
                <div class="offenses-pagination-links">
                    @if ($offenses->hasPages())
                        <nav class="offenses-pagination-nav">
                            <ul class="offenses-pagination">
                                {{-- Previous Page Link --}}
                                @if ($offenses->onFirstPage())
                                    <li class="offenses-page-item disabled" aria-disabled="true">
                                        <span class="offenses-page-link"><i class="fas fa-chevron-left"></i>
                                            Previous</span>
                                    </li>
                                @else
                                    <li class="offenses-page-item">
                                        <a class="offenses-page-link" href="{{ $offenses->previousPageUrl() }}"
                                            rel="prev">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($offenses->hasMorePages())
                                    <li class="offenses-page-item">
                                        <a class="offenses-page-link" href="{{ $offenses->nextPageUrl() }}"
                                            rel="next">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="offenses-page-item disabled" aria-disabled="true">
                                        <span class="offenses-page-link">Next <i class="fas fa-chevron-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
                <div class="offenses-pagination-info">
                    Page {{ $offenses->currentPage() }} of {{ $offenses->lastPage() }}
                </div>
            </div>
        </div>


        <!-- 📝 Offense Details Modal (Quick View) -->
        <div class="offenses-modal" id="offenseDetailsModal">
            <div class="offenses-modal-content" style="max-width: 500px;">
                <div class="offenses-info-modal-header">
                    <i class="fas fa-file-alt"></i> Offense Details
                </div>
                <div class="offenses-info-content" id="offenseModalBody">
                    <!-- Content will be filled dynamically via JS -->
                </div>
                <div class="offenses-modal-footer">
                    <button class="offenses-btn-close" onclick="closeModal('offenseDetailsModal')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Violations Modal -->
        <div class="offenses-modal" id="studentViolationsModal">
            <div class="offenses-modal-content" style="max-width: 700px;">
                <div class="offenses-info-modal-header">
                    <i class="fas fa-user-graduate"></i> Student Violation Details
                </div>
                <div class="offenses-info-content" id="studentViolationsBody">
                    <!-- Content will be filled dynamically via JS -->
                </div>
                <div class="offenses-modal-footer">
                    <button class="offenses-btn-close" onclick="closeModal('studentViolationsModal')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>

        <!-- NEW: Full Offense Details Modal with Filters -->
        <div class="offenses-modal" id="offenseFullModal">
            <div class="offenses-modal-content" style="max-width: 90%; width: 1200px; max-height: 90vh;">
                <div class="offenses-info-modal-header">
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-users"></i>
                            <span>All Students with Violation: <strong id="modalOffenseTitle"></strong></span>
                        </div>
                        <div>
                            <button class="offenses-export-btn" onclick="exportFilteredViolationsPDF()"
                                style="padding: 8px 16px;">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="offenses-info-content" style="padding: 20px; overflow-y: auto;">
                    <!-- Filter Section -->
                    <div class="offenses-filter-section"
                        style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Search
                                    Student</label>
                                <input type="text" id="filterStudentSearch" placeholder="Search by name..."
                                    class="offenses-filter-input"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Start
                                    Date</label>
                                <input type="date" id="filterStartDate" class="offenses-filter-input"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">End
                                    Date</label>
                                <input type="date" id="filterEndDate" class="offenses-filter-input"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Time
                                    Range</label>
                                <select id="filterTimeRange" class="offenses-filter-input"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                                    <option value="">All Time</option>
                                    <option value="morning">Morning (6AM-12PM)</option>
                                    <option value="afternoon">Afternoon (12PM-6PM)</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Status</label>
                                <select id="filterStatus" class="offenses-filter-input"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending - Awaiting Action</option>
                                    <option value="in_progress">In Progress - Being Handled</option>
                                    <option value="resolved">Resolved - Issue Settled</option>
                                    <option value="noncompliant">Noncompliant - Student Failed to Comply</option>
                                    <option value="dismissed">Dismissed - Not Substantiated</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Sanction</label>
                                <select id="filterSanction" class="offenses-filter-input"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                                    <option value="">All Sanctions</option>
                                    <!-- Will be populated dynamically via JS -->
                                </select>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <button onclick="applyFilters()" class="offenses-btn-action offenses-btn-analyze"
                                style="padding: 8px 16px;">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <button onclick="resetFilters()" class="offenses-btn-action offenses-btn-view"
                                style="padding: 8px 16px;">
                                <i class="fas fa-redo"></i> Reset Filters
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Summary -->
                    <div id="offenseStatistics"
                        style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                        <!-- Will be filled dynamically -->
                    </div>

                    <!-- Violations Table -->
                    <div id="offenseViolationsTable" style="margin-bottom: 20px;">
                        <!-- Will be filled dynamically -->
                    </div>
                </div>
                <div class="offenses-modal-footer">
                    <button class="offenses-btn-close" onclick="closeModal('offenseFullModal')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Include jsPDF library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

        <script>
            // ==========================
            // Tab Switching Functionality
            // ==========================
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tab functionality
                const tabs = document.querySelectorAll('.offenses-stats-tab');
                const panels = document.querySelectorAll('.offenses-tab-panel');

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        tabs.forEach(t => t.classList.remove('active'));
                        panels.forEach(p => p.classList.remove('active'));
                        tab.classList.add('active');

                        const tabId = tab.dataset.tab;
                        const panel = document.getElementById(`${tabId}-panel`);
                        if (panel) {
                            panel.classList.add('active');
                        }
                    });
                });

                if (!document.querySelector('link[href*="font-awesome"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
                    document.head.appendChild(link);
                }

                setupEventDelegation();
            });

            // ==========================
            // FIXED: Event Delegation Setup
            // ==========================
            function setupEventDelegation() {
                document.addEventListener('click', function(e) {
                    // Handle "View Details" buttons for students
                    const viewDetailsBtn = e.target.closest('.offenses-btn-view-details');
                    if (viewDetailsBtn) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Extract student ID from onclick attribute or data attribute
                        let studentId = viewDetailsBtn.dataset.studentId;
                        if (!studentId) {
                            const onclickAttr = viewDetailsBtn.getAttribute('onclick');
                            if (onclickAttr) {
                                const match = onclickAttr.match(/showStudentViolations\(['"](.*?)['"]\)/);
                                if (match) studentId = match[1];
                            }
                        }

                        if (studentId) {
                            showStudentViolations(studentId);
                        }
                    }

                    // Handle "Analyze" buttons
                    const analyzeBtn = e.target.closest('.offenses-btn-analyze');
                    if (analyzeBtn && !viewDetailsBtn) {
                        e.preventDefault();
                        e.stopPropagation();

                        let offenseType = analyzeBtn.dataset.offenseType;
                        if (!offenseType) {
                            const onclickAttr = analyzeBtn.getAttribute('onclick');
                            if (onclickAttr) {
                                const match = onclickAttr.match(/viewOffenseDetails\(['"](.*?)['"]\)/);
                                if (match) offenseType = match[1];
                            }
                        }

                        if (offenseType) {
                            viewOffenseDetails(offenseType);
                        }
                    }

                    // Handle "View" buttons for offenses
                    const viewBtn = e.target.closest('.offenses-btn-view');
                    if (viewBtn && !analyzeBtn && !viewDetailsBtn) {
                        e.preventDefault();
                        e.stopPropagation();

                        let offenseType = viewBtn.dataset.offenseType;
                        if (!offenseType) {
                            const onclickAttr = viewBtn.getAttribute('onclick');
                            if (onclickAttr) {
                                const match = onclickAttr.match(/showOffenseModalFull\(['"](.*?)['"]\)/);
                                if (match) offenseType = match[1];
                            }
                        }

                        if (offenseType) {
                            showOffenseModalFull(offenseType);
                        }
                    }

                    // Handle modal close buttons
                    const closeBtn = e.target.closest('.offenses-btn-close');
                    if (closeBtn) {
                        e.preventDefault();
                        e.stopPropagation();
                        const modal = closeBtn.closest('.offenses-modal');
                        if (modal) {
                            closeModal(modal.id);
                        }
                    }

                    // Close modal when clicking outside
                    if (e.target.classList.contains('offenses-modal')) {
                        closeModal(e.target.id);
                    }
                });
            }

            // ==========================
            // FULL OFFENSE MODAL FUNCTIONS
            // ==========================
            let currentOffenseType = '';
            let currentFilteredData = null;

            async function showOffenseModalFull(offenseType) {
                try {
                    currentOffenseType = offenseType;

                    // Show loading
                    showModal('offenseFullModal');
                    document.getElementById('modalOffenseTitle').textContent = offenseType;

                    // Load sanctions for this specific offense
                    await loadSanctionsDropdownByOffense(offenseType);

                    // Load initial data
                    await loadAllViolations(offenseType, {});

                } catch (error) {
                    console.error('Error loading full offense modal:', error);
                    showNotification('Failed to load offense details', 'error');
                }
            }

            async function loadSanctionsDropdownByOffense(offenseType) {
                try {
                    const encodedOffenseType = encodeURIComponent(offenseType);
                    const response = await fetch(
                        `/adviser/offensesandsanctions/sanctions-by-offense/${encodedOffenseType}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                    const sanctionSelect = document.getElementById('filterSanction');

                    if (sanctionSelect) {
                        // Clear existing options except first
                        while (sanctionSelect.options.length > 1) {
                            sanctionSelect.remove(1);
                        }

                        if (response.ok) {
                            const data = await response.json();

                            if (data.success && data.sanctions && data.sanctions.length > 0) {
                                // Add sanctions used for this offense
                                data.sanctions.forEach(sanction => {
                                    const option = document.createElement('option');
                                    option.value = sanction.value || sanction.sanction_consequences;
                                    option.textContent = sanction.display || sanction.sanction_consequences;
                                    sanctionSelect.appendChild(option);
                                });

                                // Also load all sanctions for completeness
                                await loadAllSanctionsDropdown(sanctionSelect);
                            } else {
                                // If no specific sanctions, load all sanctions
                                await loadAllSanctionsDropdown(sanctionSelect);
                            }
                        } else {
                            // Fallback to all sanctions
                            await loadAllSanctionsDropdown(sanctionSelect);
                        }
                    }
                } catch (error) {
                    console.error('Error loading sanctions dropdown:', error);
                    // Fallback to all sanctions
                    await loadAllSanctionsDropdown();
                }
            }

            async function loadAllSanctionsDropdown(sanctionSelect = null) {
                try {
                    if (!sanctionSelect) {
                        sanctionSelect = document.getElementById('filterSanction');
                    }

                    const response = await fetch('/adviser/offensesandsanctions/sanctions-dropdown', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok && sanctionSelect) {
                        const data = await response.json();

                        // Check if we already have these sanctions in the dropdown
                        const existingSanctions = Array.from(sanctionSelect.options).map(opt => opt.value);

                        if (data.success && data.sanctions) {
                            // Add new sanctions that aren't already in the dropdown
                            data.sanctions.forEach(sanction => {
                                const sanctionValue = sanction.value || sanction.sanction_consequences;
                                if (!existingSanctions.includes(sanctionValue)) {
                                    const option = document.createElement('option');
                                    option.value = sanctionValue;
                                    option.textContent = sanction.display || sanction.sanction_consequences;
                                    sanctionSelect.appendChild(option);
                                }
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error loading all sanctions dropdown:', error);
                }
            }

            async function loadAllViolations(offenseType, filters = {}) {
                try {
                    document.getElementById('offenseViolationsTable').innerHTML = `
            <div class="offenses-loading-state">
                <div class="offenses-loading-spinner"></div>
                <p>Loading all violations...</p>
            </div>
        `;

                    document.getElementById('offenseStatistics').innerHTML = '';

                    const encodedOffenseType = encodeURIComponent(offenseType);

                    // Build query string for filters
                    let queryParams = new URLSearchParams();
                    if (filters.startDate) queryParams.append('start_date', filters.startDate);
                    if (filters.endDate) queryParams.append('end_date', filters.endDate);
                    if (filters.timeRange) queryParams.append('time_range', filters.timeRange);
                    if (filters.search) queryParams.append('search', filters.search);
                    if (filters.status) queryParams.append('status', filters.status);
                    if (filters.sanction) queryParams.append('sanction', filters.sanction);

                    const queryString = queryParams.toString();
                    const url =
                        `/adviser/offensesandsanctions/all-violations/${encodedOffenseType}${queryString ? '?' + queryString : ''}`;

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const data = await response.json();

                    if (!data.success) throw new Error(data.message || 'Failed to load violations');

                    currentFilteredData = data;

                    // Update statistics
                    updateStatistics(data.statistics);

                    // Update violations table
                    updateViolationsTable(data.violations);

                    // After loading violations, update the sanction dropdown with any new sanctions
                    await updateSanctionDropdownFromData(data.violations);

                } catch (error) {
                    console.error('Error loading violations:', error);
                    document.getElementById('offenseViolationsTable').innerHTML = `
            <div class="offenses-error-state">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h4>Error Loading Data</h4>
                <p>${error.message || 'Failed to load violations. Please try again.'}</p>
            </div>
        `;
                }
            }

            async function loadAllSanctionsForFilter() {
                try {
                    const response = await fetch('/adviser/offensesandsanctions/sanctions-dropdown', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const sanctionSelect = document.getElementById('filterSanction');

                        if (sanctionSelect && data.success) {
                            // Clear existing options except first
                            while (sanctionSelect.options.length > 1) {
                                sanctionSelect.remove(1);
                            }

                            // Add all sanctions from database
                            data.sanctions.forEach(sanction => {
                                const option = document.createElement('option');
                                option.value = sanction.value || sanction.sanction_consequences;
                                option.textContent = sanction.display || sanction.sanction_consequences;
                                sanctionSelect.appendChild(option);
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error loading sanctions for filter:', error);
                }
            }

            // Update showOffenseModalFull function:
            async function showOffenseModalFull(offenseType) {
                try {
                    currentOffenseType = offenseType;

                    // Show loading
                    showModal('offenseFullModal');
                    document.getElementById('modalOffenseTitle').textContent = offenseType;

                    // Load all sanctions from sanction table
                    await loadAllSanctionsForFilter();

                    // Load initial data
                    await loadAllViolations(offenseType, {});

                } catch (error) {
                    console.error('Error loading full offense modal:', error);
                    showNotification('Failed to load offense details', 'error');
                }
            }

            async function updateSanctionDropdownFromData(violations) {
                try {
                    const sanctionSelect = document.getElementById('filterSanction');
                    if (!sanctionSelect || !violations) return;

                    // Get unique sanctions from the loaded data
                    const uniqueSanctions = [...new Set(violations
                        .filter(v => v.sanction_consequences && v.sanction_consequences !== 'Not assigned')
                        .map(v => v.sanction_consequences)
                    )];

                    // Get existing sanctions in dropdown
                    const existingSanctions = Array.from(sanctionSelect.options).map(opt => opt.value);

                    // Add any new sanctions found in the data
                    uniqueSanctions.forEach(sanction => {
                        if (sanction && !existingSanctions.includes(sanction)) {
                            const option = document.createElement('option');
                            option.value = sanction;
                            option.textContent = sanction;
                            sanctionSelect.appendChild(option);
                        }
                    });
                } catch (error) {
                    console.error('Error updating sanction dropdown:', error);
                }
            }

            function updateStatistics(stats) {
                const statsHtml = `
        <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: bold;">${stats.total_violations}</div>
            <div style="font-size: 12px;">Total Violations</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: bold;">${stats.unique_students}</div>
            <div style="font-size: 12px;">Students Affected</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: bold;">${stats.pending_count}</div>
            <div style="font-size: 12px;">Pending Cases</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: bold;">${stats.resolved_count}</div>
            <div style="font-size: 12px;">Resolved Cases</div>
        </div>
    `;

                document.getElementById('offenseStatistics').innerHTML = statsHtml;
            }

            function updateViolationsTable(violations) {
                if (!violations || violations.length === 0) {
                    document.getElementById('offenseViolationsTable').innerHTML = `
            <div class="offenses-empty-state">
                <i class="fas fa-database fa-3x"></i>
                <h4>No violations found</h4>
                <p>No violations found with the current filters.</p>
            </div>
        `;
                    return;
                }

                let tableHtml = `
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;" id="violationsDataTable">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">#</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">Date</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">Time</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">Student Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">Grade & Section</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">Sanction</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #374151;">Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

                violations.forEach((violation, index) => {
                    const statusClass = getStatusClass(violation.status);
                    const statusText = formatStatus(violation.status);

                    tableHtml += `
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">${index + 1}</td>
                <td style="padding: 12px;">
                    <div style="font-weight: 500;">
                        ${new Date(violation.violation_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        })}
                    </div>
                </td>
                <td style="padding: 12px;">${violation.time || 'N/A'}</td>
                <td style="padding: 12px; font-weight: 500;">${violation.student_fname} ${violation.student_lname}</td>
                <td style="padding: 12px;">
                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                        ${violation.grade_level || 'N/A'}, ${violation.section || 'N/A'}
                    </span>
                </td>
                <td style="padding: 12px;">${violation.sanction_consequences || 'Not assigned'}</td>
                <td style="padding: 12px;">
                    <span style="${statusClass} padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                        ${statusText}
                    </span>
                </td>
            </tr>
        `;
                });

                tableHtml += `
                </tbody>
            </table>
        </div>
        <div style="margin-top: 10px; text-align: right; color: #6b7280; font-size: 12px;">
            Showing ${violations.length} violation(s)
        </div>
    `;

                document.getElementById('offenseViolationsTable').innerHTML = tableHtml;
            }

            function getStatusClass(status) {
                switch (status) {
                    case 'pending':
                        return 'background: #fef3c7; color: #92400e;';
                    case 'resolved':
                        return 'background: #d1fae5; color: #065f46;';
                    case 'in_progress':
                        return 'background: #dbeafe; color: #1e40af;';
                    case 'noncompliant':
                        return 'background: #fee2e2; color: #991b1b;';
                    case 'dismissed':
                        return 'background: #e5e7eb; color: #374151;';
                    default:
                        return 'background: #e5e7eb; color: #374151;';
                }
            }

            function formatStatus(status) {
                switch (status) {
                    case 'pending':
                        return 'Pending';
                    case 'resolved':
                        return 'Resolved';
                    case 'in_progress':
                        return 'In Progress';
                    case 'noncompliant':
                        return 'Noncompliant';
                    case 'dismissed':
                        return 'Dismissed';
                    default:
                        return status.charAt(0).toUpperCase() + status.slice(1);
                }
            }

            function applyFilters() {
                const filters = {
                    startDate: document.getElementById('filterStartDate').value,
                    endDate: document.getElementById('filterEndDate').value,
                    timeRange: document.getElementById('filterTimeRange').value,
                    search: document.getElementById('filterStudentSearch').value.trim(),
                    status: document.getElementById('filterStatus').value,
                    sanction: document.getElementById('filterSanction').value
                };

                // Validate date range
                if (filters.startDate && filters.endDate && filters.startDate > filters.endDate) {
                    showNotification('Start date cannot be after end date', 'error');
                    return;
                }

                loadAllViolations(currentOffenseType, filters);
            }

            function resetFilters() {
                document.getElementById('filterStartDate').value = '';
                document.getElementById('filterEndDate').value = '';
                document.getElementById('filterTimeRange').value = '';
                document.getElementById('filterStudentSearch').value = '';
                document.getElementById('filterStatus').value = '';
                document.getElementById('filterSanction').value = '';

                loadAllViolations(currentOffenseType, {});
            }

            // Export filtered violations to PDF
            function exportFilteredViolationsPDF() {
                if (!currentFilteredData || !currentFilteredData.violations || currentFilteredData.violations.length === 0) {
                    showNotification('No data available to export', 'error');
                    return;
                }

                // Get filter info
                const startDate = document.getElementById('filterStartDate').value || 'All';
                const endDate = document.getElementById('filterEndDate').value || 'All';
                const timeRange = document.getElementById('filterTimeRange').value || 'All Time';
                const search = document.getElementById('filterStudentSearch').value || 'All Students';
                const status = document.getElementById('filterStatus').value || 'All Status';
                const sanction = document.getElementById('filterSanction').value || 'All Sanctions';

                // Get current date and time
                const currentDate = getCurrentDate();
                const currentTime = getCurrentTime();

                // Begin HTML structure
                let html = `
        <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">

            <!-- Header -->
            <div style="display:flex;align-items:center;border-bottom:3px solid #1e3a8a;padding-bottom:20px;margin-bottom:25px;padding:0 25px;">
                <div style="flex:1;">
                    <h1 style="margin:0;font-size:24px;font-weight:700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                    <p style="margin:8px 0 0;font-size:14px;">Violation Report - ${currentOffenseType}</p>
                </div>
                <div style="text-align:right;">
                    <img src="/images/Logo.png" alt="School Logo" style="width:70px;height:70px;object-fit:contain;">
                </div>
            </div>

            <!-- Report Info -->
            <div style="background:#f7fafc;border:1px solid #e2e8f0;border-radius:8px;padding:15px 20px;margin:0 25px 25px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                    <div>
                        <h3 style="margin:0;font-size:18px;font-weight:600;">${currentOffenseType} Violations Report</h3>
                        <p style="margin:5px 0 0;">Generated on: <strong>${currentDate} at ${currentTime}</strong></p>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:12px;">Report ID</div>
                        <div style="font-size:14px;font-weight:600;">VR-${Date.now().toString().slice(-6)}</div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div style="margin:0 25px;">
                <table style="width:100%;border-collapse:collapse;font-size:11px;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">#</th>
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">Date</th>
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">Time</th>
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">Student Name</th>
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">Grade & Section</th>
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">Sanction</th>
                            <th style="border:1px solid #e2e8f0;padding:10px;text-align:left;font-weight:600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

                // Build table rows
                currentFilteredData.violations.forEach((violation, index) => {
                    const statusClass = getStatusClass(violation.status);
                    const statusText = formatStatus(violation.status);

                    html += `
            <tr>
                <td style="border:1px solid #e2e8f0;padding:8px;">${index + 1}</td>
                <td style="border:1px solid #e2e8f0;padding:8px;">
                    ${new Date(violation.violation_date).toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    })}
                </td>
                <td style="border:1px solid #e2e8f0;padding:8px;">${violation.time || 'N/A'}</td>
                <td style="border:1px solid #e2e8f0;padding:8px;font-weight:500;">
                    ${violation.student_fname} ${violation.student_lname}
                </td>
                <td style="border:1px solid #e2e8f0;padding:8px;">${violation.grade_level || 'N/A'}, ${violation.section || 'N/A'}</td>
                <td style="border:1px solid #e2e8f0;padding:8px;">${violation.sanction_consequences || 'Not assigned'}</td>
                <td style="border:1px solid #e2e8f0;padding:8px;">
                    <span style="${statusClass}padding:2px 6px;border-radius:3px;font-size:10px;font-weight:600;">
                        ${statusText}
                    </span>
                </td>
            </tr>
        `;
                });

                // Close table structure
                html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;

                // Create element for PDF
                const element = document.createElement('div');
                element.innerHTML = html;

                // Show notification
                showNotification('Generating PDF...', 'info');

                // PDF config
                const options = {
                    margin: [10, 15, 25, 15],
                    filename: `${currentOffenseType.replace(/\s+/g, '_')}_Violations_${new Date().toISOString().slice(0, 10)}.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        logging: false
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true
                    }
                };

                // Generate PDF
                html2pdf()
                    .set(options)
                    .from(element)
                    .toPdf()
                    .get('pdf')
                    .then(function(pdf) {
                        const totalPages = pdf.internal.getNumberOfPages();

                        for (let i = 1; i <= totalPages; i++) {
                            pdf.setPage(i);
                            pdf.setFontSize(8);
                            pdf.setTextColor(100, 100, 100);
                            pdf.text(
                                `Page ${i} of ${totalPages}`,
                                pdf.internal.pageSize.getWidth() - 25,
                                pdf.internal.pageSize.getHeight() - 8
                            );
                        }

                        const pdfBlob = pdf.output('blob');
                        const pdfUrl = URL.createObjectURL(pdfBlob);
                        window.open(pdfUrl, '_blank');

                        showNotification('PDF exported successfully', 'success');
                    })
                    .catch(error => {
                        console.error('PDF generation error:', error);
                        showNotification('PDF generation failed. Please try again.', 'error');
                    });
            }

            function getTimeRangeDisplay(timeRange) {
                switch (timeRange) {
                    case 'morning':
                        return 'Morning (6AM-12PM)';
                    case 'afternoon':
                        return 'Afternoon (12PM-6PM)';
                    case 'evening':
                        return 'Evening (6PM-12AM)';
                    default:
                        return 'All Time';
                }
            }

            function getStatusDisplay(status) {
                switch (status) {
                    case 'pending':
                        return 'Pending - Awaiting Action';
                    case 'in_progress':
                        return 'In Progress - Being Handled';
                    case 'resolved':
                        return 'Resolved - Issue Settled';
                    case 'noncompliant':
                        return 'Noncompliant - Student Failed to Comply';
                    case 'dismissed':
                        return 'Dismissed - Not Substantiated';
                    default:
                        return 'All Status';
                }
            }

            // Helper function to get current date
            function getCurrentDate() {
                const now = new Date();
                return now.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // Helper function to get current time
            function getCurrentTime() {
                const now = new Date();
                return now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }


            // ==========================
            // Existing PDF Export Functions (keep as is)
            // ==========================
            function exportCurrentTabToPDFNewTab() {
                const {
                    title,
                    contentHTML,
                    rowCount,
                    tabType
                } = getCurrentTabData();
                const currentDate = getCurrentDate();
                const currentTime = getCurrentTime();

                // Create a temporary element for PDF generation
                const element = document.createElement('div');
                element.innerHTML = `
        <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
            <!-- Professional Header with Logo on Right -->
            <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                <div style="flex: 1;">
                    <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                    <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Offense Statistics Report</p>
                </div>
                <div style="text-align: right;">
                    <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                </div>
            </div>

            <!-- Report Summary -->
            <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${title}</h3>
                        <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                            Total Records: <strong style="color: #000000;">${rowCount} ${tabType === 'offense-details' ? 'Offense' : 'Record(s)'}</strong>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: #000000;">Document ID</div>
                        <div style="font-size: 14px; font-weight: 600; color: #000000;">OFF-${tabType.toUpperCase().replace('-', '')}-${Date.now().toString().slice(-6)}</div>
                    </div>
                </div>
            </div>


            <!-- Simple Table Container -->
            <div style="overflow: hidden; margin: 0 25px;">
                ${contentHTML}
            </div>
        </div>
    `;

                // Show notification
                showNotification('Opening PDF preview...', 'info');

                // PDF options for new tab preview
                const options = {
                    margin: [10, 15, 25, 15],
                    filename: `Offense_Statistics_${tabType}_${new Date().toISOString().slice(0,10)}.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        scrollY: -window.scrollY
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true,
                        hotfixes: ["px_scaling"]
                    }
                };

                // Generate PDF and open in new tab
                html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();

                    // Add footer to each page
                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);
                        pdf.setFontSize(8);
                        pdf.setTextColor(100, 100, 100);

                        // Page number on right footer
                        pdf.text(`Page ${i} of ${totalPages}`,
                            pdf.internal.pageSize.getWidth() - 25,
                            pdf.internal.pageSize.getHeight() - 8);
                    }

                    // Open PDF in new tab
                    const pdfBlob = pdf.output('blob');
                    const pdfUrl = URL.createObjectURL(pdfBlob);
                    window.open(pdfUrl, '_blank');

                    showNotification('PDF exported successfully', 'success');
                }).catch(error => {
                    console.error('PDF generation error:', error);
                    showNotification('PDF generation failed. Please try again.', 'error');
                });
            }

            function getCurrentDate() {
                const now = new Date();
                return now.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function getCurrentTime() {
                const now = new Date();
                return now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function getCurrentTabData() {
                const activeTab = document.querySelector('.offenses-stats-tab.active');
                const tabType = activeTab ? activeTab.dataset.tab : 'top-offenses';

                let title = '';
                let contentHTML = '';
                let rowCount = 0;

                switch (tabType) {
                    case 'top-offenses':
                        title = 'Top Offenses Report';
                        const topOffensesContainer = document.querySelector('.offenses-top-offenses-container');
                        if (topOffensesContainer) {
                            const cards = topOffensesContainer.querySelectorAll('.offenses-top-offense-card');
                            rowCount = cards.length;

                            contentHTML = `
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Rank</th>
                                <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Offense Type</th>
                                <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Violations</th>
                                <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Students Affected</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                            cards.forEach((card, index) => {
                                const offenseName = card.querySelector('.offenses-top-offense-name')?.textContent ||
                                    'N/A';
                                const stats = card.querySelectorAll('.offenses-stat-item-sm');
                                const violations = stats[0]?.textContent?.replace('violations', '').trim() || '0';
                                const students = stats[1]?.textContent?.replace('students', '').trim() || '0';

                                contentHTML += `
                        <tr>
                            <td style="border: 1px solid #e2e8f0; padding: 10px; color: #000000;">#${index + 1}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 10px; color: #000000; font-weight: 500;">${offenseName}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 10px; color: #000000;">${violations}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 10px; color: #000000;">${students}</td>
                        </tr>
                    `;
                            });

                            contentHTML += '</tbody></table>';
                        }
                        break;

                    case 'top-violators':
                        title = 'Top Violators Report';
                        const topViolatorsContainer = document.querySelector('.offenses-top-violators-container');
                        if (topViolatorsContainer) {
                            const cards = topViolatorsContainer.querySelectorAll('.offenses-top-violator-card');
                            rowCount = cards.length;

                            contentHTML = `
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Rank</th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Student Name</th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Grade & Section</th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Total Violations</th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Pending</th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Resolved</th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Last Offense</th>
                </tr>
            </thead>
            <tbody>
        `;

                            cards.forEach((card, index) => {
                                // FIXED: Get the student name properly
                                const nameElement = card.querySelector('.offenses-top-violator-name');
                                let studentName = 'N/A';
                                let gradeSection = 'N/A';

                                if (nameElement) {
                                    // Get the text content and split by newline or <br> tags
                                    const fullText = nameElement.textContent || nameElement.innerText;
                                    // Split by line breaks or other indicators
                                    const lines = fullText.split('\n').map(line => line.trim()).filter(line => line);

                                    if (lines.length > 0) {
                                        studentName = lines[0]; // First line should be the name

                                        // Look for grade section in span with class offenses-violator-grade
                                        const gradeSpan = nameElement.querySelector('.offenses-violator-grade');
                                        if (gradeSpan) {
                                            gradeSection = gradeSpan.textContent || 'N/A';
                                        } else if (lines.length > 1) {
                                            // Try to find grade section in other lines
                                            gradeSection = lines.slice(1).join(' ');
                                        }
                                    }
                                }

                                // Get the stats
                                const stats = card.querySelectorAll('.offenses-stat-item-sm');
                                const violations = stats[0]?.textContent?.replace('violations', '').trim() || '0';
                                const pending = stats[1]?.textContent?.replace('pending', '').trim() || '0';
                                const resolved = stats[2]?.textContent?.replace('resolved', '').trim() || '0';

                                // Get last offense date
                                let lastOffense = 'N/A';
                                const lastOffenseElement = card.querySelector('.offenses-info-item');
                                if (lastOffenseElement) {
                                    const lastOffenseText = lastOffenseElement.textContent || '';
                                    const match = lastOffenseText.match(/Last offense:\s*(.+)/);
                                    if (match) {
                                        lastOffense = match[1].trim();
                                    }
                                }

                                contentHTML += `
            <tr>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">#${index + 1}</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000; font-weight: 500;">${studentName}</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${gradeSection}</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${violations}</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${pending}</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${resolved}</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${lastOffense}</td>
            </tr>
            `;
                            });

                            contentHTML += '</tbody></table>';
                        }
                        break;

                    case 'all-offenses':
                        title = 'All Offenses Report';
                        const statsTable = document.querySelector('.offenses-stats-table');
                        if (statsTable) {
                            const rows = statsTable.querySelectorAll('tbody tr');
                            rowCount = rows.length;

                            contentHTML = `
                    <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Offense Type</th>
                                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Frequency</th>
                                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Students</th>
                                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">First Occurrence</th>
                                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #000000; font-weight: 600;">Last Occurrence</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                            rows.forEach(row => {
                                const cells = row.querySelectorAll('td');
                                if (cells.length >= 5) {
                                    const offenseType = cells[0].querySelector('.offenses-type-name')?.textContent ||
                                        'N/A';
                                    const frequency = cells[1].querySelector('.offenses-badge-count')?.textContent ||
                                        '0';
                                    const students = cells[2].querySelector('span')?.textContent || '0';
                                    const firstOccurrence = cells[3].querySelector('.offenses-date-value')
                                        ?.textContent || 'N/A';
                                    const lastOccurrence = cells[4].querySelector('.offenses-date-value')
                                        ?.textContent || 'N/A';

                                    contentHTML += `
                            <tr>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000; font-weight: 500;">${offenseType}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${frequency}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${students}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${firstOccurrence}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${lastOccurrence}</td>
                            </tr>
                        `;
                                }
                            });

                            contentHTML += '</tbody></table>';
                        }
                        break;

                    case 'offense-details':
                        title = 'Offense Details Report';
                        const detailsContent = document.getElementById('offenseDetailsContent');
                        const selectedOffense = document.getElementById('offenseSelect').value;

                        if (selectedOffense && detailsContent && !detailsContent.querySelector('.offenses-empty-state')) {
                            const offenseName = document.querySelector('.offenses-details-title h4')?.textContent ||
                                selectedOffense;
                            const statsCards = detailsContent.querySelectorAll('.offenses-stat-card');

                            contentHTML = `
            <div style="margin-bottom: 20px;">
                <h3 style="color: #000000; margin: 0 0 10px 0; font-size: 18px;">${offenseName}</h3>
                <p style="color: #666666; margin: 0; font-size: 14px;">
                    ${document.querySelector('.offenses-details-description')?.textContent || 'No description available'}
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 25px;">
        `;

                            statsCards.forEach(card => {
                                const value = card.querySelector('.offenses-stat-card-value')?.textContent || '0';
                                const label = card.querySelector('.offenses-stat-card-label')?.textContent || '';
                                const subtext = card.querySelector('.offenses-stat-card-subtext')?.textContent || '';

                                contentHTML += `
                <div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; background: #f8fafc;">
                    <div style="font-size: 22px; font-weight: 700; color: #1e3a8a; margin-bottom: 5px;">${value}</div>
                    <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 5px;">${label}</div>
                    ${subtext ? `<div style="font-size: 12px; color: #666666;">${subtext}</div>` : ''}
                </div>
            `;
                            });

                            contentHTML += '</div>';

                            // ADD RECENT VIOLATIONS TABLE
                            const recentViolationsSection = detailsContent.querySelector('.offenses-recent-violations');
                            if (recentViolationsSection) {
                                const recentViolationsTable = recentViolationsSection.querySelector('table');
                                if (recentViolationsTable) {
                                    contentHTML += `
                    <div style="margin-top: 30px;">

                        <div style="overflow: hidden; margin-bottom: 20px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                <thead>
                                    <tr style="background: #f1f5f9;">
                                        <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Date</th>
                                        <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Student</th>
                                        <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Grade</th>
                                        <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Sanction</th>
                                        <th style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #000000; font-weight: 600;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                                    // Get all table rows from the recent violations table
                                    const rows = recentViolationsTable.querySelectorAll('tbody tr');
                                    rowCount = rows.length;

                                    rows.forEach(row => {
                                        const cells = row.querySelectorAll('td');
                                        if (cells.length >= 5) {
                                            const date = cells[0]?.textContent || 'N/A';
                                            const student = cells[1]?.textContent || 'N/A';
                                            const grade = cells[2]?.textContent || 'N/A';
                                            const sanction = cells[3]?.textContent || 'N/A';
                                            const status = cells[4]?.textContent || 'N/A';
                                            const statusClass = status === 'pending' ?
                                                'background: #fef3c7; color: #92400e;' :
                                                status === 'resolved' ? 'background: #d1fae5; color: #065f46;' :
                                                'background: #e5e7eb; color: #374151;';

                                            contentHTML += `
                            <tr>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${date}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000; font-weight: 500;">${student}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${grade}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px; color: #000000;">${sanction}</td>
                                <td style="border: 1px solid #e2e8f0; padding: 8px;">
                                    <span style="${statusClass} padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">${status}</span>
                                </td>
                            </tr>
                        `;
                                        }
                                    });

                                    contentHTML += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                                }
                            } else {
                                contentHTML += `
                <div style="margin-top: 30px; text-align: center; padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="font-size: 14px; color: #666666; margin-bottom: 5px;">No recent violations data available</div>
                </div>
            `;
                            }

                        } else {
                            contentHTML = `
            <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                <div style="font-size: 16px; color: #666666; margin-bottom: 10px;">No offense selected</div>
                <div style="font-size: 14px; color: #999999;">Please select an offense from the dropdown to view details</div>
            </div>
        `;
                            rowCount = 0;
                        }
                        break;
                }

                return {
                    title,
                    contentHTML,
                    rowCount,
                    tabType
                };
            }

            function showNotification(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-width: 300px;
    `;

                // Set background color based on type
                if (type === 'success') {
                    notification.style.backgroundColor = '#10b981';
                } else if (type === 'error') {
                    notification.style.backgroundColor = '#ef4444';
                } else if (type === 'info') {
                    notification.style.backgroundColor = '#3b82f6';
                } else {
                    notification.style.backgroundColor = '#6b7280';
                }

                notification.textContent = message;

                // Add to document
                document.body.appendChild(notification);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            // ==========================
            // Modal Functions
            // ==========================
            function showModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    setTimeout(() => {
                        modal.style.opacity = '1';
                    }, 10);
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.opacity = '0';
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                    document.body.style.overflow = 'auto';
                }
            }

            // Close modal when clicking outside (FIXED)
            window.addEventListener('click', (e) => {
                const modals = document.querySelectorAll('.offenses-modal');
                modals.forEach(modal => {
                    if (e.target === modal) {
                        closeModal(modal.id);
                    }
                });
            });

            // ==========================
            // Show Offense Modal (Quick View) - FIXED
            // ==========================
            async function showOffenseModal(offenseType) {
                try {
                    // Show loading
                    document.getElementById('offenseModalBody').innerHTML = `
            <div class="offenses-loading-state">
                <div class="offenses-loading-spinner"></div>
                <p>Loading offense details...</p>
            </div>
        `;
                    showModal('offenseDetailsModal');

                    const encodedOffenseType = encodeURIComponent(offenseType);
                    const response = await fetch(`/adviser/offensesandsanctions/details/${encodedOffenseType}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load offense details');
                    }

                    const formatDate = (dateString) => {
                        if (!dateString) return 'N/A';
                        return new Date(dateString).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    };

                    const formatDateWithAgo = (dateString) => {
                        if (!dateString) return 'N/A';
                        const date = new Date(dateString);
                        const now = new Date();
                        const diffTime = Math.abs(now - date);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays === 0) return 'Today';
                        if (diffDays === 1) return 'Yesterday';
                        if (diffDays < 7) return `${diffDays} days ago`;
                        if (diffDays < 30) {
                            const weeks = Math.floor(diffDays / 7);
                            return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
                        }
                        if (diffDays < 365) {
                            const months = Math.floor(diffDays / 30);
                            return `${months} month${months > 1 ? 's' : ''} ago`;
                        }
                        // ... continue from where you left off

                        const years = Math.floor(diffDays / 365);
                        return `${years} year${years > 1 ? 's' : ''} ago`;
                    };

                    const html = `
            <div class="offense-modal-content">
                <div class="offense-modal-header">
                    <h4>${data.offense.offense_type}</h4>
                    <p class="offense-modal-subtitle">Quick Overview</p>
                </div>
                <div class="offense-modal-body">
                    <div class="modal-detail-item">
                        <label><i class="fas fa-align-left"></i> Description:</label>
                        <p>${data.offense.offense_description || 'No description available'}</p>
                    </div>

                    <div class="modal-detail-item">
                        <label><i class="fas fa-chart-bar"></i> Statistics:</label>
                        <div class="modal-stats-grid">
                            <div class="modal-stat">
                                <span class="modal-stat-value">${data.statistics.total_violations || 0}</span>
                                <span class="modal-stat-label">Total Violations</span>
                            </div>
                            <div class="modal-stat">
                                <span class="modal-stat-value">${data.statistics.students_affected || 0}</span>
                                <span class="modal-stat-label">Students Affected</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-detail-item">
                        <label><i class="fas fa-calendar-alt"></i> Timeline:</label>
                        <div class="modal-timeline">
                            <div class="modal-timeline-item">
                                <i class="fas fa-flag"></i>
                                <div class="modal-timeline-content">
                                    <strong>First Recorded:</strong>
                                    <span>${formatDate(data.statistics.first_occurrence)}</span>
                                </div>
                            </div>
                            <div class="modal-timeline-item">
                                <i class="fas fa-history"></i>
                                <div class="modal-timeline-content">
                                    <strong>Most Recent:</strong>
                                    <span>${formatDate(data.statistics.last_occurrence)} (${formatDateWithAgo(data.statistics.last_occurrence)})</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button class="offenses-btn-action offenses-btn-analyze" onclick="showOffenseModalFull('${offenseType.replace(/'/g, "\\'")}')">
                            <i class="fas fa-users"></i> View
                        </button>
                        <button class="offenses-btn-action offenses-btn-analyze" onclick="viewOffenseDetails('${offenseType.replace(/'/g, "\\'")}')">
                            <i class="fas fa-chart-bar"></i> View Analytics
                        </button>
                    </div>
                </div>
            </div>
        `;

                    document.getElementById('offenseModalBody').innerHTML = html;

                } catch (error) {
                    console.error('Error loading offense details:', error);
                    document.getElementById('offenseModalBody').innerHTML = `
            <div class="offenses-error-state">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h4>Error Loading Details</h4>
                <p>${error.message || 'Failed to load offense details. Please try again.'}</p>
                <button class="offenses-btn-retry" onclick="showOffenseModal('${offenseType.replace(/'/g, "\\'")}')">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;
                }
            }

            // ==========================
            // Show Student Violations Modal
            // ==========================
            async function showStudentViolations(studentId) {
                try {
                    document.getElementById('studentViolationsBody').innerHTML = `
            <div class="offenses-loading-state">
                <div class="offenses-loading-spinner"></div>
                <h4>Loading student violations...</h4>
                <p>Student ID: ${studentId}</p>
            </div>
        `;

                    showModal('studentViolationsModal');

                    const response = await fetch(`/adviser/students/${studentId}/violations`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load student violations');
                    }

                    let html = `
            <div class="student-violations-content">
                <div class="student-violations-header">
                    <h4>${data.student.student_fname} ${data.student.student_lname}</h4>
                    <p class="student-violations-subtitle">
                        <i class="fas fa-user-graduate"></i>
                        ${data.student.grade_level ? `Grade ${data.student.grade_level}, Section ${data.student.section}` : 'No class info'}
                    </p>
                </div>

                <div class="student-stats-summary">
                    <div class="student-stat-card">
                        <div class="student-stat-value">${data.total_violations}</div>
                        <div class="student-stat-label">Total Violations</div>
                    </div>
                    <div class="student-stat-card">
                        <div class="student-stat-value">${data.pending_violations}</div>
                        <div class="student-stat-label">Pending</div>
                    </div>
                    <div class="student-stat-card">
                        <div class="student-stat-value">${data.resolved_violations}</div>
                        <div class="student-stat-label">Resolved</div>
                    </div>
                </div>

                <div class="student-violations-list">
                    <h5><i class="fas fa-list"></i> Violation History</h5>
        `;

                    if (data.violations && data.violations.length > 0) {
                        data.violations.forEach((violation, index) => {
                            const statusClass = violation.status === 'pending' ? 'status-pending' :
                                violation.status === 'resolved' ? 'status-resolved' : 'status-other';

                            html += `
                    <div class="violation-item">
                        <div class="violation-header">
                            <span class="violation-number">#${index + 1}</span>
                            <span class="violation-date">${new Date(violation.violation_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })}</span>
                            <span class="violation-status ${statusClass}">
                                <i class="fas ${violation.status === 'pending' ? 'fa-clock' : 'fa-check-circle'}"></i>
                                ${violation.status}
                            </span>
                        </div>
                        <div class="violation-details">
                            <div class="violation-offense">
                                <strong>Offense:</strong> ${violation.offense_type}
                            </div>
                            <div class="violation-sanction">
                                <strong>Sanction:</strong> ${violation.sanction || 'Not assigned'}
                            </div>
                            ${violation.description ? `
                                                                                                                                                                                                                                                    <div class="violation-description">
                                                                                                                                                                                                                                                        <strong>Description:</strong> ${violation.description}
                                                                                                                                                                                                                                                    </div>` : ''}
                        </div>
                    </div>
                `;
                        });
                    } else {
                        html += `
                <div class="no-violations-message">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <p>No violations recorded for this student.</p>
                </div>
            `;
                    }

                    html += `
                </div>
            </div>
        `;

                    document.getElementById('studentViolationsBody').innerHTML = html;

                } catch (error) {
                    console.error('Error loading student violations:', error);
                    document.getElementById('studentViolationsBody').innerHTML = `
            <div class="offenses-error-state">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h4>Error Loading Student Data</h4>
                <p>${error.message || 'Failed to load student violations. Please try again.'}</p>
                <button class="offenses-btn-retry" onclick="showStudentViolations('${studentId}')">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;
                }
            }

            // ==========================
            // Load Offense Details for Analytics Panel - FIXED ROUTE
            // ==========================
            async function loadOffenseDetails(offenseType) {
                if (!offenseType) {
                    document.getElementById('offenseDetailsContent').innerHTML = `
            <div class="offenses-empty-state">
                <i class="fas fa-search fa-3x"></i>
                <h4>Select an offense to analyze</h4>
                <p>Choose an offense type from the dropdown above to view detailed statistics and analytics.</p>
            </div>
        `;
                    return;
                }

                document.getElementById('offenseDetailsContent').innerHTML = `
        <div class="offenses-loading-state">
            <div class="offenses-loading-spinner"></div>
            <h4>Loading offense details...</h4>
            <p>Fetching statistics for "${offenseType}"</p>
        </div>
    `;

                try {
                    const encodedOffenseType = encodeURIComponent(encodeURIComponent(offenseType));

                    const response = await fetch(`/adviser/offensesandsanctions/details/${encodedOffenseType}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        console.error('Response status:', response.status);
                        const errorText = await response.text();
                        console.error('Response text:', errorText);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load offense details');
                    }

                    if (!data.summary.has_data) {
                        document.getElementById('offenseDetailsContent').innerHTML = `
                <div class="offenses-empty-state">
                    <i class="fas fa-chart-line fa-3x"></i>
                    <h4>No Data Available</h4>
                    <p>The offense "${offenseType}" has no recorded violations yet.</p>
                </div>
            `;
                        return;
                    }

                    let html = `
            <div class="offenses-details-header">
                <div class="offenses-details-title">
                    <h4>${data.offense.offense_type}</h4>
                    <p class="offenses-details-description">${data.offense.offense_description || 'No description available'}</p>
                </div>
                <div class="offenses-details-actions">
                    <button class="offenses-btn-action offenses-btn-analyze" onclick="showOffenseModalFull('${offenseType.replace(/'/g, "\\'")}')">
                        <i class="fas fa-users"></i> View
                    </button>
                </div>
            </div>
        `;

                    if (data.repeatOffenders && data.repeatOffenders.length > 0) {
                        html += `
                <div class="offenses-repeat-offenders">
                    <h5><i class="fas fa-user-times"></i> Repeat Offenders</h5>
                    <div class="repeat-offenders-list">
            `;

                        data.repeatOffenders.forEach((offender, index) => {
                            html += `
                    <div class="repeat-offender-item">
                        <div class="offender-rank">${index + 1}</div>
                        <div class="offender-details">
                            <div class="offender-name">${offender.student_fname} ${offender.student_lname}</div>
                            <div class="offender-info">
                                <span class="offender-grade">${offender.grade_level || 'N/A'}</span>
                                <span class="offender-count">${offender.violation_count} violations</span>
                            </div>
                        </div>
                        <div class="offender-last-offense">
                            Last: ${offender.last_offense ? new Date(offender.last_offense).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'}
                        </div>
                    </div>
                `;
                        });

                        html += `
                    </div>
                </div>
            `;
                    }

                    if (data.recentViolations && data.recentViolations.length > 0) {
                        html += `
                <div class="offenses-recent-violations">
                    <h5><i class="fas fa-history"></i> Recent Violations</h5>
                    <div class="recent-violations-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Grade</th>
                                    <th>Sanction</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

                        data.recentViolations.forEach(violation => {
                            const statusClass = violation.status === 'pending' ? 'status-pending' :
                                violation.status === 'resolved' ? 'status-resolved' : 'status-other';

                            html += `
                    <tr>
                        <td>${new Date(violation.violation_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</td>
                        <td>${violation.student_fname} ${violation.student_lname}</td>
                        <td>${violation.grade_level || 'N/A'}</td>
                        <td>${violation.sanction_consequences || 'Not assigned'}</td>
                        <td><span class="${statusClass}">${violation.status}</span></td>
                    </tr>
                `;
                        });

                        html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="view-more-link">
                        <a href="javascript:void(0);" onclick="showOffenseModalFull('${offenseType.replace(/'/g, "\\'")}')">
                            <i class="fas fa-arrow-right"></i> View ${data.statistics.total_violations} Violations
                        </a>
                    </div>
                </div>
            `;
                    }

                    document.getElementById('offenseDetailsContent').innerHTML = html;

                } catch (error) {
                    console.error('Error loading offense details:', error);
                    document.getElementById('offenseDetailsContent').innerHTML = `
            <div class="offenses-error-state">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h4>Error Loading Details</h4>
                <p>${error.message || 'Failed to load offense details. Please try again.'}</p>
                <button class="offenses-btn-retry" onclick="loadOffenseDetails('${offenseType.replace(/'/g, "\\'")}')">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;
                }
            }

            // ==========================
            // View Offense Details (for Analytics Panel)
            // ==========================
            function viewOffenseDetails(offenseType) {
                // Close any open modals first
                closeModal('offenseDetailsModal');

                const selectElement = document.getElementById('offenseSelect');
                if (selectElement) {
                    selectElement.value = offenseType;
                }

                const tabs = document.querySelectorAll('.offenses-stats-tab');
                const panels = document.querySelectorAll('.offenses-tab-panel');

                tabs.forEach(tab => tab.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));

                const detailsTab = document.querySelector('.offenses-stats-tab[data-tab="offense-details"]');
                const detailsPanel = document.getElementById('offense-details-panel');

                if (detailsTab && detailsPanel) {
                    detailsTab.classList.add('active');
                    detailsPanel.classList.add('active');

                    setTimeout(() => {
                        detailsPanel.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }, 100);
                }

                loadOffenseDetails(offenseType);
            }

            // Add CSS animations for notifications
            const style = document.createElement('style');
            style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Top Violators Styles */
    .offenses-top-violators-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }

    .offenses-top-violator-card {
        display: flex;
        align-items: center;
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .offenses-top-violator-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        border-color: #3b82f6;
    }

    .offenses-top-violator-rank {
        margin-right: 16px;
    }

    .offenses-top-violator-rank .offenses-rank-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        font-weight: bold;
        font-size: 18px;
        border-radius: 10px;
    }

    .offenses-top-violator-content {
        flex: 1;
    }

    .offenses-top-violator-name {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .offenses-violator-grade {
        font-size: 12px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 4px;
        margin-left: 8px;
    }

    .offenses-top-violator-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 8px;
    }

    .offenses-top-violator-stats .offenses-stat-item-sm {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 13px;
        color: #6b7280;
    }

    .offenses-top-violator-stats .offenses-stat-item-sm i {
        width: 14px;
        color: #9ca3af;
    }

    .offenses-top-violator-info {
        font-size: 12px;
        color: #9ca3af;
    }

    .offenses-info-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .offenses-top-violator-action {
        margin-left: 12px;
    }

    /* Student Violations Modal Styles */
    .student-violations-content {
        padding: 10px;
    }

    .student-violations-header {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
    }

    .student-violations-header h4 {
        margin: 0;
        color: #1f2937;
        font-size: 20px;
    }

    .student-violations-subtitle {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    .student-stats-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .student-stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }

    .student-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 5px;
    }

    .student-stat-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .student-violations-list {
        background: white;
        border-radius: 10px;
        padding: 15px;
        border: 1px solid #e5e7eb;
    }

    .student-violations-list h5 {
        margin: 0 0 15px 0;
        color: #374151;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .violation-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .violation-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .violation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .violation-number {
        font-weight: 600;
        color: #374151;
        background: #e5e7eb;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

    .violation-date {
        font-size: 13px;
        color: #6b7280;
    }

    .violation-status {
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-resolved {
        background: #d1fae5;
        color: #065f46;
    }

    .status-other {
        background: #e5e7eb;
        color: #374151;
    }

    .violation-details {
        font-size: 13px;
    }

    .violation-details div {
        margin-bottom: 4px;
    }

    .violation-details strong {
        color: #374151;
        margin-right: 5px;
    }

    .no-violations-message {
        text-align: center;
        padding: 30px;
        color: #6b7280;
    }

    .no-violations-message i {
        margin-bottom: 10px;
        color: #10b981;
    }

    /* Repeat Offenders Styles */
    .offenses-repeat-offenders {
        margin-top: 25px;
        background: white;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e5e7eb;
    }

    .offenses-repeat-offenders h5 {
        margin: 0 0 15px 0;
        color: #374151;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .repeat-offenders-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .repeat-offender-item {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
    }

    .offender-rank {
        background: #3b82f6;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        margin-right: 12px;
    }

    .offender-details {
        flex: 1;
    }

    .offender-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .offender-info {
        display: flex;
        gap: 15px;
        font-size: 12px;
        color: #6b7280;
    }

    .offender-last-offense {
        font-size: 12px;
        color: #9ca3af;
        background: #f3f4f6;
        padding: 4px 8px;
        border-radius: 4px;
    }

    /* Recent Violations Table */
    .offenses-recent-violations {
        margin-top: 25px;
        background: white;
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e5e7eb;
    }

    .offenses-recent-violations h5 {
        margin: 0 0 15px 0;
        color: #374151;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .recent-violations-table {
        overflow-x: auto;
    }

    .recent-violations-table table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .recent-violations-table th {
        background: #f8fafc;
        color: #374151;
        font-weight: 600;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #e5e7eb;
    }

    .recent-violations-table td {
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
    }

    .recent-violations-table tr:hover {
        background: #f9fafb;
    }

    .view-more-link {
        text-align: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e5e7eb;
    }

    .view-more-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }

    .view-more-link a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Full Offense Modal Styles */
    .offenses-filter-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    .offenses-filter-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .offenses-filter-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Loading States */
    .offenses-loading-state {
        text-align: center;
        padding: 40px 20px;
    }

    .offenses-loading-spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 3px solid #e5e7eb;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .offenses-error-state {
        text-align: center;
        padding: 40px 20px;
        color: #dc2626;
    }

    .offenses-error-state h4 {
        margin: 15px 0 10px;
        color: #dc2626;
    }

    .offenses-error-state p {
        color: #6b7280;
        margin-bottom: 20px;
    }

    .offenses-btn-retry {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .offenses-btn-retry:hover {
        background: #2563eb;
    }

    /* Modal Specific Styles */
    #offenseFullModal .offenses-info-content {
        max-height: calc(90vh - 120px);
        overflow-y: auto;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .offenses-filter-section > div {
            grid-template-columns: 1fr;
        }

        #offenseFullModal .offenses-modal-content {
            width: 95%;
            margin: 10px;
        }
    }
`;
            document.head.appendChild(style);
        </script>
    @endsection
