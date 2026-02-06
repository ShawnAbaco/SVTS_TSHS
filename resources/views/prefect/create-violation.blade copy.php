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

        /* Add to your existing CSS */
.review-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.review-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.review-table .table-info {
    background-color: rgba(13, 110, 253, 0.05);
}
        /* Add to your existing CSS */
.review-modal-content {
    max-height: 80vh;
    overflow-y: auto;
}

.review-table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.review-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Loading spinner for review modal */
#reviewViolationsList .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Ensure modal backdrop is properly layered */
.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
}

        /* Add to your existing CSS */
.student-history-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 0;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.student-history-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.student-history-card .student-header {
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid #f1f3f4;
}

.student-history-card .student-name {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0 0 0.5rem 0;
}

.offense-sanction-boxes {
    flex-grow: 1;
}

.offense-entry {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
    border-left: 4px solid #007bff;
}

.offense-entry:hover {
    background: #f1f3f4;
    border-color: #007bff;
}

.recent-violations .list-group-item {
    border-left: 0;
    border-right: 0;
}

.recent-violations .list-group-item:first-child {
    border-top: 0;
}

.recent-violations .list-group-item:last-child {
    border-bottom: 0;
}
        /* Modal Fixes */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            background: #4b0000;
            color: white;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title i {
            color: white;
        }

        .modal-body {
            padding: 1.5rem;
        }

        /* Ensure modals display properly */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: block;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Fix for modal scrolling */
        .modal-dialog {
            margin: 1.75rem auto;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }

        /* Review modal specific styles */
        #reviewModal .modal-header {
            background: #007bff;
        }

        #reviewModal .alert-info {
            background-color: #e7f6ff;
            border-color: #b3e0ff;
            color: #0066cc;
        }

        /* Statistics cards in review modal */
        #reviewModal .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s ease;
        }

        #reviewModal .card:hover {
            transform: translateY(-2px);
        }

        #reviewModal .card.bg-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }

        #reviewModal .card.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
        }

        #reviewModal .card.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        }

        #reviewModal .card.bg-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%) !important;
        }

        /* Compact text for review tables */
        .compact-text {
            font-size: 0.875rem;
            line-height: 1.4;
        }

        /* Table improvements */
        .table-sm th,
        .table-sm td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Form section improvements */
        .form-section {
            border-left: 4px solid #007bff;
        }

        /* Button fixes */
        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Ensure modal z-index is correct */
        .modal {
            z-index: 1060;
        }

        .modal-backdrop {
            z-index: 1050;
        }

        /* ========================================== */
        /* 3-COLUMN OFFENSE HISTORY LAYOUT */
        /* ========================================== */
        /* Style for the Generate button */
        #loadOffenseHistoryBtn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        #loadOffenseHistoryBtn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        #loadOffenseHistoryBtn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Success alert styling */
        .alert-success {
            background-color: #d1f7dc;
            border-color: #a3e9b9;
            color: #0d6832;
        }

        .offense-history-three-col {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        .offense-history-three-col>.history-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Responsive adjustments for 3-column layout */
        @media (max-width: 1200px) {
            .offense-history-three-col {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .offense-history-three-col {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Improve the student history item appearance */
        .student-history-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 0;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .student-history-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .student-history-card .student-header {
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #f1f3f4;
        }

        .student-history-card .student-name {
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }

        /* Offense entries within the card */
        .offense-sanction-boxes {
            flex-grow: 1;
            margin-top: 0.5rem;
            padding-left: 0;
        }

        .offense-entry {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
            border-left: 4px solid #007bff;
        }

        .additional-offense-entry {
            background: rgba(13, 110, 253, 0.08);
            border: 1px solid rgba(13, 110, 253, 0.2);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-left: 4px solid #0d6efd;
        }

        /* Add Another Offense button */
        .add-offense-history-btn {
            width: 100%;
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        /* Remove the old 2-column grid styles */
        .offense-history-grid {
            display: none;
        }

        /* Add to your existing CSS */
        /* ========================================== */
        /* OFFENSE HISTORY LAYOUT IMPROVEMENTS */
        /* ========================================== */

        .offense-history-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .offense-history-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .offense-sanction-boxes {
            margin-top: 1rem;
            padding-left: 0;
        }

        .offense-entry {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            border-left: 4px solid #007bff;
        }

        .offense-entry:hover {
            background: #e9ecef;
            border-color: #ced4da;
        }

        .offense-entry .small {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .offense-entry .small:last-child {
            margin-bottom: 0;
        }

        .offense-entry strong {
            color: #495057;
            min-width: 160px;
            display: inline-block;
        }

        .additional-offense-entry {
            background: rgba(13, 110, 253, 0.08);
            border: 1px solid rgba(13, 110, 253, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-left: 4px solid #0d6efd;
        }

        .additional-offense-entry .small {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .additional-offense-entry .small:last-child {
            margin-bottom: 0;
        }

        .add-offense-history-btn {
            margin-top: 1rem;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .add-offense-history-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* Student header styling */
        .offense-history-item .mb-3 {
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #f1f3f4;
        }

        .offense-history-item .h5 {
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.25rem;
            margin: 0;
        }

        .offense-history-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        .offense-history-grid>div {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .offense-history-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .offense-history-item {
                padding: 1.25rem;
            }

            .offense-entry {
                padding: 1rem;
            }
        }

        /* Animation for new entries */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .offense-entry,
        .additional-offense-entry {
            animation: fadeInUp 0.3s ease-out;
        }

        /* Make the layout more compact for better viewing */
        .offense-sanction-boxes .offense-entry:not(:last-child),
        .offense-sanction-boxes .additional-offense-entry:not(:last-child) {
            margin-bottom: 0.75rem;
        }

        /* Add some spacing between the boxes */
        .offense-sanction-boxes {
            padding: 0.5rem 0;
        }

        /* Style for the "Add Another Offense" button section */
        .text-center.mt-3.pt-3.border-top {
            border-top: 2px dashed #dee2e6 !important;
            padding-top: 1.25rem !important;
            margin-top: 1.5rem !important;
        }

        /* Improve the overall card appearance */
        .card {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .card-header {
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Alert styling improvements */
        .alert {
            border-radius: 10px;
            border: 1px solid transparent;
        }

        .alert-info {
            background-color: #e7f6ff;
            border-color: #b3e0ff;
            color: #0066cc;
        }

        /* Form section improvements */
        .form-section {
            border-radius: 12px;
            padding: 1.75rem;
        }

        /* Modal body spacing */
        .modal-body {
            padding: 1.5rem;
        }

        /* Ensure consistent spacing */
        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .offense-list {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 0.5rem;
        }

        .offense-item {
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 0.25rem;
            transition: background-color 0.2s ease;
            border: 1px solid transparent;
        }

        .offense-item:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .offense-item.selected {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .offense-item.selected:hover {
            background-color: #0056b3;
        }

        .offense-check {
            width: 20px;
            text-align: center;
        }

        .visible {
            visibility: visible;
        }

        .invisible {
            visibility: hidden;
        }

        /* Selected offenses tags styling */
        .selected-tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            min-height: 40px;
            padding: 5px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .offense-tag {
            background: #e9ecef;
            border-radius: 20px;
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            animation: fadeIn 0.3s;
        }

        .offense-tag .remove-offense {
            cursor: pointer;
            color: #6c757d;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.15s;
        }

        .offense-tag .remove-offense:hover {
            background-color: #dc3545;
            color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Scrollbar styling */
        .offense-list::-webkit-scrollbar {
            width: 6px;
        }

        .offense-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .offense-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .offense-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .card-header {
            background: #4b0000;
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
            background: #4b0000;
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
            border-color: #660000;
            transform: translateY(-2px);
        }

        .violation-type-btn.active {
            border-color: #660000;
            background-color: #f0f8ff;
        }

        .violation-type-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #660000;
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

        .additional-offense-item {
            border-left: 4px solid #17a2b8 !important;
        }

        @media (max-width: 768px) {

            .two-column-grid,
            .offense-history-grid {
                grid-template-columns: 1fr;
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
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <!-- Navigation Breadcrumb -->
        <div class="nav-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('prefect.violation') }}"
                            class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Violation</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-plus-circle me-1"></i>Create
                        Violation</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card main-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title text-white mb-1"><i class="fas fa-gavel me-2"></i>Create New
                                    Violation</h2>
                                <p class="text-white-50 mb-0">Report disciplinary issues or conflicts between students
                                </p>
                            </div>
                            <a href="{{ route('prefect.violation') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {!! session('success') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
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
                                    <div class="violation-type-btn active" id="individualBtn"
                                        onclick="selectViolationType('individual')">
                                        <div class="violation-type-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <h5>Individual Violation</h5>
                                        <p class="text-muted mb-0">One violator</p>
                                        <input type="radio" name="violation_type" id="individual_violation"
                                            value="individual" checked hidden>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="violation-type-btn" id="groupBtn"
                                        onclick="selectViolationType('group')">
                                        <div class="violation-type-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h5>Group Violation</h5>
                                        <p class="text-muted mb-0">Multiple violators</p>
                                        <input type="radio" name="violation_type" id="group_violation" value="group"
                                            hidden>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Updated Individual Violation Section -->
                        <div id="individualSection" class="form-section">
                            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Individual Violation Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="individual_violator_search"
                                        class="form-label required-field">Violator</label>
                                    <input type="text" class="form-control" id="individual_violator_search"
                                        placeholder="Search violator...">
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
                                <input type="text" class="form-control" id="violatorSearch"
                                    placeholder="Search students...">
                                <div class="student-list mt-2">
                                    <div id="violatorResults"></div>
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
                                <small class="text-muted"><span class="text-danger">*</span> indicates required
                                    field</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo me-1"></i> Reset Form
                                </button>
                                <button type="button" class="btn btn-primary" id="submitBtn"
                                    onclick="submitViolations()" disabled>
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
    <div class="modal fade" id="offenseModal" tabindex="-1" aria-labelledby="offenseModalLabel"
        aria-hidden="true">
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
                        Offenses are filtered to show only violation-related categories. Sanctions are loaded from
                        predefined stages.
                    </div>

                    <!-- Student Offense History Section -->
                    <div id="historyAndCustomSection" style="display: none;">
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-history me-2"></i>Student Offense Preview</h5>

                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Violator Offense Records
                                    </h6>
                                </div>

                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <small><i class="fas fa-info-circle me-1"></i>
                                            You can set custom sanctions for any student regardless of offense count
                                        </small>
                                    </div>

                                    <div id="offenseHistoryContent" class="offense-history-three-col">
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
                                        <h6 class="additional-offense-title">Violation Details</h6>
                                    </div>

                                    <div class="row">
                                        <!-- Left Column: Offense Selection -->
                                        <div class="col-md-6 mb-3">
                                            <label for="offense_search" class="form-label required-field">Search
                                                Offense Type</label>

                                            <!-- Search Input -->
                                            <input type="text" class="form-control mb-2" id="offense_search"
                                                placeholder="Search offenses..." onkeyup="searchOffenses()">

                                            <!-- Selected Offenses Display (like student tags) -->
                                            <div class="selected-tags-container mb-2"
                                                id="selected_offenses_container">
                                                <!-- Selected offenses will appear here as tags -->
                                            </div>

                                            <!-- Scrollable Offense List -->
                                            <div class="offense-list mt-2" id="offense_list_container">
                                                <div id="offense_results">
                                                    @foreach ($offenses as $offense)
                                                        <div class="offense-item"
                                                            data-offense-id="{{ $offense->offense_id }}"
                                                            data-offense-type="{{ $offense->offense_type }}"
                                                            onclick="toggleOffense(this)">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-grow-1">
                                                                    <strong>{{ $offense->offense_type }}</strong>
                                                                </div>
                                                                <div class="offense-check">
                                                                    <i class="fas fa-check"
                                                                        style="display: none;"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- NEW: Button to load violator offense records -->
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-primary"
                                                    id="loadOffenseHistoryBtn" onclick="loadOffenseHistoryManually()">
                                                    <i class="fas fa-history me-1"></i> Generate Violator Offense
                                                    Records
                                                </button>
                                            </div>

                                            <!-- Hidden select field for form submission -->
                                            <select class="form-select d-none" id="offense_id" name="offense_ids[]"
                                                multiple>
                                                <!-- Options will be added dynamically -->
                                            </select>

                                            <div class="form-text mt-1">Only violation-related offenses are shown. You
                                                can select multiple offenses.</div>
                                        </div>

                                        <!-- Right Column: Incident Description -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-3">
                                                <label for="violation_incident"
                                                    class="form-label required-field">Incident Description</label>
                                                <textarea class="form-control" id="violation_incident" rows="8"
                                                    placeholder="Provide a detailed description of what happened..." maxlength="1000"></textarea>
                                                <div class="form-text">
                                                    <span id="charCount">0</span>/1000 characters
                                                </div>
                                            </div>

                                            <!-- Date & Time Below Incident Description -->
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="violation_date"
                                                        class="form-label required-field">Incident Date</label>
                                                    <input type="date" class="form-control" id="violation_date"
                                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="violation_time"
                                                        class="form-label required-field">Incident Time</label>
                                                    <input type="time" class="form-control" id="violation_time"
                                                        value="{{ date('H:i') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="complainant" class="form-label">Complainant <small
                                                class="text-muted">(optional)</small></label>
                                        <textarea class="form-control" id="complainant" rows="2"
                                            placeholder="List complainant(s) separated by commas or each line (e.g., John D., Maria S.)"></textarea>
                                    </div>

                                    <!-- Witnesses -->
                                    <div class="mb-3">
                                        <label for="witnesses" class="form-label">Witnesses <small
                                                class="text-muted">(optional)</small></label>
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
                                                    <input type="file" class="form-control evidence-file-input"
                                                        name="evidence_files[]"
                                                        accept="image/*,video/*,.mp4,.mov,.avi,.mkv,.webm">
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="removeEvidenceFile(this)" disabled>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Add more files button -->
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addEvidenceFile()">
                                            <i class="fas fa-plus me-1"></i> Add Another File
                                        </button>

                                        <div class="form-text">You may attach multiple photos or videos as evidence
                                        </div>
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

                <!-- Error container for validation messages - INSERT THIS RIGHT HERE -->
                <div id="reviewErrors" style="display: none;"></div>

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

    <!-- UPDATED MODAL: Additional Offense Modal -->
    <div class="modal fade additional-offense-modal" id="additionalOffenseModal" tabindex="-1"
        aria-labelledby="additionalOffenseModalLabel" aria-hidden="true">
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
                        <!-- REMOVED the previous offense count display -->
                    </div>

                    <div class="mb-3">
                        <label for="additional_offense_select" class="form-label required-field">Offense Type</label>
                        <select class="form-select" id="additional_offense_select"
                            onchange="loadAdditionalOffenseSanctions();">
                            <option value="">Select Offense Type</option>
                            @foreach ($offenses as $offense)
                                <option value="{{ $offense->offense_id }}">
                                    {{ $offense->offense_type }} ({{ $offense->category }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CHANGED: Sanction display instead of dropdown -->
                    <div class="mb-3">
                        <label class="form-label required-field">Sanction to be Applied</label>
                        <div class="sanction-display p-3 border rounded bg-light" id="additional_sanction_display">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Select an offense type to see the recommended sanction
                            </div>
                        </div>
                        <!-- Hidden input to store the sanction ID for form submission -->
                        <input type="hidden" id="additional_sanction_id" name="additional_sanction_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAdditionalOffenseBtn"
                        onclick="confirmAdditionalOffense()">
                        <i class="fas fa-check me-1"></i> Add Offense
                    </button>
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
        let additionalOffenses = [];
        let currentAdditionalOffenseStudent = null;

        // NEW: Variables to track selected individual students
        let selectedIndividualViolator = null;

        let allOffenses = [
            @foreach ($offenses as $offense)
                {
                    id: "{{ $offense->offense_id }}",
                    type: "{{ $offense->offense_type }}"
                },
            @endforeach
        ];

        let selectedOffenses = [];

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedOffensesDisplay();
    initializeStudentSearch();
    initializeIndividualSearch();
    document.getElementById('violation_date').max = new Date().toISOString().split('T')[0];
    document.getElementById('violation_incident').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });

    // Clear any existing timeouts
    if (window.historyLoadTimeout) {
        clearTimeout(window.historyLoadTimeout);
    }

    // Reset loading flag
    window.isLoadingHistory = false;

    // Make sure to initialize the offense history section
    const historySection = document.getElementById('historyAndCustomSection');
    if (historySection) {
        historySection.style.display = 'none';
    }

    // Initialize offense display
    initializeOffenseDisplay();

    // Debug: Test if modals exist
    console.log('Modal 1 exists:', document.getElementById('offenseModal') !== null);
    console.log('Modal 2 exists:', document.getElementById('reviewModal') !== null);
});

function initializeOffenseDisplay() {
    // Make sure the selected offenses display is updated
    updateSelectedOffensesDisplay();
}

        // ==========================================
        // OFFENSE SELECTION FUNCTIONS
        // ==========================================

        function searchOffenses() {
            const searchInput = document.getElementById('offense_search');
            const searchTerm = searchInput.value.toLowerCase().trim();
            const resultsDiv = document.getElementById('offense_results');

            resultsDiv.innerHTML = '';

            if (searchTerm === '') {
                renderOffenseList(allOffenses);
            } else {
                const filteredOffenses = allOffenses.filter(offense =>
                    offense.type.toLowerCase().includes(searchTerm)
                );

                if (filteredOffenses.length === 0) {
                    resultsDiv.innerHTML = '<div class="text-muted p-2">No offenses found</div>';
                } else {
                    renderOffenseList(filteredOffenses);
                }
            }
        }

        function renderOffenseList(offenses) {
            const resultsDiv = document.getElementById('offense_results');

            offenses.forEach(offense => {
                const isSelected = selectedOffenses.some(selected => selected.id === offense.id);
                const offenseItem = document.createElement('div');
                offenseItem.className = `offense-item ${isSelected ? 'selected' : ''}`;
                offenseItem.setAttribute('data-offense-id', offense.id);
                offenseItem.setAttribute('data-offense-type', offense.type);
                offenseItem.onclick = () => toggleOffense(offenseItem);

                offenseItem.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <strong>${offense.type}</strong>
                        </div>
                        <div class="offense-check">
                            <i class="fas fa-check ${isSelected ? 'visible' : 'invisible'}"></i>
                        </div>
                    </div>
                `;

                resultsDiv.appendChild(offenseItem);
            });
        }

        function toggleOffense(element) {
            const offenseId = element.getAttribute('data-offense-id');
            const offenseType = element.getAttribute('data-offense-type');

            const existingIndex = selectedOffenses.findIndex(offense => offense.id === offenseId);

            if (existingIndex > -1) {
                selectedOffenses.splice(existingIndex, 1);
            } else {
                selectedOffenses.push({
                    id: offenseId,
                    type: offenseType
                });
            }

            updateSelectedOffensesDisplay();
            updateHiddenSelectField();
            searchOffenses();
        }

        function updateSelectedOffensesDisplay() {
            const container = document.getElementById('selected_offenses_container');

            if (selectedOffenses.length === 0) {
                container.innerHTML = '<div class="text-muted p-2">No offenses selected yet</div>';
                return;
            }

            container.innerHTML = '';

            selectedOffenses.forEach(offense => {
                const tag = document.createElement('span');
                tag.className = 'offense-tag';
                tag.innerHTML = `
                    ${offense.type}
                    <span class="remove-offense" onclick="removeSelectedOffense('${offense.id}')">
                        <i class="fas fa-times"></i>
                    </span>
                `;
                container.appendChild(tag);
            });
        }

        function removeSelectedOffense(offenseId) {
            selectedOffenses = selectedOffenses.filter(offense => offense.id !== offenseId);
            updateSelectedOffensesDisplay();
            updateHiddenSelectField();
            searchOffenses();
        }

        function updateHiddenSelectField() {
            const hiddenSelect = document.getElementById('offense_id');
            hiddenSelect.innerHTML = '';

            selectedOffenses.forEach(offense => {
                const option = document.createElement('option');
                option.value = offense.id;
                option.textContent = offense.type;
                option.selected = true;
                hiddenSelect.appendChild(option);
            });
        }

        // ==========================================
        // INDIVIDUAL VIOLATION SEARCH FUNCTIONS
        // ==========================================

        function initializeIndividualSearch() {
            const violatorSearch = document.getElementById('individual_violator_search');
            violatorSearch.addEventListener('input', function() {
                searchIndividualStudents(this.value, 'violator');
            });
            searchIndividualStudents('', 'violator');
        }

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

                const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
                const section = student.adviser?.adviser_section || '';
                const displayText =
                    `${student.student_lname}, ${student.student_fname} - Gr. ${gradeLevel} ${section}`.trim();

                studentDiv.innerHTML = displayText;
                studentDiv.onclick = () => selectIndividualStudent(student.student_id, type);
                resultsDiv.appendChild(studentDiv);
            });
        }

        function selectIndividualStudent(studentId, type) {
            if (selectedIndividualViolator === studentId) {
                selectedIndividualViolator = null;
            } else {
                selectedIndividualViolator = studentId;
            }

            updateIndividualTags(type);
            searchIndividualStudents(document.getElementById(`individual_${type}_search`).value, type);
        }

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

        function removeIndividualStudent(type) {
            selectedIndividualViolator = null;
            updateIndividualTags(type);
            searchIndividualStudents(document.getElementById(`individual_${type}_search`).value, type);
        }

        // ==========================================
        // GROUP VIOLATION SEARCH FUNCTIONS
        // ==========================================

        function initializeStudentSearch() {
            const violatorSearch = document.getElementById('violatorSearch');
            violatorSearch.addEventListener('input', function() {
                searchStudents(this.value, 'violator');
            });
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

                const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
                const section = student.adviser?.adviser_section || '';
                const displayText =
                    `${student.student_lname}, ${student.student_fname} - Gr. ${gradeLevel} ${section}`.trim();

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
        // UTILITY FUNCTIONS
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
// UTILITY FUNCTIONS
// ==========================================

function getBadgeClass(count) {
    if (count >= 5) return 'bg-danger';
    if (count >= 3) return 'bg-warning';
    if (count >= 1) return 'bg-info';
    return 'bg-success';
}

        function getSeverityText(count) {
            if (count >= 5) return 'High repeat offender';
            if (count >= 3) return 'Medium repeat offender';
            if (count >= 1) return 'Low repeat offender';
            return 'No prior offenses';
        }

        function getUniqueOffenses(records) {
            const uniqueMap = new Map();
            records.forEach(record => {
                const uniqueKey = `${record.date}|${record.time}|${record.description}|${record.offense_id}`;
                if (!uniqueMap.has(uniqueKey)) {
                    uniqueMap.set(uniqueKey, record);
                }
            });
            return Array.from(uniqueMap.values());
        }


// ==========================================
// HELPER: GET OFFENSE SPECIFIC COUNT FROM RECORDS
// ==========================================

function getOffenseSpecificCount(studentHistory, offenseId) {
    if (!studentHistory?.records) return 0;

    // Count unique violations for this specific offense
    const uniqueViolations = new Set();
    studentHistory.records.forEach(record => {
        if (record.offense_id == offenseId) {
            const uniqueKey = `${record.date}|${record.time}|${record.description}`;
            uniqueViolations.add(uniqueKey);
        }
    });

    return uniqueViolations.size;
}


        function getOffenseTextById(offenseId) {
            if (!offenseId) return 'Unknown Offense';
            const mainOption = document.querySelector(`#offense_id option[value="${offenseId}"]`);
            if (mainOption) return mainOption.text;
            return 'Unknown Offense';
        }
// ==========================================
// UPDATED: CREATE STUDENT HISTORY DIV
// ==========================================

async function createStudentHistoryDiv(student, studentId, count, records) {
    const studentName = `${student.student_fname} ${student.student_lname}`;
    const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
    const section = student.adviser?.adviser_section || '';
    const fullName = `${studentName} - Gr. ${gradeLevel} ${section}`.trim();

    const studentDiv = document.createElement('div');
    studentDiv.className = 'student-history-card';
    studentDiv.setAttribute('data-student-id', studentId);

    // Get offense entries for this student
    const offenseEntries = await createOffenseEntries(studentId, student, count);

    studentDiv.innerHTML = `
        <!-- Student Header -->
        <div class="student-header">
            <h5 class="student-name" title="${fullName}">${studentName}</h5>
            ${gradeLevel !== 'N/A' ? `<small class="text-muted">Grade ${gradeLevel} ${section}</small>` : ''}
        </div>

        <!-- Offense Sanction Boxes -->
        <div class="offense-sanction-boxes mt-3">
            ${offenseEntries}
        </div>

        <!-- Recent Violations -->
        ${createRecentViolationsSection(records, count)}

        <!-- Add Another Offense Button -->
        <div class="mt-4 pt-3 border-top">
            <button type="button" class="btn btn-outline-info btn-sm w-100 add-offense-history-btn"
                    onclick="openAdditionalOffenseModal(${studentId}, '${studentName.replace(/'/g, "\\'")}', ${count})">
                <i class="fas fa-plus me-1"></i> Add Another Offense
            </button>
            <small class="text-muted d-block mt-1 text-center">Add additional offense for this student</small>
        </div>
    `;

    return studentDiv;
}



// ==========================================
// UPDATED: CREATE RECENT VIOLATIONS SECTION
// ==========================================

function createRecentViolationsSection(records, count) {
    if (count === 0) return '';

    const recentRecords = records.slice(0, 3);

    let html = `
        <div class="recent-violations mt-3 pt-3 border-top">
            <h6 class="mb-2"><i class="fas fa-history me-1"></i>Recent Violations:</h6>
            <div class="list-group">
    `;

    recentRecords.forEach((record, index) => {
        const sanction = record.sanction || 'No sanction';
        html += `
            <div class="list-group-item border-0 py-1 px-0 bg-transparent">
                <small class="text-muted">
                    <i class="fas fa-circle text-danger me-1" style="font-size: 0.5rem;"></i>
                    ${record.date} - ${record.offense_type}
                    <br><span class="ms-2"><strong>Sanction:</strong> ${sanction}</span>
                </small>
            </div>
        `;
    });

    if (count > 3) {
        html += `
            <div class="list-group-item border-0 py-1 px-0 bg-transparent">
                <small class="text-muted">
                    <i class="fas fa-ellipsis-h me-1"></i>
                    ... and ${count - 3} more violation(s)
                </small>
            </div>
        `;
    }

    html += `
            </div>
        </div>
    `;

    return html;
}



       // ==========================================
// UPDATED: CREATE STUDENT HISTORY DIV
// ==========================================

async function createStudentHistoryDiv(student, studentId, count, records) {
    const studentName = `${student.student_fname} ${student.student_lname}`;
    const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
    const section = student.adviser?.adviser_section || '';
    const fullName = `${studentName} - Gr. ${gradeLevel} ${section}`.trim();

    const studentDiv = document.createElement('div');
    studentDiv.className = 'student-history-card';
    studentDiv.setAttribute('data-student-id', studentId);

    // Get offense entries for this student
    const offenseEntries = await createOffenseEntries(studentId, student, count);

    studentDiv.innerHTML = `
        <!-- Student Header -->
        <div class="student-header">
            <h5 class="student-name" title="${fullName}">${studentName}</h5>
            ${gradeLevel !== 'N/A' ? `<small class="text-muted">Grade ${gradeLevel} ${section}</small>` : ''}
            <div class="mt-1">
                <span class="badge ${getBadgeClass(count)}">${count} previous violation(s)</span>
            </div>
        </div>

        <!-- Offense Sanction Boxes -->
        <div class="offense-sanction-boxes mt-3">
            ${offenseEntries}
        </div>

        <!-- Recent Violations -->
        ${createRecentViolationsSection(records, count)}

        <!-- Add Another Offense Button -->
        <div class="mt-4 pt-3 border-top">
            <button type="button" class="btn btn-outline-info btn-sm w-100 add-offense-history-btn"
                    onclick="openAdditionalOffenseModal(${studentId}, '${studentName.replace(/'/g, "\\'")}', ${count})">
                <i class="fas fa-plus me-1"></i> Add Another Offense
            </button>
            <small class="text-muted d-block mt-1 text-center">Add additional offense for this student</small>
        </div>
    `;

    return studentDiv;
}

// ==========================================
// FIXED: LOAD OFFENSE HISTORY FROM DATABASE
// ==========================================

async function loadOffenseHistoryManually() {
    const selectedOffenseIds = selectedOffenses.map(offense => offense.id);
    const violatorIds = [...new Set(violationPairs.map(pair => pair.violator_id))];

    // Validate selections
    if (selectedOffenseIds.length === 0) {
        alert('Please select at least one offense type first.');
        return;
    }

    if (violatorIds.length === 0) {
        alert('Please add violators first from the main form.');
        return;
    }

    const loadBtn = document.getElementById('loadOffenseHistoryBtn');
    const originalText = loadBtn.innerHTML;
    loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating...';
    loadBtn.disabled = true;

    try {
        // Clear previous history
        studentOffenseHistory = {};

        // Load history for ALL selected offenses
        for (const offenseId of selectedOffenseIds) {
            console.log('📥 Loading history for offense ID:', offenseId);

            const response = await fetch('{{ route("prefect.violations.get-offense-history") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    student_ids: violatorIds,
                    offense_id: offenseId
                })
            });

            if (!response.ok) {
                console.error('❌ Server returned error:', response.status);
                continue;
            }

            const data = await response.json();
            console.log('📊 Received history data for offense', offenseId, ':', data);

            // Process the response - check if it's in the expected format
            if (typeof data === 'object' && !Array.isArray(data)) {
                Object.keys(data).forEach(studentId => {
                    if (!studentOffenseHistory[studentId]) {
                        studentOffenseHistory[studentId] = {
                            count: 0,
                            records: [],
                            current_sanction: null,
                            previous_sanctions: []
                        };
                    }

                    const studentData = data[studentId];

                    // Update records
                    if (studentData.records && Array.isArray(studentData.records)) {
                        // Add unique records
                        studentData.records.forEach(newRecord => {
                            const exists = studentOffenseHistory[studentId].records.some(existingRecord =>
                                existingRecord.date === newRecord.date &&
                                existingRecord.time === newRecord.time &&
                                existingRecord.description === newRecord.description
                            );
                            if (!exists) {
                                studentOffenseHistory[studentId].records.push(newRecord);
                            }
                        });
                    }

                    // Update current sanction
                    if (studentData.current_sanction) {
                        studentOffenseHistory[studentId].current_sanction = studentData.current_sanction;
                    }

                    // Update previous sanctions
                    if (studentData.previous_sanctions && Array.isArray(studentData.previous_sanctions)) {
                        studentOffenseHistory[studentId].previous_sanctions = [
                            ...new Set([...studentOffenseHistory[studentId].previous_sanctions, ...studentData.previous_sanctions])
                        ];
                    }

                    // Update count
                    studentOffenseHistory[studentId].count = studentOffenseHistory[studentId].records.length;
                });
            } else {
                console.error('❌ Unexpected response format:', data);
            }
        }

        console.log('📊 Final combined student offense history:', studentOffenseHistory);

        // Display the history
        await displayOffenseHistoryCards();

        // Show success message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            Violator offense records generated successfully for ${violatorIds.length} student(s).
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert after the button
        loadBtn.parentNode.appendChild(alertDiv);

        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);

    } catch (error) {
        console.error('❌ Error loading offense history:', error);
        alert('Error generating violator offense records. Please try again.');
    } finally {
        loadBtn.innerHTML = originalText;
        loadBtn.disabled = false;
    }
}

// ==========================================
// UPDATED: DISPLAY OFFENSE HISTORY CARDS
// ==========================================

async function displayOffenseHistoryCards() {
    const historyContent = document.getElementById('offenseHistoryContent');
    historyContent.innerHTML = '';
    document.getElementById('historyAndCustomSection').style.display = 'block';

    const violatorIds = [...new Set(violationPairs.map(pair => pair.violator_id))];

    if (violatorIds.length === 0) {
        historyContent.innerHTML = '<div class="text-muted text-center p-4">No violators selected</div>';
        return;
    }

    // Create grid container
    const gridContainer = document.createElement('div');
    gridContainer.className = 'offense-history-three-col';
    historyContent.appendChild(gridContainer);

    // Create 4 columns
    const columns = [];
    for (let i = 0; i < 3; i++) {
        const column = document.createElement('div');
        column.className = 'history-column';
        gridContainer.appendChild(column);
        columns.push(column);
    }

    // Track processed students
    const processedStudents = new Set();
    let columnIndex = 0;

    // Process each student
    for (const violatorId of violatorIds) {
        if (processedStudents.has(violatorId)) continue;

        const student = allStudents.find(s => s.student_id == violatorId);
        if (!student) continue;

        processedStudents.add(violatorId);
        const studentHistory = studentOffenseHistory[violatorId];
        const count = studentHistory?.count || 0;
        const records = studentHistory?.records || [];

        // Create student card
        const studentDiv = await createStudentHistoryDiv(student, violatorId, count, records);

        // Add to current column
        columns[columnIndex].appendChild(studentDiv);

        // Move to next column
        columnIndex = (columnIndex + 1) % 3;
    }

    // Remove empty columns
    columns.forEach((column, index) => {
        if (column.children.length === 0) {
            column.style.display = 'none';
        }
    });

    // If no cards were created, show message
    if (processedStudents.size === 0) {
        historyContent.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No offense history found for the selected students.
                <br><small>This may be their first violation.</small>
            </div>
        `;
    }
}

        function createAdditionalOffensesForStudent(studentId) {
            const studentAdditionalOffenses = additionalOffenses.filter(offense => offense.studentId == studentId);

            if (studentAdditionalOffenses.length === 0) return '';

            let html = '';
            studentAdditionalOffenses.forEach((offense, index) => {
                html += `
                    <div class="additional-offense-entry">
                        <div class="additional-offense-details">
                            <div class="small compact-text mb-1">
                                <strong>Additional:</strong> ${offense.offense_text}
                            </div>
                            <div class="small compact-text mb-1">
                                <strong>Previous:</strong> ${offense.previous_sanction || 'None'}
                            </div>
                            <div class="small compact-text">
                                <strong>Recommended:</strong> ${offense.recommended_sanction || offense.sanction_text}
                            </div>
                        </div>
                    </div>
                `;
            });

            return html;
        }

function getSelectedOffensesData() {
    return selectedOffenses;
}

// ==========================================
// FIXED: CREATE OFFENSE ENTRIES WITH CORRECT DATA FOR EACH OFFENSE
// ==========================================
async function createOffenseEntries(studentId, student, count) {
    const selectedOffensesData = getSelectedOffensesData();

    if (selectedOffensesData.length === 0) {
        return await createDefaultOffenseEntry(studentId, student, count);
    }

    let entriesHTML = '';

    for (const offense of selectedOffensesData) {
        const offenseId = offense.id;
        const offenseText = offense.type;

        // Get offense history for this specific offense
        const studentHistory = await getStudentOffenseHistoryForOffense(studentId, offenseId);

        if (studentHistory) {
            const previousSanction = studentHistory.previous_sanction || 'None';
            const currentSanction = studentHistory.current_sanction || 'Verbal Warning';
            const nextSanction = studentHistory.next_sanction || 'Maximum stage reached';

            // Get specific count for this offense, not total count
            const specificOffenseCount = getOffenseSpecificCountFromHistory(studentHistory, offenseId);

            entriesHTML += `
                <div class="offense-entry mb-3">
                    <div class="offense-details">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <div class="small mb-2">
                                    <strong class="text-primary">Offense:</strong> ${offenseText}
                                </div>
                                <div class="small mb-2">
                                    <strong>Previous sanction:</strong> ${previousSanction}
                                </div>
                                <div class="small mb-2">
                                    <strong>Current Sanction:</strong>
                                    <span class="fw-bold text-success">${currentSanction}</span>
                                </div>
                                <div class="small">
                                    <strong>Next sanction (if repeated):</strong> ${nextSanction}
                                </div>
                            </div>
                            <div class="badge ${getBadgeClass(specificOffenseCount)} ms-2">
                                ${specificOffenseCount} previous
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // If no specific history found for this offense
            entriesHTML += `
                <div class="offense-entry mb-3">
                    <div class="offense-details">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <div class="small mb-2">
                                    <strong class="text-primary">Offense:</strong> ${offenseText}
                                </div>
                                <div class="small mb-2">
                                    <strong>Previous sanction:</strong> None
                                </div>
                                <div class="small mb-2">
                                    <strong>Current Sanction:</strong>
                                    <span class="fw-bold text-success">Verbal Warning</span>
                                </div>
                                <div class="small">
                                    <strong>Next sanction (if repeated):</strong> Detention
                                </div>
                            </div>
                            <div class="badge bg-success ms-2">
                                0 previous
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    return entriesHTML;
}

// ==========================================
// NEW: GET SPECIFIC OFFENSE COUNT FROM HISTORY
// ==========================================
function getOffenseSpecificCountFromHistory(studentHistory, offenseId) {
    if (!studentHistory?.records) return 0;

    let count = 0;
    studentHistory.records.forEach(record => {
        if (record.offense_id == offenseId) {
            count++;
        }
    });
    return count;
}
// ==========================================
// UPDATED: GET STUDENT OFFENSE HISTORY FOR SPECIFIC OFFENSE
// ==========================================
async function getStudentOffenseHistoryForOffense(studentId, offenseId) {
    try {
        const response = await fetch('{{ route("prefect.violations.get-offense-history") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                student_ids: [studentId],
                offense_id: offenseId
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data && data[studentId]) {
                return data[studentId];
            }
        }
        return null;
    } catch (error) {
        console.error('Error getting offense history:', error);
        return null;
    }
}


// ==========================================
// IMPROVED: GET CURRENT SANCTION FROM DATABASE
// ==========================================

async function getCurrentSanction(studentId, offenseId) {
    try {
        const studentHistory = studentOffenseHistory[studentId];

        // If we have current_sanction from database, use it
        if (studentHistory?.current_sanction) {
            if (studentHistory.current_sanction.sanction_consequences) {
                return studentHistory.current_sanction.sanction_consequences;
            }
        }

        // Fallback: count offenses for this specific type
        const offenseCount = studentHistory ? getOffenseSpecificCount(studentHistory, offenseId) : 0;

        // Fetch sanction stages from database
        const response = await fetch('{{ route("prefect.violations.get-sanction-stages") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                offense_id: offenseId,
                offense_count: offenseCount
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success && data.recommended_sanction) {
                return data.recommended_sanction;
            }
        }

        // If no stages found, check if we have any previous sanctions
        if (studentHistory?.previous_sanctions && studentHistory.previous_sanctions.length > 0) {
            return studentHistory.previous_sanctions[0]; // Return first previous sanction
        }

        // Ultimate fallback
        return 'Verbal Warning';

    } catch (error) {
        console.error('Error getting current sanction:', error);
        return 'Verbal Warning';
    }
}

      async function getRecommendedSanction(studentId, offenseId) {
    // Get offense count for this specific offense
    const studentHistory = studentOffenseHistory[studentId];
    const offenseCount = studentHistory ? calculateOffenseCountForType(studentHistory, offenseId) : 0;

    // Check if we have a custom sanction for this student+offense
    const customSanction = additionalOffenses.find(
        offense => offense.studentId == studentId && offense.offense_id == offenseId
    );

    if (customSanction) {
        return {
            sanction: customSanction.sanction_text,
            description: "Custom sanction applied",
            isCustom: true,
            previous: customSanction.previous_sanction || 'None',
            count: offenseCount,
            next: getNextSanction(customSanction.sanction_text),
            stage: getSanctionStage(customSanction.sanction_text)
        };
    }

    // Try to get from database
    const dbSanction = await fetchRecommendedSanctionFromDB(studentId, offenseId, offenseCount);

    if (dbSanction) {
        return {
            sanction: dbSanction.sanction,
            description: dbSanction.description,
            isCustom: false,
            previous: getPreviousSanctionForOffense(studentHistory, offenseId),
            count: offenseCount,
            next: dbSanction.next_sanction,
            stage: dbSanction.stage,
            is_max_stage: dbSanction.is_max_stage
        };
    }

    // Fallback: Use offense count to determine sanction stage
    // This should match your database logic
    let stage = Math.min(offenseCount, SANCTION_PROGRESSION.length - 1);
    const fallbackSanction = SANCTION_PROGRESSION[stage];

    return {
        sanction: fallbackSanction,
        description: getSanctionDescription(fallbackSanction),
        isCustom: false,
        previous: getPreviousSanctionForOffense(studentHistory, offenseId),
        count: offenseCount,
        next: getNextSanction(fallbackSanction),
        stage: stage
    };
}

async function createOffensesAndSanctionsReviewSection() {
    const selectedOffensesData = getSelectedOffensesData();

    if (selectedOffensesData.length === 0) {
        return '<div class="alert alert-warning mb-4"><i class="fas fa-exclamation-triangle me-2"></i>No offenses selected</div>';
    }

    let html = `
        <div class="violation-pair mb-4">
            <h6 class="mb-3"><i class="fas fa-gavel me-2"></i>Recommended Sanctions</h6>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Sanctions are recommended based on each student's specific offense history following the school's 7-stage disciplinary policy.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover review-table">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Student</th>
                            <th width="20%">Offense</th>
                            <th width="10%">Count</th>
                            <th width="15%">Previous</th>
                            <th width="20%">Recommended Sanction</th>
                            <th width="10%">Stage</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

    let rowCount = 1;

    // For each student
    for (const pair of violationPairs) {
        const studentId = pair.violator_id;
        const studentName = pair.violator_name.split(' - ')[0];
        const studentHistory = studentOffenseHistory[studentId];

        // For each selected offense
        for (const offense of selectedOffensesData) {
            const offenseId = offense.id;
            const offenseText = offense.type;

            // Get recommended sanction
            const recommendedSanction = await getRecommendedSanction(studentId, offenseId);

            // Get offense count for this specific type
            const specificCount = calculateOffenseCountForType(studentHistory, offenseId);

            // Determine if this is the maximum stage
            const isMaxStage = recommendedSanction.stage >= SANCTION_PROGRESSION.length - 1;

            html += `
                <tr>
                    <td><strong>${rowCount}</strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-medium">${studentName}</div>
                                <small class="text-muted">${getStudentGradeInfo(studentId)}</small>
                            </div>
                        </div>
                    </td>
                    <td>${offenseText}</td>
                    <td class="text-center">
                        <span class="badge ${getBadgeClass(specificCount)}">
                            ${specificCount}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">${recommendedSanction.previous}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="badge ${getSanctionColor(recommendedSanction.sanction)} me-2">
                                ${recommendedSanction.sanction}
                            </span>
                            ${recommendedSanction.isCustom ?
                                '<i class="fas fa-star text-warning" title="Custom Sanction"></i>' :
                                ''}
                            ${isMaxStage ?
                                '<i class="fas fa-exclamation-triangle text-danger ms-2" title="Maximum sanction stage reached"></i>' :
                                ''}
                        </div>
                        <small class="text-muted d-block mt-1">
                            ${recommendedSanction.description.substring(0, 60)}...
                        </small>
                    </td>
                    <td class="text-center">
                        <span class="badge ${isMaxStage ? 'bg-danger' : 'bg-info'}">
                            ${recommendedSanction.stage + 1}
                        </span>
                    </td>
                </tr>
            `;
            rowCount++;
        }
    }

    // Add additional offenses
    if (additionalOffenses.length > 0) {
        html += `
            <tr class="table-warning">
                <td colspan="7" class="fw-bold bg-warning-subtle">
                    <i class="fas fa-plus-circle me-2"></i>Additional Offenses (Custom Sanctions)
                </td>
            </tr>
        `;

        for (const offense of additionalOffenses) {
            const student = allStudents.find(s => s.student_id == offense.studentId);
            const studentName = student ?
                `${student.student_fname} ${student.student_lname}` :
                offense.studentName;

            const specificCount = calculateOffenseCountForType(
                studentOffenseHistory[offense.studentId],
                offense.offense_id
            );

            html += `
                <tr class="table-warning">
                    <td><strong>${rowCount}</strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-medium">${studentName}</div>
                                <small class="text-muted">${getStudentGradeInfo(offense.studentId)}</small>
                            </div>
                            <span class="badge bg-warning ms-2">Custom</span>
                        </div>
                    </td>
                    <td>${offense.offense_text}</td>
                    <td class="text-center">
                        <span class="badge bg-info">${specificCount}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">${offense.previous_sanction || 'None'}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning me-2">
                                ${offense.sanction_text}
                            </span>
                            <i class="fas fa-star text-warning" title="Custom Sanction"></i>
                        </div>
                        <small class="text-muted d-block mt-1">Manually assigned sanction</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-dark">Custom</span>
                    </td>
                </tr>
            `;
            rowCount++;
        }
    }

    html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;

    return html;
}
async function createSanctionProgressionChart() {
    const selectedOffensesData = getSelectedOffensesData();
    if (selectedOffensesData.length === 0 || violationPairs.length === 0) return '';

    // Get sample student and offense
    const sampleStudentId = violationPairs[0].violator_id;
    const sampleStudentName = violationPairs[0].violator_name.split(' - ')[0];
    const sampleOffenseId = selectedOffensesData[0].id;
    const sampleOffenseName = selectedOffensesData[0].type;

    const studentHistory = studentOffenseHistory[sampleStudentId];
    const specificCount = studentHistory ? calculateOffenseCountForType(studentHistory, sampleOffenseId) : 0;
    const currentStage = Math.min(specificCount, SANCTION_PROGRESSION.length - 1);

    let html = `
        <div class="violation-pair mb-4">
            <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Sanction Progression System</h6>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Tagoloan Senior High School follows a 7-stage disciplinary progression system.
                <br><small>Example progression for <strong>${sampleStudentName}</strong> on offense: <strong>${sampleOffenseName}</strong></small>
            </div>

            <div class="progression-timeline">
    `;

    // Create all 7 stages
    for (let i = 0; i < SANCTION_PROGRESSION.length; i++) {
        const sanction = SANCTION_PROGRESSION[i];
        const isCurrent = i === currentStage;
        const isPast = i < currentStage;
        const isFuture = i > currentStage;

        html += `
            <div class="progression-step ${isCurrent ? 'current' : ''} ${isPast ? 'past' : ''} ${isFuture ? 'future' : ''}">
                <div class="step-circle ${getSanctionColor(sanction)}">
                    ${i + 1}
                </div>
                <div class="step-content">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">${sanction}</h6>
                        ${isCurrent ? '<span class="badge bg-primary">Current</span>' : ''}
                        ${isPast ? '<span class="badge bg-success"><i class="fas fa-check"></i></span>' : ''}
                    </div>
                    <p class="mb-1 text-muted small">${getSanctionDescription(sanction).substring(0, 80)}...</p>
                    <div class="step-meta">
                        <small class="text-muted">
                            <i class="fas fa-hashtag me-1"></i>Stage ${i + 1}
                            ${i === currentStage ?
                                ` • <i class="fas fa-user me-1"></i>Student at this stage` :
                                ''}
                            ${i === SANCTION_PROGRESSION.length - 1 ?
                                ' • <i class="fas fa-exclamation-triangle me-1"></i>Maximum sanction' :
                                ''}
                        </small>
                    </div>
                </div>
            </div>
        `;
    }

    html += `
            </div>

            <!-- Current Status Summary -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Current Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Previous Offenses (this type):</strong>
                                <div class="mt-1">
                                    <span class="badge ${getBadgeClass(specificCount)}">
                                        ${specificCount} offense(s)
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Current Sanction Stage:</strong>
                                <div class="mt-1">
                                    <span class="badge bg-info">
                                        Stage ${currentStage + 1} of ${SANCTION_PROGRESSION.length}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Recommended Sanction:</strong>
                                <div class="mt-1">
                                    <span class="badge ${getSanctionColor(SANCTION_PROGRESSION[currentStage])}">
                                        ${SANCTION_PROGRESSION[currentStage]}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Next Steps</h6>
                        </div>
                        <div class="card-body">
                            ${currentStage < SANCTION_PROGRESSION.length - 1 ? `
                                <div class="mb-3">
                                    <strong>Next Sanction if Repeated:</strong>
                                    <div class="mt-1">
                                        <span class="badge ${getSanctionColor(SANCTION_PROGRESSION[currentStage + 1])}">
                                            ${SANCTION_PROGRESSION[currentStage + 1]}
                                        </span>
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    If this offense is repeated, the student will progress to Stage ${currentStage + 2}
                                </div>
                            ` : `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Maximum sanction stage reached!</strong>
                                    <br>Further violations may result in permanent expulsion.
                                </div>
                            `}
                            <div class="small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Each offense type maintains its own progression counter
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="alert alert-light mt-3">
                <h6 class="mb-2"><i class="fas fa-key me-2"></i>Stage Legend</h6>
                <div class="row">
                    ${SANCTION_PROGRESSION.map((sanction, index) => `
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge ${getSanctionColor(sanction)} me-2">${index + 1}</span>
                                <small>${sanction}</small>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>

        <style>
            .progression-timeline {
                position: relative;
                padding-left: 30px;
            }
            .progression-timeline::before {
                content: '';
                position: absolute;
                left: 15px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(to bottom, #28a745, #dc3545);
            }
            .progression-step {
                position: relative;
                margin-bottom: 25px;
                padding-left: 20px;
            }
            .progression-step:last-child {
                margin-bottom: 0;
            }
            .progression-step.current .step-circle {
                transform: scale(1.2);
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.3);
                border: 2px solid white;
            }
            .progression-step.past::before {
                background: #28a745;
            }
            .step-circle {
                position: absolute;
                left: -25px;
                top: 0;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 0.9rem;
                z-index: 1;
                transition: all 0.3s ease;
            }
            .step-content {
                background: white;
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .progression-step.current .step-content {
                border-color: #007bff;
                background: rgba(13, 110, 253, 0.05);
            }
            .progression-step.past .step-content {
                border-color: #28a745;
                background: rgba(40, 167, 69, 0.05);
            }
            .step-meta {
                padding-top: 8px;
                border-top: 1px dashed #dee2e6;
                margin-top: 8px;
            }
            .bg-orange {
                background-color: #fd7e14 !important;
            }
        </style>
    `;

    return html;
}
async function createSanctionSummaryCards() {
    const selectedOffensesData = getSelectedOffensesData();
    if (selectedOffensesData.length === 0) return '';

    // Count sanctions by type
    const sanctionCounts = {};
    const studentSanctionMap = {};

    for (const pair of violationPairs) {
        const studentId = pair.violator_id;
        const studentName = pair.violator_name;

        for (const offense of selectedOffensesData) {
            const offenseId = offense.id;
            const recommendedSanction = await getRecommendedSanction(studentId, offenseId);
            const sanctionType = recommendedSanction.sanction;

            if (!sanctionCounts[sanctionType]) {
                sanctionCounts[sanctionType] = {
                    count: 0,
                    students: [],
                    offenses: new Set(),
                    stage: recommendedSanction.stage
                };
            }

            sanctionCounts[sanctionType].count++;
            if (!sanctionCounts[sanctionType].students.includes(studentName)) {
                sanctionCounts[sanctionType].students.push(studentName);
            }
            sanctionCounts[sanctionType].offenses.add(offense.type);

            // Track student-sanction mapping
            if (!studentSanctionMap[studentId]) {
                studentSanctionMap[studentId] = new Set();
            }
            studentSanctionMap[studentId].add(sanctionType);
        }
    }

    // Add additional offenses
    additionalOffenses.forEach(offense => {
        const sanctionType = offense.sanction_text;

        if (!sanctionCounts[sanctionType]) {
            sanctionCounts[sanctionType] = {
                count: 0,
                students: [],
                offenses: new Set(),
                stage: getSanctionStage(sanctionType),
                isCustom: true
            };
        }

        sanctionCounts[sanctionType].count++;
        if (!sanctionCounts[sanctionType].students.includes(offense.studentName)) {
            sanctionCounts[sanctionType].students.push(offense.studentName);
        }
        sanctionCounts[sanctionType].offenses.add(offense.offense_text);
        sanctionCounts[sanctionType].isCustom = true;
    });

    // Sort sanctions by stage
    const sortedSanctions = Object.entries(sanctionCounts)
        .sort(([, a], [, b]) => a.stage - b.stage);

    let html = `
        <div class="violation-pair mb-4">
            <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Sanction Distribution</h6>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Distribution of recommended sanctions across all students and offenses.
            </div>

            <div class="row">
    `;

    // Create cards for each sanction type
    sortedSanctions.forEach(([sanctionType, data], index) => {
        const cardColor = data.isCustom ? 'warning' : getSanctionColor(sanctionType).replace('bg-', '');
        const stageNumber = data.stage + 1;

        html += `
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-${cardColor}">
                    <div class="card-header bg-${cardColor} text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Stage ${stageNumber}: ${sanctionType}</h6>
                            ${data.isCustom ?
                                '<span class="badge bg-light text-dark"><i class="fas fa-star me-1"></i>Custom</span>' :
                                ''}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h1 class="display-5 text-${cardColor}">${data.count}</h1>
                            <small class="text-muted">Total Applications</small>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-users me-1"></i>Affected Students (${data.students.length}):
                            </small>
                            <div class="d-flex flex-wrap gap-1">
                                ${data.students.slice(0, 3).map(student =>
                                    `<span class="badge bg-light text-dark">${student.split(' - ')[0]}</span>`
                                ).join('')}
                                ${data.students.length > 3 ?
                                    `<span class="badge bg-secondary">+${data.students.length - 3} more</span>` :
                                    ''}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-gavel me-1"></i>Offense Types:
                            </small>
                            <div class="d-flex flex-wrap gap-1">
                                ${Array.from(data.offenses).slice(0, 3).map(offense =>
                                    `<span class="badge bg-light text-dark small">${offense}</span>`
                                ).join('')}
                                ${data.offenses.size > 3 ?
                                    `<span class="badge bg-secondary">+${data.offenses.size - 3} more</span>` :
                                    ''}
                            </div>
                        </div>

                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-${cardColor}"
                                 style="width: ${(data.count / (selectedOffensesData.length * violationPairs.length + additionalOffenses.length)) * 100}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Close row and start new one every 3 cards
        if ((index + 1) % 3 === 0) {
            html += `</div><div class="row">`;
        }
    });

    html += `
            </div>

            <!-- Statistics Summary -->
            <div class="alert alert-light mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h4 class="mb-0">${selectedOffensesData.length * violationPairs.length + additionalOffenses.length}</h4>
                            <small class="text-muted">Total Sanction Applications</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h4 class="mb-0">${Object.keys(sanctionCounts).length}</h4>
                            <small class="text-muted">Different Sanction Types</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h4 class="mb-0">${additionalOffenses.length}</h4>
                            <small class="text-muted">Custom Sanctions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    return html;
}

function calculateOffenseCountForType(studentHistory, offenseId) {
    if (!studentHistory || !studentHistory.records) return 0;

    return studentHistory.records.filter(record =>
        record.offense_id == offenseId
    ).length;
}

// ==========================================
// HELPER: GET STUDENT GRADE INFO
// ==========================================

function getStudentGradeInfo(studentId) {
    const student = allStudents.find(s => s.student_id == studentId);
    if (!student) return 'Grade: N/A';

    const gradeLevel = student.adviser?.adviser_gradelevel || 'N/A';
    const section = student.adviser?.adviser_section || '';
    return `Grade ${gradeLevel} ${section}`.trim();
}

// ==========================================
// UPDATED: GET PREVIOUS SANCTION
// ==========================================

function getPreviousSanctionForOffense(studentHistory, offenseId) {
    if (!studentHistory || !studentHistory.records) return 'None';

    const offenseRecords = studentHistory.records
        .filter(record => record.offense_id == offenseId)
        .sort((a, b) => {
            const dateA = new Date(a.date + ' ' + a.time);
            const dateB = new Date(b.date + ' ' + b.time);
            return dateB - dateA; // Most recent first
        });

    return offenseRecords.length > 0 ? (offenseRecords[0].sanction || 'None') : 'None';
}
        async function createOffensesReviewSection() {
    const selectedOffensesData = getSelectedOffensesData();
    if (selectedOffensesData.length === 0) return '<p class="text-danger">No offenses selected</p>';

    let html = `
        <h6 class="mb-3"><i class="fas fa-gavel me-2"></i>Selected Offenses</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm review-table">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Student</th>
                        <th width="25%">Offense</th>
                        <th width="20%">Previous Sanction</th>
                        <th width="25%">Recommended Sanction</th>
                    </tr>
                </thead>
                <tbody>
    `;

    let rowCount = 1;

    // Loop through each student and each offense
    for (const pair of violationPairs) {
        const studentId = pair.violator_id;
        const studentName = pair.violator_name;

        for (const offense of selectedOffensesData) {
            const offenseId = offense.id;
            const offenseText = offense.type;

            // Get specific history for this offense
            const studentHistory = await getStudentOffenseHistoryForOffense(studentId, offenseId);
            const previousSanction = studentHistory ? getPreviousSanctionForOffense(studentHistory, offenseId) : 'None';
            const recommendedSanction = await getRecommendedSanction(studentId, offenseId);

            html += `
                <tr>
                    <td><strong>${rowCount}</strong></td>
                    <td>${studentName}</td>
                    <td>${offenseText}</td>
                    <td>${previousSanction}</td>
                    <td>${recommendedSanction}</td>
                </tr>
            `;
            rowCount++;
        }
    }

    // Add additional offenses
    for (const offense of additionalOffenses) {
        html += `
            <tr class="table-info">
                <td><strong>${rowCount}</strong></td>
                <td>${offense.studentName}</td>
                <td><small class="text-info">[Additional]</small> ${offense.offense_text}</td>
                <td>${offense.previous_sanction || 'None'}</td>
                <td>${offense.recommended_sanction || offense.sanction_text}</td>
            </tr>
        `;
        rowCount++;
    }

    html += `
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-2">
            <i class="fas fa-info-circle me-1"></i>
            Each offense has its own sanction progression based on the student's specific history with that offense type.
        </div>
    `;

    return html;
}

     async function createDefaultOffenseEntry(studentId, student, uniqueCount) {
    const mainOffenseSelect = document.getElementById('offense_id');
    const selectedOffenseName = mainOffenseSelect.value ? mainOffenseSelect.options[mainOffenseSelect.selectedIndex].text : 'None';
    const offenseId = mainOffenseSelect.value;

    // Get current sanction from database
    const currentSanction = await getCurrentSanction(studentId, offenseId);

    return `
        <div class="offense-entry mb-3 p-3 border rounded bg-light">
            <div class="small text-muted mb-1"><strong class="text-primary">Selected Violation:</strong> ${selectedOffenseName}</div>
            <div class="small text-muted mb-1"><strong>Previous Sanction:</strong> None</div>
            <div class="small text-muted">
                <strong class="text-success">Current Sanction:</strong> ${currentSanction}
            </div>
        </div>
    `;
}

        function createRecentOffenses(count, records) {
            if (count === 0) return '<span class="text-success d-block mt-3">No previous violations found</span>';

            const uniqueRecords = getUniqueOffenses(records);
            const displayCount = Math.min(uniqueRecords.length, 3);

            return `
                <div class="small text-muted mt-3">
                    <strong>Recent unique violations:</strong>
                    <ul class="mb-0 mt-1">
                        ${uniqueRecords.slice(0, displayCount).map(record => `<li>${record.date} - ${record.offense_type}</li>`).join('')}
                        ${count > displayCount ? `<li>... and ${count - displayCount} more unique violation(s)</li>` : ''}
                    </ul>
                </div>
            `;
        }

        function updatePrimaryOffenseDisplay() {
            console.log('🔄 Updating primary offense display...');
            updateOffenseHistoryDisplay();
        }

        async function updateOffenseHistoryDisplay() {
            const historyContent = document.getElementById('offenseHistoryContent');
            const violatorIds = [...new Set(violationPairs.map(pair => pair.violator_id))];

            if (violatorIds.length === 0) {
                historyContent.innerHTML = '';
                return;
            }

            historyContent.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading student violation history...</div>';
            document.getElementById('historyAndCustomSection').style.display = 'block';

            // Clear any existing content
            historyContent.innerHTML = '';

            // Create container for the 3-column grid
            const gridContainer = document.createElement('div');
            gridContainer.className = 'offense-history-three-col';
            historyContent.appendChild(gridContainer);

            // Track which students we've already processed to avoid duplicates
            const processedStudents = new Set();

            // Process each unique student only once
            const studentDivs = [];

            for (const violatorId of violatorIds) {
                // Skip if we've already processed this student
                if (processedStudents.has(violatorId)) {
                    continue;
                }

                const student = allStudents.find(s => s.student_id == violatorId);
                if (!student) continue;

                // Mark this student as processed
                processedStudents.add(violatorId);

                const studentHistory = studentOffenseHistory[violatorId];
                const count = studentHistory?.count || 0;
                const records = studentHistory?.records || [];

                const studentDiv = await createStudentHistoryDiv(student, violatorId, count, records);
                studentDivs.push(studentDiv);
            }

            // Append all student divs directly to the grid container
            studentDivs.forEach(div => {
                gridContainer.appendChild(div);
            });

            // Handle different numbers of students
            if (studentDivs.length <= 2) {
                gridContainer.style.gridTemplateColumns = `repeat(${studentDivs.length}, 1fr)`;
            }
        }

        // ==========================================
        // ADDITIONAL OFFENSE MODAL FUNCTIONS
        // ==========================================

        function openAdditionalOffenseModal(studentId, studentName, offenseCount) {
            currentAdditionalOffenseStudent = {
                id: studentId,
                name: studentName,
                offenseCount: offenseCount
            };
            document.getElementById('additionalOffenseStudentName').textContent = studentName;

            // Removed previous offense count display
            document.getElementById('additional_offense_select').value = '';
            document.getElementById('additional_sanction_display').innerHTML = `
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Select an offense type to see the recommended sanction
                </div>
            `;
            document.getElementById('additional_sanction_id').value = '';

            const modal = new bootstrap.Modal(document.getElementById('additionalOffenseModal'));
            modal.show();
        }

        async function loadAdditionalOffenseSanctions() {
            const offenseId = document.getElementById('additional_offense_select').value;
            const sanctionDisplay = document.getElementById('additional_sanction_display');
            const hiddenSanctionInput = document.getElementById('additional_sanction_id');

            if (!offenseId) {
                sanctionDisplay.innerHTML = `
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Select an offense type to see the recommended sanction
                    </div>
                `;
                hiddenSanctionInput.value = '';
                return;
            }

            sanctionDisplay.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading sanction...</div>';

            try {
                // Get student's offense count for this specific offense type
                const studentHistory = studentOffenseHistory[currentAdditionalOffenseStudent.id];
                const offenseCount = studentHistory ? getOffenseSpecificCount(studentHistory, offenseId) : 0;

                // Fetch recommended sanction based on offense count
                const response = await fetch('/prefect/violations/get-sanction-stages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        offense_id: offenseId,
                        offense_count: offenseCount
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.recommended_sanction) {
                        sanctionDisplay.innerHTML = `
                            <div class="sanction-display-content">
                                <h6 class="mb-2">${data.recommended_sanction}</h6>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Previous sanction on this offense:</span>
                                        <span class="badge bg-info">${getPreviousSanctionForOffense(studentHistory, offenseId)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span>Previous occurrences:</span>
                                        <span class="badge ${getBadgeClass(offenseCount)}">${offenseCount} time(s)</span>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Store the sanction ID if available (for form submission)
                        if (data.sanction_id) {
                            hiddenSanctionInput.value = data.sanction_id;
                        }
                    } else {
                        // Fallback to default
                        sanctionDisplay.innerHTML = `
                            <div class="sanction-display-content">
                                <h6 class="mb-2">Verbal Warning</h6>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Previous sanction on this offense:</span>
                                        <span class="badge bg-info">None</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span>Previous occurrences:</span>
                                        <span class="badge ${getBadgeClass(offenseCount)}">${offenseCount} time(s)</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        hiddenSanctionInput.value = '';
                    }
                } else {
                    throw new Error(`Server returned ${response.status}`);
                }
            } catch (error) {
                console.error('Error loading sanction:', error);
                sanctionDisplay.innerHTML = `
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Error loading sanction. Please try again.
                    </div>
                `;
                hiddenSanctionInput.value = '';
            }
        }

        async function confirmAdditionalOffense() {
            const offenseId = document.getElementById('additional_offense_select').value;
            const sanctionId = document.getElementById('additional_sanction_id').value;

            if (!offenseId) {
                alert('Please select an offense type.');
                return;
            }

            const offenseText = document.getElementById('additional_offense_select').selectedOptions[0].text;

            // Get the displayed sanction text
            const sanctionDisplay = document.getElementById('additional_sanction_display');
            const sanctionText = sanctionDisplay.querySelector('h6') ? sanctionDisplay.querySelector('h6').textContent :
                'Verbal Warning';

            // Get previous sanction for this offense type
            const studentHistory = studentOffenseHistory[currentAdditionalOffenseStudent.id];
            const previousSanction = studentHistory ? getPreviousSanctionForOffense(studentHistory, offenseId) : 'None';

            // Get recommended sanction for this additional offense
            const recommendedSanction = await getRecommendedSanction(currentAdditionalOffenseStudent.id, offenseId);

            // Add to additional offenses array
            additionalOffenses.push({
                studentId: currentAdditionalOffenseStudent.id,
                studentName: currentAdditionalOffenseStudent.name,
                offense_id: offenseId,
                sanction_id: sanctionId,
                offense_text: offenseText,
                sanction_text: sanctionText,
                previous_sanction: previousSanction,
                recommended_sanction: recommendedSanction
            });

            // Update the display
            updateOffenseHistoryDisplay();

            const modal = bootstrap.Modal.getInstance(document.getElementById('additionalOffenseModal'));
            modal.hide();

            showAlert(`Additional offense added for ${currentAdditionalOffenseStudent.name}`, 'success');
        }

        // ==========================================
        // VIOLATION PAIR MANAGEMENT
        // ==========================================

        function getAllOffensesAndSanctions() {
            const offenses = [];

            // Add all selected offenses (multiple if selected)
            selectedOffenses.forEach(offense => {
                offenses.push({
                    offense_id: offense.id,
                    is_main: true
                });
            });

            // Add additional offenses for the current student if any
            if (selectedIndividualViolator) {
                additionalOffenses.forEach(offense => {
                    if (offense.studentId == selectedIndividualViolator) {
                        offenses.push({
                            offense_id: offense.offense_id,
                            sanction_id: offense.sanction_id,
                            is_additional: true,
                            studentName: offense.studentName
                        });
                    }
                });
            }

            return offenses;
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

        // ==========================================
        // LOAD OFFENSE HISTORY
        // ==========================================

        async function loadOffenseHistory() {
            // Get ALL selected offense IDs
            const selectedOffenseIds = selectedOffenses.map(offense => offense.id);

            if (selectedOffenseIds.length === 0) {
                document.getElementById('historyAndCustomSection').style.display = 'none';
                return;
            }

            const violatorIds = [...new Set(violationPairs.map(pair => pair.violator_id))];
            if (violatorIds.length === 0) {
                document.getElementById('historyAndCustomSection').style.display = 'none';
                return;
            }

            // Check if we're already loading
            if (window.isLoadingHistory) {
                return;
            }

            window.isLoadingHistory = true;

            const historyContent = document.getElementById('offenseHistoryContent');
            historyContent.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading violator violation history...</div>';
            document.getElementById('historyAndCustomSection').style.display = 'block';

            try {
                // Only reset if we have new data to load
                studentOffenseHistory = {};

                // Load history for ALL selected offenses
                for (const offenseId of selectedOffenseIds) {
                    const response = await fetch('/prefect/violations/get-offense-history', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            student_ids: violatorIds,
                            offense_id: offenseId
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        Object.keys(data).forEach(studentId => {
                            if (!studentOffenseHistory[studentId]) {
                                studentOffenseHistory[studentId] = {
                                    count: 0,
                                    records: []
                                };
                            }

                            const offenseData = data[studentId];
                            const uniqueRecords = getUniqueOffenses(offenseData.records || []);

                            // Only add unique records that aren't already there
                            const existingRecords = studentOffenseHistory[studentId].records;
                            const newRecords = uniqueRecords.filter(newRecord =>
                                !existingRecords.some(existingRecord =>
                                    existingRecord.date === newRecord.date &&
                                    existingRecord.time === newRecord.time &&
                                    existingRecord.offense_id === newRecord.offense_id
                                )
                            );

                            studentOffenseHistory[studentId].records = [
                                ...existingRecords,
                                ...newRecords
                            ];

                            // Update count based on unique records
                            studentOffenseHistory[studentId].count = getUniqueOffenses(studentOffenseHistory[
                                studentId].records).length;
                        });
                    }
                }

                console.log('📊 Combined UNIQUE violation history data received:', studentOffenseHistory);
                await updateOffenseHistoryDisplay();

            } catch (error) {
                console.error('❌ Error loading violation history:', error);
                const historyContent = document.getElementById('offenseHistoryContent');
                historyContent.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Violation history is temporarily unavailable.
                        <br><small>You can still proceed with creating the violation.</small>
                    </div>
                `;
                document.getElementById('historyAndCustomSection').style.display = 'block';
            } finally {
                window.isLoadingHistory = false;
            }
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
            fileInputs.forEach(input => {
                if (input.files.length > 0) allFiles.push(input.files[0]);
            });
            return allFiles;
        }

      // ==========================================
// REVIEW MODAL FUNCTIONS - UPDATED VERSION
// ==========================================

async function openReviewModal() {
    const date = document.getElementById('violation_date').value;
    const time = document.getElementById('violation_time').value;
    const incident = document.getElementById('violation_incident').value;

    if (!date || !time || !incident || selectedOffenses.length === 0) {
        alert('Please fill in all required details and select at least one offense.');
        return;
    }

    // Check if there are any violation pairs
    if (violationPairs.length === 0) {
        alert('No violation entries to review. Please add violators first.');
        return;
    }

    // Hide the current offense modal
    const offenseModalEl = document.getElementById('offenseModal');
    const offenseModal = bootstrap.Modal.getInstance(offenseModalEl);
    if (offenseModal) {
        offenseModal.hide();
    }

    // Wait for modal to hide
    setTimeout(async () => {
        // Update the review modal content
        await updateReviewModalContent();

        // Show the review modal
        const reviewModalEl = document.getElementById('reviewModal');
        const reviewModal = new bootstrap.Modal(reviewModalEl);
        reviewModal.show();
    }, 300);
}

      // Update the backToOffenseModal function
function backToOffenseModal() {
    const reviewModalEl = document.getElementById('reviewModal');
    const reviewModal = bootstrap.Modal.getInstance(reviewModalEl);
    if (reviewModal) {
        reviewModal.hide();
    }

    // Wait for modal to hide
    setTimeout(() => {
        const offenseModal = new bootstrap.Modal(document.getElementById('offenseModal'));
        offenseModal.show();
    }, 300);
}


        async function updateReviewModalContent() {
    const reviewList = document.getElementById('reviewViolationsList');
    reviewList.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading review data...</div>';

    try {
        // Update statistics
        updateReviewStatistics();

        // Build the review content
        let reviewHTML = '';

        // Violation type header
        reviewHTML += `
            <div class="alert ${currentViolationType === 'individual' ? 'alert-success' : 'alert-warning'} mb-4">
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
            </div>
        `;

        // Offenses Section
        const offensesSectionHTML = await createOffensesReviewSection();
        reviewHTML += `<div class="violation-pair mb-4">${offensesSectionHTML}</div>`;

        // Shared Details Section
        const sharedDetailsHTML = createSharedDetailsReviewSection();
        reviewHTML += `<div class="violation-pair mb-4">${sharedDetailsHTML}</div>`;

        // Individual Violation Pairs Section
        const pairsSectionHTML = createViolationPairsReviewSection();
        reviewHTML += `<div class="violation-pair mb-4">${pairsSectionHTML}</div>`;

        // Additional Offenses Section (if any)
        if (additionalOffenses.length > 0) {
            const additionalSectionHTML = createAdditionalOffensesReviewSection();
            reviewHTML += `<div class="violation-pair mb-4">${additionalSectionHTML}</div>`;
        }

        reviewList.innerHTML = reviewHTML;

    } catch (error) {
        console.error('Error updating review modal:', error);
        reviewList.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading review data. Please try again.
            </div>
        `;
    }
}


        function updateReviewStatistics() {
    const selectedOffensesData = getSelectedOffensesData();
    const totalMainOffenses = selectedOffensesData.length * violationPairs.length;
    const totalOffenses = totalMainOffenses + additionalOffenses.length;

    document.getElementById('reviewViolationPairs').textContent = violationPairs.length;
    document.getElementById('reviewTotalOffenses').textContent = totalOffenses;
    document.getElementById('reviewCustomSanctions').textContent = additionalOffenses.length; // Changed this
    document.getElementById('reviewAdditionalOffenses').textContent = additionalOffenses.length;
}

       // Helper function to get previous sanction for an offense
function getPreviousSanctionForOffense(studentHistory, offenseId) {
    if (!studentHistory?.records) return 'None';

    // Filter records for this specific offense
    const offenseRecords = studentHistory.records.filter(record =>
        record.offense_id == offenseId
    );

    if (offenseRecords.length === 0) return 'None';

    // Sort by date (most recent first)
    const sortedRecords = [...offenseRecords].sort((a, b) => {
        const dateA = new Date(a.date + ' ' + a.time);
        const dateB = new Date(b.date + ' ' + b.time);
        return dateB - dateA;
    });

    // Return the sanction from the most recent record
    return sortedRecords[0].sanction || 'None';
}
        // Update the character count function
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedOffensesDisplay();
            initializeStudentSearch();
            initializeIndividualSearch();
            document.getElementById('violation_date').max = new Date().toISOString().split('T')[0];
            document.getElementById('violation_incident').addEventListener('input', function() {
                document.getElementById('charCount').textContent = this.value.length;
            });

            // Initialize offense history display
            if (violationPairs.length > 0) {
                updateOffenseHistoryDisplay();
            }
        });

        function createAdditionalOffensesReviewSection() {
            let html = `
                <h6 class="mb-3"><i class="fas fa-plus-circle me-2"></i>Additional Offenses</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">#</th>
                                <th width="30%">Student</th>
                                <th width="30%">Offense</th>
                                <th width="30%">Sanction</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            additionalOffenses.forEach((offense, index) => {
                html += `
                    <tr>
                        <td><strong>${index + 1}</strong></td>
                        <td>${offense.studentName}</td>
                        <td>${offense.offense_text}</td>
                        <td>${offense.sanction_text}</td>
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

       // Updated function to create shared details review section
function createSharedDetailsReviewSection() {
    const date = document.getElementById('violation_date').value;
    const time = document.getElementById('violation_time').value;
    const incident = document.getElementById('violation_incident').value;
    const complainant = document.getElementById('complainant').value;
    const witnesses = document.getElementById('witnesses').value;
    const evidenceDescription = document.getElementById('evidence_description').value;
    const evidenceFiles = getAllEvidenceFiles();

    // Format date for display
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    let html = `
        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Incident Details</h6>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong><i class="fas fa-calendar me-2"></i>Date:</strong>
                    <div class="mt-1 p-2 bg-light rounded">${formattedDate}</div>
                </div>
                <div class="mb-3">
                    <strong><i class="fas fa-clock me-2"></i>Time:</strong>
                    <div class="mt-1 p-2 bg-light rounded">${time}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <strong><i class="fas fa-gavel me-2"></i>Selected Offenses:</strong>
                    <div class="mt-1">
    `;

    selectedOffenses.forEach((offense, index) => {
        html += `<span class="badge bg-primary me-1 mb-1">${offense.type}</span>`;
    });

    html += `
                    </div>
                </div>
                <div class="mb-3">
                    <strong><i class="fas fa-files me-2"></i>Evidence Files:</strong>
                    <div class="mt-1 p-2 bg-light rounded">
                        ${evidenceFiles.length > 0 ? `${evidenceFiles.length} file(s) attached` : 'No files attached'}
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <strong><i class="fas fa-file-alt me-2"></i>Incident Description:</strong>
            <div class="mt-2 p-3 bg-light rounded" style="min-height: 100px;">
                ${incident || '<span class="text-muted">No description provided</span>'}
            </div>
        </div>
    `;

    // Additional information sections
    if (complainant || witnesses || evidenceDescription) {
        html += `<div class="row">`;

        if (complainant) {
            html += `
                <div class="col-md-4 mb-3">
                    <strong><i class="fas fa-user-tag me-2"></i>Complainant(s):</strong>
                    <div class="mt-1 p-2 bg-light rounded small">
                        ${complainant.split('\n').map(line => `<div>${line}</div>`).join('')}
                    </div>
                </div>
            `;
        }

        if (witnesses) {
            html += `
                <div class="col-md-4 mb-3">
                    <strong><i class="fas fa-users me-2"></i>Witnesses:</strong>
                    <div class="mt-1 p-2 bg-light rounded small">
                        ${witnesses.split('\n').map(line => `<div>${line}</div>`).join('')}
                    </div>
                </div>
            `;
        }

        if (evidenceDescription) {
            html += `
                <div class="col-md-4 mb-3">
                    <strong><i class="fas fa-clipboard-check me-2"></i>Evidence Notes:</strong>
                    <div class="mt-1 p-2 bg-light rounded small">
                        ${evidenceDescription}
                    </div>
                </div>
            `;
        }

        html += `</div>`;
    }

    return html;
}

// Add a new helper function to ensure the review modal is properly initialized
function initializeReviewModal() {
    // Clear any existing modal event listeners
    const reviewModalEl = document.getElementById('reviewModal');

    // Remove existing event listeners by cloning and replacing
    const newModalEl = reviewModalEl.cloneNode(true);
    reviewModalEl.parentNode.replaceChild(newModalEl, reviewModalEl);

    // Reinitialize the modal
    const reviewModal = new bootstrap.Modal(newModalEl, {
        backdrop: 'static',
        keyboard: false
    });

    return reviewModal;
}


// Define sanction progression order based on your database
const SANCTION_PROGRESSION = [
    "Verbal Warning",            // Stage 1
    "Detention",                 // Stage 2
    "Parent/Guardian Notification", // Stage 3
    "Restorative Actions",       // Stage 4
    "Counseling and Intervention", // Stage 5
    "Suspension",                // Stage 6
    "Expulsion"                  // Stage 7
];

function getSanctionStage(sanctionName) {
    return SANCTION_PROGRESSION.findIndex(s => s === sanctionName);
}
// Get sanction by stage number
function getSanctionByStage(stage) {
    if (stage < 0) return SANCTION_PROGRESSION[0];
    if (stage >= SANCTION_PROGRESSION.length) return SANCTION_PROGRESSION[SANCTION_PROGRESSION.length - 1];
    return SANCTION_PROGRESSION[stage];
}

// Get next sanction in progression
function getNextSanction(currentSanction) {
    const currentStage = getSanctionStage(currentSanction);
    return getSanctionByStage(currentStage + 1);
}
// Get sanction color based on severity
function getSanctionColor(sanctionName) {
    const stage = getSanctionStage(sanctionName);
    switch(stage) {
        case 0: return "bg-success";      // Verbal Warning - Green
        case 1: return "bg-primary";      // Detention - Blue
        case 2: return "bg-info";         // Parent Notification - Light Blue
        case 3: return "bg-orange";       // Restorative Actions - Orange
        case 4: return "bg-warning";      // Counseling - Yellow
        case 5: return "bg-danger";       // Suspension - Red
        case 6: return "bg-dark";         // Expulsion - Dark
        default: return "bg-secondary";
    }
}

// Get sanction description based on your database
function getSanctionDescription(sanctionName) {
    const descriptions = {
        "Verbal Warning": "A verbal reminder to the student about expected behavior and potential consequences of further violations.",
        "Detention": "The student stays after school for a specified period as a consequence for their actions.",
        "Parent/Guardian Notification": "Parents/guardians are informed about the incident and the corresponding consequences.",
        "Restorative Actions": "Actions aimed at helping students understand the impact of their behavior and make amends.",
        "Counseling and Intervention": "Referral to the guidance and counseling department for additional support and intervention.",
        "Suspension": "Temporary removal of a student from school for a designated period.",
        "Expulsion": "Permanent removal of a student from the school due to serious or repeated violations."
    };
    return descriptions[sanctionName] || "No description available";
}

// ==========================================
// FETCH RECOMMENDED SANCTION FROM DATABASE
// ==========================================

async function fetchRecommendedSanctionFromDB(studentId, offenseId, offenseCount) {
    try {
        const response = await fetch('/prefect/violations/get-recommended-sanction', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                student_id: studentId,
                offense_id: offenseId,
                offense_count: offenseCount
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                return {
                    sanction: data.recommended_sanction,
                    sanction_id: data.sanction_id,
                    description: data.sanction_description,
                    next_sanction: data.next_sanction,
                    stage: data.sanction_stage,
                    offense_count: offenseCount,
                    is_max_stage: data.is_max_stage || false
                };
            }
        }
    } catch (error) {
        console.error('Error fetching recommended sanction:', error);
    }
    return null;
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

       // Updated function to create violation pairs review section
function createViolationPairsReviewSection() {
    let html = `
        <h6 class="mb-3"><i class="fas fa-list me-2"></i>Violation Entries</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="30%">Student</th>
                        <th width="20%">Grade Level</th>
                        <th width="25%">Previous Offenses</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

    violationPairs.forEach((pair, index) => {
        const student = allStudents.find(s => s.student_id == pair.violator_id);
        const gradeLevel = student?.adviser?.adviser_gradelevel || 'N/A';
        const section = student?.adviser?.adviser_section || '';
        const studentHistory = studentOffenseHistory[pair.violator_id];
        const offenseCount = studentHistory?.count || 0;

        html += `
            <tr>
                <td><strong>${index + 1}</strong></td>
                <td>${pair.violator_name}</td>
                <td>Grade ${gradeLevel} ${section}</td>
                <td>
                    <span class="badge ${getBadgeClass(offenseCount)}">
                        ${offenseCount} previous violation(s)
                    </span>
                </td>
                <td>
                    <span class="badge ${offenseCount > 0 ? 'bg-warning' : 'bg-success'}">
                        ${offenseCount > 0 ? 'Repeat Offender' : 'First Offense'}
                    </span>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            Each student will receive the appropriate sanction based on their offense history.
        </div>
    `;

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

        async function finalSubmit() {
            const date = document.getElementById('violation_date').value;
            const time = document.getElementById('violation_time').value;
            const incident = document.getElementById('violation_incident').value;
            const complainant = document.getElementById('complainant').value;
            const witnesses = document.getElementById('witnesses').value;
            const evidenceDescription = document.getElementById('evidence_description').value;
            const evidenceFiles = getAllEvidenceFiles();

            const offenses = getAllOffensesAndSanctions();
            if (offenses.length === 0) {
                alert('Please select at least one offense.');
                return;
            }

            const formData = new FormData();
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('_token', csrfToken);
            formData.append('violation_type', currentViolationType);

            // Add main offenses
            const mainOffenses = offenses.filter(o => !o.is_additional);
            mainOffenses.forEach((offense, index) => {
                formData.append(`offenses[${index}][offense_id]`, offense.offense_id);
            });

            // Add additional offenses
            additionalOffenses.forEach((offense, index) => {
                formData.append(`additional_offenses[${index}][offense_id]`, offense.offense_id);
                formData.append(`additional_offenses[${index}][sanction_id]`, offense.sanction_id);
                formData.append(`additional_offenses[${index}][student_id]`, offense.studentId);
            });

            violationPairs.forEach((pair, index) => {
                formData.append(`violations[${index}][violator_id]`, pair.violator_id);
                formData.append(`violations[${index}][date]`, date);
                formData.append(`violations[${index}][time]`, time);
                formData.append(`violations[${index}][incident]`, incident);
                formData.append(`violations[${index}][complainant]`, complainant);
                formData.append(`violations[${index}][witnesses]`, witnesses);
                formData.append(`violations[${index}][evidence_description]`, evidenceDescription);
            });

            evidenceFiles.forEach((file, index) => {
                formData.append('evidence_files[]', file);
            });

            const submitBtn = document.querySelector('#reviewModal .btn-success');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('{{ route('violations.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    if (response.ok) {
                        window.location.href = '{{ route('prefect.violation') }}';
                    } else {
                        throw new Error(data.message || 'Submission failed');
                    }
                } else {
                    const text = await response.text();
                    console.error('Non-JSON response:', text.substring(0, 500));
                    throw new Error('Server returned an error page. Please check the console for details.');
                }
            } catch (error) {
                console.error('Submission error:', error);
                let errorMessage = 'Error submitting violations: ';
                if (error.message.includes('Server returned an error page')) {
                    errorMessage += 'Server error occurred. Please check if all required fields are filled.';
                } else {
                    errorMessage += error.message;
                }
                alert(errorMessage);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                violationPairs = [];
                selectedViolators = [];
                selectedIndividualViolator = null;
                additionalOffenses = [];
                currentAdditionalOffenseStudent = null;

                // Reset individual search
                document.getElementById('individual_violator_search').value = '';
                document.getElementById('individual_violator_tag').innerHTML = '';
                document.getElementById('individual_violator_results').innerHTML = '';

                // Reset group search
                document.getElementById('violatorTags').innerHTML = '';
                document.getElementById('violationPairsList').innerHTML = '';
                document.getElementById('violationPairsSection').style.display = 'none';
                document.getElementById('submitBtn').disabled = true;

                // Reset offense selection
                document.getElementById('offense_id').value = '';
                document.getElementById('offense_search').value = '';
                document.getElementById('selected_offenses_container').innerHTML =
                    '<div class="text-muted p-2">No offenses selected yet</div>';
                selectedOffenses = [];
                searchOffenses();

                // Reset other fields
                document.getElementById('violation_date').value = '{{ date('Y-m-d') }}';
                document.getElementById('violation_time').value = '{{ date('H:i') }}';
                document.getElementById('violation_incident').value = '';
                document.getElementById('charCount').textContent = '0';
                document.getElementById('complainant').value = '';
                document.getElementById('witnesses').value = '';
                document.getElementById('evidence_description').value = '';

                // Reset evidence files
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

                // Reset history section
                document.getElementById('historyAndCustomSection').style.display = 'none';
                document.getElementById('offenseHistoryContent').innerHTML = '';

                selectViolationType('individual');
                searchIndividualStudents('', 'violator');
            }
        }

        function submitViolations() {
            openOffenseModal();
        }

        // Helper function to show alerts
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;

            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);
            setTimeout(() => alertDiv.remove(), 5000);
        }
    </script>
</body>

</html>
