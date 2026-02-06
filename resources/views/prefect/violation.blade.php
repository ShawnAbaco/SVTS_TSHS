@extends('prefect.layout')

@section('content')
    <div class="main-container">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Violation Management</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput" class="search-input">

                <!-- Export Buttons Group -->
                <div class="export-buttons">
                    <!-- Print PDF Button -->
                    <button class="btn-export btn-pdf" id="printPdfBtn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>

                    <!-- Export Excel Button -->
                    <button class="btn-export btn-excel" id="exportExcelBtn">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>

                    <!-- Add Violation Button -->
                    <a href="{{ route('violations.create') }}" class="btn-primary" id="createBtn">
                        <i class="fas fa-plus"></i> Add Violation
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Filter & Bulk Actions -->
        <div class="select-options">
            <!-- Left Side: Status Filter and View Type Dropdown -->
            <div class="left-controls">
                <!-- Status Filter -->
                <div class="status-filter">
                    <label for="statusFilter">Filter by Status:</label>
                    <select id="statusFilter" class="status-dropdown">
                        <option value="all">📊 All Status</option>
                        <option value="in_progress" style="background-color: #17a2b8; color: white;">🔄 In Progress</option>
                        <option value="pending" style="background-color: #ffeb3b; color: #8a6d3b;">⏳ Pending</option>
                        <option value="resolved" style="background-color: #28a745; color: white;">✅ Resolved</option>
                        <option value="noncompliant" style="background-color: #ff9800; color: white;">⚠️ Noncompliant
                        </option>
                        <option value="dismissed" style="background-color: #dc3545; color: white;">🚫 Dismissed</option>
                    </select>
                </div>

                <!-- View Type Dropdown -->
                <div class="view-type-dropdown">
                    <select id="viewTypeSelect" class="view-type-select">
                        <option value="individual_per_offense"
                            {{ $viewType == 'individual_per_offense' ? 'selected' : '' }}>👤 Individual Per Offense</option>
                        <option value="individual" {{ $viewType == 'individual' ? 'selected' : '' }}>👤 Individual View
                        </option>
                        <option value="group_per_offense" {{ $viewType == 'group_per_offense' ? 'selected' : '' }}>👥 Group
                            By Offense</option>
                        <option value="group" {{ $viewType == 'group' ? 'selected' : '' }}>👥 Group View</option>
                    </select>
                </div>
            </div>

            <!-- Right Side: Bulk Actions -->
            <div class="right-controls">
                <button class="btn-schedule" id="setScheduleBtn">📅 Set Appointment</button>
                <button class="btn-anecdotal" id="createAnecdotalBtn">📝 Create Anecdotal</button>
                <button class="btn-info" id="updateSanctionBtn">⏱️ Update Sanction</button>
            </div>
        </div>

        <!-- Violation Table -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <label class="select-label">
                                <input type="checkbox" id="selectAll">
                            </label>
                        </th>
                        @if ($viewType == 'group' || $viewType == 'group_per_offense')
                            <th>Students</th>
                        @else
                            <th>Student Name</th>
                        @endif
                        <th>Incident</th>
                        <th>Offense Type</th>
                        <th>Sanction</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @if ($viewType == 'group' || $viewType == 'group_per_offense')
                        <!-- GROUP VIEWS -->
                        @forelse($byGroupViolations as $group)
                            <tr data-group-key="{{ $group->group_key }}" data-incident="{{ $group->violation_incident }}"
                                data-offense-type="{{ $group->offense_type }}"
                                data-sanction="{{ $group->sanction_consequences }}"
                                data-date="{{ $viewType == 'group_per_offense' ? ($group->has_multiple_dates ? $group->earliest_date : $group->original_date) : $group->violation_date }}"
                                data-time="{{ $viewType == 'group' ? ($group->has_multiple_times ? $group->earliest_time : $group->original_time) : '' }}"
                                data-status="{{ $group->status ?? 'pending' }}"
                                data-updated-at="{{ $group->updated_at ?? now() }}"
                                data-sanction-start-at="{{ $group->sanction_start_at ? \Carbon\Carbon::parse($group->sanction_start_at)->format('M d, Y h:i A') : '' }}"
                                data-sanction-end-at="{{ $group->sanction_end_at ? \Carbon\Carbon::parse($group->sanction_end_at)->format('M d, Y h:i A') : '' }}"
                                data-sanction-status="{{ $group->sanction_status ?? '' }}" class="group-row">
                                <td>
                                    <input type="checkbox" class="row-checkbox group-checkbox"
                                        value="{{ $group->group_key }}" data-type="group">
                                </td>
                                <td>
                                    <div class="student-list">
                                        @foreach ($group->students as $student)
                                            <span class="student-name">{{ $student->student_fname }}
                                                {{ $student->student_lname }}</span>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ $group->violation_incident }}</td>
                                <td>{{ $group->offense_type }}</td>
                                <td>{{ $group->sanction_consequences }}</td>
                                <td>
                                    @if ($viewType == 'group_per_offense')
                                        {{ $group->has_multiple_dates ? \Carbon\Carbon::parse($group->earliest_date)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($group->latest_date)->format('M d, Y') : \Carbon\Carbon::parse($group->original_date)->format('M d, Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($group->violation_date)->format('F j, Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($viewType == 'group')
                                        {{ $group->violation_time }}
                                    @else
                                        <!-- For group_per_offense, show date range or just the date -->
                                        {{ $group->has_multiple_dates ? 'Multiple Dates' : \Carbon\Carbon::parse($group->original_date)->format('F j, Y') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $group->status ?? 'pending' }}">
                                        {{ ucfirst($group->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-view view-btn" data-group-key="{{ $group->group_key }}"
                                            data-incident="{{ $group->violation_incident }}"
                                            data-offense-type="{{ $group->offense_type }}"
                                            data-sanction="{{ $group->sanction_consequences }}"
                                            data-date="{{ $viewType == 'group_per_offense' ? ($group->has_multiple_dates ? $group->earliest_date : $group->original_date) : $group->violation_date }}"
                                            data-time="{{ $viewType == 'group' ? ($group->has_multiple_times ? $group->earliest_time : $group->original_time) : '' }}"
                                            data-status="{{ $group->status ?? 'pending' }}"
                                            data-updated-at="{{ $group->updated_at ?? now() }}"
                                            data-sanction-start-at="{{ $group->sanction_start_at ? \Carbon\Carbon::parse($group->sanction_start_at)->format('M d, Y h:i A') : '' }}"
                                            data-sanction-end-at="{{ $group->sanction_end_at ? \Carbon\Carbon::parse($group->sanction_end_at)->format('M d, Y h:i A') : '' }}"
                                            data-sanction-status="{{ $group->sanction_status ?? '' }}"
                                            data-students="{{ json_encode(
                                                $group->students->map(function ($student) {
                                                    return $student->student_fname . ' ' . $student->student_lname;
                                                }),
                                            ) }}">
                                            👁️ View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="no-data">⚠️ No violations found</td>
                            </tr>
                        @endforelse
                    @else
                        <!-- INDIVIDUAL VIEWS (individual and individual_per_offense) -->
                        @forelse($violations as $violation)
                            @if ($viewType == 'individual')
                                @php
                                    $isMerged = isset($violation->merged_count) && $violation->merged_count > 1;
                                    $offenseType = $isMerged
                                        ? $violation->merged_offense_types
                                        : $violation->offense->offense_type;
                                    $sanction = $isMerged
                                        ? $violation->merged_sanctions
                                        : $violation->sanction->sanction_consequences;
                                @endphp
                            @else
                                @php
                                    $isMerged = false;
                                    $offenseType = $violation->offense->offense_type;
                                    $sanction = $violation->sanction->sanction_consequences;
                                @endphp
                            @endif

                            <tr data-violation-id="{{ $violation->violation_id }}"
                                data-violation-ids="{{ $isMerged ? json_encode($violation->merged_violation_ids) : json_encode([$violation->violation_id]) }}"
                                data-student-id="{{ $violation->student->student_id }}"
                                data-student-name="{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}"
                                data-offense-type="{{ $offenseType }}" data-sanction="{{ $sanction }}"
                                data-incident="{{ $violation->violation_incident }}"
                                data-date="{{ \Carbon\Carbon::parse($violation->violation_date)->format('F j, Y') }}"
                                data-status="{{ $violation->status }}"
                                data-time="{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}"
                                data-updated-at="{{ $violation->updated_at }}"
                                data-is-merged="{{ $isMerged ? 'true' : 'false' }}"
                                data-sanction-start-at="{{ $violation->sanction_start_at ? \Carbon\Carbon::parse($violation->sanction_start_at)->format('M d, Y h:i A') : '' }}"
                                data-sanction-end-at="{{ $violation->sanction_end_at ? \Carbon\Carbon::parse($violation->sanction_end_at)->format('M d, Y h:i A') : '' }}"
                                data-sanction-status="{{ $violation->sanction_status ?? '' }}"
                                data-previous-statuses="{{ $violation->previous_statuses ? json_encode($violation->previous_statuses) : '[]' }}"
                                class="individual-row">
                                <td>
                                    <input type="checkbox" class="row-checkbox violation-checkbox"
                                        value="{{ $violation->violation_id }}"
                                        data-violation-ids="{{ $isMerged ? json_encode($violation->merged_violation_ids) : json_encode([$violation->violation_id]) }}"
                                        data-type="individual" data-is-merged="{{ $isMerged ? 'true' : 'false' }}">
                                </td>
                                <td>
                                    {{ $violation->student->student_fname }} {{ $violation->student->student_lname }}
                                    @if ($isMerged)
                                        <span class="badge badge-info" style="font-size: 0.7em; margin-left: 5px;">
                                            ({{ $violation->merged_count }} violations)
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $violation->violation_incident }}</td>
                                <td>{{ $offenseType }}</td>
                                <td>{{ $sanction }}</td>
                                <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('F j, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $violation->status }}">
                                        {{ ucfirst($violation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-view view-btn"
                                            data-violation-id="{{ $violation->violation_id }}"
                                            data-student-id="{{ $violation->student->student_id }}"
                                            data-student-name="{{ $violation->student->student_fname }} {{ $violation->student->student_lname }}"
                                            data-incident="{{ $violation->violation_incident }}"
                                            data-offense-type="{{ $offenseType }}" data-sanction="{{ $sanction }}"
                                            data-date="{{ \Carbon\Carbon::parse($violation->violation_date)->format('F j, Y') }}"
                                            data-time="{{ \Carbon\Carbon::parse($violation->violation_time)->format('h:i A') }}"
                                            data-status="{{ $violation->status }}"
                                            data-updated-at="{{ $violation->updated_at }}"
                                            data-sanction-start-at="{{ $violation->sanction_start_at ? \Carbon\Carbon::parse($violation->sanction_start_at)->format('M d, Y h:i A') : '' }}"
                                            data-sanction-end-at="{{ $violation->sanction_end_at ? \Carbon\Carbon::parse($violation->sanction_end_at)->format('M d, Y h:i A') : '' }}"
                                            data-sanction-status="{{ $violation->sanction_status ?? '' }}"
                                            data-is-merged="{{ $isMerged ? 'true' : 'false' }}"
                                            data-merged-count="{{ $isMerged ? $violation->merged_count : '' }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn-secondary settle-btn" data-is-merged="false"
                                            data-status="{{ $violation->status }}">
                                            ⚖️ Settle
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="no-data">⚠️ No violations found</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container">
            <div class="pagination-links">
                {{-- Individual Views Pagination --}}
                @if (($viewType == 'individual' || $viewType == 'individual_per_offense') && $violations->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($violations->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $violations->previousPageUrl() }}" rel="prev">‹
                                        Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $start = max(1, $violations->currentPage() - 2);
                                $end = min($violations->lastPage(), $start + 4);
                                if ($end - $start < 4 && $start > 1) {
                                    $start = max(1, $end - 4);
                                }
                            @endphp

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $violations->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $violations->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($violations->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $violations->nextPageUrl() }}" rel="next">Next
                                        ›</a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">Next ›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                    {{-- Group Views Pagination --}}
                @elseif (($viewType == 'group' || $viewType == 'group_per_offense') && $byGroupViolations->hasPages())
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($byGroupViolations->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $byGroupViolations->previousPageUrl() }}"
                                        rel="prev">‹
                                        Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $start = max(1, $byGroupViolations->currentPage() - 2);
                                $end = min($byGroupViolations->lastPage(), $start + 4);
                                if ($end - $start < 4 && $start > 1) {
                                    $start = max(1, $end - 4);
                                }
                            @endphp

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $byGroupViolations->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $byGroupViolations->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($byGroupViolations->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $byGroupViolations->nextPageUrl() }}"
                                        rel="next">Next
                                        ›</a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true">
                                    <span class="page-link">Next ›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
            <div class="pagination-info">
                @if ($viewType == 'individual' || $viewType == 'individual_per_offense')
                    @if ($violations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        Showing {{ $violations->firstItem() ?? 0 }} to {{ $violations->lastItem() ?? 0 }} of
                        {{ $violations->total() }} entries
                    @endif
                @elseif ($viewType == 'group' || $viewType == 'group_per_offense')
                    @if ($byGroupViolations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        Showing {{ $byGroupViolations->firstItem() ?? 0 }} to {{ $byGroupViolations->lastItem() ?? 0 }}
                        of
                        {{ $byGroupViolations->total() }} groups
                    @else
                        Showing {{ count($byGroupViolations) }} group(s)
                    @endif
                @endif
            </div>
        </div>

        <!-- 👤 Violation Info Modal - Updated with Compact Design -->
        <div class="modal" id="infoModal">
            <div class="modal-content compact-modal">
                <!-- Header -->
                <div class="modal-header">
                    <div class="header-content">
                        <div class="profile-avatar">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Violation Information</h3>
                            <p class="modal-subtitle" id="info_violation_id_display"></p>
                        </div>
                    </div>
                    <button class="close-modal" id="closeInfoModalBtn">&times;</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="modal-tabs">
                    <button class="tab-btn active" data-tab="violation-info">
                        <i class="fas fa-info-circle"></i> Violation Info
                    </button>
                    <button class="tab-btn" data-tab="student-details">
                        <i class="fas fa-user-graduate"></i> Student Details
                    </button>
                    <button class="tab-btn" data-tab="sanction-timeline">
                        <i class="fas fa-clock"></i> Sanction Timeline
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Violation Information Tab -->
                    <div class="tab-pane active" id="violation-info-tab">
                        <div class="modal-body">
                            <!-- Basic Information -->
                            <div class="info-row">
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-id-badge"></i> Violation ID
                                    </label>
                                    <span class="info-value" id="info_violation_id"></span>
                                </div>
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-exclamation-circle"></i> Status
                                    </label>
                                    <span class="info-value" id="info_status"></span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-gavel"></i> Offense Type
                                    </label>
                                    <span class="info-value" id="info_offense_type"></span>
                                </div>
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-calendar-day"></i> Date
                                    </label>
                                    <span class="info-value" id="info_date"></span>
                                </div>
                                <div class="info-group">
                                    <label class="info-label">
                                        <i class="fas fa-clock"></i> Time
                                    </label>
                                    <span class="info-value" id="info_time"></span>
                                </div>
                            </div>

                            <!-- Incident Details -->
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-exclamation-triangle"></i> Incident Details
                                </h4>
                                <div class="info-row">
                                    <div class="info-group full-width">
                                        <label class="info-label">
                                            <i class="fas fa-clipboard"></i> Incident Description
                                        </label>
                                        <span class="info-value" id="info_incident"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sanction Information -->
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-balance-scale"></i> Sanction Information
                                </h4>
                                <div class="info-row">
                                    <div class="info-group full-width">
                                        <label class="info-label">
                                            <i class="fas fa-hammer"></i> Sanction Applied
                                        </label>
                                        <span class="info-value" id="info_sanction"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Details Tab -->
                    <div class="tab-pane" id="student-details-tab">
                        <div class="modal-body">
                            <!-- Individual Student View -->
                            <div class="info-section individual-view">
                                <h4 class="section-title">
                                    <i class="fas fa-user-graduate"></i> Student Information
                                </h4>
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-id-card"></i> Student ID
                                        </label>
                                        <span class="info-value" id="info_student_id"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-user"></i> Student Name
                                        </label>
                                        <span class="info-value" id="info_student_name"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Group Student View -->
                            <div class="info-section group-view" style="display: none;">
                                <h4 class="section-title">
                                    <i class="fas fa-users"></i> Students Involved
                                </h4>
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-hashtag"></i> Number of Students
                                        </label>
                                        <span class="info-value" id="info_students_count"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-key"></i> Group Key
                                        </label>
                                        <span class="info-value" id="info_group_key"></span>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-group full-width">
                                        <label class="info-label">
                                            <i class="fas fa-list"></i> Students List
                                        </label>
                                        <span class="info-value" id="info_students_list"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sanction Timeline Tab -->
                    <div class="tab-pane" id="sanction-timeline-tab">
                        <div class="modal-body">
                            <div class="info-section">
                                <h4 class="section-title">
                                    <i class="fas fa-clock"></i> Timeline & Status
                                </h4>

                                <!-- Status Information -->
                                <div class="info-row">
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-history"></i> Current Status
                                        </label>
                                        <span class="info-value" id="info_status_timeline"></span>
                                    </div>
                                    <div class="info-group">
                                        <label class="info-label">
                                            <i class="fas fa-calendar-check"></i> Last Updated
                                        </label>
                                        <span class="info-value" id="info_status_updated"></span>
                                    </div>
                                </div>

                                <!-- Sanction Schedule -->
                                <div class="contact-section">
                                    <h4 class="section-title">
                                        <i class="fas fa-calendar-alt"></i> Sanction Schedule
                                    </h4>

                                    <!-- Start Time -->
                                    <div class="contact-item">
                                        <div class="contact-icon" style="background: #3b82f6;">
                                            <i class="fas fa-play-circle"></i>
                                        </div>
                                        <div class="contact-details">
                                            <div class="contact-label">Sanction Start</div>
                                            <span class="contact-value" id="info_sanction_start_at"></span>
                                        </div>
                                    </div>

                                    <!-- End Time -->
                                    <div class="contact-item">
                                        <div class="contact-icon" style="background: #10b981;">
                                            <i class="fas fa-stop-circle"></i>
                                        </div>
                                        <div class="contact-details">
                                            <div class="contact-label">Sanction End</div>
                                            <span class="contact-value" id="info_sanction_end_at"></span>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="contact-item">
                                        <div class="contact-icon" style="background: #f59e0b;">
                                            <i class="fas fa-tasks"></i>
                                        </div>
                                        <div class="contact-details">
                                            <div class="contact-label">Sanction Status</div>
                                            <span class="contact-value" id="info_sanction_status"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button class="btn-export modal-export" id="printInfoBtn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- 📅 Set Schedule Modal -->
        <div class="modal" id="setScheduleModal">
            <div class="modal-content">
                <!-- Header -->
                <div class="edit-modal-headerr">
                    <h2>Set Schedule for Selected Violations</h2>
                    <button class="close-btn" id="closeScheduleModal">✖</button>
                </div>

                <!-- Body -->
                <div class="edit-modal-body">
                    <form id="setScheduleForm" method="POST"
                        action="{{ route('prefect.storeMultipleAppointments') }}">
                        @csrf

                        <div class="selected-violations">
                            <h5 class="section-title">Selected Violations</h5>
                            <div id="selectedViolationsList" class="selected-list"></div>
                        </div>

                        <div class="edit-form-grid">
                            <!-- Appointment Date -->
                            <div class="edit-form-group">
                                <label>Appointment Date</label>
                                <input type="date" id="schedule_date" name="schedule_date" required
                                    min="{{ date('Y-m-d') }}">
                                <span class="error-message" id="schedule_date_error"></span>
                                <div class="form-hint">Select a future date</div>
                            </div>

                            <!-- Appointment Time -->
                            <div class="edit-form-group">
                                <label>Appointment Time</label>
                                <input type="time" id="schedule_time" name="schedule_time" required>
                                <span class="error-message" id="schedule_time_error"></span>
                                <div class="form-hint">Select appointment time</div>
                            </div>

                            <!-- Additional Notes -->
                            <div class="edit-form-group full-width">
                                <label class="optional">Additional Notes</label>
                                <textarea id="violation_app_notes" name="violation_app_notes"
                                    placeholder="Enter any additional notes or instructions..." rows="3"></textarea>
                                <span class="error-message" id="notes_error"></span>
                            </div>
                        </div>

                        <div class="required-fields-note">Indicates required fields</div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="edit-modal-actions">
                    <button type="button" class="btn-secondary" id="cancelScheduleBtn">
                        <span>❌ Cancel</span>
                    </button>
                    <button type="submit" class="btn-primary" form="setScheduleForm">
                        <span>📅 Create Appointments</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ⏱️ Update Sanction Modal - MAXIMIZED SPACE -->
        <div class="modal" id="updateSanctionModal">
            <div class="modal-content">
                <!-- Header -->
                <div class="edit-modal-headerr">
                    <h2>Update Sanction for Selected Violations</h2>
                    <button class="close-btn" id="closeSanctionModal">✖</button>
                </div>

                <!-- Body - Maximized Space -->
                <div class="edit-modal-body expanded">
                    <form id="updateSanctionForm" method="POST" action="{{ route('prefect.updateSanction') }}">
                        @csrf

                        <div class="selected-violations expanded">
                            <h5 class="section-title">Selected Violations</h5>
                            <div id="selectedViolationsForSanction" class="selected-list expanded-list"></div>
                        </div>

                        <div class="edit-form-grid expanded-grid">
                            <!-- Sanction Start Date -->
                            <div class="edit-form-group expanded">
                                <label for="sanction_start_date">Sanction Start Date *</label>
                                <input type="date" id="sanction_start_date" name="sanction_start_date" required>
                                <span class="error-message" id="sanction_start_date_error"></span>
                            </div>

                            <!-- Sanction Start Time -->
                            <div class="edit-form-group expanded">
                                <label for="sanction_start_time">Sanction Start Time</label>
                                <input type="time" id="sanction_start_time" name="sanction_start_time">
                                <span class="error-message" id="sanction_start_time_error"></span>
                                <div class="form-hint">Optional</div>
                            </div>

                            <!-- Sanction End Date -->
                            <div class="edit-form-group expanded">
                                <label for="sanction_end_date">Sanction End Date</label>
                                <input type="date" id="sanction_end_date" name="sanction_end_date">
                                <span class="error-message" id="sanction_end_date_error"></span>
                            </div>

                            <!-- Sanction End Time -->
                            <div class="edit-form-group expanded">
                                <label for="sanction_end_time">Sanction End Time</label>
                                <input type="time" id="sanction_end_time" name="sanction_end_time">
                                <span class="error-message" id="sanction_end_time_error"></span>
                                <div class="form-hint">Optional</div>
                            </div>

                            <!-- Sanction Status -->
                            <div class="edit-form-group full-width expanded">
                                <label for="sanction_status">Sanction Status *</label>
                                <select id="sanction_status" name="sanction_status" required class="expanded-select">
                                    <option value="" disabled selected>-- Select Status --</option>
                                    <option value="pending">Pending</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="neglected">Neglected</option>
                                    <option value="completed">Completed</option>
                                    <option value="dismissed">Dismissed</option>
                                </select>
                                <span class="error-message" id="sanction_status_error"></span>
                            </div>
                        </div>

                        <div class="required-fields-note">* Indicates required fields</div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="edit-modal-actions">
                    <button type="button" class="btn-secondary" id="cancelSanctionBtn">
                        <span>❌ Cancel</span>
                    </button>
                    <button type="submit" class="btn-primary" form="updateSanctionForm">
                        <span>💾 Update Sanction</span>
                    </button>
                </div>
            </div>
        </div>





        <!-- 📝 Create Anecdotal Modal -->
        <div class="modal" id="createAnecdotalModal">
            <div class="modal-content">
                <!-- Header -->
                <div class="edit-modal-headerr">
                    <h2>Create Anecdotal Record for Selected Violations</h2>
                    <button class="close-btn" id="closeAnecdotalModal">✖</button>
                </div>

                <!-- Body -->
                <div class="edit-modal-body">
                    <form id="createAnecdotalForm" method="POST"
                        action="{{ route('prefect.storeMultipleAnecdotals') }}">
                        @csrf

                        <div class="selected-violations">
                            <h5 class="section-title">Selected Violations</h5>
                            <div id="selectedViolationsForAnecdotal" class="selected-list"></div>
                        </div>

                        <div class="edit-form-grid">
                            <!-- Anecdotal Date -->
                            <div class="edit-form-group">
                                <label>Anecdotal Date</label>
                                <input type="date" id="anecdotal_date" name="anecdotal_date" required
                                    value="{{ date('Y-m-d') }}">
                                <span class="error-message" id="anecdotal_date_error"></span>
                            </div>

                            <!-- Anecdotal Time -->
                            <div class="edit-form-group">
                                <label>Anecdotal Time</label>
                                <input type="time" id="anecdotal_time" name="anecdotal_time" required
                                    value="{{ date('H:i') }}">
                                <span class="error-message" id="anecdotal_time_error"></span>
                            </div>

                            <!-- Solution -->
                            <div class="edit-form-group full-width">
                                <label>Solution</label>
                                <textarea id="violation_anec_solution" name="violation_anec_solution"
                                    placeholder="Describe the solution implemented..." required rows="4"></textarea>
                                <span class="error-message" id="solution_error"></span>
                            </div>

                            <!-- Recommendation -->
                            <div class="edit-form-group full-width">
                                <label>Recommendation</label>
                                <textarea id="violation_anec_recommendation" name="violation_anec_recommendation"
                                    placeholder="Provide recommendations for future prevention..." required rows="4"></textarea>
                                <span class="error-message" id="recommendation_error"></span>
                            </div>
                        </div>

                        <div class="required-fields-note">Indicates required fields</div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="edit-modal-actions">
                    <button type="button" class="btn-secondary" id="cancelAnecdotalBtn">
                        <span>❌ Cancel</span>
                    </button>
                    <button type="submit" class="btn-primary" form="createAnecdotalForm">
                        <span>📝 Create Anecdotal Records</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ⚖️ Settlement Modal (Update Status Only) -->
        <div class="modal" id="settlementModal">
            <div class="modal-content settlement-modal">
                <div class="edit-modal-header"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px 8px 0 0;">
                    <div>
                        <h2 style="margin: 0; font-size: 1.5em;">⚖️ Settle Violation - Status Update Only</h2>
                        <p style="margin: 5px 0 0 0; font-size: 0.9em; opacity: 0.9;">Update only the sanction status;
                            all
                            other details remain unchanged</p>
                    </div>
                    <button class="close-btn" id="closeSettlementModal">✖</button>
                </div>

                <div class="edit-modal-body">
                    <form id="settlementForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="record_id" id="settlement_violation_record_id">
                        <input type="hidden" name="current_status" id="settlement_current_status">
                        {{-- <input type="hidden" name="previous_statuses" id="settlement_previous_statuses"> --}}

                        <!-- Violation Details (Read-Only) -->
                        <div class="settlement-details-section"
                            style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #667eea;">
                            <h4 style="margin-top: 0; color: #333; font-weight: 600;">📋 Violation Details</h4>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">Student
                                        Name</label>
                                    <p id="settlement_student_name"
                                        style="margin: 5px 0 0 0; font-size: 1em; color: #333; font-weight: 500;"></p>
                                </div>

                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">Incident</label>
                                    <p id="settlement_incident"
                                        style="margin: 5px 0 0 0; font-size: 1em; color: #333; font-weight: 500;"></p>
                                </div>

                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">Offense
                                        Type</label>
                                    <p id="settlement_offense_type"
                                        style="margin: 5px 0 0 0; font-size: 1em; color: #333;"></p>
                                </div>

                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">Sanction</label>
                                    <p id="settlement_sanction" style="margin: 5px 0 0 0; font-size: 1em; color: #333;">
                                    </p>
                                </div>

                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">Date</label>
                                    <p id="settlement_date" style="margin: 5px 0 0 0; font-size: 1em; color: #333;">
                                    </p>
                                </div>

                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">Time</label>
                                    <p id="settlement_time" style="margin: 5px 0 0 0; font-size: 1em; color: #333;">
                                    </p>
                                </div>
                            </div>

                            <!-- Current Status Display -->
                            <div
                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(102, 126, 234, 0.2);">
                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">🔄
                                        Current Violation Status</label>
                                    <p id="settlement_current_violation_status"
                                        style="margin: 5px 0 0 0; font-size: 1em; color: #333; font-weight: 500; padding: 8px 12px; background: white; border-radius: 4px;">
                                    </p>
                                </div>

                                <div class="readonly-field">
                                    <label
                                        style="color: #666; font-size: 0.85em; text-transform: uppercase; font-weight: 600;">📅
                                        Sanction Status</label>
                                    <p id="settlement_sanction_status"
                                        style="margin: 5px 0 0 0; font-size: 1em; color: #333; font-weight: 500; padding: 8px 12px; background: white; border-radius: 4px;">
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- STATUS UPDATE SECTION -->
                        <div
                            style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 20px; border-radius: 8px; border: 2px solid #667eea; position: relative;">
                            <div style="position: absolute; top: -12px; left: 20px; background: white; padding: 0 8px;">
                                <span
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85em; font-weight: 600;">✏️
                                    STATUS UPDATE</span>
                            </div>

                            <div class="edit-form-group" style="margin-top: 15px;">
                                <label for="settlement_status" style="color: #333; font-weight: 600; font-size: 1.05em;">
                                    Update Violation Status
                                    <span style="color: #e74c3c;">*</span>
                                </label>
                                <select name="status" id="settlement_status" required
                                    style="font-size: 1.05em; padding: 12px; border: 2px solid #667eea; background-color: #fff; border-radius: 6px; font-weight: 500;">
                                    <option value="" disabled selected>-- Select New Status --</option>
                                    <option value="pending">⏳ Pending - Awaiting Action</option>
                                    <option value="in_progress">🔄 In Progress - Being Handled</option>
                                    <option value="resolved">✅ Resolved - Issue Settled</option>
                                    <option value="noncompliant">⚠️ Noncompliant - Student Failed to Comply</option>
                                    <option value="dismissed">🚫 Dismissed - Not Substantiated</option>
                                </select>
                                <span class="error-message" id="settlement_status_error"></span>
                                <div class="form-hint" style="margin-top: 8px; color: #667eea; font-weight: 500;">
                                    💡 Tip: Select the new status to finalize this violation settlement
                                </div>
                            </div>


                        </div>



                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:25px;">
                            <button type="button" class="btn-secondary" id="cancelSettlementBtn"
                                style="padding: 10px 20px;">
                                <span>❌ Cancel</span>
                            </button>
                            <button type="button" class="btn-primary" id="proceedSettlementBtn"
                                style="padding: 10px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-weight: 600;">
                                <span>✅ Proceed with Settlement</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <div class="notification-modal" id="notificationModal">
            <div class="notification-content" id="notificationContent">
                <div class="notification-icon" id="notificationIcon"></div>
                <div class="notification-message" id="notificationMessage"></div>
                <div class="notification-actions" id="notificationActions">
                    <!-- OK button removed for success messages -->
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="notification-modal" id="confirmationModal">
            <div class="notification-content">
                <div class="notification-icon">⚠️</div>
                <div class="notification-message" id="confirmationMessage"></div>
                <div class="notification-actions">
                    <button class="btn-confirm" id="confirmAction">Confirm</button>
                    <button class="btn-cancel" id="cancelAction">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // ==========================
        // Violation Management
        // ==========================

        class NotificationManager {
            constructor() {
                this.notificationModal = document.getElementById('notificationModal');
                this.confirmationModal = document.getElementById('confirmationModal');
                this.notificationMessage = document.getElementById('notificationMessage');
                this.confirmationMessage = document.getElementById('confirmationMessage');
                this.notificationIcon = document.getElementById('notificationIcon');
                this.notificationActions = document.getElementById('notificationActions');
                this.confirmAction = document.getElementById('confirmAction');
                this.cancelAction = document.getElementById('cancelAction');

                this.autoCloseTimeout = null;
                try {
                    if (this.notificationModal && this.notificationModal.parentElement !== document.body) {
                        document.body.appendChild(this.notificationModal);
                    }
                    if (this.confirmationModal && this.confirmationModal.parentElement !== document.body) {
                        document.body.appendChild(this.confirmationModal);
                    }
                } catch (err) {
                    console.warn('NotificationManager: could not move modals to body', err);
                }

                this.setupEventListeners();
            }

            setupEventListeners() {
                this.confirmAction.addEventListener('click', () => {
                    if (this.confirmCallback) {
                        this.confirmCallback();
                    }
                    this.hideConfirmation();
                });

                this.cancelAction.addEventListener('click', () => {
                    if (this.cancelCallback) {
                        this.cancelCallback();
                    }
                    this.hideConfirmation();
                });

                this.notificationModal.addEventListener('click', (e) => {
                    if (e.target === this.notificationModal) {
                        this.hideNotification();
                    }
                });

                this.confirmationModal.addEventListener('click', (e) => {
                    if (e.target === this.confirmationModal) {
                        this.hideConfirmation();
                    }
                });
            }

            showNotification(message, type = 'info') {
                const icons = {
                    success: '✅',
                    error: '❌',
                    warning: '⚠️',
                    info: 'ℹ️'
                };

                this.notificationIcon.textContent = icons[type] || icons.info;
                this.notificationMessage.textContent = message;
                this.notificationModal.className = `notification-modal notification-${type}`;

                if (this.autoCloseTimeout) {
                    clearTimeout(this.autoCloseTimeout);
                }

                try {
                    if (this.notificationModal && this.notificationModal.parentElement !== document.body) {
                        document.body.appendChild(this.notificationModal);
                    }
                    Object.assign(this.notificationModal.style, {
                        position: 'fixed',
                        top: '0',
                        left: '0',
                        width: '100%',
                        height: '100%',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        zIndex: '99999'
                    });
                } catch (err) {
                    console.warn('NotificationManager showNotification:', err);
                }

                try {
                    document.body.style.overflow = 'hidden';
                } catch (e) {}

                if (type === 'success') {
                    this.notificationActions.innerHTML = '';
                    this.notificationModal.style.display = 'flex';

                    this.autoCloseTimeout = setTimeout(() => {
                        this.hideNotification();
                    }, 1000);
                } else {
                    this.notificationActions.innerHTML =
                        '<button class="btn-confirm" id="notificationConfirm">OK</button>';

                    const okButton = document.getElementById('notificationConfirm');
                    if (okButton) {
                        okButton.addEventListener('click', () => {
                            this.hideNotification();
                        });
                    }

                    this.notificationModal.style.display = 'flex';
                }
            }

            hideNotification() {
                try {
                    this.notificationModal.style.display = 'none';
                } catch (e) {}
                try {
                    document.body.style.overflow = '';
                } catch (e) {}
                if (this.autoCloseTimeout) {
                    clearTimeout(this.autoCloseTimeout);
                    this.autoCloseTimeout = null;
                }
            }

            showConfirmation(message, confirmCallback, cancelCallback = null) {
                this.confirmationMessage.textContent = message;
                this.confirmCallback = confirmCallback;
                this.cancelCallback = cancelCallback;
                this.confirmationModal.style.display = 'flex';
            }

            hideConfirmation() {
                this.confirmationModal.style.display = 'none';
                this.confirmCallback = null;
                this.cancelCallback = null;
            }
        }

        const notifications = new NotificationManager();

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }

        const csrfToken = getCsrfToken();

        class ViolationStatusOptionsManager {
            static getAvailableOptions(currentStatus) {
                // Define all possible status options
                const allOptions = [{
                        value: 'pending',
                        label: '⏳ Pending - Awaiting Action'
                    },
                    {
                        value: 'in_progress',
                        label: '🔄 In Progress - Being Handled'
                    },
                    {
                        value: 'resolved',
                        label: '✅ Resolved - Issue Settled'
                    },
                    {
                        value: 'noncompliant',
                        label: '⚠️ Noncompliant - Student Failed to Comply'
                    },
                    {
                        value: 'dismissed',
                        label: '🚫 Dismissed - Not Substantiated'
                    },
                ];

                // Define allowed transitions (must match backend)
                const allowedTransitions = {
                    'pending': ['in_progress', 'dismissed'],
                    'in_progress': ['resolved', 'noncompliant', 'dismissed'],
                    'noncompliant': ['resolved'],
                    'resolved': [],
                    'dismissed': [],
                };

                const allowedValues = allowedTransitions[currentStatus.toLowerCase()] || [];

                // Filter options based on allowed transitions
                const availableOptions = allOptions.filter(opt =>
                    allowedValues.includes(opt.value)
                );

                return availableOptions;
            }

            static populateDropdown(selectElement, currentStatus) {
                const availableOptions = this.getAvailableOptions(currentStatus);

                selectElement.innerHTML = '';

                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select New Status --';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                selectElement.appendChild(defaultOption);

                // Add available options
                availableOptions.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.label;
                    selectElement.appendChild(option);
                });

                // If no options available (final status), disable the dropdown
                if (availableOptions.length === 0) {
                    selectElement.disabled = true;
                    const disabledOption = document.createElement('option');
                    disabledOption.value = currentStatus;
                    disabledOption.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1) +
                        ' (Final Status)';
                    disabledOption.selected = true;
                    selectElement.innerHTML = '';
                    selectElement.appendChild(disabledOption);
                } else {
                    selectElement.disabled = false;
                }

                selectElement.dataset.currentStatus = currentStatus;
            }

            static validateStatusChange(currentStatus, selectedStatus) {
                const allowedTransitions = {
                    'pending': ['in_progress', 'dismissed'],
                    'in_progress': ['resolved', 'noncompliant', 'dismissed'],
                    'noncompliant': ['resolved', 'dismissed'],
                    'resolved': [],
                    'dismissed': [],
                };

                const allowedValues = allowedTransitions[currentStatus.toLowerCase()] || [];
                const isValid = allowedValues.includes(selectedStatus);

                if (!isValid) {
                    const statusMessages = {
                        'pending': '"In Progress" or "Dismissed"',
                        'in_progress': '"Resolved", "Noncompliant", or "Dismissed"',
                        'noncompliant': '"Resolved" or "Dismissed"',
                        'resolved': '(no further changes allowed)',
                        'dismissed': '(no further changes allowed)',
                    };

                    const message = `Cannot change status from "${currentStatus}" to "${selectedStatus}". ` +
                        `Allowed transitions: ${statusMessages[currentStatus]}`;

                    return {
                        valid: false,
                        message: message
                    };
                }

                return {
                    valid: true
                };
            }
        }

        // ==========================
        // SANCTION STATUS OPTIONS MANAGEMENT
        // ==========================

        class SanctionStatusOptionsManager {
            static getAvailableOptions(currentStatus, isForDisplayOnly = false) {
                const allOptions = [{
                        value: 'pending',
                        label: '⏳ Pending',
                        disabled: false
                    },
                    {
                        value: 'ongoing',
                        label: '🔄 Ongoing',
                        disabled: false
                    },
                    {
                        value: 'neglected',
                        label: '⚠️ Neglected',
                        disabled: false
                    },
                    {
                        value: 'completed',
                        label: '✅ Completed',
                        disabled: false
                    },
                    {
                        value: 'dismissed',
                        label: '🚫 Dismissed',
                        disabled: false
                    }
                ];

                // Define which statuses CANNOT be changed to dismissed
                const cannotBeDismissed = ['ongoing', 'neglected', 'completed'];

                // If current status is one that cannot be dismissed, disable dismissed option
                const shouldDisableDismissed = cannotBeDismissed.includes(currentStatus) && !isForDisplayOnly;

                switch (currentStatus) {
                    case 'pending':
                        return allOptions.map(opt => ({
                            ...opt,
                            disabled: isForDisplayOnly ? false : (opt.value !== 'ongoing' && opt.value !==
                                'dismissed')
                        }));

                    case 'ongoing':
                        return allOptions.map(opt => ({
                            ...opt,
                            disabled: isForDisplayOnly ? false : (opt.value === 'pending' || opt.value ===
                                'ongoing' ||
                                (opt.value === 'dismissed' && shouldDisableDismissed))
                        }));

                    case 'neglected':
                        return allOptions.map(opt => ({
                            ...opt,
                            disabled: isForDisplayOnly ? false : ((opt.value !== 'completed' && opt
                                    .value !== 'neglected') ||
                                (opt.value === 'dismissed' && shouldDisableDismissed))
                        }));

                    case 'completed':
                        return allOptions.map(opt => ({
                            ...opt,
                            disabled: isForDisplayOnly ? false : true
                        }));

                    case 'dismissed':
                        return allOptions.map(opt => ({
                            ...opt,
                            disabled: isForDisplayOnly ? false : true
                        }));

                    default:
                        // For any other status, allow dismissed unless current status cannot be dismissed
                        return allOptions.map(opt => ({
                            ...opt,
                            disabled: isForDisplayOnly ? false : (opt.value === 'dismissed' &&
                                shouldDisableDismissed)
                        }));
                }
            }

            static populateDropdown(selectElement, currentStatus) {
                const selectOptions = this.getAvailableOptions(currentStatus, false);

                selectElement.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select Status --';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                selectElement.appendChild(defaultOption);

                selectOptions.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.label;
                    option.disabled = opt.disabled;

                    if (opt.disabled) {
                        if (opt.value === currentStatus) {
                            option.textContent += ' (Current status)';
                        }
                    }

                    // Add helpful text for dismissed option
                    if (opt.value === 'dismissed' && !opt.disabled) {
                        const cannotBeDismissed = ['ongoing', 'neglected', 'completed'];
                        if (!cannotBeDismissed.includes(currentStatus)) {
                            option.textContent += ' (Available for non-active sanctions)';
                        }
                    }

                    if (currentStatus === 'pending' && opt.value === 'ongoing' && !opt.disabled) {
                        option.textContent += ' (Only option for Pending status)';
                    }

                    if (currentStatus === 'neglected' && opt.value === 'completed' && !opt.disabled) {
                        option.textContent += ' (Only option for Neglected status)';
                    }

                    selectElement.appendChild(option);
                });

                selectElement.dataset.currentStatus = currentStatus;
            }

            static shouldDisableForm(currentStatus) {
                return currentStatus === 'completed' || currentStatus === 'dismissed';
            }

            static toggleFormFields(container, currentStatus) {
                const shouldDisable = this.shouldDisableForm(currentStatus);
                const form = container.closest('form');

                if (form) {
                    const inputs = form.querySelectorAll('input, select, textarea');
                    const submitBtn = form.querySelector('button[type="submit"]');

                    inputs.forEach(input => {
                        if (input.type !== 'hidden') {
                            input.disabled = shouldDisable;
                        }
                    });

                    if (submitBtn) {
                        submitBtn.disabled = shouldDisable;
                        if (shouldDisable) {
                            if (currentStatus === 'completed') {
                                submitBtn.innerHTML = '<span>🔒 Locked (Completed)</span>';
                            } else if (currentStatus === 'dismissed') {
                                submitBtn.innerHTML = '<span>🔒 Locked (Dismissed)</span>';
                            }
                            submitBtn.style.opacity = '0.6';
                            submitBtn.style.cursor = 'not-allowed';
                        } else {
                            submitBtn.innerHTML = '<span>💾 Update Sanction</span>';
                            submitBtn.style.opacity = '1';
                            submitBtn.style.cursor = 'pointer';
                        }
                    }

                    const formHint = container.querySelector('.form-hint');
                    if (formHint) {
                        const cannotBeDismissed = ['ongoing', 'neglected', 'completed'];

                        if (currentStatus === 'completed') {
                            formHint.innerHTML = '⚠️ This sanction is marked as completed and cannot be edited.';
                            formHint.style.color = '#856404';
                        } else if (currentStatus === 'dismissed') {
                            formHint.innerHTML = '⚠️ This sanction is dismissed and cannot be edited.';
                            formHint.style.color = '#856404';
                        } else if (currentStatus === 'neglected') {
                            formHint.innerHTML = 'Note: Neglected sanctions can only be changed to "Completed".';
                            formHint.style.color = '#0c5460';
                        } else if (currentStatus === 'ongoing') {
                            formHint.innerHTML =
                                'Note: Ongoing sanctions can be changed to "Completed" or "Neglected". Dismissed option is NOT available.';
                            formHint.style.color = '#0c5460';
                        } else if (currentStatus === 'pending') {
                            formHint.innerHTML =
                                'Note: Pending sanctions can only be changed to "Ongoing" or "Dismissed".';
                            formHint.style.color = '#0c5460';
                        } else if (cannotBeDismissed.includes(currentStatus)) {
                            formHint.innerHTML = 'Note: Dismissed option is NOT available for this status.';
                            formHint.style.color = '#0c5460';
                        } else {
                            formHint.innerHTML = 'Cannot select the same status. Please choose a different status.';
                            formHint.style.color = '#6c757d';
                        }
                    }
                }
            }

            static validateStatusChange(currentStatus, selectedStatus) {
                // Define which statuses CANNOT be changed to dismissed
                const cannotBeDismissed = ['ongoing', 'neglected', 'completed'];

                // Check if trying to change to dismissed when not allowed
                if (selectedStatus === 'dismissed' && cannotBeDismissed.includes(currentStatus)) {
                    return {
                        valid: false,
                        message: `Cannot dismiss sanctions with "${currentStatus}" status. Dismissed option is only available for pending or in-progress sanctions.`
                    };
                }

                if (currentStatus === 'pending' && !['ongoing', 'dismissed'].includes(selectedStatus)) {
                    return {
                        valid: false,
                        message: 'Pending sanctions can only be changed to "Ongoing" or "Dismissed".'
                    };
                }

                if (currentStatus === 'completed' || currentStatus === 'dismissed') {
                    return {
                        valid: false,
                        message: `${currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1)} sanctions cannot be changed.`
                    };
                }

                if (currentStatus === 'neglected' && selectedStatus !== 'completed') {
                    return {
                        valid: false,
                        message: 'Neglected sanctions can only be changed to "Completed".'
                    };
                }

                if (currentStatus === 'ongoing' && !['completed', 'neglected'].includes(selectedStatus)) {
                    return {
                        valid: false,
                        message: 'Ongoing sanctions can only be changed to "Completed" or "Neglected". Dismissed is NOT available.'
                    };
                }

                if (currentStatus === selectedStatus) {
                    return {
                        valid: false,
                        message: `Cannot update to same status (${currentStatus}). Please select a different status.`
                    };
                }

                return {
                    valid: true
                };
            }
        }

        // ==========================
        // VIEW TYPE DROPDOWN FUNCTIONALITY
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const viewTypeSelect = document.getElementById('viewTypeSelect');
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');

            function initializeViewTypeDropdown() {
                if (!viewTypeSelect) return;

                const urlParams = new URLSearchParams(window.location.search);
                const viewFromUrl = urlParams.get('view');
                if (viewFromUrl) {
                    viewTypeSelect.value = viewFromUrl;
                }

                viewTypeSelect.addEventListener('change', function() {
                    switchViewType(this.value);
                });
            }

            function switchViewType(viewType) {
                const currentUrl = new URL(window.location.href);

                currentUrl.searchParams.set('view', viewType);

                if (viewType === 'group' || viewType === 'group_per_offense') {
                    currentUrl.searchParams.delete('group');
                }

                if (viewType === 'individual' || viewType === 'individual_per_offense') {
                    currentUrl.searchParams.set('page', 1);
                }

                if (statusFilter && statusFilter.value !== 'all') {
                    currentUrl.searchParams.set('status', statusFilter.value);
                } else {
                    currentUrl.searchParams.delete('status');
                }

                if (searchInput && searchInput.value.trim() !== '') {
                    currentUrl.searchParams.set('search', searchInput.value);
                } else {
                    currentUrl.searchParams.delete('search');
                }

                showLoadingIndicator();

                window.location.href = currentUrl.toString();
            }

            function showLoadingIndicator() {
                const tableBody = document.getElementById('tableBody');
                if (tableBody) {
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="no-data">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 20px;">
                                <div class="spinner" style="width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #4a6baf; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <span>Loading ${viewTypeSelect.value} view...</span>
                            </div>
                        </td>
                    </tr>
                `;

                    const style = document.createElement('style');
                    style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                    document.head.appendChild(style);
                }
            }

            initializeViewTypeDropdown();

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#tableBody tr');
                    const selectedStatus = statusFilter ? statusFilter.value : 'all';
                    let visibleCount = 0;

                    rows.forEach(row => {
                        if (row.classList.contains('no-data')) return;

                        const rowStatus = row.getAttribute('data-status')?.toLowerCase() ||
                            'pending';
                        const text = row.innerText.toLowerCase();

                        const statusMatch = selectedStatus === 'all' || rowStatus ===
                            selectedStatus;
                        const searchMatch = text.includes(filter);

                        if (statusMatch && searchMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    updateRowCountDisplay(visibleCount);
                    updateNoResultsMessage(visibleCount);
                });
            }

            if (statusFilter) {
                const urlParams = new URLSearchParams(window.location.search);
                const statusFromUrl = urlParams.get('status');
                if (statusFromUrl) {
                    statusFilter.value = statusFromUrl;
                }

                applyStatusFilter();

                statusFilter.addEventListener('change', function() {
                    const selectedStatus = this.value;
                    const rows = document.querySelectorAll('#tableBody tr');
                    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                    let visibleCount = 0;

                    rows.forEach(row => {
                        if (row.classList.contains('no-data')) return;

                        const rowStatus = row.getAttribute('data-status')?.toLowerCase() ||
                            'pending';
                        const rowText = row.innerText.toLowerCase();

                        const statusMatch = selectedStatus === 'all' || rowStatus ===
                            selectedStatus;
                        const searchMatch = searchTerm === '' || rowText.includes(searchTerm);

                        if (statusMatch && searchMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    updateRowCountDisplay(visibleCount);
                    updateNoResultsMessage(visibleCount);
                    updateUrlWithStatus(selectedStatus);
                });
            }

            function applyStatusFilter() {
                const selectedStatus = statusFilter.value;
                const rows = document.querySelectorAll('#tableBody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.classList.contains('no-data')) return;

                    const rowStatus = row.getAttribute('data-status')?.toLowerCase() || 'pending';

                    if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateRowCountDisplay(visibleCount);
                updateNoResultsMessage(visibleCount);
            }

            function updateRowCountDisplay(visibleCount) {
                const totalRows = document.querySelectorAll('#tableBody tr:not(.no-data)').length;
                const paginationInfo = document.querySelector('.pagination-info');
                if (paginationInfo) {
                    if (visibleCount === totalRows) {
                        const originalText = paginationInfo.getAttribute('data-original-text') || paginationInfo
                            .textContent;
                        paginationInfo.textContent = originalText;
                    } else {
                        paginationInfo.textContent = `Showing ${visibleCount} of ${totalRows} entries (filtered)`;
                    }
                }
            }

            function updateNoResultsMessage(visibleCount) {
                const totalRows = document.querySelectorAll('#tableBody tr:not(.no-data)').length;
                const noDataRow = document.querySelector('#tableBody tr.no-data');

                if (visibleCount === 0 && totalRows > 0) {
                    if (!noDataRow) {
                        const tbody = document.getElementById('tableBody');
                        const newRow = document.createElement('tr');
                        newRow.className = 'no-data';
                        newRow.innerHTML = `<td colspan="9">⚠️ No violations found with the selected filters</td>`;
                        tbody.appendChild(newRow);
                    }
                } else if (noDataRow && visibleCount > 0) {
                    noDataRow.remove();
                }
            }

            function updateUrlWithStatus(selectedStatus) {
                const url = new URL(window.location);

                if (selectedStatus === 'all') {
                    url.searchParams.delete('status');
                } else {
                    url.searchParams.set('status', selectedStatus);
                }

                window.history.replaceState({}, '', url.toString());
            }

            function saveOriginalPaginationText() {
                const paginationInfo = document.querySelector('.pagination-info');
                if (paginationInfo && !paginationInfo.hasAttribute('data-original-text')) {
                    paginationInfo.setAttribute('data-original-text', paginationInfo.textContent);
                }
            }

            saveOriginalPaginationText();
        });

        // ==========================
        // PRINT PDF FUNCTIONALITY
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const printPdfBtn = document.getElementById('printPdfBtn');
            const printInfoBtn = document.getElementById('printInfoBtn');
            const exportExcelBtn = document.getElementById('exportExcelBtn');

            if (printPdfBtn) {
                printPdfBtn.addEventListener('click', function() {
                    printAllViolationsPDF();
                });
            }

            if (printInfoBtn) {
                printInfoBtn.addEventListener('click', function() {
                    printIndividualViolationPDF();
                });
            }

            if (exportExcelBtn) {
                exportExcelBtn.addEventListener('click', function() {
                    exportToExcel();
                });
            }

            function printAllViolationsPDF() {
                const selectedStatus = document.getElementById('statusFilter').value;
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const viewType = document.getElementById('viewTypeSelect') ?
                    document.getElementById('viewTypeSelect').value :
                    '{{ $viewType }}';

                const rows = document.querySelectorAll('#tableBody tr:not(.no-data)');
                let filteredRows = [];

                rows.forEach(row => {
                    if (row.style.display === 'none') return;

                    const rowStatus = row.getAttribute('data-status')?.toLowerCase() || 'pending';
                    const rowText = row.innerText.toLowerCase();

                    const statusMatch = selectedStatus === 'all' || rowStatus === selectedStatus;
                    const searchMatch = searchTerm === '' || rowText.includes(searchTerm);

                    if (statusMatch && searchMatch) {
                        filteredRows.push(row);
                    }
                });

                if (filteredRows.length === 0) {
                    notifications.showNotification('No violations to print. Please adjust your filters.',
                        'warning');
                    return;
                }

                const violationsData = [];

                filteredRows.forEach(row => {
                    const violation = {
                        student_name: (viewType === 'group' || viewType === 'group_per_offense') ?
                            row.querySelector('.student-list')?.innerText || 'Multiple Students' : row
                            .querySelector('td:nth-child(2)')?.innerText || '',
                        incident: row.querySelector('td:nth-child(3)')?.innerText || '',
                        offense_type: row.querySelector('td:nth-child(4)')?.innerText || '',
                        sanction: row.querySelector('td:nth-child(5)')?.innerText || '',
                        date: row.querySelector('td:nth-child(6)')?.innerText || '',
                        time: row.querySelector('td:nth-child(7)')?.innerText || '',
                        status: row.querySelector('.status-badge')?.innerText || ''
                    };
                    violationsData.push(violation);
                });

                let pdfTitle = 'Violations Report';
                if (selectedStatus !== 'all') {
                    const statusText = document.getElementById('statusFilter').options[document.getElementById(
                        'statusFilter').selectedIndex].text;
                    pdfTitle += ` - ${statusText.replace(/[^a-zA-Z\s]/g, '')}`;
                }
                pdfTitle += ` (${violationsData.length} records)`;

                const printContent = createAllViolationsPDFContent(violationsData, pdfTitle, viewType,
                    selectedStatus, searchTerm);

                generatePDF(printContent, pdfTitle);
            }

            function exportToExcel() {
                const selectedStatus = document.getElementById('statusFilter').value;
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const viewType = document.getElementById('viewTypeSelect') ?
                    document.getElementById('viewTypeSelect').value :
                    '{{ $viewType }}';

                const rows = document.querySelectorAll('#tableBody tr:not(.no-data)');
                let filteredRows = [];

                rows.forEach(row => {
                    if (row.style.display === 'none') return;

                    const rowStatus = row.getAttribute('data-status')?.toLowerCase() || 'pending';
                    const rowText = row.innerText.toLowerCase();

                    const statusMatch = selectedStatus === 'all' || rowStatus === selectedStatus;
                    const searchMatch = searchTerm === '' || rowText.includes(searchTerm);

                    if (statusMatch && searchMatch) {
                        filteredRows.push(row);
                    }
                });

                if (filteredRows.length === 0) {
                    notifications.showNotification('No violations to export. Please adjust your filters.',
                        'warning');
                    return;
                }

                const excelData = [];

                const headers = [
                    viewType === 'group' ? 'Students' : 'Student Name',
                    'Incident',
                    'Offense Type',
                    'Sanction',
                    'Date',
                    'Time',
                    'Status',
                    'Student ID',
                    'Violation ID',
                    'Status Updated On',
                    'Sanction Start',
                    'Sanction End',
                    'Sanction Status'
                ];
                excelData.push(headers);

                filteredRows.forEach(row => {
                    const studentName = viewType === 'group' ?
                        row.querySelector('.student-list')?.innerText || 'Multiple Students' :
                        row.querySelector('td:nth-child(2)')?.innerText || '';

                    const incident = row.querySelector('td:nth-child(3)')?.innerText || '';
                    const offenseType = row.querySelector('td:nth-child(4)')?.innerText || '';
                    const sanction = row.querySelector('td:nth-child(5)')?.innerText || '';
                    const date = row.querySelector('td:nth-child(6)')?.innerText || '';
                    const time = row.querySelector('td:nth-child(7)')?.innerText || '';
                    const status = row.querySelector('.status-badge')?.innerText || '';

                    const studentId = row.getAttribute('data-student-id') || '';
                    const violationId = row.getAttribute('data-violation-id') || row.getAttribute(
                        'data-group-key') || '';
                    const updatedAt = row.getAttribute('data-updated-at') || '';
                    const sanctionStartAt = row.getAttribute('data-sanction-start-at') || '';
                    const sanctionEndAt = row.getAttribute('data-sanction-end-at') || '';
                    const sanctionStatus = row.getAttribute('data-sanction-status') || '';

                    let formattedUpdatedAt = '';
                    if (updatedAt) {
                        try {
                            const updatedDate = new Date(updatedAt);
                            formattedUpdatedAt = updatedDate.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        } catch (error) {
                            console.error('Error parsing updated_at:', error);
                        }
                    }

                    excelData.push([
                        studentName,
                        incident,
                        offenseType,
                        sanction,
                        date,
                        time,
                        status,
                        studentId,
                        violationId,
                        formattedUpdatedAt,
                        sanctionStartAt,
                        sanctionEndAt,
                        sanctionStatus
                    ]);
                });

                generateExcelFile(excelData, selectedStatus, searchTerm, filteredRows.length);
            }

            function generateExcelFile(data, selectedStatus, searchTerm, recordCount) {
                const wb = XLSX.utils.book_new();

                const ws = XLSX.utils.aoa_to_sheet(data);

                const colWidths = [{
                    wch: 30
                }, {
                    wch: 40
                }, {
                    wch: 20
                }, {
                    wch: 30
                }, {
                    wch: 15
                }, {
                    wch: 12
                }, {
                    wch: 15
                }, {
                    wch: 15
                }, {
                    wch: 15
                }, {
                    wch: 25
                }, {
                    wch: 20
                }, {
                    wch: 20
                }, {
                    wch: 15
                }];
                ws['!cols'] = colWidths;

                ws['!autofilter'] = {
                    ref: "A1:M1"
                };

                const headerRange = XLSX.utils.decode_range(ws['!ref']);
                for (let C = headerRange.s.c; C <= headerRange.e.c; ++C) {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: 0,
                        c: C
                    });
                    if (!ws[cellAddress]) continue;

                    ws[cellAddress].s = {
                        font: {
                            bold: true,
                            color: {
                                rgb: "FFFFFF"
                            }
                        },
                        fill: {
                            fgColor: {
                                rgb: "1E3A8A"
                            }
                        },
                        alignment: {
                            horizontal: "center",
                            vertical: "center"
                        }
                    };
                }

                let title = 'Violations Report';
                if (selectedStatus !== 'all') {
                    const statusText = document.getElementById('statusFilter').options[document.getElementById(
                        'statusFilter').selectedIndex].text;
                    title += ` - ${statusText.replace(/[^a-zA-Z\s]/g, '')}`;
                }
                title += ` (${recordCount} records)`;

                XLSX.utils.sheet_add_aoa(ws, [
                    [title]
                ], {
                    origin: -1
                });
                ws['A1'].s = {
                    font: {
                        bold: true,
                        size: 16,
                        color: {
                            rgb: "1E3A8A"
                        }
                    },
                    alignment: {
                        horizontal: "center"
                    }
                };

                if (!ws['!merges']) ws['!merges'] = [];
                ws['!merges'].push({
                    s: {
                        r: 0,
                        c: 0
                    },
                    e: {
                        r: 0,
                        c: 12
                    }
                });

                XLSX.utils.sheet_add_aoa(ws, [
                    [''],
                    ['TAGOLOAN SENIOR HIGH SCHOOL'],
                    ['Violation Management System'],
                    ['Generated: ' + new Date().toLocaleDateString('en-PH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })],
                    [
                        'Prepared By: {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}'
                    ],
                    ['']
                ], {
                    origin: -1
                });

                XLSX.utils.book_append_sheet(wb, ws, 'Violations');

                let fileName = 'Violations_Report';
                if (selectedStatus !== 'all') {
                    fileName += '_' + selectedStatus;
                }
                fileName += '_' + new Date().toISOString().slice(0, 10) + '.xlsx';

                XLSX.writeFile(wb, fileName);

                notifications.showNotification('Excel file generated successfully!', 'success');
            }

            function printIndividualViolationPDF() {
                const isGroupView = document.getElementById('info_group_key') && document.getElementById(
                    'info_group_key').style.display !== 'none';

                if (isGroupView) {
                    const groupKey = document.getElementById('info_group_key').textContent;
                    const studentsList = document.getElementById('info_students_list').textContent;
                    const studentsCount = document.getElementById('info_students_count').textContent;
                    const incident = document.getElementById('info_incident').textContent;
                    const offenseType = document.getElementById('info_offense_type').textContent;
                    const sanction = document.getElementById('info_sanction').textContent;
                    const date = document.getElementById('info_date').textContent;
                    const status = document.getElementById('info_status').textContent;
                    const statusUpdated = document.getElementById('info_status_updated').textContent;
                    const sanctionStart = document.getElementById('info_sanction_start_at').textContent;
                    const sanctionEnd = document.getElementById('info_sanction_end_at').textContent;
                    const sanctionStatus = document.getElementById('info_sanction_status').textContent;

                    const groupData = {
                        group_key: groupKey,
                        students_list: studentsList,
                        students_count: studentsCount,
                        incident: incident,
                        offense_type: offenseType,
                        sanction: sanction,
                        date: date,
                        status: status,
                        status_updated: statusUpdated,
                        sanction_start: sanctionStart,
                        sanction_end: sanctionEnd,
                        sanction_status: sanctionStatus
                    };

                    const pdfTitle = `Group Violation Report - ${studentsCount} Student(s)`;

                    const printContent = createGroupViolationPDFContent(groupData, pdfTitle);

                    generatePDF(printContent, pdfTitle);
                } else {
                    const studentId = document.getElementById('info_student_id').textContent;
                    const studentName = document.getElementById('info_student_name').textContent;
                    const violationId = document.getElementById('info_violation_id').textContent;
                    const incident = document.getElementById('info_incident').textContent;
                    const offenseType = document.getElementById('info_offense_type').textContent;
                    const sanction = document.getElementById('info_sanction').textContent;
                    const date = document.getElementById('info_date').textContent;
                    const status = document.getElementById('info_status').textContent;
                    const statusUpdated = document.getElementById('info_status_updated').textContent;
                    const sanctionStart = document.getElementById('info_sanction_start_at').textContent;
                    const sanctionEnd = document.getElementById('info_sanction_end_at').textContent;
                    const sanctionStatus = document.getElementById('info_sanction_status').textContent;

                    const violationData = {
                        student_id: studentId,
                        student_name: studentName,
                        violation_id: violationId,
                        incident: incident,
                        offense_type: offenseType,
                        sanction: sanction,
                        date: date,
                        status: status,
                        status_updated: statusUpdated,
                        sanction_start: sanctionStart,
                        sanction_end: sanctionEnd,
                        sanction_status: sanctionStatus
                    };

                    const pdfTitle = `Violation Report - ${studentName}`;

                    const printContent = createIndividualViolationPDFContent(violationData, pdfTitle);

                    generatePDF(printContent, pdfTitle);
                }
            }

            function createGroupViolationPDFContent(groupData, title) {
                const currentDate = new Date().toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const currentTime = new Date().toLocaleTimeString('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const statusColorMap = {
                    'pending': '#ffeb3b',
                    'in_progress': '#17a2b8',
                    'resolved': '#28a745',
                    'dismissed': '#dc3545',
                    'closed': '#6c757d'
                };

                const sanctionStatusColorMap = {
                    'pending': '#6c757d',
                    'ongoing': '#17a2b8',
                    'completed': '#28a745',
                    'missed': '#dc3545',
                    'cancelled': '#6c757d'
                };

                let statusColor = statusColorMap[groupData.status.toLowerCase()] || '#6c757d';
                let statusText = groupData.status.toLowerCase();

                let sanctionStatusColor = sanctionStatusColorMap[groupData.sanction_status.toLowerCase()] ||
                    '#6c757d';
                let sanctionStatusText = groupData.sanction_status.toLowerCase();

                return `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
                    <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                        <div style="flex: 1;">
                            <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                            <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Violation Management System</h2>
                            <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Group Violation Profile</p>
                        </div>
                        <div style="text-align: right;">
                            <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                        </div>
                    </div>

                    <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${title}</h3>
                                <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                                    Generated: ${currentDate} at ${currentTime}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000;">Document ID</div>
                                <div style="font-size: 14px; font-weight: 600; color: #000000;">GROUP-${Date.now().toString().slice(-6)}</div>
                            </div>
                        </div>
                    </div>

                    <div style="margin: 0 25px 25px 25px;">
                        <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Group Violation Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; margin-top: 10px; border: 1px solid #e2e8f0;">
                            <thead>
                                <tr>
                                    <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase;">Field</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000; width: 30%;">Group Key</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.group_key}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Number of Students</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.students_count}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Students Involved</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.students_list}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Incident</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.incident}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Offense Type</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.offense_type}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.sanction}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Date</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.date}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Status</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; background-color: ${statusColor}; color: ${statusColor === '#ffeb3b' ? '#8a6d3b' : 'white'}">
                                            ${groupData.status}
                                        </span>
                                    </td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Status Updated On</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.status_updated}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction Start</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.sanction_start}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction End</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${groupData.sanction_end}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction Status</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; background-color: ${sanctionStatusColor}; color: ${sanctionStatusColor === '#ffeb3b' ? '#8a6d3b' : 'white'}">
                                            ${groupData.sanction_status}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div style="text-align: left;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Prefect of Discipline
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Verified By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    School Principal
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Tagoloan Senior High School
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                            <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                                CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                            </div>
                            <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
                                This document contains sensitive violation information. Unauthorized distribution is prohibited.
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            function createAllViolationsPDFContent(data, title, viewType, selectedStatus, searchTerm) {
                const currentDate = new Date().toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const currentTime = new Date().toLocaleTimeString('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const statusColorMap = {
                    'pending': '#ffeb3b',
                    'in_progress': '#17a2b8',
                    'resolved': '#28a745',
                    'dismissed': '#dc3545',
                    'closed': '#6c757d'
                };

                let filterInfo = 'All Status';
                if (selectedStatus !== 'all') {
                    const statusText = document.getElementById('statusFilter').options[document.getElementById(
                        'statusFilter').selectedIndex].text;
                    filterInfo = statusText.replace(/[^a-zA-Z\s]/g, '');
                }

                let tableRows = '';
                data.forEach((violation, index) => {
                    let statusColor = statusColorMap[violation.status.toLowerCase()] || '#6c757d';
                    let statusText = violation.status.toLowerCase();

                    tableRows += `
                    <tr style="background-color: ${index % 2 === 0 ? '#ffffff' : '#f7fafc'};">
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${index + 1}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${violation.student_name}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${violation.incident}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${violation.offense_type}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${violation.sanction}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${violation.date}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">${violation.time}</td>
                        <td style="padding: 8px 6px; border: 1px solid #e2e8f0; font-size: 10px; color: #000000; word-wrap: break-word;">
                            <span style="display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; background-color: ${statusColor}; color: ${statusColor === '#ffeb3b' ? '#8a6d3b' : 'white'}">
                                ${violation.status}
                            </span>
                        </td>
                    </tr>
                `;
                });

                return `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
                    <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                        <div style="flex: 1;">
                            <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                            <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Violation Management System</h2>
                            <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Official Registry Document</p>
                        </div>
                        <div style="text-align: right;">
                            <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                        </div>
                    </div>

                    <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${title}</h3>
                                <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                                    Total Records: <strong style="color: #000000;">${data.length} Violation(s)</strong>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000;">Document ID</div>
                                <div style="font-size: 14px; font-weight: 600; color: #000000;">VIOL-${Date.now().toString().slice(-6)}</div>
                            </div>
                        </div>
                    </div>

                    <div style="overflow: hidden; margin: 0 25px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 10px; table-layout: fixed; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">${(viewType === 'group' || viewType === 'group_per_offense') ? 'Students' : 'Student Name'}</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Incident</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Offense Type</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Sanction</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Time</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px 8px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div style="text-align: left;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Prefect of Discipline
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Generated On:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    ${currentDate} at ${currentTime}
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                            <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                                CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                            </div>
                            <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
                                This document contains sensitive violation information. Unauthorized distribution is prohibited.
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            function createIndividualViolationPDFContent(violationData, title) {
                const currentDate = new Date().toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const currentTime = new Date().toLocaleTimeString('en-PH', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const statusColorMap = {
                    'pending': '#ffeb3b',
                    'in_progress': '#17a2b8',
                    'resolved': '#28a745',
                    'dismissed': '#dc3545',
                    'closed': '#6c757d'
                };

                const sanctionStatusColorMap = {
                    'pending': '#6c757d',
                    'ongoing': '#17a2b8',
                    'completed': '#28a745',
                    'missed': '#dc3545',
                    'cancelled': '#6c757d'
                };

                let statusColor = statusColorMap[violationData.status.toLowerCase()] || '#6c757d';
                let statusText = violationData.status.toLowerCase();

                let sanctionStatusColor = sanctionStatusColorMap[violationData.sanction_status.toLowerCase()] ||
                    '#6c757d';
                let sanctionStatusText = violationData.sanction_status.toLowerCase();

                return `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000000; background: #ffffff;">
                    <div style="display: flex; align-items: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 25px; padding: 0 25px;">
                        <div style="flex: 1;">
                            <h1 style="margin: 0; color: #000000; font-size: 24px; font-weight: 700;">TAGOLOAN SENIOR HIGH SCHOOL</h1>
                            <h2 style="margin: 5px 0 0 0; color: #000000; font-size: 16px; font-weight: 500;">Violation Management System</h2>
                            <p style="margin: 8px 0 0 0; color: #000000; font-size: 14px;">Individual Violation Profile</p>
                        </div>
                        <div style="text-align: right;">
                            <img src="/images/Logo.png" alt="School Logo" style="width: 70px; height: 70px; object-fit: contain;">
                        </div>
                    </div>

                    <div style="background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; margin: 0 25px 25px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0; color: #000000; font-size: 18px; font-weight: 600;">${title}</h3>
                                <p style="margin: 5px 0 0 0; color: #000000; font-size: 14px;">
                                    Generated: ${currentDate} at ${currentTime}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000;">Document ID</div>
                                <div style="font-size: 14px; font-weight: 600; color: #000000;">VIOL-PROFILE-${Date.now().toString().slice(-6)}</div>
                            </div>
                        </div>
                    </div>

                    <div style="margin: 0 25px 25px 25px;">
                        <h4 style="color: #000000; font-size: 16px; font-weight: 600; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0;">Violation Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; margin-top: 10px; border: 1px solid #e2e8f0;">
                            <thead>
                                <tr>
                                    <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase;">Field</th>
                                    <th style="background: #1e3a8a; color: white; padding: 10px; text-align: left; font-weight: 600; border: 1px solid #2d3748; font-size: 11px; text-transform: uppercase;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000; width: 30%;">Student ID</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.student_id}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Student Name</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.student_name}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Violation ID</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.violation_id}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: ' #000000;">Incident</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.incident}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Offense Type</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.offense_type}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.sanction}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Date</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.date}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Status</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; background-color: ${statusColor}; color: ${statusColor === '#ffeb3b' ? '#8a6d3b' : 'white'}">
                                            ${violationData.status}
                                        </span>
                                    </td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Status Updated On</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.status_updated}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction Start</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.sanction_start}</td>
                                </tr>
                                <tr style="background-color: #f7fafc;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction End</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">${violationData.sanction_end}</td>
                                </tr>
                                <tr style="background-color: #ffffff;">
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #000000;">Sanction Status</td>
                                    <td style="padding: 10px; border: 1px solid #e2e8f0; color: #000000;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; background-color: ${sanctionStatusColor}; color: ${sanctionStatusColor === '#ffeb3b' ? '#8a6d3b' : 'white'}">
                                            ${violationData.sanction_status}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 40px; border-top: 2px solid #e2e8f0; padding-top: 20px; padding: 20px 25px 0 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div style="text-align: left;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Prepared By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Prefect of Discipline
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <div style="font-size: 12px; color: #000000; margin-bottom: 5px;">Verified By:</div>
                                <div style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 8px;">
                                    School Principal
                                </div>
                                <div style="border-bottom: 1px solid #cbd5e0; width: 250px; padding: 15px 0 5px 0;"></div>
                                <div style="font-size: 12px; color: #000000; margin-top: 5px;">
                                    Tagoloan Senior High School
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center; margin-top: 30px; padding: 15px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px;">
                            <div style="font-size: 11px; color: #c53030; font-weight: 600;">
                                CONFIDENTIAL DOCUMENT - For Authorized Personnel Only
                            </div>
                            <div style="font-size: 10px; color: #e53e3e; margin-top: 5px;">
                                This document contains sensitive violation information. Unauthorized distribution is prohibited.
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            function generatePDF(content, title) {
                const element = document.createElement('div');
                element.innerHTML = content;

                if (typeof html2pdf === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    script.onload = () => generatePDFWithLibrary(element, title);
                    document.head.appendChild(script);
                } else {
                    generatePDFWithLibrary(element, title);
                }
            }

            function generatePDFWithLibrary(element, title) {
                const options = {
                    margin: [10, 15, 25, 15],
                    filename: `${title.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`,
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
                        orientation: 'landscape',
                        compress: true,
                        hotfixes: ["px_scaling"]
                    },
                    pagebreak: {
                        mode: ['avoid-all', 'css', 'legacy'],
                        before: '.page-break-before',
                        after: '.page-break-after',
                        avoid: ['tr', 'td', 'th']
                    }
                };

                notifications.showNotification('Opening PDF preview...', 'info');

                html2pdf().set(options).from(element).toPdf().get('pdf').then(function(pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();

                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);

                        pdf.setFontSize(8);
                        pdf.setTextColor(100, 100, 100);

                        pdf.text('Tagoloan Senior High School - Violation Management System',
                            pdf.internal.pageSize.getWidth() / 2 - 65,
                            pdf.internal.pageSize.getHeight() - 8);

                        pdf.text(`Page ${i} of ${totalPages}`,
                            pdf.internal.pageSize.getWidth() - 25,
                            pdf.internal.pageSize.getHeight() - 8);
                    }

                    const pdfBlob = pdf.output('blob');
                    const pdfUrl = URL.createObjectURL(pdfBlob);
                    window.open(pdfUrl, '_blank');

                    notifications.showNotification('PDF opened in new tab', 'success');
                }).catch(error => {
                    console.error('PDF generation error:', error);
                    notifications.showNotification('PDF generation failed. Please try again.', 'error');
                });
            }
        });

        // ==========================
        // VIOLATION INFO MODAL FUNCTIONALITY
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const infoModal = document.getElementById('infoModal');
            const closeModalBtn = document.getElementById('closeInfoModalBtn');
            const printInfoBtn = document.getElementById('printInfoBtn');

            const tabBtns = document.querySelectorAll('#infoModal .tab-btn');
            const tabPanes = document.querySelectorAll('#infoModal .tab-pane');

            if (infoModal) {
                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', () => {
                        infoModal.style.display = 'none';
                    });
                }

                if (printInfoBtn) {
                    printInfoBtn.addEventListener('click', function() {
                        printIndividualViolationPDF();
                    });
                }

                infoModal.addEventListener('click', function(event) {
                    if (event.target === infoModal) {
                        infoModal.style.display = 'none';
                    }
                });

                if (tabBtns.length > 0) {
                    tabBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tabId = this.getAttribute('data-tab');

                            tabBtns.forEach(b => b.classList.remove('active'));
                            this.classList.add('active');

                            tabPanes.forEach(pane => pane.classList.remove('active'));
                            document.getElementById(`${tabId}-tab`).classList.add('active');
                        });
                    });
                }

                document.querySelectorAll('.view-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();

                        const isGroupView = this.hasAttribute('data-group-key');

                        const individualView = document.querySelectorAll('.individual-view');
                        const groupView = document.querySelectorAll('.group-view');

                        if (isGroupView) {
                            individualView.forEach(el => el.style.display = 'none');
                            groupView.forEach(el => el.style.display = 'block');

                            const groupKey = this.getAttribute('data-group-key');
                            const students = JSON.parse(this.getAttribute('data-students') || '[]');
                            const studentsList = students.join(', ');
                            const studentsCount = students.length;

                            document.getElementById('info_violation_id').textContent = groupKey ||
                                'N/A';
                            document.getElementById('info_violation_id_display').textContent =
                                `Group: ${groupKey || 'N/A'}`;
                            document.getElementById('info_group_key').textContent = groupKey ||
                                'N/A';
                            document.getElementById('info_students_list').textContent =
                                studentsList;
                            document.getElementById('info_students_count').textContent =
                                `${studentsCount} student(s)`;
                        } else {
                            individualView.forEach(el => el.style.display = 'block');
                            groupView.forEach(el => el.style.display = 'none');

                            const violationId = this.getAttribute('data-violation-id');
                            const studentId = this.getAttribute('data-student-id');
                            const studentName = this.getAttribute('data-student-name');

                            document.getElementById('info_violation_id').textContent =
                                violationId || 'N/A';
                            document.getElementById('info_violation_id_display').textContent =
                                `Violation ID: ${violationId || 'N/A'}`;
                            document.getElementById('info_student_id').textContent = studentId ||
                                'N/A';
                            document.getElementById('info_student_name').textContent =
                                studentName || 'N/A';
                        }

                        const incident = this.getAttribute('data-incident');
                        const offenseType = this.getAttribute('data-offense-type');
                        const sanction = this.getAttribute('data-sanction');
                        const date = this.getAttribute('data-date');
                        const time = this.getAttribute('data-time');
                        const status = this.getAttribute('data-status');
                        const updatedAt = this.getAttribute('data-updated-at');
                        const sanctionStartAt = this.getAttribute('data-sanction-start-at');
                        const sanctionEndAt = this.getAttribute('data-sanction-end-at');
                        const sanctionStatus = this.getAttribute('data-sanction-status');

                        document.getElementById('info_incident').textContent = incident || 'N/A';
                        document.getElementById('info_offense_type').textContent = offenseType ||
                            'N/A';
                        document.getElementById('info_sanction').textContent = sanction || 'N/A';
                        document.getElementById('info_date').textContent = date || 'N/A';
                        document.getElementById('info_time').textContent = time || 'N/A';

                        const statusColorMap = {
                            'pending': '#ffeb3b',
                            'in_progress': '#17a2b8',
                            'resolved': '#28a745',
                            'dismissed': '#dc3545',
                            'closed': '#6c757d'
                        };

                        const sanctionStatusColorMap = {
                            'pending': '#6c757d',
                            'ongoing': '#17a2b8',
                            'completed': '#28a745',
                            'missed': '#dc3545',
                            'cancelled': '#6c757d'
                        };

                        const statusElement = document.getElementById('info_status');
                        const statusTimelineElement = document.getElementById(
                            'info_status_timeline');
                        const statusText = status ? status.charAt(0).toUpperCase() + status.slice(
                            1) : 'N/A';
                        statusElement.textContent = statusText;
                        statusTimelineElement.textContent = statusText;

                        const statusColor = statusColorMap[status] || '#6c757d';
                        statusElement.style.color = statusColor === '#ffeb3b' ? '#8a6d3b' : 'white';
                        statusElement.style.backgroundColor = statusColor;
                        statusElement.style.padding = '4px 8px';
                        statusElement.style.borderRadius = '4px';
                        statusElement.style.fontSize = '12px';
                        statusElement.style.fontWeight = '600';
                        statusElement.style.display = 'inline-block';

                        if (updatedAt) {
                            try {
                                const updatedDate = new Date(updatedAt);
                                const dateOptions = {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                };
                                const formattedDate = updatedDate.toLocaleDateString('en-US',
                                    dateOptions);
                                const formattedTime = updatedDate.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                                document.getElementById('info_status_updated').textContent =
                                    `${formattedDate} at ${formattedTime}`;
                            } catch (error) {
                                console.error('Error parsing updated_at:', error);
                                document.getElementById('info_status_updated').textContent = 'N/A';
                            }
                        } else {
                            document.getElementById('info_status_updated').textContent = 'N/A';
                        }

                        document.getElementById('info_sanction_start_at').textContent =
                            sanctionStartAt || 'Not set';
                        document.getElementById('info_sanction_end_at').textContent =
                            sanctionEndAt || 'Not set';

                        const sanctionStatusElement = document.getElementById(
                            'info_sanction_status');
                        const sanctionStatusText = sanctionStatus ? sanctionStatus.charAt(0)
                            .toUpperCase() + sanctionStatus.slice(1) : 'Not set';
                        sanctionStatusElement.textContent = sanctionStatusText;

                        const sanctionStatusColor = sanctionStatusColorMap[sanctionStatus] ||
                            '#6c757d';
                        sanctionStatusElement.style.color = sanctionStatusColor === '#ffeb3b' ?
                            '#8a6d3b' : 'white';
                        sanctionStatusElement.style.backgroundColor = sanctionStatusColor;
                        sanctionStatusElement.style.padding = '4px 8px';
                        sanctionStatusElement.style.borderRadius = '4px';
                        sanctionStatusElement.style.fontSize = '12px';
                        sanctionStatusElement.style.fontWeight = '600';
                        sanctionStatusElement.style.display = 'inline-block';

                        if (tabBtns.length > 0) {
                            tabBtns.forEach(b => b.classList.remove('active'));
                            tabPanes.forEach(pane => pane.classList.remove('active'));
                            document.querySelector('.tab-btn[data-tab="violation-info"]').classList
                                .add('active');
                            document.getElementById('violation-info-tab').classList.add('active');
                        }

                        infoModal.style.display = 'flex';
                    });
                });
            }
        });

        // 🔍 Search Functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            const selectedStatus = document.getElementById('statusFilter')?.value || 'all';
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.classList.contains('no-data')) return;

                const rowStatus = row.getAttribute('data-status')?.toLowerCase() || 'pending';
                const text = row.innerText.toLowerCase();

                const statusMatch = selectedStatus === 'all' || rowStatus === selectedStatus;
                const searchMatch = text.includes(filter);

                if (statusMatch && searchMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateRowCountDisplay(visibleCount);
            updateNoResultsMessage(visibleCount);
        });

        function updateRowCountDisplay(visibleCount) {
            const totalRows = document.querySelectorAll('#tableBody tr:not(.no-data)').length;
            const paginationInfo = document.querySelector('.pagination-info');
            if (paginationInfo) {
                if (visibleCount === totalRows) {
                    const originalText = paginationInfo.getAttribute('data-original-text') || paginationInfo.textContent;
                    paginationInfo.textContent = originalText;
                } else {
                    paginationInfo.textContent = `Showing ${visibleCount} of ${totalRows} entries (filtered)`;
                }
            }
        }

        function updateNoResultsMessage(visibleCount) {
            const totalRows = document.querySelectorAll('#tableBody tr:not(.no-data)').length;
            const noDataRow = document.querySelector('#tableBody tr.no-data');

            if (visibleCount === 0 && totalRows > 0) {
                if (!noDataRow) {
                    const tbody = document.getElementById('tableBody');
                    const newRow = document.createElement('tr');
                    newRow.className = 'no-data';
                    newRow.innerHTML = `<td colspan="9">⚠️ No violations found with the selected filters</td>`;
                    tbody.appendChild(newRow);
                }
            } else if (noDataRow && visibleCount > 0) {
                noDataRow.remove();
            }
        }

        // ✅ Select All - Main Table
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });

        // ==========================
        // ⚖️ SETTLEMENT MODAL FUNCTIONALITY - UPDATED
        // ==========================

        document.addEventListener('DOMContentLoaded', function() {
            const settleButtons = document.querySelectorAll('.settle-btn');
            const settlementModal = document.getElementById('settlementModal');
            const closeSettlementModal = document.getElementById('closeSettlementModal');
            const cancelSettlementBtn = document.getElementById('cancelSettlementBtn');
            const settlementForm = document.getElementById('settlementForm');
            const proceedSettlementBtn = document.getElementById('proceedSettlementBtn');
            const settlementStatusSelect = document.getElementById('settlement_status');

            let currentViolationData = null;

            if (settleButtons && settlementModal && settlementForm) {
                settleButtons.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const row = this.closest('tr');

                        // Get the current status
                        const currentStatus = (row.getAttribute('data-status') || 'pending')
                            .toLowerCase();

                        // Check if the violation is resolved or dismissed (REMOVED 'closed' check)
                        if (currentStatus === 'resolved' || currentStatus === 'dismissed') {
                            // Show notification instead of opening modal
                            if (currentStatus === 'resolved') {
                                notifications.showNotification(
                                    'This violation has already been resolved. You cannot change settlement once resolved.',
                                    'warning'
                                );
                            } else if (currentStatus === 'dismissed') {
                                notifications.showNotification(
                                    'This violation has been dismissed. You cannot change settlement once dismissed.',
                                    'warning'
                                );
                            }
                            return; // Exit the function early
                        }

                        currentViolationData = {
                            violationId: row.getAttribute('data-violation-id'),
                            studentName: row.getAttribute('data-student-name') || 'Unknown',
                            incident: row.getAttribute('data-incident') || 'N/A',
                            offenseType: row.getAttribute('data-offense-type') || 'N/A',
                            sanction: row.getAttribute('data-sanction') || 'N/A',
                            date: row.getAttribute('data-date') || 'N/A',
                            time: row.getAttribute('data-time') || 'N/A',
                            currentStatus: currentStatus,
                            sanctionStatus: (row.getAttribute('data-sanction-status') ||
                                'pending').toLowerCase(),
                            updatedAt: row.getAttribute('data-updated-at') || new Date()
                                .toISOString()
                        };

                        // Populate violation details
                        document.getElementById('settlement_student_name').textContent =
                            currentViolationData.studentName;
                        document.getElementById('settlement_incident').textContent =
                            currentViolationData.incident;
                        document.getElementById('settlement_offense_type').textContent =
                            currentViolationData.offenseType;
                        document.getElementById('settlement_sanction').textContent =
                            currentViolationData.sanction;
                        document.getElementById('settlement_date').textContent =
                            currentViolationData.date;
                        document.getElementById('settlement_time').textContent =
                            currentViolationData.time;
                        document.getElementById('settlement_current_violation_status').textContent =
                            currentViolationData.currentStatus.charAt(0).toUpperCase() +
                            currentViolationData.currentStatus.slice(1);
                        document.getElementById('settlement_sanction_status').textContent =
                            currentViolationData.sanctionStatus.charAt(0).toUpperCase() +
                            currentViolationData.sanctionStatus.slice(1);

                        // Set form values
                        document.getElementById('settlement_violation_record_id').value =
                            currentViolationData.violationId;
                        document.getElementById('settlement_current_status').value =
                            currentViolationData.currentStatus;
                        settlementForm.action =
                            `/prefect/violations/update/${currentViolationData.violationId}`;

                        // Populate status dropdown with available options
                        ViolationStatusOptionsManager.populateDropdown(settlementStatusSelect,
                            currentViolationData.currentStatus);

                        // Show modal
                        settlementModal.style.display = 'flex';
                    });
                });

                // Proceed with settlement button
                proceedSettlementBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const selectedStatus = settlementStatusSelect.value;

                    if (!selectedStatus) {
                        notifications.showNotification('Please select a new status.', 'warning');
                        return;
                    }

                    // Validate the status change
                    const validation = ViolationStatusOptionsManager.validateStatusChange(
                        currentViolationData.currentStatus,
                        selectedStatus
                    );

                    if (!validation.valid) {
                        notifications.showNotification(validation.message, 'warning');
                        return;
                    }

                    // Submit the form directly since there are no notes anymore
                    submitSettlementForm();
                });

                function submitSettlementForm() {
                    const formData = new FormData(settlementForm);
                    formData.delete('previous_statuses'); // Add this line


                    // Only send status, record_id, and current_status
                    // DO NOT add settlement_date or settlement_time

                    // Log what's being sent
                    console.log('Form data being sent:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    const originalText = proceedSettlementBtn.innerHTML;
                    proceedSettlementBtn.innerHTML = '<span>⏳ Settling...</span>';
                    proceedSettlementBtn.disabled = true;

                    fetch(settlementForm.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new URLSearchParams(formData).toString()
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers);

                            if (!response.ok) {
                                // Try to get the error response as text first
                                return response.text().then(text => {
                                    console.error('Error response text:', text);
                                    try {
                                        return JSON.parse(text);
                                    } catch (e) {
                                        return {
                                            success: false,
                                            message: text
                                        };
                                    }
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);

                            if (data.success) {
                                notifications.showNotification('✅ ' + data.message, 'success');
                                settlementModal.style.display = 'none';

                                // Update the row in the table
                                const recordId = formData.get('record_id');
                                const row = document.querySelector(`tr[data-violation-id="${recordId}"]`);
                                if (row) {
                                    const statusEl = row.querySelector('.status-badge');
                                    if (statusEl) {
                                        const newStatus = formData.get('status');
                                        statusEl.textContent = newStatus.charAt(0).toUpperCase() + newStatus
                                            .slice(1);
                                        statusEl.className = `status-badge status-${newStatus}`;
                                        row.setAttribute('data-status', newStatus);

                                        // Update the settle button state
                                        const settleBtn = row.querySelector('.settle-btn');
                                        if (settleBtn) {
                                            // Disable button for final statuses
                                            const finalStatuses = ['resolved', 'dismissed']; // REMOVED 'closed'
                                            if (finalStatuses.includes(newStatus)) {
                                                settleBtn.disabled = true;
                                                settleBtn.style.opacity = '0.5';
                                                settleBtn.style.cursor = 'not-allowed';
                                                settleBtn.title = `Cannot edit ${newStatus} violations`;
                                            }
                                        }
                                    }
                                }

                                // Reload after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 900);
                            } else {
                                notifications.showNotification('❌ Error: ' + (data.message || 'Update failed'),
                                    'error');
                                proceedSettlementBtn.innerHTML = originalText;
                                proceedSettlementBtn.disabled = false;
                            }
                        })
                        .catch(err => {
                            console.error('Settlement update error:', err);
                            notifications.showNotification('❌ An error occurred while updating status.',
                                'error');
                            proceedSettlementBtn.innerHTML = originalText;
                            proceedSettlementBtn.disabled = false;
                        });
                }

                // Close settlement modal
                closeSettlementModal.addEventListener('click', () => {
                    settlementModal.style.display = 'none';
                });

                cancelSettlementBtn.addEventListener('click', () => {
                    settlementModal.style.display = 'none';
                });
            }
        });

        // ==========================
        // ⏱️ UPDATE SANCTION MODAL FUNCTIONALITY
        // ==========================

        document.getElementById('updateSanctionBtn').addEventListener('click', function() {
            console.log('🔔 Update Sanction button clicked');

            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            console.log('Selected checkboxes count:', selectedCheckboxes.length);

            if (!selectedCheckboxes.length) {
                notifications.showNotification('Please select at least one violation to update sanction.',
                    'warning');
                return;
            }

            function checkIfAllSelectedHaveSameStatus(selectedCheckboxes) {
                if (selectedCheckboxes.length === 0) return true;

                const firstStatus = selectedCheckboxes[0].closest('tr').getAttribute('data-sanction-status') ||
                    'pending';

                for (let i = 1; i < selectedCheckboxes.length; i++) {
                    const currentStatus = selectedCheckboxes[i].closest('tr').getAttribute(
                        'data-sanction-status') || 'pending';
                    if (currentStatus !== firstStatus) {
                        return false;
                    }
                }
                return true;
            }

            function getUniqueStatusesFromSelected(selectedCheckboxes) {
                const statuses = new Set();
                selectedCheckboxes.forEach(cb => {
                    const status = cb.closest('tr').getAttribute('data-sanction-status') || 'pending';
                    statuses.add(status);
                });
                return Array.from(statuses);
            }

            const allSameStatus = checkIfAllSelectedHaveSameStatus(selectedCheckboxes);
            if (!allSameStatus) {
                const uniqueStatuses = getUniqueStatusesFromSelected(selectedCheckboxes);
                const statusList = uniqueStatuses.join(', ');

                notifications.showNotification(
                    `Cannot update violations with different sanction statuses. Selected violations have: ${statusList}. ` +
                    'Please select violations with the same sanction status only.',
                    'warning'
                );
                return;
            }

            const selectedViolations = Array.from(selectedCheckboxes).map(cb => {
                const row = cb.closest('tr');
                const isGroup = cb.classList.contains('group-checkbox');

                return {
                    violation_id: isGroup ? cb.value : row.dataset.violationId,
                    student_name: row.dataset.studentName || getStudentNamesFromGroup(row),
                    incident: row.dataset.incident,
                    sanction_status: row.getAttribute('data-sanction-status') || 'pending',
                    sanction_start_at: row.getAttribute('data-sanction-start-at') || '',
                    sanction_end_at: row.getAttribute('data-sanction-end-at') || '',
                    is_group: isGroup,
                    group_key: isGroup ? cb.value : null
                };
            });

            console.log('Selected violations:', selectedViolations);

            const selectedList = document.getElementById('selectedViolationsForSanction');
            selectedList.innerHTML = '';

            let hasCompletedSanctions = false;
            const completedSanctions = [];

            selectedViolations.forEach(violation => {
                const item = document.createElement('div');
                item.className = 'selected-violation-item';

                const status = violation.sanction_status || 'pending';

                if (status === 'completed') {
                    hasCompletedSanctions = true;
                    completedSanctions.push(violation);
                }

                const displayText = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>${violation.student_name}</strong>
                        ${violation.is_group ? '<span class="group-badge">(Group)</span>' : ''}
                        <br>
                        <small style="color: #666;">
                            Current Sanction Status: <span class="current-status" style="color: ${getSanctionStatusColor(status)}">${status}</span>
                        </small>
                    </div>
                </div>
            `;

                item.innerHTML = displayText;

                if (violation.is_group) {
                    const groupKeyInput = document.createElement('input');
                    groupKeyInput.type = 'hidden';
                    groupKeyInput.name = 'group_keys[]';
                    groupKeyInput.value = violation.group_key;
                    item.appendChild(groupKeyInput);
                } else {
                    const violationIdInput = document.createElement('input');
                    violationIdInput.type = 'hidden';
                    violationIdInput.name = 'violation_ids[]';
                    violationIdInput.value = violation.violation_id;
                    item.appendChild(violationIdInput);
                }

                selectedList.appendChild(item);
            });

            if (hasCompletedSanctions) {
                const completedCount = completedSanctions.length;
                let message = 'Cannot update sanctions that are already marked as Completed: ';
                if (completedCount === 1) {
                    message += `"${completedSanctions[0].student_name}"`;
                } else {
                    message += `(${completedCount} sanctions)`;
                }

                notifications.showNotification(message, 'warning');
                return;
            }

            const commonStatus = selectedViolations[0].sanction_status || 'pending';

            const startDateInput = document.getElementById('sanction_start_date');
            const startTimeInput = document.getElementById('sanction_start_time');
            const endDateInput = document.getElementById('sanction_end_date');
            const endTimeInput = document.getElementById('sanction_end_time');
            const sanctionStatusSelect = document.getElementById('sanction_status');

            [startDateInput, startTimeInput, endDateInput, endTimeInput].forEach(input => {
                if (input) {
                    input.disabled = false;
                    input.style.backgroundColor = '';
                    input.style.cursor = '';
                }
            });

            sanctionStatusSelect.disabled = false;
            sanctionStatusSelect.style.backgroundColor = '';
            sanctionStatusSelect.style.cursor = '';

            Array.from(sanctionStatusSelect.options).forEach(option => {
                option.disabled = false;
                option.style.color = '';
                option.style.cursor = '';
            });

            SanctionStatusOptionsManager.populateDropdown(sanctionStatusSelect, commonStatus);
            sanctionStatusSelect.value = '';

            SanctionStatusOptionsManager.toggleFormFields(sanctionStatusSelect, commonStatus);

            const firstViolation = selectedViolations[0];
            if (firstViolation) {
                if (firstViolation.sanction_start_at && firstViolation.sanction_start_at.trim() !== '') {
                    const startParts = parseDisplayToParts(firstViolation.sanction_start_at);
                    if (startParts) {
                        startDateInput.value = startParts.date;
                        startTimeInput.value = startParts.time;
                    } else {
                        const today = new Date().toISOString().split('T')[0];
                        startDateInput.value = today;
                    }
                } else {
                    const today = new Date().toISOString().split('T')[0];
                    startDateInput.value = today;
                }

                if (firstViolation.sanction_end_at && firstViolation.sanction_end_at.trim() !== '') {
                    const endParts = parseDisplayToParts(firstViolation.sanction_end_at);
                    if (endParts) {
                        endDateInput.value = endParts.date;
                        endTimeInput.value = endParts.time;
                    } else {
                        endDateInput.value = '';
                        endTimeInput.value = '';
                    }
                } else {
                    endDateInput.value = '';
                    endTimeInput.value = '';
                }
            }

            document.getElementById('updateSanctionModal').style.display = 'flex';
            console.log('✅ Modal should be visible now');
        });

        document.getElementById('sanction_status').addEventListener('change', function() {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            if (!selectedCheckboxes.length) return;

            const statusVals = [];
            selectedCheckboxes.forEach(cb => {
                const row = cb.closest('tr');
                statusVals.push(row.getAttribute('data-sanction-status') || 'pending');
            });

            const allStatusSame = statusVals.every(v => v === statusVals[0]);
            const currentStatus = allStatusSame ? statusVals[0] : 'pending';
            const newStatus = this.value;

            if (!newStatus) return;

            const validation = SanctionStatusOptionsManager.validateStatusChange(currentStatus, newStatus);

            if (!validation.valid) {
                notifications.showNotification(validation.message, 'warning');

                this.value = '';

                SanctionStatusOptionsManager.populateDropdown(this, currentStatus);
                SanctionStatusOptionsManager.toggleFormFields(this, currentStatus);
            } else {
                const startDateInput = document.getElementById('sanction_start_date');
                const startTimeInput = document.getElementById('sanction_start_time');
                const endDateInput = document.getElementById('sanction_end_date');
                const endTimeInput = document.getElementById('sanction_end_time');

                if (newStatus === 'completed') {
                    [startDateInput, startTimeInput, endDateInput, endTimeInput].forEach(input => {
                        if (input) {
                            input.disabled = true;
                            input.style.backgroundColor = '#f5f5f5';
                            input.style.cursor = 'not-allowed';
                        }
                    });
                } else {
                    [startDateInput, startTimeInput, endDateInput, endTimeInput].forEach(input => {
                        if (input) {
                            input.disabled = false;
                            input.style.backgroundColor = '';
                            input.style.cursor = '';
                        }
                    });
                }
            }
        });

        document.getElementById('updateSanctionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('📝 Sanction form submission started');

            const formData = new FormData();

            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenMeta) {
                formData.append('_token', csrfTokenMeta.getAttribute('content'));
            }

            const violationIdInputs = document.querySelectorAll(
                '#selectedViolationsForSanction input[name="violation_ids[]"]');
            violationIdInputs.forEach(input => {
                formData.append('violation_ids[]', input.value);
            });

            const groupKeyInputs = document.querySelectorAll(
                '#selectedViolationsForSanction input[name="group_keys[]"]');
            groupKeyInputs.forEach(input => {
                formData.append('group_keys[]', input.value);
            });

            formData.append('sanction_start_date', document.getElementById('sanction_start_date').value);
            formData.append('sanction_start_time', document.getElementById('sanction_start_time').value);
            formData.append('sanction_end_date', document.getElementById('sanction_end_date').value);
            formData.append('sanction_end_time', document.getElementById('sanction_end_time').value);
            formData.append('sanction_status', document.getElementById('sanction_status').value);

            console.log('Form data keys:', Array.from(formData.keys()));

            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            const newStatus = formData.get('sanction_status');

            if (!newStatus) {
                notifications.showNotification('Please select a sanction status.', 'warning');
                return;
            }

            const invalidTransitions = [];

            selectedCheckboxes.forEach(cb => {
                const row = cb.closest('tr');
                const currentStatus = row.getAttribute('data-sanction-status') || 'pending';

                const validation = SanctionStatusOptionsManager.validateStatusChange(currentStatus,
                    newStatus);

                if (!validation.valid) {
                    invalidTransitions.push({
                        student: row.getAttribute('data-student-name') ||
                            getStudentNamesFromGroup(row),
                        from: currentStatus,
                        to: newStatus,
                        message: validation.message
                    });
                }
            });

            if (invalidTransitions.length > 0) {
                const firstError = invalidTransitions[0];
                let message = `${firstError.message}`;

                if (invalidTransitions.length > 1) {
                    message +=
                        ` (and ${invalidTransitions.length - 1} more violation${invalidTransitions.length > 2 ? 's' : ''})`;
                }

                notifications.showNotification(message, 'warning');
                return;
            }

            if ((newStatus === 'ongoing' || newStatus === 'neglected') &&
                (!formData.get('sanction_start_date') || !formData.get('sanction_start_time'))) {
                notifications.showNotification(
                    'Start date and time are required when setting status to Ongoing or Neglected.',
                    'warning');
                return;
            }

            const saveBtn = document.querySelector('#updateSanctionModal .btn-primary');
            const originalText = saveBtn ? saveBtn.innerHTML : '';
            if (saveBtn) {
                saveBtn.innerHTML = '<span>⏳ Updating...</span>';
                saveBtn.disabled = true;
            }

            try {
                function normalizeTimeInput(val) {
                    if (!val) return '';
                    if (/^\d{2}:\d{2}$/.test(val)) return val;
                    const m = val.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
                    if (m) {
                        let h = parseInt(m[1], 10);
                        const mm = m[2];
                        const period = m[3].toUpperCase();
                        if (period === 'AM' && h === 12) h = 0;
                        if (period === 'PM' && h !== 12) h += 12;
                        return (h.toString().padStart(2, '0')) + ':' + mm;
                    }
                    const parsed = new Date('1970-01-01T' + val);
                    if (!isNaN(parsed.getTime())) {
                        const hh = String(parsed.getHours()).padStart(2, '0');
                        const mm = String(parsed.getMinutes()).padStart(2, '0');
                        return `${hh}:${mm}`;
                    }
                    return val;
                }

                const rawStartTime = formData.get('sanction_start_time');
                const rawEndTime = formData.get('sanction_end_time');
                if (rawStartTime) formData.set('sanction_start_time', normalizeTimeInput(rawStartTime));
                if (rawEndTime) formData.set('sanction_end_time', normalizeTimeInput(rawEndTime));

                console.log('🚀 Sending request to:', this.action);
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                console.log('Response status:', response.status);
                const result = await response.json();
                console.log('Response result:', result);

                if (result.success) {
                    notifications.showNotification(result.message, 'success');
                    document.getElementById('updateSanctionModal').style.display = 'none';
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const errorElement = document.getElementById(field + '_error');
                            if (errorElement) {
                                errorElement.textContent = result.errors[field][0];
                            }
                        });
                    } else {
                        notifications.showNotification('Error: ' + (result.message ||
                            'Sanction update failed'), 'error');
                    }
                }
            } catch (error) {
                console.error('❌ Error:', error);
                notifications.showNotification('An error occurred while updating sanction.', 'error');
            } finally {
                if (saveBtn) {
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                }
            }
        });

        // ==========================
        // HELPER FUNCTIONS
        // ==========================

        function getSanctionStatusColor(status) {
            switch (status?.toLowerCase()) {
                case 'pending':
                    return '#856404';
                case 'ongoing':
                    return '#004085';
                case 'neglected':
                    return '#721c24';
                case 'completed':
                    return '#155724';
                default:
                    return '#495057';
            }
        }

        function parseDisplayToParts(displayStr) {
            if (!displayStr) return null;
            const parsed = new Date(displayStr);
            if (isNaN(parsed.getTime())) return null;
            const y = parsed.getFullYear();
            const m = String(parsed.getMonth() + 1).padStart(2, '0');
            const d = String(parsed.getDate()).padStart(2, '0');
            const hh = String(parsed.getHours()).padStart(2, '0');
            const mm = String(parsed.getMinutes()).padStart(2, '0');
            return {
                date: `${y}-${m}-${d}`,
                time: `${hh}:${mm}`
            };
        }

        function getStudentNamesFromGroup(row) {
            const studentElements = row.querySelectorAll('.student-name');
            const names = Array.from(studentElements).map(el => el.textContent.trim());
            return names.join(', ');
        }

        // ==========================
        // 📅 SET SCHEDULE MODAL FUNCTIONALITY
        // ==========================

        document.getElementById('setScheduleBtn').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

            if (!selectedCheckboxes.length) {
                notifications.showNotification('Please select at least one violation to schedule.', 'warning');
                return;
            }

            const selectedViolations = Array.from(selectedCheckboxes).map(cb => {
                const row = cb.closest('tr');
                const isGroup = cb.classList.contains('group-checkbox');

                return {
                    violation_id: isGroup ? cb.value : row.dataset.violationId,
                    student_name: row.dataset.studentName || getStudentNamesFromGroup(row),
                    incident: row.dataset.incident,
                    is_group: isGroup,
                    group_key: isGroup ? cb.value : null
                };
            });

            const selectedList = document.getElementById('selectedViolationsList');
            selectedList.innerHTML = '';

            selectedViolations.forEach(violation => {
                const item = document.createElement('div');
                item.className = 'selected-violation-item';
                item.innerHTML = `
                <strong>${violation.student_name}</strong>
                ${violation.is_group ? '<span class="group-badge">(Group)</span>' : ''}
                <br><small>Incident: ${violation.incident}</small>
                <input type="hidden" name="violation_ids[]" value="${violation.violation_id}">
                ${violation.is_group ? `<input type="hidden" name="group_keys[]" value="${violation.group_key}">` : ''}
            `;
                selectedList.appendChild(item);
            });

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('schedule_date').min = today;
            document.getElementById('schedule_date').value = today;

            const nextHour = new Date();
            nextHour.setHours(nextHour.getHours() + 1);
            document.getElementById('schedule_time').value = nextHour.toTimeString().substring(0, 5);

            document.getElementById('setScheduleModal').style.display = 'flex';
        });

        document.getElementById('closeScheduleModal').addEventListener('click', function() {
            document.getElementById('setScheduleModal').style.display = 'none';
        });

        document.getElementById('cancelScheduleBtn').addEventListener('click', function() {
            document.getElementById('setScheduleModal').style.display = 'none';
        });

        document.getElementById('setScheduleForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            const saveBtn = document.querySelector('#setScheduleModal .btn-primary');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span>⏳ Creating...</span>';
            saveBtn.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(result.message, 'success');
                    document.getElementById('setScheduleModal').style.display = 'none';
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const errorElement = document.getElementById(field + '_error');
                            if (errorElement) {
                                errorElement.textContent = result.errors[field][0];
                            }
                        });
                    } else {
                        notifications.showNotification('Error: ' + (result.message ||
                            'Schedule creation failed'), 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('An error occurred while creating appointments.', 'error');
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        });

        // ==========================
        // 📝 CREATE ANECDOTAL MODAL FUNCTIONALITY
        // ==========================

        document.getElementById('createAnecdotalBtn').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

            if (!selectedCheckboxes.length) {
                notifications.showNotification('Please select at least one violation to create anecdotal record.',
                    'warning');
                return;
            }

            const selectedViolations = Array.from(selectedCheckboxes).map(cb => {
                const row = cb.closest('tr');
                const isGroup = cb.classList.contains('group-checkbox');

                return {
                    violation_id: isGroup ? cb.value : row.dataset.violationId,
                    student_name: row.dataset.studentName || getStudentNamesFromGroup(row),
                    incident: row.dataset.incident,
                    is_group: isGroup,
                    group_key: isGroup ? cb.value : null
                };
            });

            const selectedList = document.getElementById('selectedViolationsForAnecdotal');
            selectedList.innerHTML = '';

            selectedViolations.forEach(violation => {
                const item = document.createElement('div');
                item.className = 'selected-violation-item';
                item.innerHTML = `
                <strong>${violation.student_name}</strong>
                ${violation.is_group ? '<span class="group-badge">(Group)</span>' : ''}
                <br><small>Incident: ${violation.incident}</small>
                <input type="hidden" name="violation_ids[]" value="${violation.violation_id}">
                ${violation.is_group ? `<input type="hidden" name="group_keys[]" value="${violation.group_key}">` : ''}
            `;
                selectedList.appendChild(item);
            });

            const now = new Date();
            document.getElementById('anecdotal_date').value = now.toISOString().split('T')[0];
            document.getElementById('anecdotal_time').value = now.toTimeString().substring(0, 5);

            document.getElementById('createAnecdotalModal').style.display = 'flex';
        });

        document.getElementById('closeAnecdotalModal').addEventListener('click', function() {
            document.getElementById('createAnecdotalModal').style.display = 'none';
        });

        document.getElementById('cancelAnecdotalBtn').addEventListener('click', function() {
            document.getElementById('createAnecdotalModal').style.display = 'none';
        });

        document.getElementById('createAnecdotalForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            const saveBtn = document.querySelector('#createAnecdotalModal .btn-primary');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span>⏳ Creating...</span>';
            saveBtn.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    notifications.showNotification(result.message, 'success');
                    document.getElementById('createAnecdotalModal').style.display = 'none';
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const errorElement = document.getElementById(field + '_error');
                            if (errorElement) {
                                errorElement.textContent = result.errors[field][0];
                            }
                        });
                    } else {
                        notifications.showNotification('Error: ' + (result.message ||
                            'Anecdotal creation failed'), 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                notifications.showNotification('An error occurred while creating anecdotal records.', 'error');
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        });

        function convertTo24Hour(timeStr) {
            if (!timeStr || !timeStr.includes(' ')) return timeStr;

            const [time, mod] = timeStr.split(' ');
            let [h, m] = time.split(':');
            h = parseInt(h);
            if (mod === 'PM' && h !== 12) h += 12;
            if (mod === 'AM' && h === 12) h = 0;
            return `${h.toString().padStart(2, '0')}:${m}`;
        }

        // ==========================
        // MODAL CLOSE HANDLERS
        // ==========================

        document.addEventListener('click', function(event) {
            const modals = [
                'infoModal', 'setScheduleModal',
                'createAnecdotalModal', 'updateSanctionModal',
                'notificationModal', 'confirmationModal',
            ];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    if (modalId === 'notificationModal') {
                        notifications.hideNotification();
                    } else if (modalId === 'confirmationModal') {
                        notifications.hideConfirmation();
                    } else if (modalId === 'settlementWarningModal') {
                        modal.style.display = 'none';
                    } else {
                        modal.style.display = 'none';
                    }
                }
            });
        });

        // Close Update Sanction Modal
        document.getElementById('closeSanctionModal').addEventListener('click', function() {
            document.getElementById('updateSanctionModal').style.display = 'none';
        });

        document.getElementById('cancelSanctionBtn').addEventListener('click', function() {
            document.getElementById('updateSanctionModal').style.display = 'none';
        });
    </script>

    <style>
        /* ==================== */
        /* Compact Modal Styles for Violations */
        /* ==================== */

        .compact-modal {
            max-width: 550px !important;
            width: 90% !important;
            margin: auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }

        /* Modal Header */
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-shrink: 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .modal-subtitle {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }

        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .close-modal:hover {
            opacity: 1;
        }

        /* Modal Tabs */
        .modal-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .tab-btn {
            padding: 12px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            flex: 1;
            justify-content: center;
        }

        .tab-btn:hover {
            color: #1e293b;
            background: #f1f5f9;
        }

        .tab-btn.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
            background: #ffffff;
        }

        /* Tab Content */
        .tab-content {
            flex-grow: 1;
            overflow-y: auto;
        }

        .tab-pane {
            display: none;
            height: 100%;
        }

        .tab-pane.active {
            display: block;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px;
            background: #ffffff;
            height: 100%;
        }

        .info-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-group {
            flex: 1;
        }

        .info-group.full-width {
            flex: 100%;
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-value {
            display: block;
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            min-height: 36px;
        }

        /* Info Section */
        .info-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #475569;
            margin: 0 0 15px 0;
            font-weight: 600;
        }

        /* Contact Section */
        .contact-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: background 0.2s;
        }

        .contact-item:hover {
            background: #f1f5f9;
        }

        .contact-icon {
            width: 36px;
            height: 36px;
            background: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .contact-details {
            flex: 1;
        }

        .contact-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .contact-value {
            display: block;
            font-size: 13px;
            color: #1e40af;
            text-decoration: none;
            font-weight: 500;
            margin-top: 2px;
            word-break: break-all;
        }

        .contact-value:hover {
            text-decoration: underline;
        }

        .contact-value.disabled {
            color: #94a3b8;
            cursor=default;
            text-decoration: none;
        }

        /* Modal Footer */
        .modal-footer {
            display: flex;
            justify-content: center;
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .btn-secondary {
            padding: 10px 20px;
            background: #64748b;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .modal-export {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .modal-export:hover {
            background: #2563eb;
        }

        /* Scrollbar Styling */
        .tab-content::-webkit-scrollbar {
            width: 6px;
        }

        .tab-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .tab-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .tab-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Status Filter & View Type Dropdown Styles */
        .select-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .left-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .right-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-filter label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            white-space: nowrap;
        }

        .status-dropdown {
            padding: 8px 12px;
            border: 2px solid #4a6baf;
            border-radius: 6px;
            background: white;
            color: #333;
            font-weight: 500;
            cursor: pointer;
            min-width: 180px;
            transition: all 0.3s ease;
        }

        .status-dropdown:hover {
            border-color: #3a5a9f;
            box-shadow: 0 0 0 3px rgba(74, 107, 175, 0.1);
        }

        .status-dropdown:focus {
            outline: none;
            border-color: #2a4a8f;
            box-shadow: 0 0 0 3px rgba(74, 107, 175, 0.2);
        }

        .status-dropdown option {
            padding: 8px;
            font-weight: 500;
        }

        /* View Type Dropdown Styles */
        .view-type-dropdown {
            position: relative;
        }

        .view-type-select {
            padding: 8px 35px 8px 12px;
            border: 2px solid #4a6baf;
            border-radius: 6px;
            background: white;
            color: #333;
            font-weight: 500;
            cursor: pointer;
            min-width: 160px;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
        }

        .view-type-select:hover {
            border-color: #3a5a9f;
            box-shadow: 0 0 0 3px rgba(74, 107, 175, 0.1);
        }

        .view-type-select:focus {
            outline: none;
            border-color: #2a4a8f;
            box-shadow: 0 0 0 3px rgba(74, 107, 175, 0.2);
        }

        .view-type-select option {
            padding: 8px;
            font-weight: 500;
        }

        /* Style for bulk action buttons */
        .btn-schedule,
        .btn-anecdotal,
        .btn-info {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-schedule {
            background: #28a745;
            color: white;
        }

        .btn-schedule:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-anecdotal {
            background: #17a2b8;
            color: white;
        }

        .btn-anecdotal:hover {
            background: #138496;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
        }

        .btn-info {
            background: #ffc107;
            color: #212529;
        }

        .btn-info:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }

        /* Action buttons in table */
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: nowrap;
        }

        .btn-view {
            padding: 6px 12px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-view:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
        }

        .btn-view:active {
            transform: translateY(0);
        }

        .btn-secondary {
            padding: 6px 12px;
            background: #64748b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(71, 85, 105, 0.3);
        }

        .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ==================== */
        /* UPDATED STATUS BADGE STYLES */
        /* ==================== */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            min-width: 85px;
            text-align: center;
            display: inline-block;
        }

        .status-pending {
            background-color: #ffeb3b;
            /* Yellow */
            color: #8a6d3b;
            border: 1px solid #f0ad4e;
        }

        .status-in_progress {
            background-color: #17a2b8;
            /* Teal/Blue */
            color: white;
            border: 1px solid #138496;
        }

        .status-resolved {
            background-color: #28a745;
            /* Green */
            color: white;
            border: 1px solid #1e7e34;
        }

        .status-dismissed {
            background-color: #dc3545;
            /* Red */
            color: white;
            border: 1px solid #bd2130;
        }



        /* Add hover effect to status badges */
        .status-badge:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .select-options {
                flex-wrap: wrap;
                gap: 15px;
            }

            .left-controls,
            .right-controls {
                width: 100%;
            }

            .right-controls {
                justify-content: flex-start;
                margin-top: 10px;
            }
        }

        @media (max-width: 768px) {
            .actions {
                flex-wrap: wrap;
            }

            .search-input {
                max-width: 100%;
                order: 1;
            }

            .btn-print,
            #createBtn {
                order: 2;
                flex: 1;
                min-width: 120px;
            }

            .select-options {
                flex-direction: column;
                gap: 15px;
            }

            .left-controls {
                flex-direction: column;
                width: 100%;
                gap: 15px;
            }

            .status-filter,
            .view-type-dropdown {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .status-dropdown,
            .view-type-select {
                flex: 1;
                min-width: 0;
            }

            .right-controls {
                width: 100%;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 0;
            }

            .btn-schedule,
            .btn-anecdotal,
            .btn-info {
                flex: 1;
                min-width: 120px;
                justify-content: center;
                font-size: 13px;
                padding: 8px 12px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-view,
            .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {

            .status-filter,
            .view-type-dropdown {
                flex-direction: column;
                align-items: stretch;
                gap: 5px;
            }

            .status-filter label,
            .view-type-dropdown {
                width: 100%;
                margin-bottom: 5px;
            }

            .right-controls {
                flex-direction: column;
            }

            .btn-schedule,
            .btn-anecdotal,
            .btn-info {
                width: 100%;
            }

            .compact-modal {
                width: 95% !important;
                max-height: 90vh;
            }

            .info-row {
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn-secondary,
            .modal-export {
                width: 100%;
                justify-content: center;
            }

            .modal-tabs {
                flex-wrap: wrap;
            }

            .tab-btn {
                flex: 1 0 auto;
                min-width: 33.33%;
                font-size: 12px;
                padding: 10px 5px;
            }

            .status-badge {
                min-width: 70px;
                font-size: 10px;
                padding: 3px 8px;
            }
        }

        /* Noncompliant status badge - matches the style in your image */
        .status-noncompliant {
            background-color: #ff9800;
            /* Orange background like in the image */
            color: white;
            /* White text like the others */
            border: 1px solid #e68900;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            min-width: 85px;
            text-align: center;
            display: inline-block;
        }

        /* Hover effect to match other badges */
        .status-noncompliant:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(255, 152, 0, 0.3);
        }

        /* Maximized Update Sanction Modal */
        #updateSanctionModal .modal-content {
            max-width: 900px !important;
            width: 90% !important;
            max-height: 85vh !important;
        }

        .edit-modal-body.expanded {
            max-height: 60vh !important;
            overflow-y: auto;
            padding: 25px !important;
        }

        .selected-violations.expanded {
            margin-bottom: 25px;
        }

        .selected-list.expanded-list {
            max-height: 150px;
            overflow-y: auto;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .edit-form-grid.expanded-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .edit-form-group.expanded {
            margin-bottom: 20px;
        }

        .edit-form-group.expanded input,
        .edit-form-group.expanded select {
            width: 100%;
            padding: 12px 15px;
            font-size: 14px;
        }

        .edit-form-group.full-width.expanded {
            grid-column: span 2;
        }

        .expanded-select {
            padding: 12px 15px !important;
            font-size: 14px !important;
            height: auto !important;
        }

        /* Scrollbar for expanded list */
        .selected-list.expanded-list::-webkit-scrollbar {
            width: 6px;
        }

        .selected-list.expanded-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .selected-list.expanded-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .selected-list.expanded-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .edit-form-grid.expanded-grid {
                grid-template-columns: 1fr;
            }

            .edit-form-group.full-width.expanded {
                grid-column: span 1;
            }
        }
    </style>
@endsection
