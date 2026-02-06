<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create New Violation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add to existing CSS */
.individual-search-results {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    padding: 0.5rem;
}

.individual-student-item {
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 5px;
    margin-bottom: 0.25rem;
    transition: background-color 0.2s ease;
}

.individual-student-item:hover {
    background-color: #f8f9fa;
}

.individual-student-item.selected {
    background-color: #007bff;
    color: white;
}
        .evidence-file-input-group {
    transition: all 0.3s ease;
}
        .evidence-file-card {
    transition: transform 0.2s ease;
}
.evidence-file-card:hover {
    transform: translateY(-2px);
}
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .card-header {
            background: #003366;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #007bff;
        }
        .btn-primary {
            background: #003366;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .nav-breadcrumb {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .violation-type-btn {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        .violation-type-btn:hover {
            border-color: #003366;
            transform: translateY(-2px);
        }
        .violation-type-btn.active {
            border-color: #003366;
            background-color: #f0f8ff;
        }
        .violation-type-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #003366;
        }
        .student-tag {
            background: #e9ecef;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            display: inline-flex;
            align-items: center;
            font-size: 0.875rem;
        }
        .student-tag .remove-student {
            margin-left: 0.5rem;
            cursor: pointer;
            color: #dc3545;
        }
        .student-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 1rem;
        }
        .student-item {
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 0.25rem;
        }
        .student-item:hover {
            background-color: #f8f9fa;
        }
        .student-item.selected {
            background-color: #007bff;
            color: white;
        }
        .violation-pair {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .offense-count-badge {
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        .modal-xl {
            max-width: 1200px;
        }
        .review-individual {
            border-left: 4px solid #28a745;
        }
        .review-group {
            border-left: 4px solid #ffc107;
        }
        .review-type-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .offense-history-item {
            border-left: 3px solid #6c757d;
            padding-left: 1rem;
            margin-bottom: 0.75rem;
        }
        .offense-count-high {
            background-color: #dc3545 !important;
        }
        .offense-count-medium {
            background-color: #fd7e14 !important;
        }
        .offense-count-low {
            background-color: #ffc107 !important;
        }
        .offense-count-none {
            background-color: #28a745 !important;
        }
        .two-column-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .two-column-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
        }
        .offense-history-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .custom-sanctions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* NEW STYLES FOR ADDITIONAL OFFENSE SECTIONS */
        .additional-offense-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
        }
        .additional-offense-section:not(:first-of-type) {
            margin-top: 1.5rem;
        }
        .additional-offense-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #007bff;
        }
        .additional-offense-title {
            font-weight: 600;
            color: #007bff;
            margin: 0;
        }
        .remove-offense-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .remove-offense-btn:hover {
            background: #c82333;
        }
        .add-offense-btn-container {
            display: flex;
            justify-content: center;
            margin: 1rem 0;
        }
        .sanction-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }
        .sanction-select-wrapper {
            flex: 1;
        }
        .add-offense-btn {
            margin-top: 1.8rem;
        }

        /* NEW STYLES FOR CUSTOM SANCTIONS DISPLAY */
        .offense-sanction-pair {
            transition: all 0.3s ease;
        }
        .offense-sanction-pair:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .custom-sanction-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }
        .additional-offense-item {
            border-left: 4px solid #17a2b8 !important;
        }
        .sanction-display-section {
            border-left: 4px solid #28a745;
            background: #f8fff9;
        }

        @media (max-width: 768px) {
            .two-column-grid,
            .offense-history-grid,
            .custom-sanctions-grid {
                grid-template-columns: 1fr;
            }
            .sanction-input-group {
                flex-direction: column;
            }
            .add-offense-btn {
                margin-top: 0;
                align-self: stretch;
            }
        }

        /* NEW STYLES FOR ADD ANOTHER OFFENSE BUTTON */
        .add-offense-history-btn {
            margin-top: 0.5rem;
        }

        /* NEW STYLES FOR ADDITIONAL OFFENSE MODAL */
        .additional-offense-modal .offense-count-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        .additional-offense-modal .student-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        /* NEW STYLES FOR INLINE CUSTOM SANCTION */
        .inline-custom-sanction {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        .inline-sanction-select {
            font-size: 0.875rem;
        }
        .inline-sanction-actions {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Navigation Breadcrumb -->
        <div class="nav-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('adviser.violation') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Violation</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-plus-circle me-1"></i>Create Violation</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card main-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title text-white mb-1"><i class="fas fa-gavel me-2"></i>Create New Violation</h2>
                                <p class="text-white-50 mb-0">Report disciplinary issues or conflicts between students</p>
                            </div>
                            <a href="{{ route('adviser.violation') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {!! session('success') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Violation Type Selection -->
                        <div class="form-section">
                            <h5 class="mb-3"><i class="fas fa-tag me-2"></i>Violation Type</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="violation-type-btn active" id="individualBtn" onclick="selectViolationType('individual')">
                                        <div class="violation-type-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <h5>Individual Violation</h5>
                                        <p class="text-muted mb-0">One violator</p>
                                        <input type="radio" name="violation_type" id="individual_violation" value="individual" checked hidden>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="violation-type-btn" id="groupBtn" onclick="selectViolationType('group')">
                                        <div class="violation-type-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h5>Group Violation</h5>
                                        <p class="text-muted mb-0">Multiple violators</p>
                                        <input type="radio" name="violation_type" id="group_violation" value="group" hidden>
                                    </div>
                                </div>
                            </div>
                        </div>

<!-- Updated Individual Violation Section -->
<div id="individualSection" class="form-section">
    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Individual Violation Details</h5>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="individual_violator_search" class="form-label required-field">Violator</label>
            <input type="text" class="form-control" id="individual_violator_search" placeholder="Search violator...">
            <div class="student-list mt-2" style="max-height: 200px; overflow-y: auto;">
                <div id="individual_violator_results"></div>
            </div>
            <div class="mt-2">
                <div id="individual_violator_tag" class="d-flex flex-wrap"></div>
            </div>
        </div>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-primary" onclick="addIndividualViolation()">
            <i class="fas fa-plus me-1"></i> Add to Violation List
        </button>
    </div>
</div>

                        <!-- Group Violation Section -->
                        <div id="groupSection" class="form-section" style="display: none;">
                            <h5 class="mb-3"><i class="fas fa-users me-2"></i>Group Violation Details</h5>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Violators</label>
                                    <div class="student-list mb-2">
                                        <div id="violatorTags" class="d-flex flex-wrap"></div>
                                    </div>
                                    <input type="text" class="form-control" id="violatorSearch" placeholder="Search students...">
                                    <div class="student-list mt-2">
                                        <div id="violatorResults"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-primary" onclick="generateGroupViolations()">
                                    <i class="fas fa-cogs me-1"></i> Generate Violation List
                                </button>
                            </div>
                        </div>

                        <!-- Violation Pairs Preview -->
                        <div id="violationPairsSection" class="form-section" style="display: none;">
                            <h5 class="mb-3"><i class="fas fa-list me-2"></i>Violation List Preview</h5>
                            <div id="violationPairsList" class="student-list"></div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-success" onclick="openOffenseModal()">
                                    <i class="fas fa-arrow-right me-1"></i> Set Offenses & Sanctions
                                </button>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <small class="text-muted"><span class="text-danger">*</span> indicates required field</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo me-1"></i> Reset Form
                                </button>
                                <button type="button" class="btn btn-primary" id="submitBtn" onclick="submitViolations()" disabled>
                                    <i class="fas fa-save me-1"></i> Create Violation(s)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Modal 1: Set Offenses & Sanctions -->
<div class="modal fade" id="offenseModal" tabindex="-1" aria-labelledby="offenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="offenseModalLabel">
                    <i class="fas fa-gavel me-2"></i>Set Offenses & Sanctions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Offenses are filtered to show only violation-related categories. Sanctions are loaded from predefined stages.
                </div>

                <!-- Student Offense History Section -->
                <div id="historyAndCustomSection" style="display: none;">
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-history me-2"></i>Student Offense History</h5>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Violator Offense Records</h6>
                            </div>

                            <div class="card-body">
                                <div class="alert alert-info mb-3">
                                    <small><i class="fas fa-info-circle me-1"></i>
                                        You can set custom sanctions for any student regardless of offense count
                                    </small>
                                </div>

                                <div id="offenseHistoryContent" class="offense-history-grid">
                                    <!-- History dynamically inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- MERGED SECTION: GENERAL VIOLATION DETAILS -->
                <!-- ========================================== -->
                <div class="main-sanction-section mb-4">
                    <h5 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>General Violation Details</h5>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Complete Information</h6>
                        </div>

                        <div class="card-body">

                            <!-- Main Offense & Sanction Section -->
                            <div class="additional-offense-section" id="main-offense-section">
                                <div class="additional-offense-header">
                                    <h6 class="additional-offense-title">Primary Offense & Sanction</h6>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="offense_id" class="form-label required-field">Offense Type</label>
                                        <select class="form-select" id="offense_id"
        onchange="loadSanctions(); loadOffenseHistory(); updatePrimaryOffenseDisplay();">
                                            <option value="">Select Offense Type</option>
                                            @foreach($offenses as $offense)
                                                <option value="{{ $offense->offense_id }}">
                                                    {{ $offense->offense_type }} ({{ $offense->category }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Only violation-related offenses are shown</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
    <label for="sanction_id" class="form-label required-field">General Sanction</label>
    <div class="sanction-input-group">
        <div class="sanction-select-wrapper">
            <select class="form-select" id="sanction_id" onchange="updateOffenseHistoryDisplay()">
                <option value="">Select Sanction</option>
            </select>
            <div class="form-text">Sanctions are loaded from predefined stages for this offense</div>
        </div>
        <button type="button" class="btn btn-primary add-offense-btn" onclick="addAdditionalOffense()">
            <i class="fas fa-plus me-1"></i> Add Another Offense
        </button>
    </div>
</div>
                                </div>
                            </div>

                            <!-- Container for Additional Offense Sections -->
                            <div id="additional-offenses-container">
                                <!-- Additional offense sections will be added here dynamically -->
                            </div>

                            <!-- Date & Time -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="violation_date" class="form-label required-field">Incident Date</label>
                                    <input type="date" class="form-control" id="violation_date"
                                           value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="violation_time" class="form-label required-field">Incident Time</label>
                                    <input type="time" class="form-control" id="violation_time"
                                           value="{{ date('H:i') }}">
                                </div>
                            </div>

                            <!-- Incident Description -->
                            <div class="mb-3">
                                <label for="violation_incident" class="form-label required-field">Incident Description</label>
                                <textarea class="form-control" id="violation_incident" rows="4"
                                          placeholder="Provide a detailed description of what happened..."
                                          maxlength="1000"></textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span>/1000 characters
                                </div>
                            </div>

                            <!-- Witnesses -->
                            <div class="mb-3">
                                <label for="witnesses" class="form-label">Witnesses <small class="text-muted">(optional)</small></label>
                                <textarea class="form-control" id="witnesses" rows="2"
                                          placeholder="List witnesses separated by commas or each line (e.g., John D., Maria S.)"></textarea>
                            </div>

                            <!-- Evidence Description -->
                            <div class="mb-3">
                                <label for="evidence_description" class="form-label">
                                    Evidence Description <small class="text-muted">(optional)</small>
                                </label>
                                <textarea class="form-control" id="evidence_description" rows="3"
                                          placeholder="Describe any evidence presented..."></textarea>
                            </div>

<!-- Evidence Files -->
<div class="mb-3">
    <label class="form-label">
        Evidence Files <small class="text-muted">(optional)</small>
    </label>

    <!-- File inputs container -->
    <div id="evidenceFilesContainer">
        <div class="evidence-file-input-group mb-2">
            <div class="input-group">
                <input type="file" class="form-control evidence-file-input" name="evidence_files[]"
                       accept="image/*,video/*,.mp4,.mov,.avi,.mkv,.webm">
                <button type="button" class="btn btn-outline-danger" onclick="removeEvidenceFile(this)" disabled>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Add more files button -->
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addEvidenceFile()">
        <i class="fas fa-plus me-1"></i> Add Another File
    </button>

    <div class="form-text">You may attach multiple photos or videos as evidence</div>
</div>

                        </div>
                    </div>
                </div>

                <!-- Custom Sanctions -->
                <div class="mb-4">
                    <h5 class="mb-3"><i class="fas fa-user-cog me-2"></i>Custom Sanctions</h5>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user-cog me-2"></i>Custom Sanctions</h6>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-warning mb-3">
                                <small><i class="fas fa-info-circle me-1"></i>
                                    Custom sanctions will override the general sanction for specific students
                                </small>
                            </div>

                            <div id="customSanctionsContent" class="custom-sanctions-grid">
                                <div class="text-center text-muted py-4" style="grid-column: 1 / -1;">
                                    <i class="fas fa-user-cog fa-2x mb-2"></i>
                                    <p>No custom sanctions set yet</p>
                                    <small>Click "Set Custom Sanction" on any student to add custom sanctions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn btn-primary" onclick="openReviewModal()">
                    <i class="fas fa-eye me-1"></i> Review Violations
                </button>
            </div>

        </div>
    </div>
</div>



    <!-- Modal 2: Review Violations -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">
                        <i class="fas fa-eye me-2"></i>Review Violations
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Review all violation details before submission. You can go back to make changes if needed.
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center bg-primary text-white">
                                <div class="card-body py-3">
                                    <h4 class="mb-0" id="reviewViolationPairs">0</h4>
                                    <small>Violation Entries</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-success text-white">
                                <div class="card-body py-3">
                                    <h4 class="mb-0" id="reviewTotalOffenses">0</h4>
                                    <small>Total Offenses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-warning text-white">
                                <div class="card-body py-3">
                                    <h4 class="mb-0" id="reviewCustomSanctions">0</h4>
                                    <small>Custom Sanctions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-info text-white">
                                <div class="card-body py-3">
                                    <h4 class="mb-0" id="reviewAdditionalOffenses">0</h4>
                                    <small>Additional Offenses</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="reviewViolationsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="backToOffenseModal()">
                        <i class="fas fa-arrow-left me-1"></i> Back to Edit
                    </button>
                    <button type="button" class="btn btn-success" onclick="finalSubmit()">
                        <i class="fas fa-check me-1"></i> Submit All Violations
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- NEW MODAL: Additional Offense Modal -->
<div class="modal fade additional-offense-modal" id="additionalOffenseModal" tabindex="-1" aria-labelledby="additionalOffenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="additionalOffenseModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add Another Offense
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="student-info">
                    <h6 id="additionalOffenseStudentName"></h6>
                    <div class="d-flex align-items-center">
                        <span class="badge me-2" id="additionalOffenseCountBadge">0 previous offenses</span>
                        <small class="text-muted" id="additionalOffenseSeverityText">No prior offenses</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="additional_offense_select" class="form-label required-field">Offense Type</label>
                    <select class="form-select" id="additional_offense_select" onchange="loadAdditionalOffenseSanctions(); updateAdditionalOffenseHistory();">
                        <option value="">Select Offense Type</option>
                        @foreach($offenses as $offense)
                            <option value="{{ $offense->offense_id }}">
                                {{ $offense->offense_type }} ({{ $offense->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="additional_sanction_select" class="form-label required-field">Sanction</label>
                    <select class="form-select" id="additional_sanction_select">
                        <option value="">Select Offense First</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAdditionalOffenseBtn" onclick="confirmAdditionalOffense()">
                    <i class="fas fa-check me-1"></i> Add Offense
                </button>
            </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let allStudents = @json($students);
let violationPairs = [];
let selectedViolators = [];
let currentViolationType = 'individual';
let studentOffenseHistory = {};
let customSanctions = {};
let additionalOffenses = [];
let currentAdditionalOffenseStudent = null;

// NEW: Variables to track selected individual students
let selectedIndividualViolator = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeStudentSearch();
    initializeIndividualSearch(); // NEW: Initialize individual search
    document.getElementById('violation_date').max = new Date().toISOString().split('T')[0];
    document.getElementById('violation_incident').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });
});

// ==========================================
// NEW: INDIVIDUAL VIOLATION LIVE SEARCH FUNCTIONS
// ==========================================

// Initialize individual search functionality
function initializeIndividualSearch() {
    const violatorSearch = document.getElementById('individual_violator_search');

    violatorSearch.addEventListener('input', function() {
        searchIndividualStudents(this.value, 'violator');
    });

    searchIndividualStudents('', 'violator');
}

// Search students for individual violation
function searchIndividualStudents(query, type) {
    const resultsDiv = document.getElementById(`individual_${type}_results`);
    const filteredStudents = allStudents.filter(student => {
        const fullName = `${student.student_lname}, ${student.student_fname}`.toLowerCase();
        return fullName.includes(query.toLowerCase());
    });

    resultsDiv.innerHTML = '';

    if (filteredStudents.length === 0 && query) {
        resultsDiv.innerHTML = '<div class="text-muted p-2">No students found</div>';
        return;
    }

    filteredStudents.forEach(student => {
        const isSelected = selectedIndividualViolator === student.student_id;

        const studentDiv = document.createElement('div');
        studentDiv.className = `individual-student-item ${isSelected ? 'selected' : ''}`;

        // UPDATED: Format as "Lastname, Firstname - Gr. Gradelevel Section"
        const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
        const section = student.adviser?.adviser_section || '';
        const displayText = `${student.student_lname}, ${student.student_fname} - Gr. ${gradeLevel} ${section}`.trim();

        studentDiv.innerHTML = displayText;
        studentDiv.onclick = () => selectIndividualStudent(student.student_id, type);
        resultsDiv.appendChild(studentDiv);
    });
}

// Select a student for individual violation
function selectIndividualStudent(studentId, type) {
    // Deselect if clicking the same student
    if (selectedIndividualViolator === studentId) {
        selectedIndividualViolator = null;
    } else {
        selectedIndividualViolator = studentId;
    }

    updateIndividualTags(type);
    searchIndividualStudents(document.getElementById(`individual_${type}_search`).value, type);
}

// Update tags for individual violation
function updateIndividualTags(type) {
    const tagsDiv = document.getElementById(`individual_${type}_tag`);
    const studentId = selectedIndividualViolator;

    tagsDiv.innerHTML = '';

    if (studentId) {
        const student = allStudents.find(s => s.student_id == studentId);
        if (student) {
            const tag = document.createElement('span');
            tag.className = 'student-tag';
            tag.innerHTML = `
                ${student.student_fname} ${student.student_lname}
                <span class="remove-student" onclick="removeIndividualStudent('${type}')">
                    <i class="fas fa-times"></i>
                </span>
            `;
            tagsDiv.appendChild(tag);
        }
    }
}

// Remove selected individual student
function removeIndividualStudent(type) {
    selectedIndividualViolator = null;
    updateIndividualTags(type);
    searchIndividualStudents(document.getElementById(`individual_${type}_search`).value, type);
}

// ==========================================
// GROUP VIOLATION SEARCH FUNCTIONS (UPDATED)
// ==========================================

function initializeStudentSearch() {
    const violatorSearch = document.getElementById('violatorSearch');

    violatorSearch.addEventListener('input', function() { searchStudents(this.value, 'violator'); });

    searchStudents('', 'violator');
}

function searchStudents(query, type) {
    const resultsDiv = document.getElementById(`${type}Results`);
    const filteredStudents = allStudents.filter(student => {
        const fullName = `${student.student_lname}, ${student.student_fname}`.toLowerCase();
        return fullName.includes(query.toLowerCase());
    });

    resultsDiv.innerHTML = '';
    filteredStudents.forEach(student => {
        const isSelected = selectedViolators.includes(student.student_id);

        const studentDiv = document.createElement('div');
        studentDiv.className = `student-item ${isSelected ? 'selected' : ''}`;

        // UPDATED: Same format for group violation search
        const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
        const section = student.adviser?.adviser_section || '';
        const displayText = `${student.student_lname}, ${student.student_fname} - Gr. ${gradeLevel} ${section}`.trim();

        studentDiv.innerHTML = displayText;
        studentDiv.onclick = () => toggleStudent(student.student_id, type);
        resultsDiv.appendChild(studentDiv);
    });
}

function toggleStudent(studentId, type) {
    const array = selectedViolators;
    const index = array.indexOf(studentId);

    if (index === -1) array.push(studentId);
    else array.splice(index, 1);

    updateStudentTags(type);
    searchStudents(document.getElementById(`${type}Search`).value, type);
}

function updateStudentTags(type) {
    const tagsDiv = document.getElementById(`${type}Tags`);
    const students = selectedViolators;

    tagsDiv.innerHTML = '';
    students.forEach(studentId => {
        const student = allStudents.find(s => s.student_id == studentId);
        if (student) {
            const tag = document.createElement('span');
            tag.className = 'student-tag';
            tag.innerHTML = `
                ${student.student_fname} ${student.student_lname}
                <span class="remove-student" onclick="removeStudent(${studentId}, '${type}')">
                    <i class="fas fa-times"></i>
                </span>
            `;
            tagsDiv.appendChild(tag);
        }
    });
}

function removeStudent(studentId, type) {
    selectedViolators = selectedViolators.filter(id => id != studentId);
    updateStudentTags(type);
    searchStudents(document.getElementById(`${type}Search`).value, type);
}

// ==========================================
// UTILITY FUNCTIONS (UPDATED)
// ==========================================

function getStudentName(studentId) {
    const student = allStudents.find(s => s.student_id == studentId);
    if (student) {
        const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
        const section = student.adviser?.adviser_section || '';
        return `${student.student_fname} ${student.student_lname} - Gr. ${gradeLevel} ${section}`.trim();
    }
    return 'Unknown';
}

// ==========================================
// UPDATED CUSTOM SANCTION FUNCTIONS - FIXED MULTIPLE DISPLAY
// ==========================================

function manageCustomSanction(studentId, studentName, action = 'set', offenseIndex = 0) {
    console.log('🔄 Managing custom sanction for:', studentName, 'Action:', action, 'Offense Index:', offenseIndex);

    const offenseId = document.getElementById('offense_id').value;
    if (!offenseId) {
        alert('Please select an offense type first.');
        return;
    }

    // Find the specific student card
    const studentCard = document.querySelector(`[data-student-id="${studentId}"]`);
    if (!studentCard) {
        console.error('Student card not found for ID:', studentId);
        return;
    }

    // Find the specific offense entry within the student card
    const offenseEntries = studentCard.querySelectorAll('.offense-entry');
    if (offenseIndex >= offenseEntries.length) {
        console.error('Offense entry not found at index:', offenseIndex);
        return;
    }

    const offenseEntry = offenseEntries[offenseIndex];

    // Remove any existing dropdown in this specific offense entry
    const existingDropdown = offenseEntry.querySelector('.inline-sanction-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
    }

    const isModify = action === 'modify';
    const currentSanctionId = customSanctions[studentId]?.customSanctions?.[offenseIndex]?.sanctionId;

    // Create new dropdown
    const dropdownDiv = document.createElement('div');
    dropdownDiv.className = 'inline-sanction-dropdown mt-2';
    dropdownDiv.innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" id="sanction_${studentId}_${offenseIndex}" style="width: auto; min-width: 200px;">
                <option value="">Select Sanction...</option>
                <option value="loading">Loading sanctions...</option>
            </select>
            <button type="button" class="btn btn-${isModify ? 'warning' : 'success'} btn-sm"
                    onclick="${isModify ? `updateCustomSanction(${studentId}, '${studentName.replace(/'/g, "\\'")}', ${offenseIndex})` : `applyCustomSanction(${studentId}, '${studentName.replace(/'/g, "\\'")}', ${offenseIndex})`}">
                <i class="fas ${isModify ? 'fa-sync-alt' : 'fa-check'}"></i>
            </button>
            ${isModify ? `<button type="button" class="btn btn-danger btn-sm" onclick="removeCustomSanction(${studentId}, '${studentName.replace(/'/g, "\\'")}', ${offenseIndex})">
                <i class="fas fa-trash"></i>
            </button>` : ''}
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelCustomSanction(${studentId}, ${offenseIndex})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="form-text small mt-1">${isModify ? 'Modify or remove custom sanction' : 'This will override the general sanction'}</div>
    `;

    // Find the sanction button in this specific offense entry
    const sanctionButton = offenseEntry.querySelector('.custom-sanction-btn');
    if (sanctionButton) {
        // Insert dropdown after the button's parent div (the flex container)
        const buttonContainer = sanctionButton.closest('.d-flex.justify-content-between');
        if (buttonContainer) {
            buttonContainer.parentNode.insertBefore(dropdownDiv, buttonContainer.nextSibling);
        } else {
            offenseEntry.appendChild(dropdownDiv);
        }
        sanctionButton.style.display = 'none';
    } else {
        // If no button found, append to the offense entry
        offenseEntry.appendChild(dropdownDiv);
    }

    loadSanctionsForStudent(studentId, offenseId, currentSanctionId, offenseIndex);
}

async function loadSanctionsForStudent(studentId, offenseId, currentSanctionId = null, offenseIndex = 0) {
    const sanctionSelect = document.getElementById(`sanction_${studentId}_${offenseIndex}`);
    if (!sanctionSelect) return;

    try {
        const response = await fetch('/adviser/violations/get-sanctions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ offense_id: offenseId })
        });

        if (!response.ok) throw new Error(`Server returned ${response.status}`);

        const data = await response.json();
        sanctionSelect.innerHTML = '<option value="">Select Custom Sanction</option>';

        if (data?.length > 0) {
            data.forEach((sanction) => {
                const option = document.createElement('option');
                option.value = sanction.sanction_id;
                option.textContent = sanction.sanction_consequences;
                if (sanction.sanction_description) option.title = sanction.sanction_description;
                if (sanction.sanction_id == currentSanctionId) option.selected = true;
                sanctionSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading sanctions:', error);
        sanctionSelect.innerHTML = '<option value="">Error loading sanctions</option>';
    }
}

function applyCustomSanction(studentId, studentName, offenseIndex = 0) {
    const sanctionSelect = document.getElementById(`sanction_${studentId}_${offenseIndex}`);
    if (!sanctionSelect) {
        console.error('Sanction select not found for student:', studentId, 'offense index:', offenseIndex);
        return;
    }

    const sanctionId = sanctionSelect.value;
    if (!sanctionId) {
        alert('Please select a custom sanction.');
        return;
    }

    const sanctionText = sanctionSelect.selectedOptions[0].text;

    // Initialize student data if not exists
    if (!customSanctions[studentId]) {
        customSanctions[studentId] = {
            customSanctions: {}, // Store custom sanctions per offense index
            offenses: []
        };
    }

    // Store custom sanction for this specific offense
    if (!customSanctions[studentId].customSanctions) {
        customSanctions[studentId].customSanctions = {};
    }

    customSanctions[studentId].customSanctions[offenseIndex] = {
        sanctionId: sanctionId,
        sanctionText: sanctionText
    };

    updateSanctionDisplay(studentId, studentName, sanctionText, offenseIndex);
    updateAdditionalOffensesDisplay();
    showAlert(`Custom sanction applied for ${studentName} (Offense ${offenseIndex + 1})`, 'success');
}

function updateCustomSanction(studentId, studentName, offenseIndex = 0) {
    applyCustomSanction(studentId, studentName, offenseIndex);
}

function updateSanctionDisplay(studentId, studentName, sanctionText, offenseIndex = 0) {
    console.log('🎨 Updating sanction display for:', studentName, 'Sanction:', sanctionText, 'Offense Index:', offenseIndex);

    const studentCard = document.querySelector(`[data-student-id="${studentId}"]`);
    if (!studentCard) return;

    // Find the specific offense entry
    const offenseEntries = studentCard.querySelectorAll('.offense-entry');
    if (offenseIndex >= offenseEntries.length) return;

    const offenseEntry = offenseEntries[offenseIndex];
    const sanctionButton = offenseEntry.querySelector('.custom-sanction-btn');

    if (sanctionButton) {
        sanctionButton.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${sanctionText}`;
        sanctionButton.className = 'btn btn-outline-success btn-sm custom-sanction-btn';
        sanctionButton.style.display = 'inline-block';

        // Update the onclick handler properly
        const safeStudentName = studentName.replace(/'/g, "\\'");
        sanctionButton.setAttribute('onclick', `manageCustomSanction(${studentId}, '${safeStudentName}', 'modify', ${offenseIndex})`);
    }

    const dropdownDiv = offenseEntry.querySelector('.inline-sanction-dropdown');
    if (dropdownDiv) {
        dropdownDiv.remove();
    }

    updateAdditionalOffensesDisplay();
}

function removeCustomSanction(studentId, studentName, offenseIndex = 0) {
    if (confirm(`Remove custom sanction for ${studentName} for this offense?`)) {
        if (customSanctions[studentId]?.customSanctions?.[offenseIndex]) {
            delete customSanctions[studentId].customSanctions[offenseIndex];

            // Clean up if no custom sanctions left
            if (Object.keys(customSanctions[studentId].customSanctions).length === 0 &&
                (!customSanctions[studentId].offenses || customSanctions[studentId].offenses.length === 0)) {
                delete customSanctions[studentId];
            }
        }

        const studentCard = document.querySelector(`[data-student-id="${studentId}"]`);
        if (studentCard) {
            const offenseEntries = studentCard.querySelectorAll('.offense-entry');
            if (offenseIndex < offenseEntries.length) {
                const offenseEntry = offenseEntries[offenseIndex];
                const sanctionButton = offenseEntry.querySelector('.custom-sanction-btn');

                if (sanctionButton) {
                    sanctionButton.innerHTML = '<i class="fas fa-user-cog me-1"></i> Set Custom Sanction';
                    sanctionButton.className = 'btn btn-outline-primary btn-sm custom-sanction-btn';
                    sanctionButton.style.display = 'inline-block';

                    const safeStudentName = studentName.replace(/'/g, "\\'");
                    sanctionButton.setAttribute('onclick', `manageCustomSanction(${studentId}, '${safeStudentName}', 'set', ${offenseIndex})`);
                }
            }
        }

        cancelCustomSanction(studentId, offenseIndex);
        updateAdditionalOffensesDisplay();
        showAlert(`Custom sanction removed for ${studentName}`, 'warning');
    }
}

function cancelCustomSanction(studentId, offenseIndex = 0) {
    console.log('❌ Canceling custom sanction for student:', studentId, 'Offense Index:', offenseIndex);

    const studentCard = document.querySelector(`[data-student-id="${studentId}"]`);
    if (!studentCard) return;

    // Find the specific offense entry
    const offenseEntries = studentCard.querySelectorAll('.offense-entry');
    if (offenseIndex >= offenseEntries.length) return;

    const offenseEntry = offenseEntries[offenseIndex];
    const dropdownDiv = offenseEntry.querySelector('.inline-sanction-dropdown');
    const sanctionButton = offenseEntry.querySelector('.custom-sanction-btn');

    if (dropdownDiv) {
        dropdownDiv.remove();
    }

    if (sanctionButton) {
        sanctionButton.style.display = 'inline-block';
    }
}

// ==========================================
// UPDATED OFFENSE DISPLAY FUNCTIONS
// ==========================================

function updateOffenseHistoryDisplay() {
    const allOffenses = getAllOffensesAndSanctions();
    if (allOffenses.length === 0) return;

    const violatorIds = [...new Set(violationPairs.map(pair => pair.violator_id))];
    if (violatorIds.length === 0) return;

    if (Object.keys(studentOffenseHistory).length > 0) {
        displayOffenseHistory(studentOffenseHistory, violatorIds);
    } else {
        loadOffenseHistory();
    }
}

function displayOffenseHistory(historyData, violatorIds) {
    const historyContent = document.getElementById('offenseHistoryContent');
    historyContent.innerHTML = '';
    document.getElementById('historyAndCustomSection').style.display = 'block';

    let currentColumn = 1;
    let column1 = document.createElement('div');
    let column2 = document.createElement('div');

    violatorIds.forEach(violatorId => {
        const student = allStudents.find(s => s.student_id == violatorId);
        if (!student) return;

        const studentHistory = historyData[violatorId];
        const count = studentHistory?.count || 0;
        const records = studentHistory?.records || [];

        const studentDiv = createStudentHistoryDiv(student, violatorId, count, records);

        if (currentColumn === 1) {
            column1.appendChild(studentDiv);
            currentColumn = 2;
        } else {
            column2.appendChild(studentDiv);
            currentColumn = 1;
        }
    });

    historyContent.appendChild(column1);
    historyContent.appendChild(column2);
}

function createStudentHistoryDiv(student, studentId, count, records) {
    const badgeClass = getBadgeClass(count);
    const severityText = getSeverityText(count);

    const studentDiv = document.createElement('div');
    studentDiv.className = 'offense-history-item mb-3 p-3 border rounded';
    studentDiv.setAttribute('data-student-id', studentId);

    studentDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-3">
                    <strong class="h5">${student.student_fname} ${student.student_lname}</strong>
                    <span class="badge ${badgeClass} ms-2">${count} total offenses</span>
                </div>
                ${createOffenseEntries(studentId, student, count)}
                ${createRecentOffenses(count, records)}
                <div class="text-center mt-3 pt-3 border-top">
                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="openAdditionalOffenseModal(${studentId}, '${student.student_fname} ${student.student_lname}', ${count})">
                        <i class="fas fa-plus me-1"></i> Add Another Offense
                    </button>
                    <small class="text-muted d-block mt-1">Add additional offenses for this student</small>
                </div>
            </div>
        </div>
    `;

    return studentDiv;
}

function createOffenseEntries(studentId, student, count) {
    const allOffenses = getAllOffensesAndSanctions();
    if (allOffenses.length === 0) return createDefaultOffenseEntry(studentId, student, count, 0);

    let entriesHTML = '';
    allOffenses.forEach((offense, index) => {
        const offenseText = getOffenseTextById(offense.offense_id);

        // Get student's specific offense history
        const studentHistory = studentOffenseHistory[studentId];
        const offenseCount = studentHistory?.count || 0;

        // IMPORTANT: The offenseCount is PREVIOUS offenses, not including current
        // So if offenseCount = 1, they have 1 previous violation

        const currentSanction = studentHistory?.current_sanction;
        const nextSanction = studentHistory?.next_sanction;
        const previousSanctions = studentHistory?.previous_sanctions || [];
        const allStages = studentHistory?.all_stages || [];

        // Display information correctly
        let displayHTML = `
            <div class="small text-muted mb-1">
                <strong>Offense:</strong> ${offenseText}
            </div>
            <div class="small text-muted mb-1">
                <strong>Previous Offenses of this type:</strong>
                <span class="badge ${offenseCount > 0 ? 'bg-warning' : 'bg-success'} ms-1">
                    ${offenseCount}
                </span>
            </div>
        `;

        if (offenseCount > 0) {
            // Show previous sanctions if they exist
            if (previousSanctions.length > 0) {
                displayHTML += `
                    <div class="small text-muted mb-1">
                        <strong>Previously Received:</strong>
                        ${previousSanctions.map(sanction =>
                            `<span class="badge bg-secondary ms-1">${sanction}</span>`
                        ).join('')}
                    </div>
                `;
            }

            // Show what sanction they should get NOW for their NEXT offense
            if (currentSanction) {
                displayHTML += `
                    <div class="small text-success mb-1">
                        <strong>Recommended Sanction (for this offense):</strong>
                        <span class="fw-bold ms-1">${currentSanction.sanction_consequences}</span>
                        <small class="text-muted ms-2">(Based on ${offenseCount} previous offense(s))</small>
                    </div>
                `;
            }

            // Show what they'll get if they do it AGAIN
            if (nextSanction) {
                displayHTML += `
                    <div class="small text-warning mb-1">
                        <strong>If they commit this again:</strong>
                        <span class="ms-1">${nextSanction.sanction_consequences}</span>
                    </div>
                `;
            }
        } else {
            // First-time offender
            displayHTML += `
                <div class="small text-success mb-1">
                    <strong>Status:</strong> First-time offender
                </div>
                <div class="small text-success mb-1">
                    <strong>Recommended Sanction:</strong> Verbal Warning (First offense)
                </div>
            `;

            if (allStages.length > 0 && allStages[0]) {
                displayHTML += `
                    <div class="small text-muted mb-1">
                        <strong>Next offense would be:</strong> ${allStages[1]?.sanction_consequences || allStages[0].sanction_consequences}
                    </div>
                `;
            }
        }

        // Show stage progression
        if (allStages.length > 0) {
            displayHTML += `
                <div class="small text-muted mb-1">
                    <strong>Sanction Progression:</strong>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        ${allStages.map((stage, stageIndex) => {
                            const isCurrent = stageIndex === offenseCount;
                            const isPast = stageIndex < offenseCount;

                            let badgeClass = 'badge bg-light text-dark border';
                            if (isCurrent) badgeClass = 'badge bg-primary';
                            else if (isPast) badgeClass = 'badge bg-secondary';

                            return `
                                <span class="${badgeClass}" style="font-size: 0.7rem;"
                                      title="${stage.sanction_consequences}">
                                    ${stageIndex + 1}
                                </span>
                            `;
                        }).join('')}
                    </div>
                    <small class="text-muted">
                        Stage ${offenseCount + 1} applies for this offense
                    </small>
                </div>
            `;
        }

        // Show recent violations
        if (studentHistory?.records && studentHistory.records.length > 0) {
            displayHTML += `
                <div class="small text-muted mb-1">
                    <strong>Recent Offenses:</strong>
                    <ul class="mb-0 mt-1" style="font-size: 0.8rem;">
                        ${studentHistory.records.map(record =>
                            `<li>
                                ${record.date} - ${record.sanction || 'No sanction'}
                            </li>`
                        ).join('')}
                    </ul>
                </div>
            `;
        }

        entriesHTML += `
            <div class="offense-entry mb-3 p-3 border rounded bg-light">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        ${displayHTML}
                    </div>
                    ${createSanctionButton(studentId, student, count, index)}
                </div>
            </div>
        `;
    });

    return entriesHTML;
}

function createDefaultOffenseEntry(studentId, student, count, offenseIndex = 0) {
    const mainOffenseSelect = document.getElementById('offense_id');
    const selectedOffenseName = mainOffenseSelect.value ? mainOffenseSelect.options[mainOffenseSelect.selectedIndex].text : 'None';
    const badgeClass = getBadgeClass(count);
    const severityText = getSeverityText(count);

    return `
        <div class="offense-entry mb-3 p-3 border rounded bg-light">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1">
                    <div class="small text-muted mb-1"><strong>Selected Offense:</strong> ${selectedOffenseName}</div>
                    <div class="small text-muted mb-1"><strong>Severity:</strong> ${severityText}</div>
                    <span class="badge ${badgeClass} offense-count-badge">${count} previous offense(s)</span>
                </div>
                ${createSanctionButton(studentId, student, count, offenseIndex)}
            </div>
        </div>
    `;
}

function createSanctionButton(studentId, student, count, offenseIndex = 0) {
    const safeStudentName = student.student_fname + ' ' + student.student_lname;

    // Check if this specific offense has a custom sanction
    const hasCustomForThisOffense = customSanctions[studentId]?.customSanctions?.[offenseIndex];
    const currentCustomText = hasCustomForThisOffense ? customSanctions[studentId].customSanctions[offenseIndex].sanctionText : null;

    if (hasCustomForThisOffense) {
        return `
            <button type="button" class="btn btn-outline-success btn-sm custom-sanction-btn"
                    onclick="manageCustomSanction(${studentId}, '${safeStudentName.replace(/'/g, "\\'")}', 'modify', ${offenseIndex})">
                <i class="fas fa-check-circle me-1"></i> ${currentCustomText}
            </button>
        `;
    }
    return `
        <button type="button" class="btn btn-outline-primary btn-sm custom-sanction-btn"
                onclick="manageCustomSanction(${studentId}, '${safeStudentName.replace(/'/g, "\\'")}', 'set', ${offenseIndex})">
            <i class="fas fa-user-cog me-1"></i> Set Custom Sanction
        </button>
    `;
}

function createRecentOffenses(count, records) {
    if (count === 0) return '<span class="text-success d-block mt-3">No previous offenses found</span>';

    return `
        <div class="small text-muted mt-3">
            <strong>Recent offenses:</strong>
            <ul class="mb-0 mt-1">
                ${records.slice(0, 3).map(record => `<li>${record.date} - ${record.offense_type}</li>`).join('')}
                ${count > 3 ? `<li>... and ${count - 3} more</li>` : ''}
            </ul>
        </div>
    `;
}

// ==========================================
// UPDATED CUSTOM SANCTIONS DISPLAY FUNCTION
// ==========================================

function updateAdditionalOffensesDisplay() {
    const customSanctionsContent = document.getElementById('customSanctionsContent');
    customSanctionsContent.innerHTML = '';

    let hasContent = false;

    // Get all offenses (main + additional)
    const allOffenses = getAllOffensesAndSanctions();

    Object.keys(customSanctions).forEach(studentId => {
        const student = allStudents.find(s => s.student_id == studentId);
        if (!student) return;

        const studentData = customSanctions[studentId];
        const studentSection = document.createElement('div');
        studentSection.className = 'custom-sanction-item mb-4 p-3 border border-primary rounded';

        let offensesHTML = '';

        // Display custom sanctions for each offense
        allOffenses.forEach((offense, offenseIndex) => {
            const offenseText = getOffenseTextById(offense.offense_id);
            const generalSanctionText = getSanctionTextById(offense.sanction_id);

            // Check if this student has a custom sanction for this specific offense
            const hasCustomForThisOffense = studentData.customSanctions &&
                                          studentData.customSanctions[offenseIndex];

            const customSanctionData = hasCustomForThisOffense ?
                                     studentData.customSanctions[offenseIndex] : null;

            offensesHTML += `
                <div class="offense-sanction-pair mb-3 p-3 border rounded ${hasCustomForThisOffense ? 'border-warning bg-light' : ''}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong class="text-primary">Offense:</strong>
                                <div class="mt-1">${offenseText}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong class="${hasCustomForThisOffense ? 'text-warning' : 'text-secondary'}">
                                    ${hasCustomForThisOffense ? 'Custom Sanction:' : 'General Sanction:'}
                                </strong>
                                <div class="mt-1 d-flex align-items-center justify-content-between">
                                    <span class="${hasCustomForThisOffense ? 'text-warning fw-bold' : 'text-muted'}">
                                        ${hasCustomForThisOffense ? customSanctionData.sanctionText : generalSanctionText}
                                    </span>
                                    ${!hasCustomForThisOffense ? `
                                        <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                                onclick="manageCustomSanction(${studentId}, '${student.student_fname} ${student.student_lname}', 'set', ${offenseIndex})">
                                            <i class="fas fa-user-cog me-1"></i> Set Custom
                                        </button>
                                    ` : `
                                        <div class="btn-group ms-2">
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                    onclick="manageCustomSanction(${studentId}, '${student.student_fname} ${student.student_lname}', 'modify', ${offenseIndex})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="removeCustomSanction(${studentId}, '${student.student_fname} ${student.student_lname}', ${offenseIndex})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            if (hasCustomForThisOffense) hasContent = true;
        });

        // Display additional offenses (from the modal)
        if (studentData.offenses?.length > 0) {
            hasContent = true;
            offensesHTML += `<div class="mt-3 pt-3 border-top"><h6>Additional Offenses:</h6>`;

            studentData.offenses.forEach((offense, index) => {
                offensesHTML += `
                    <div class="additional-offense-item mb-2 p-2 border rounded bg-info bg-opacity-10">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <strong>${offense.offense_text}</strong>
                                    <span class="badge bg-info ms-2">${offense.sanction_text}</span>
                                </div>
                                <small class="text-muted">Additional offense specifically for this student</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                    onclick="removeAdditionalOffenseItem(${studentId}, ${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            offensesHTML += `</div>`;
        }

        studentSection.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="mb-1"><i class="fas fa-user me-2"></i>${student.student_fname} ${student.student_lname}</h6>
                    <small class="text-muted">ID: ${studentId}</small>
                </div>
                <span class="badge bg-primary">
                    ${Object.keys(studentData.customSanctions || {}).length} custom sanction(s)
                    ${studentData.offenses?.length ? `, ${studentData.offenses.length} additional offense(s)` : ''}
                </span>
            </div>
            <div class="mt-2">
                <h6 class="mb-3 border-bottom pb-2">Sanctions per Offense:</h6>
                ${offensesHTML || '<p class="text-muted text-center py-3">No custom sanctions set for this student.</p>'}
            </div>
        `;

        customSanctionsContent.appendChild(studentSection);
    });

    if (!hasContent && Object.keys(customSanctions).length === 0) {
        customSanctionsContent.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-user-cog fa-2x mb-2"></i>
                <p>No custom sanctions set yet</p>
                <small>Click "Set Custom Sanction" on any student to add custom sanctions</small>
            </div>
        `;
    }
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

function getBadgeClass(count) {
    if (count >= 5) return 'badge bg-danger';
    if (count >= 3) return 'badge bg-warning';
    if (count >= 1) return 'badge bg-info';
    return 'badge bg-success';
}

function getSeverityText(count) {
    if (count >= 5) return 'Chronic Offender (5+ violations)';
    if (count >= 3) return 'Repeat Offender (3-4 violations)';
    if (count >= 1) return 'First-time/Infrequent Offender';
    return 'No prior violations';
}

function getOffenseSpecificCount(studentHistory, offenseId) {
    if (!studentHistory?.records) return 0;
    return studentHistory.records.filter(record => record.offense_id == offenseId).length;
}

function getOffenseTextById(offenseId) {
    if (!offenseId) return 'Unknown Offense';

    const mainOption = document.querySelector(`#offense_id option[value="${offenseId}"]`);
    if (mainOption) return mainOption.text;

    for (let offenseNumber of additionalOffenses) {
        const additionalOption = document.querySelector(`#additional_offense_${offenseNumber} option[value="${offenseId}"]`);
        if (additionalOption) return additionalOption.text;
    }

    return 'Unknown Offense';
}

function getSanctionTextById(sanctionId) {
    if (!sanctionId) return 'No sanction selected';

    const mainOption = document.querySelector(`#sanction_id option[value="${sanctionId}"]`);
    if (mainOption) return mainOption.text;

    for (let offenseNumber of additionalOffenses) {
        const additionalOption = document.querySelector(`#additional_sanction_${offenseNumber} option[value="${sanctionId}"]`);
        if (additionalOption) return additionalOption.text;
    }

    return 'Unknown Sanction';
}

function updatePrimaryOffenseDisplay() {
    console.log('🔄 Updating primary offense display...');
    updateOffenseHistoryDisplay();
}

// ==========================================
// ADDITIONAL OFFENSE FUNCTIONS
// ==========================================

function addAdditionalOffense() {
    const container = document.getElementById('additional-offenses-container');
    const offenseCount = container.children.length + 1;

    const offenseSection = document.createElement('div');
    offenseSection.className = 'additional-offense-section';
    offenseSection.id = `offense-section-${offenseCount}`;
    offenseSection.innerHTML = createAdditionalOffenseHTML(offenseCount);

    container.appendChild(offenseSection);
    additionalOffenses.push(offenseCount);

    setTimeout(updateOffenseHistoryDisplay, 100);
}

function createAdditionalOffenseHTML(offenseCount) {
    return `
        <div class="additional-offense-header">
            <h6 class="additional-offense-title">Additional Offense & Sanction #${offenseCount}</h6>
            <button type="button" class="remove-offense-btn" onclick="removeAdditionalOffense(${offenseCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="additional_offense_${offenseCount}" class="form-label required-field">Offense Type</label>
                <select class="form-select additional-offense-select" id="additional_offense_${offenseCount}"
                        onchange="loadAdditionalSanctions(${offenseCount}); updateOffenseHistoryDisplay();">
                    <option value="">Select Offense Type</option>
                    @foreach($offenses as $offense)
                        <option value="{{ $offense->offense_id }}">
                            {{ $offense->offense_type }} ({{ $offense->category }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="additional_sanction_${offenseCount}" class="form-label required-field">General Sanction</label>
                <select class="form-select additional-sanction-select" id="additional_sanction_${offenseCount}" onchange="updateOffenseHistoryDisplay()">
                    <option value="">Select Offense First</option>
                </select>
            </div>
        </div>
    `;
}

function removeAdditionalOffense(offenseNumber) {
    const offenseSection = document.getElementById(`offense-section-${offenseNumber}`);
    if (offenseSection) {
        offenseSection.remove();
        additionalOffenses = additionalOffenses.filter(num => num !== offenseNumber);
        updateOffenseHistoryDisplay();
    }
}

async function loadAdditionalSanctions(offenseNumber) {
    const offenseId = document.getElementById(`additional_offense_${offenseNumber}`).value;
    const sanctionSelect = document.getElementById(`additional_sanction_${offenseNumber}`);

    if (!offenseId) {
        sanctionSelect.innerHTML = '<option value="">Select Offense First</option>';
        updateOffenseHistoryDisplay();
        return;
    }

    await loadSanctionsIntoSelect(offenseId, sanctionSelect);
    updateOffenseHistoryDisplay();
}

async function loadSanctions() {
    const offenseId = document.getElementById('offense_id').value;
    const sanctionSelect = document.getElementById('sanction_id');

    if (!offenseId) {
        sanctionSelect.innerHTML = '<option value="">Select Offense First</option>';
        updateOffenseHistoryDisplay();
        return;
    }

    await loadSanctionsIntoSelect(offenseId, sanctionSelect);
    updateOffenseHistoryDisplay();
}

async function loadSanctionsIntoSelect(offenseId, selectElement) {
    selectElement.innerHTML = '<option value="">Loading sanctions...</option>';
    selectElement.disabled = true;

    try {
        const response = await fetch('/adviser/violations/get-sanctions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ offense_id: offenseId })
        });

        if (!response.ok) throw new Error(`Server returned ${response.status}`);

        const data = await response.json();
        selectElement.innerHTML = '<option value="">Select Sanction</option>';

        if (data?.length > 0) {
            data.forEach((sanction) => {
                const option = document.createElement('option');
                option.value = sanction.sanction_id;
                option.textContent = sanction.sanction_consequences;
                if (sanction.sanction_description) option.title = sanction.sanction_description;
                selectElement.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading sanctions:', error);
        selectElement.innerHTML = '<option value="">Error loading sanctions</option>';
    } finally {
        selectElement.disabled = false;
    }
}

// ==========================================
// ADDITIONAL OFFENSE MODAL FUNCTIONS
// ==========================================

function openAdditionalOffenseModal(studentId, studentName, offenseCount) {
    currentAdditionalOffenseStudent = { id: studentId, name: studentName, offenseCount: offenseCount };
    document.getElementById('additionalOffenseStudentName').textContent = studentName;

    const countBadge = document.getElementById('additionalOffenseCountBadge');
    countBadge.textContent = `${offenseCount} previous offense(s)`;
    countBadge.className = `badge ${getBadgeClass(offenseCount)} me-2`;

    document.getElementById('additionalOffenseSeverityText').textContent = getSeverityText(offenseCount);
    document.getElementById('additional_offense_select').value = '';
    document.getElementById('additional_sanction_select').innerHTML = '<option value="">Select Offense First</option>';

    const modal = new bootstrap.Modal(document.getElementById('additionalOffenseModal'));
    modal.show();
}

async function loadAdditionalOffenseSanctions() {
    const offenseId = document.getElementById('additional_offense_select').value;
    const sanctionSelect = document.getElementById('additional_sanction_select');
    await loadSanctionsIntoSelect(offenseId, sanctionSelect);
    updateAdditionalOffenseHistory();
}

async function updateAdditionalOffenseHistory() {
    const offenseId = document.getElementById('additional_offense_select').value;
    const studentId = currentAdditionalOffenseStudent?.id;

    if (!offenseId || !studentId) {
        document.getElementById('additionalOffenseCountBadge').textContent = `${currentAdditionalOffenseStudent.offenseCount} previous offense(s)`;
        document.getElementById('additionalOffenseSeverityText').textContent = getSeverityText(currentAdditionalOffenseStudent.offenseCount);
        return;
    }

    try {
        const response = await fetch('/adviser/violations/get-offense-history', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ student_ids: [studentId], offense_id: offenseId })
        });

        if (response.ok) {
            const data = await response.json();
            const studentHistory = data[studentId];
            const count = studentHistory?.count || 0;

            document.getElementById('additionalOffenseCountBadge').textContent = `${count} previous offense(s)`;
            document.getElementById('additionalOffenseSeverityText').textContent = getSeverityText(count);
            document.getElementById('additionalOffenseCountBadge').className = `badge ${getBadgeClass(count)} me-2`;
        }
    } catch (error) {
        console.error('Error loading additional offense history:', error);
    }
}

function confirmAdditionalOffense() {
    const offenseId = document.getElementById('additional_offense_select').value;
    const sanctionId = document.getElementById('additional_sanction_select').value;

    if (!offenseId || !sanctionId) {
        alert('Please select both an offense type and sanction.');
        return;
    }

    const offenseText = document.getElementById('additional_offense_select').selectedOptions[0].text;
    const sanctionText = document.getElementById('additional_sanction_select').selectedOptions[0].text;

    if (!customSanctions[currentAdditionalOffenseStudent.id]) {
        customSanctions[currentAdditionalOffenseStudent.id] = { customSanctions: {}, offenses: [] };
    }

    if (!customSanctions[currentAdditionalOffenseStudent.id].offenses) {
        customSanctions[currentAdditionalOffenseStudent.id].offenses = [];
    }

    customSanctions[currentAdditionalOffenseStudent.id].offenses.push({
        offense_id: offenseId, sanction_id: sanctionId, offense_text: offenseText, sanction_text: sanctionText
    });

    updateAdditionalOffensesDisplay();
    updateOffenseHistoryDisplay();

    const modal = bootstrap.Modal.getInstance(document.getElementById('additionalOffenseModal'));
    modal.hide();

    showAlert(`Additional offense added for ${currentAdditionalOffenseStudent.name}`, 'success');
}

function removeAdditionalOffenseItem(studentId, offenseIndex) {
    if (customSanctions[studentId]?.offenses) {
        customSanctions[studentId].offenses.splice(offenseIndex, 1);
        if (customSanctions[studentId].offenses.length === 0 &&
            Object.keys(customSanctions[studentId].customSanctions || {}).length === 0) {
            delete customSanctions[studentId];
        }
        updateAdditionalOffensesDisplay();
    }
}

// ==========================================
// VIOLATION PAIR MANAGEMENT
// ==========================================

function getAllOffensesAndSanctions() {
    const offenses = [];
    const mainOffenseId = document.getElementById('offense_id')?.value;
    const mainSanctionId = document.getElementById('sanction_id')?.value;

    if (mainOffenseId) {
        offenses.push({ offense_id: mainOffenseId, sanction_id: mainSanctionId || null, is_main: true });
    }

    additionalOffenses.forEach(offenseNumber => {
        const offenseSelect = document.getElementById(`additional_offense_${offenseNumber}`);
        const sanctionSelect = document.getElementById(`additional_sanction_${offenseNumber}`);
        if (offenseSelect?.value) {
            offenses.push({
                offense_id: offenseSelect.value,
                sanction_id: sanctionSelect?.value || null,
                is_additional: true,
                offense_number: offenseNumber
            });
        }
    });

    return offenses;
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;

    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    setTimeout(() => alertDiv.remove(), 5000);
}

function selectViolationType(type) {
    currentViolationType = type;
    const individualBtn = document.getElementById('individualBtn');
    const groupBtn = document.getElementById('groupBtn');
    const individualSection = document.getElementById('individualSection');
    const groupSection = document.getElementById('groupSection');

    if (type === 'individual') {
        individualBtn.classList.add('active');
        groupBtn.classList.remove('active');
        individualSection.style.display = 'block';
        groupSection.style.display = 'none';
    } else {
        individualBtn.classList.remove('active');
        groupBtn.classList.add('active');
        individualSection.style.display = 'none';
        groupSection.style.display = 'block';
    }

    violationPairs = [];
    document.getElementById('violationPairsSection').style.display = 'none';
    document.getElementById('submitBtn').disabled = true;
}

// UPDATED: Individual violation function using live search
function addIndividualViolation() {
    if (!selectedIndividualViolator) {
        alert('Please select a violator.');
        return;
    }

    violationPairs = [{
        violator_id: selectedIndividualViolator,
        violator_name: getStudentName(selectedIndividualViolator)
    }];

    showViolationPairs();
}

function generateGroupViolations() {
    if (selectedViolators.length === 0) {
        alert('Please select at least one violator.');
        return;
    }

    violationPairs = [];
    selectedViolators.forEach(violatorId => {
        violationPairs.push({
            violator_id: violatorId,
            violator_name: getStudentName(violatorId)
        });
    });

    if (violationPairs.length === 0) {
        alert('No valid violation entries generated.');
        return;
    }

    showViolationPairs();
}

function showViolationPairs() {
    const pairsList = document.getElementById('violationPairsList');
    const section = document.getElementById('violationPairsSection');

    pairsList.innerHTML = '';
    const typeInfo = document.createElement('div');
    typeInfo.className = 'alert alert-info mb-3';
    typeInfo.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        <strong>${currentViolationType === 'individual' ? 'Individual' : 'Group'} Violation</strong> -
        ${currentViolationType === 'individual' ? 'Single violator' : `${selectedViolators.length} violator(s)`}
        <span class="badge bg-primary ms-2">${violationPairs.length} entry(s)</span>
    `;
    pairsList.appendChild(typeInfo);

    violationPairs.forEach((pair, index) => {
        const pairDiv = document.createElement('div');
        pairDiv.className = 'violation-pair';
        pairDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>#${index + 1}</strong>
                    <span class="ms-2">${pair.violator_name}</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeViolationPair(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        pairsList.appendChild(pairDiv);
    });

    section.style.display = 'block';
    document.getElementById('submitBtn').disabled = false;
}

function removeViolationPair(index) {
    violationPairs.splice(index, 1);
    if (violationPairs.length === 0) {
        document.getElementById('violationPairsSection').style.display = 'none';
        document.getElementById('submitBtn').disabled = true;
    } else {
        showViolationPairs();
    }
}

function openOffenseModal() {
    if (violationPairs.length === 0) {
        alert('No violation entries to process.');
        return;
    }
    const modal = new bootstrap.Modal(document.getElementById('offenseModal'));
    modal.show();
}

async function loadOffenseHistory() {
    const offenseId = document.getElementById('offense_id').value;
    if (!offenseId) {
        document.getElementById('historyAndCustomSection').style.display = 'none';
        return;
    }

    const violatorIds = [...new Set(violationPairs.map(pair => pair.violator_id))];
    if (violatorIds.length === 0) {
        document.getElementById('historyAndCustomSection').style.display = 'none';
        return;
    }

    const historyContent = document.getElementById('offenseHistoryContent');
    historyContent.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading violator offense history...</div>';
    document.getElementById('historyAndCustomSection').style.display = 'block';

    try {
        // Load offense history with recommended sanctions
        const response = await fetch('/adviser/violations/get-offense-history', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                student_ids: violatorIds,
                offense_id: offenseId,
                include_recommended_sanctions: true // Make sure this is included
            })
        });

        if (!response.ok) throw new Error(`Server returned ${response.status}`);

        const data = await response.json();
        studentOffenseHistory = data;

        displayOffenseHistory(data, violatorIds);

    } catch (error) {
        console.error('Error loading offense history:', error);
        const historyContent = document.getElementById('offenseHistoryContent');
        historyContent.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Offense history is temporarily unavailable.
                <br><small>You can still proceed with creating the violation and set custom sanctions.</small>
            </div>
        `;
        displayAllViolatorsWithCustomOptions(violatorIds, {});
        document.getElementById('historyAndCustomSection').style.display = 'block';
    }
}
// NEW FUNCTION: Load recommended sanctions based on offense count
async function loadRecommendedSanctions(violatorIds, offenseId) {
    try {
        // Load sanction stages for this offense
        const stagesResponse = await fetch('/adviser/violations/get-sanction-stages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ offense_id: offenseId })
        });

        if (stagesResponse.ok) {
            const stagesData = await stagesResponse.json();

            // Store sanction stages globally for use in display
            window.sanctionStages = stagesData.stages || [];

            // For each student, determine recommended sanction
            violatorIds.forEach(studentId => {
                const studentHistory = studentOffenseHistory[studentId];
                const count = studentHistory?.count || 0;

                // Determine which stage applies based on offense count
                let recommendedSanction = null;

                if (window.sanctionStages.length > 0) {
                    // Find stage where offense_count matches or is closest
                    const applicableStages = window.sanctionStages.filter(stage => stage.offense_count <= count);

                    if (applicableStages.length > 0) {
                        // Get the highest stage that applies
                        recommendedSanction = applicableStages.reduce((prev, current) =>
                            (prev.offense_count > current.offense_count) ? prev : current
                        );
                    } else {
                        // Default to first stage
                        recommendedSanction = window.sanctionStages[0];
                    }
                }

                // Store recommended sanction for this student
                if (studentHistory) {
                    studentHistory.recommended_sanction = recommendedSanction;
                }
            });
        }
    } catch (error) {
        console.error('Error loading recommended sanctions:', error);
    }
}

function displayAllViolatorsWithCustomOptions(violatorIds, historyData) {
    const historyContent = document.getElementById('offenseHistoryContent');
    historyContent.innerHTML = '';

    let currentColumn = 1;
    let column1 = document.createElement('div');
    let column2 = document.createElement('div');

    violatorIds.forEach(violatorId => {
        const student = allStudents.find(s => s.student_id == violatorId);
        if (!student) return;

        const studentHistory = historyData[violatorId];
        const count = studentHistory?.count || 0;

        const studentDiv = document.createElement('div');
        studentDiv.className = 'offense-history-item mb-3 p-3 border rounded';
        studentDiv.setAttribute('data-student-id', violatorId);

        const safeStudentName = student.student_fname + ' ' + student.student_lname;

        studentDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <strong>${student.student_fname} ${student.student_lname}</strong>
                    </div>
                    <div class="offense-entries-container">
                        <div class="offense-entry mb-2 p-2 border rounded bg-light">
                            <div class="small text-muted mb-1">
                                <strong>Selected Offense:</strong> ${document.getElementById('offense_id').value ? document.getElementById('offense_id').options[document.getElementById('offense_id').selectedIndex].text : 'None'}
                            </div>
                            <div class="small text-muted mb-1">
                                <strong>Severity:</strong> ${getSeverityText(count)}
                            </div>
                            <span class="badge ${getBadgeClass(count)}">${count} previous offense(s)</span>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <button type="button" class="btn btn-outline-primary btn-sm custom-sanction-btn"
                                onclick="manageCustomSanction(${violatorId}, '${safeStudentName.replace(/'/g, "\\'")}', 'set', 0)">
                            <i class="fas fa-user-cog me-1"></i> Set Custom Sanction
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm add-offense-history-btn"
                                onclick="openAdditionalOffenseModal(${violatorId}, '${safeStudentName.replace(/'/g, "\\'")}', ${count})">
                            <i class="fas fa-plus me-1"></i> Add Another Offense
                        </button>
                        <small class="text-muted ms-2">Set individual sanction or add offenses for this student</small>
                    </div>
                </div>
            </div>
        `;

        if (currentColumn === 1) {
            column1.appendChild(studentDiv);
            currentColumn = 2;
        } else {
            column2.appendChild(studentDiv);
            currentColumn = 1;
        }
    });

    historyContent.appendChild(column1);
    historyContent.appendChild(column2);
}

// ==========================================
// EVIDENCE FILE MANAGEMENT
// ==========================================

function addEvidenceFile() {
    const container = document.getElementById('evidenceFilesContainer');
    const fileGroup = document.createElement('div');
    fileGroup.className = 'evidence-file-input-group mb-2';
    fileGroup.innerHTML = `
        <div class="input-group">
            <input type="file" class="form-control evidence-file-input" name="evidence_files[]"
                   accept="image/*,video/*,.mp4,.mov,.avi,.mkv,.webm">
            <button type="button" class="btn btn-outline-danger" onclick="removeEvidenceFile(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(fileGroup);
    updateRemoveButtons();
}

function removeEvidenceFile(button) {
    const fileGroup = button.closest('.evidence-file-input-group');
    fileGroup.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const fileGroups = document.querySelectorAll('.evidence-file-input-group');
    const removeButtons = document.querySelectorAll('.evidence-file-input-group .btn-outline-danger');
    removeButtons.forEach(button => button.disabled = fileGroups.length === 1);
}

function getAllEvidenceFiles() {
    const fileInputs = document.querySelectorAll('.evidence-file-input');
    const allFiles = [];
    fileInputs.forEach(input => { if (input.files.length > 0) allFiles.push(input.files[0]); });
    return allFiles;
}

// ==========================================
// UPDATED REVIEW MODAL FUNCTIONS
// ==========================================

function openReviewModal() {
    // Grab values but treat them as OPTIONAL
    const mainOffenseId   = document.getElementById('offense_id')?.value || '';
    const mainSanctionId  = document.getElementById('sanction_id')?.value || '';
    const date            = document.getElementById('violation_date')?.value || '';
    const time            = document.getElementById('violation_time')?.value || '';
    const incident        = document.getElementById('violation_incident')?.value || '';

    // Only validation: additional offenses must not be half-filled
    let hasInvalidAdditionalOffense = false;
    additionalOffenses.forEach(offenseNumber => {
        const offenseId  = document.getElementById(`additional_offense_${offenseNumber}`)?.value || '';
        const sanctionId = document.getElementById(`additional_sanction_${offenseNumber}`)?.value || '';

        if ((offenseId && !sanctionId) || (!offenseId && sanctionId)) {
            hasInvalidAdditionalOffense = true;
        }
    });

    if (hasInvalidAdditionalOffense) {
        alert('Please make sure all additional offenses have both offense type and sanction selected.');
        return;
    }

    // Safely hide the Offense modal if it exists
    const offenseModalEl = document.getElementById('offenseModal');
    const offenseModal   = bootstrap.Modal.getInstance(offenseModalEl);
    if (offenseModal) {
        offenseModal.hide();
    }

    // Build the review modal content (it will show "No offenses selected" if empty)
    updateReviewModalContent();

    // Show the Review modal (create instance if needed)
    const reviewModalEl = document.getElementById('reviewModal');
    const reviewModal   = bootstrap.Modal.getOrCreateInstance(reviewModalEl);
    reviewModal.show();
}


    let hasInvalidAdditionalOffense = false;
    additionalOffenses.forEach(offenseNumber => {
        const offenseId = document.getElementById(`additional_offense_${offenseNumber}`).value;
        const sanctionId = document.getElementById(`additional_sanction_${offenseNumber}`).value;
        if ((offenseId && !sanctionId) || (!offenseId && sanctionId)) hasInvalidAdditionalOffense = true;
    });

    if (hasInvalidAdditionalOffense) {
        alert('Please make sure all additional offenses have both offense type and sanction selected.');
        return;
    }

    const offenseModal = bootstrap.Modal.getInstance(document.getElementById('offenseModal'));
    offenseModal.hide();

    updateReviewModalContent();

    const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    reviewModal.show();
}

function updateReviewModalContent() {
    const reviewList = document.getElementById('reviewViolationsList');
    reviewList.innerHTML = '';

    // Update statistics
    updateReviewStatistics();

    // Violation type header
    const typeHeader = document.createElement('div');
    typeHeader.className = `alert ${currentViolationType === 'individual' ? 'alert-success' : 'alert-warning'} mb-4`;
    typeHeader.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas ${currentViolationType === 'individual' ? 'fa-user' : 'fa-users'} me-2"></i>
                <strong>${currentViolationType === 'individual' ? 'INDIVIDUAL VIOLATION' : 'GROUP VIOLATION'}</strong>
            </div>
            <span class="badge ${currentViolationType === 'individual' ? 'bg-success' : 'bg-warning'} review-type-badge">
                ${violationPairs.length} ${violationPairs.length === 1 ? 'entry' : 'entries'}
            </span>
        </div>
        ${currentViolationType === 'group' ?
            `<small class="d-block mt-1">${selectedViolators.length} violator(s)</small>` :
            ''}
    `;
    reviewList.appendChild(typeHeader);

    // Offenses & Sanctions Section
    const offensesSection = document.createElement('div');
    offensesSection.className = 'violation-pair mb-4';
    offensesSection.innerHTML = createOffensesReviewSection();
    reviewList.appendChild(offensesSection);

    // Shared Details Section
    const sharedDetails = document.createElement('div');
    sharedDetails.className = 'violation-pair mb-4';
    sharedDetails.innerHTML = createSharedDetailsReviewSection();
    reviewList.appendChild(sharedDetails);

    // Individual Violation Pairs Section
    const pairsSection = document.createElement('div');
    pairsSection.className = 'violation-pair mb-4';
    pairsSection.innerHTML = createViolationPairsReviewSection();
    reviewList.appendChild(pairsSection);

    // Custom Sanctions Summary
    if (Object.keys(customSanctions).length > 0) {
        const customSection = document.createElement('div');
        customSection.className = 'violation-pair mb-4';
        customSection.innerHTML = createCustomSanctionsReviewSection();
        reviewList.appendChild(customSection);
    }
}

function updateReviewStatistics() {
    const totalOffenses = getAllOffensesAndSanctions().length;
    const customSanctionsCount = Object.keys(customSanctions).length;
    const additionalOffensesCount = Object.values(customSanctions).reduce((total, student) => {
        return total + (student.offenses ? student.offenses.length : 0);
    }, 0);

    document.getElementById('reviewViolationPairs').textContent = violationPairs.length;
    document.getElementById('reviewTotalOffenses').textContent = totalOffenses;
    document.getElementById('reviewCustomSanctions').textContent = customSanctionsCount;
    document.getElementById('reviewAdditionalOffenses').textContent = additionalOffensesCount;
}

function createOffensesReviewSection() {
    const offenses = getAllOffensesAndSanctions();
    if (offenses.length === 0) return '<p class="text-danger">No offenses selected</p>';

    let html = `
        <h6 class="mb-3"><i class="fas fa-gavel me-2"></i>Selected Offenses & Sanctions</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="10%">#</th>
                        <th width="45%">Offense Type</th>
                        <th width="45%">General Sanction</th>
                    </tr>
                </thead>
                <tbody>
    `;

    offenses.forEach((offense, index) => {
        const isMain = index === 0;
        const offenseSelect = isMain ?
            document.getElementById('offense_id') :
            document.getElementById(`additional_offense_${additionalOffenses[index-1]}`);
        const sanctionSelect = isMain ?
            document.getElementById('sanction_id') :
            document.getElementById(`additional_sanction_${additionalOffenses[index-1]}`);

        const offenseText = offenseSelect ? offenseSelect.selectedOptions[0].text : 'Unknown Offense';
        const sanctionText = sanctionSelect ? sanctionSelect.selectedOptions[0].text : 'No Sanction Selected';

        html += `
            <tr>
                <td><strong>${index + 1}</strong> ${isMain ? '<span class="badge bg-primary ms-1">Main</span>' : '<span class="badge bg-info ms-1">Additional</span>'}</td>
                <td>${offenseText}</td>
                <td>${sanctionText}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function createSharedDetailsReviewSection() {
    const date = document.getElementById('violation_date').value;
    const time = document.getElementById('violation_time').value;
    const incident = document.getElementById('violation_incident').value;
    const witnesses = document.getElementById('witnesses').value;
    const evidenceDescription = document.getElementById('evidence_description').value;
    const hasCustomSanctions = Object.keys(customSanctions).length > 0;
    const evidenceFiles = getAllEvidenceFiles();

    let sanctionInfo = '';
    if (hasCustomSanctions) {
        sanctionInfo = `
            <div class="alert alert-warning mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Mixed Sanctions Applied:</strong> Some students have custom sanctions that override the general sanction.
                See "Custom Sanctions" section below for details.
            </div>
        `;
    }

    let html = `
        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Shared Violation Details</h6>
        ${sanctionInfo}

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-2">
                    <strong><i class="fas fa-calendar me-2"></i>Incident Date:</strong>
                    <span class="ms-2">${date}</span>
                </div>
                <div class="mb-2">
                    <strong><i class="fas fa-clock me-2"></i>Incident Time:</strong>
                    <span class="ms-2">${time}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <strong><i class="fas fa-list me-2"></i>Total Offenses:</strong>
                    <span class="badge bg-primary ms-2">${getAllOffensesAndSanctions().length}</span>
                </div>
                <div class="mb-2">
                    <strong><i class="fas fa-user-cog me-2"></i>Custom Sanctions:</strong>
                    <span class="badge ${hasCustomSanctions ? 'bg-warning' : 'bg-secondary'} ms-2">
                        ${Object.keys(customSanctions).length} student(s)
                    </span>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <strong><i class="fas fa-file-alt me-2"></i>Incident Description:</strong>
            <div class="mt-2 p-3 bg-light rounded">
                ${incident || '<span class="text-muted">No description provided</span>'}
            </div>
        </div>
    `;

    // Witnesses
    if (witnesses) {
        html += `
            <div class="mb-3">
                <strong><i class="fas fa-users me-2"></i>Witnesses:</strong>
                <div class="mt-2 p-3 bg-light rounded">
                    ${witnesses}
                </div>
            </div>
        `;
    }

    // Evidence Description
    if (evidenceDescription) {
        html += `
            <div class="mb-3">
                <strong><i class="fas fa-clipboard-check me-2"></i>Evidence Description:</strong>
                <div class="mt-2 p-3 bg-light rounded">
                    ${evidenceDescription}
                </div>
            </div>
        `;
    }

    // Evidence Files
    if (evidenceFiles.length > 0) {
        html += createEvidenceFilesReviewSection(evidenceFiles);
    }

    return html;
}

function createEvidenceFilesReviewSection(evidenceFiles) {
    let filesHTML = `
        <div class="mb-3">
            <strong><i class="fas fa-paperclip me-2"></i>Evidence Files (${evidenceFiles.length}):</strong>
            <div class="row mt-2">
    `;

    evidenceFiles.forEach((file, index) => {
        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
        const fileType = file.type.split('/')[0];

        if (fileType === 'image') {
            filesHTML += `
                <div class="col-md-4 mb-3">
                    <div class="card evidence-file-card">
                        <img src="${URL.createObjectURL(file)}" class="card-img-top" alt="${file.name}"
                             style="height: 150px; object-fit: cover; cursor: pointer;"
                             onclick="openImageModal('${URL.createObjectURL(file)}', '${file.name}')">
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                            <small class="text-muted">${fileSize} MB</small>
                            <span class="badge bg-success ms-1">Image</span>
                        </div>
                    </div>
                </div>
            `;
        } else if (fileType === 'video') {
            filesHTML += `
                <div class="col-md-6 mb-3">
                    <div class="card evidence-file-card">
                        <video controls class="card-img-top" style="height: 150px; object-fit: cover;" preload="metadata">
                            <source src="${URL.createObjectURL(file)}" type="${file.type}">
                            Your browser does not support the video tag.
                        </video>
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                            <small class="text-muted">${fileSize} MB</small>
                            <span class="badge bg-info ms-1">Video</span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            filesHTML += `
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-2 text-center evidence-file-card">
                        <i class="fas fa-file fa-2x text-muted mb-2"></i>
                        <small class="d-block text-muted text-truncate" title="${file.name}">${file.name}</small>
                        <small class="text-muted">${fileSize} MB</small>
                        <span class="badge bg-secondary ms-1">File</span>
                    </div>
                </div>
            `;
        }
    });

    filesHTML += `</div></div>`;
    return filesHTML;
}

function createViolationPairsReviewSection() {
    let html = `
        <h6 class="mb-3"><i class="fas fa-list me-2"></i>Individual Violation Entries</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="80%">Violator</th>
                        <th width="15%">Sanctions Applied</th>
                    </tr>
                </thead>
                <tbody>
    `;

    violationPairs.forEach((pair, index) => {
        const violatorId = pair.violator_id;
        const hasCustomSanction = customSanctions[violatorId]?.customSanctions?.[0];
        const hasAdditionalOffenses = customSanctions[violatorId]?.offenses?.length > 0;

        let sanctionInfo = 'General Sanction';
        let badgeClass = 'bg-primary';

        if (hasCustomSanction) {
            sanctionInfo = 'Custom Sanction';
            badgeClass = 'bg-warning';
        }

        if (hasAdditionalOffenses) {
            sanctionInfo += ' + Additional Offenses';
            badgeClass = 'bg-info';
        }

        html += `
            <tr>
                <td><strong>${index + 1}</strong></td>
                <td>
                    ${pair.violator_name}
                    ${hasCustomSanction ? '<i class="fas fa-user-cog text-warning ms-1" title="Has custom sanction"></i>' : ''}
                    ${hasAdditionalOffenses ? '<i class="fas fa-plus-circle text-info ms-1" title="Has additional offenses"></i>' : ''}
                </td>
                <td>
                    <span class="badge ${badgeClass}">${sanctionInfo}</span>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function createCustomSanctionsReviewSection() {
    let html = `
        <h6 class="mb-3"><i class="fas fa-user-cog me-2"></i>Custom Sanctions & Additional Offenses</h6>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            The following students have custom sanctions or additional offenses that override the general settings.
        </div>
    `;

    Object.keys(customSanctions).forEach(studentId => {
        const student = allStudents.find(s => s.student_id == studentId);
        if (!student) return;

        const studentData = customSanctions[studentId];
        const customSanctionsCount = Object.keys(studentData.customSanctions || {}).length;
        const additionalOffensesCount = studentData.offenses?.length || 0;

        html += `
            <div class="custom-sanction-item mb-3 p-3 border border-warning rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>${student.student_fname} ${student.student_lname}
                    </h6>
                    <div>
                        ${customSanctionsCount > 0 ?
                            `<span class="badge bg-warning me-1">${customSanctionsCount} custom sanction(s)</span>` : ''}
                        ${additionalOffensesCount > 0 ?
                            `<span class="badge bg-info">${additionalOffensesCount} additional offense(s)</span>` : ''}
                    </div>
                </div>
        `;

        // Display custom sanctions per offense
        if (customSanctionsCount > 0) {
            html += `<div class="mb-2"><strong>Custom Sanctions:</strong></div>`;

            Object.keys(studentData.customSanctions).forEach(offenseIndex => {
                const sanctionData = studentData.customSanctions[offenseIndex];
                const offenseNumber = parseInt(offenseIndex) + 1;

                html += `
                    <div class="ps-3 mb-1">
                        <small>
                            <i class="fas fa-gavel text-warning me-1"></i>
                            Offense ${offenseNumber}: <span class="text-warning">${sanctionData.sanctionText}</span>
                        </small>
                    </div>
                `;
            });
        }

        // Display additional offenses
        if (additionalOffensesCount > 0) {
            html += `<div class="mb-2 mt-2"><strong>Additional Offenses:</strong></div>`;

            studentData.offenses.forEach((offense, index) => {
                html += `
                    <div class="ps-3 mb-1">
                        <small>
                            <i class="fas fa-plus-circle text-info me-1"></i>
                            ${offense.offense_text} - <span class="text-info">${offense.sanction_text}</span>
                        </small>
                    </div>
                `;
            });
        }

        html += `</div>`;
    });

    return html;
}

function openImageModal(imageSrc, imageName) {
    if (!document.getElementById('imagePreviewModal')) {
        const modalHTML = `
            <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${imageName}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${imageSrc}" class="img-fluid" alt="${imageName}" style="max-height: 70vh;">
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    const imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
    imageModal.show();
}

function backToOffenseModal() {
    const reviewModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
    reviewModal.hide();

    // Small delay to ensure modal is fully hidden before showing the next one
    setTimeout(() => {
        const offenseModal = new bootstrap.Modal(document.getElementById('offenseModal'));
        offenseModal.show();
    }, 300);
}

async function finalSubmit() {
    const date = document.getElementById('violation_date').value;
    const time = document.getElementById('violation_time').value;
    const incident = document.getElementById('violation_incident').value;
    const witnesses = document.getElementById('witnesses').value;
    const evidenceDescription = document.getElementById('evidence_description').value;
    const evidenceFiles = getAllEvidenceFiles();

    const offenses = getAllOffensesAndSanctions();
    if (offenses.length === 0) {
        alert('Please select at least one offense and sanction.');
        return;
    }

    const formData = new FormData();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    formData.append('_token', csrfToken);

    // Add offenses
    offenses.forEach((offense, index) => {
        formData.append(`offenses[${index}][offense_id]`, offense.offense_id);
        formData.append(`offenses[${index}][sanction_id]`, offense.sanction_id);
    });

    // Add violations
    violationPairs.forEach((pair, index) => {
        // For individual violation, use violator_id
        // For group violation, use violator_ids array
        if (currentViolationType === 'individual') {
            formData.append(`violations[${index}][violator_id]`, pair.violator_id);
        } else {
            // For group violations, we need to collect all violator IDs
            const allViolatorIds = violationPairs.map(p => p.violator_id);
            formData.append(`violations[${index}][violator_ids]`, JSON.stringify(allViolatorIds));
        }

        formData.append(`violations[${index}][date]`, date);
        formData.append(`violations[${index}][time]`, time);
        formData.append(`violations[${index}][incident]`, incident);
        formData.append(`violations[${index}][witnesses]`, witnesses);
        formData.append(`violations[${index}][evidence_description]`, evidenceDescription);

        // Add custom sanctions if any
        if (customSanctions[pair.violator_id]) {
            formData.append(`violations[${index}][custom_sanctions]`, JSON.stringify(customSanctions[pair.violator_id]));
        }
    });

    // Add evidence files
    evidenceFiles.forEach((file) => {
        formData.append('evidence_files[]', file);
    });

    // Submit the form
    try {
        const response = await fetch('{{ route("prefect.violations.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alert(data.message);
            window.location.href = '{{ route("prefect.violation") }}';
        } else {
            alert(data.message || 'Error submitting violations');
        }
    } catch (error) {
        console.error('Submission error:', error);
        alert('Error submitting violations: ' + error.message);
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        violationPairs = [];
        selectedViolators = [];
        additionalOffenses = [];
        customSanctions = {};

        // NEW: Reset individual violation selections
        selectedIndividualViolator = null;

        // Reset individual search inputs and results
        document.getElementById('individual_violator_search').value = '';
        document.getElementById('individual_violator_tag').innerHTML = '';
        document.getElementById('individual_violator_results').innerHTML = '';

        document.getElementById('violatorTags').innerHTML = '';
        document.getElementById('violationPairsList').innerHTML = '';
        document.getElementById('violationPairsSection').style.display = 'none';
        document.getElementById('submitBtn').disabled = true;

        document.getElementById('offense_id').value = '';
        document.getElementById('sanction_id').innerHTML = '<option value="">Select Sanction</option>';
        document.getElementById('violation_date').value = '{{ date('Y-m-d') }}';
        document.getElementById('violation_time').value = '{{ date('H:i') }}';
        document.getElementById('violation_incident').value = '';
        document.getElementById('charCount').textContent = '0';

        document.getElementById('additional-offenses-container').innerHTML = '';
        document.getElementById('witnesses').value = '';
        document.getElementById('evidence_description').value = '';

        const evidenceFilesContainer = document.getElementById('evidenceFilesContainer');
        evidenceFilesContainer.innerHTML = `
            <div class="evidence-file-input-group mb-2">
                <div class="input-group">
                    <input type="file" class="form-control evidence-file-input" name="evidence_files[]"
                           accept="image/*,video/*,.mp4,.mov,.avi,.mkv,.webm">
                    <button type="button" class="btn btn-outline-danger" onclick="removeEvidenceFile(this)" disabled>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        document.getElementById('historyAndCustomSection').style.display = 'none';
        document.getElementById('offenseHistoryContent').innerHTML = '';
        document.getElementById('customSanctionsContent').innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-user-cog fa-2x mb-2"></i>
                <p>No custom sanctions set yet</p>
                <small>Click "Set Custom Sanction" on any student to add custom sanctions</small>
            </div>
        `;

        selectViolationType('individual');

        // NEW: Reinitialize individual search
        searchIndividualStudents('', 'violator');
    }
}

function submitViolations() {
    openOffenseModal();
}
</script>
</body>
</html>
