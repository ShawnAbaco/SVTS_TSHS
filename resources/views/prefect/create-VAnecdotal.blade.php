@extends('prefect.layout')

@section('content')
<div class="main-container">

    {{-- ✅ Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {!! session('success') !!}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {!! session('error') !!}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="toolbar">
        <h2>Create Violation Anecdotal</h2>
        <div class="actions">
            <input type="search" placeholder="🔍 Search anecdotal..." id="searchInput">

            <div class="buttons-row">
                <button type="button" class="btn-Add-Anecdotal" id="btnAddAnecdotal">
                    <i class="fas fa-plus-circle"></i> Add Another Anecdotal
                </button>
                <button type="submit" class="btn-save" form="anecdotalForm">
                    <i class="fas fa-save"></i> Save All
                </button>
            </div>
        </div>
    </div>

    <!-- Anecdotal Container -->
    <form id="anecdotalForm" method="POST" action="{{ route('violation-anecdotal.store') }}">
        @csrf
        <div class="students-wrapper" id="anecdotalWrapper">
            <!-- Anecdotal forms will be dynamically added here -->
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let anecdotalCount = 0;
    const violationSearchUrl = "{{ route('violation-anecdotal.search-violations') }}";

    // Initialize with one anecdotal form
    document.addEventListener('DOMContentLoaded', function() {
        addAnecdotalForm();
    });

    // Add new anecdotal form
    document.getElementById('btnAddAnecdotal').addEventListener('click', function() {
        addAnecdotalForm();
        updateLayout();
    });

    function addAnecdotalForm() {
        anecdotalCount++;

        const anecdotalWrapper = document.getElementById('anecdotalWrapper');
        const newAnecdotal = document.createElement('div');
        newAnecdotal.className = 'student-container';
        newAnecdotal.innerHTML = `
            <div class="student-header">
                <span class="student-title">Anecdotal Record #${anecdotalCount}</span>
                <button type="button" class="remove-student" onclick="removeAnecdotal(this)">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="violation_search_${anecdotalCount}">Violation Record * <span class="note">(comma-separated for multiple)</span></label>
                    <input type="text" id="violation_search_${anecdotalCount}" class="form-control violation-search-input" placeholder="e.g. Shawn Laurence, Kent Zyrone, Jayvee Charles" data-ids="" autocomplete="off">
                    <input type="hidden" id="violation_id_${anecdotalCount}" name="anecdotal[${anecdotalCount-1}][violation_id]" class="violation-id-input" required>
                    <div class="search-results violation-results" id="violation_results_${anecdotalCount}"></div>
                </div>

                <div class="form-group full-width">
                    <label for="violation_anec_solution_${anecdotalCount}">Solution Implemented *</label>
                    <textarea id="violation_anec_solution_${anecdotalCount}" name="anecdotal[${anecdotalCount-1}][violation_anec_solution]" class="form-control" rows="4" placeholder="Describe the solution or intervention implemented..." required></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="violation_anec_recommendation_${anecdotalCount}">Recommendations *</label>
                    <textarea id="violation_anec_recommendation_${anecdotalCount}" name="anecdotal[${anecdotalCount-1}][violation_anec_recommendation]" class="form-control" rows="4" placeholder="Provide recommendations for future prevention..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="violation_anec_date_${anecdotalCount}">Anecdotal Date *</label>
                    <input type="date" id="violation_anec_date_${anecdotalCount}" name="anecdotal[${anecdotalCount-1}][violation_anec_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="violation_anec_time_${anecdotalCount}">Anecdotal Time *</label>
                    <input type="time" id="violation_anec_time_${anecdotalCount}" name="anecdotal[${anecdotalCount-1}][violation_anec_time]" class="form-control" value="{{ date('H:i') }}" required>
                </div>

                <div class="form-group">
                    <label for="status_${anecdotalCount}">Status</label>
                    <select id="status_${anecdotalCount}" name="anecdotal[${anecdotalCount-1}][status]" class="form-control">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="resolved">Resolved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        `;

        anecdotalWrapper.appendChild(newAnecdotal);

        // Attach search functionality to the new form
        attachSearchListeners(newAnecdotal, anecdotalCount);
    }

    // Attach search functionality to violation field
    function attachSearchListeners(container, anecdotalIndex) {
        const violationSearch = container.querySelector('.violation-search-input');
        const violationIdInput = container.querySelector('.violation-id-input');
        const violationResults = container.querySelector('.violation-results');

        // Violation search functionality - Multiple selection
        violationSearch.addEventListener('input', function() {
            let parts = this.value.split(",");
            let query = parts[parts.length - 1].trim();

            console.log('Violation search query:', query);

            if (query.length < 2) {
                violationResults.innerHTML = '';
                return;
            }

            // Show loading
            violationResults.innerHTML = '<div class="no-results">Searching...</div>';

            // Use jQuery AJAX for better compatibility
            $.ajax({
                url: violationSearchUrl,
                method: 'POST',
                data: {
                    query: query,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    console.log('Violation search SUCCESS - data received:', data);
                    violationResults.innerHTML = '';

                    if (!data || data.length === 0) {
                        violationResults.innerHTML = '<div class="no-results">No violation records found. Try a different search term.</div>';
                        return;
                    }

                    const selectedIds = (violationSearch.dataset.ids || "").split(",").filter(id => id.trim() !== "");

                    data.forEach(violation => {
                        // Skip if this student is already selected
                        if (selectedIds.includes(violation.violation_id.toString())) {
                            return;
                        }

                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.textContent = `${violation.student_name} - ${violation.offense_type} (${violation.violation_date})`;
                        item.dataset.id = violation.violation_id;
                        item.dataset.studentName = violation.student_name;

                        item.addEventListener('click', function() {
                            const currentIds = (violationSearch.dataset.ids || "").split(",").filter(id => id.trim() !== "");
                            const currentNames = violationSearch.value.split(",").map(n => n.trim()).filter(n => n !== "");

                            if (!currentIds.includes(violation.violation_id.toString())) {
                                // Replace the last incomplete name with the full selected name
                                if (currentNames.length > 0 && query.length > 0) {
                                    const lastIndex = currentNames.length - 1;
                                    if (currentNames[lastIndex].toLowerCase().includes(query.toLowerCase())) {
                                        currentNames[lastIndex] = violation.student_name;
                                    } else {
                                        currentNames.push(violation.student_name);
                                    }
                                } else {
                                    currentNames.push(violation.student_name);
                                }

                                violationSearch.value = currentNames.join(", ");
                                currentIds.push(violation.violation_id.toString());
                                violationSearch.dataset.ids = currentIds.join(",");

                                // Store the first violation ID (you might want to modify this logic based on your needs)
                                if (currentIds.length === 1) {
                                    violationIdInput.value = violation.violation_id;
                                }
                            }

                            violationResults.innerHTML = '';

                            // Clear error styling
                            violationIdInput.style.borderColor = '#ddd';
                            violationSearch.style.borderColor = '#ddd';
                        });

                        violationResults.appendChild(item);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('❌ Violation search ERROR:', error);
                    console.error('❌ Status:', status);
                    console.error('❌ XHR response:', xhr.responseText);
                    violationResults.innerHTML = '<div class="no-results">Search failed. Please try again.</div>';
                }
            });
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!violationSearch.contains(e.target) && !violationResults.contains(e.target)) {
                violationResults.innerHTML = '';
            }
        });
    }

    // Remove anecdotal form
    function removeAnecdotal(button) {
        const anecdotalContainers = document.querySelectorAll('.student-container');
        if (anecdotalContainers.length > 1) {
            button.closest('.student-container').remove();
            updateAnecdotalNumbers();
            updateLayout();
        } else {
            alert('You need at least one anecdotal form.');
        }
    }

    function updateAnecdotalNumbers() {
        const anecdotalContainers = document.querySelectorAll('.student-container');
        anecdotalContainers.forEach((container, index) => {
            const title = container.querySelector('.student-title');
            title.textContent = `Anecdotal Record #${index + 1}`;

            const inputs = container.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }

                const id = input.getAttribute('id');
                if (id) {
                    input.setAttribute('id', id.replace(/\d+$/, index + 1));
                }
            });

            const labels = container.querySelectorAll('label');
            labels.forEach(label => {
                const forAttr = label.getAttribute('for');
                if (forAttr) {
                    label.setAttribute('for', forAttr.replace(/\d+$/, index + 1));
                }
            });
        });
        anecdotalCount = anecdotalContainers.length;
    }

    function updateLayout() {
        const anecdotalContainers = document.querySelectorAll('.student-container');
        const anecdotalWrapper = document.getElementById('anecdotalWrapper');

        anecdotalContainers.forEach(container => {
            container.style.flex = '1 1 400px';
            container.style.maxWidth = '600px';
        });

        if (anecdotalContainers.length === 1) {
            anecdotalContainers[0].style.maxWidth = '800px';
            anecdotalWrapper.style.justifyContent = 'center';
        } else {
            anecdotalWrapper.style.justifyContent = 'flex-start';
        }
    }

    // Form validation
    document.getElementById('anecdotalForm').addEventListener('submit', function(e) {
        const anecdotalContainers = document.querySelectorAll('.student-container');
        let isValid = true;

        anecdotalContainers.forEach((container, index) => {
            const violationId = container.querySelector(`input[name="anecdotal[${index}][violation_id]"]`);
            const violationSearch = container.querySelector('.violation-search-input');
            const solution = container.querySelector(`textarea[name="anecdotal[${index}][violation_anec_solution]"]`);
            const recommendation = container.querySelector(`textarea[name="anecdotal[${index}][violation_anec_recommendation]"]`);
            const anecdotalDate = container.querySelector(`input[name="anecdotal[${index}][violation_anec_date]"]`);
            const anecdotalTime = container.querySelector(`input[name="anecdotal[${index}][violation_anec_time]"]`);

            if (!violationId.value || !solution.value || !recommendation.value || !anecdotalDate.value || !anecdotalTime.value) {
                isValid = false;
                if (!violationId.value) {
                    violationId.style.borderColor = '#e74c3c';
                    violationSearch.style.borderColor = '#e74c3c';
                }
                if (!solution.value) solution.style.borderColor = '#e74c3c';
                if (!recommendation.value) recommendation.style.borderColor = '#e74c3c';
                if (!anecdotalDate.value) anecdotalDate.style.borderColor = '#e74c3c';
                if (!anecdotalTime.value) anecdotalTime.style.borderColor = '#e74c3c';
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields (marked with *) before submitting.');
        }
    });

    // Clear error styling on input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('form-control')) {
            e.target.style.borderColor = '#ddd';
            if (e.target.classList.contains('violation-search-input')) {
                const violationIdInput = e.target.closest('.form-group').querySelector('.violation-id-input');
                violationIdInput.style.borderColor = '#ddd';
            }
        }
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const anecdotalContainers = document.querySelectorAll('.student-container');

        anecdotalContainers.forEach(container => {
            const violationSearch = container.querySelector('.violation-search-input').value.toLowerCase();
            const solution = container.querySelector('textarea[name*="[violation_anec_solution]"]').value.toLowerCase();
            const recommendation = container.querySelector('textarea[name*="[violation_anec_recommendation]"]').value.toLowerCase();

            if (violationSearch.includes(searchTerm) || solution.includes(searchTerm) || recommendation.includes(searchTerm)) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    });
</script>

@endsection
