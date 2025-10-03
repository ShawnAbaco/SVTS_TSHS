@extends('prefect.layout')

@section('content')
<div class="main-container">

            <!-- Toolbar -->
            <div class="toolbar">
                <h2>Create Student</h2>
                <div class="actions">
                    <input type="search" placeholder="🔍 Search student..." id="searchInput">

                    <div class="buttons-row">
                        <button type="button" class="btn-Add-Student" id="btnAddStudent">
                            <i class="fas fa-plus-circle"></i> Add Another Student
                        </button>
                        <button type="submit" class="btn-save" form="studentForm">
                            <i class="fas fa-save"></i> Save All
                        </button>
                    </div>
                </div>
            </div>

            <!-- Student Container -->
            <form id="studentForm" method="POST" action="{{ route('students.store') }}">
                @csrf
                <div class="students-wrapper" id="studentsWrapper">
                    <!-- Student forms will be dynamically added here -->
                </div>
            </form>
        </div>

        <script>
            let studentCount = 0;
            const parentSearchUrl = "{{ route('students.search-parents') }}";
            const adviserSearchUrl = "{{ route('students.search-advisers') }}";

            // Initialize with one student form
            document.addEventListener('DOMContentLoaded', function() {
                addStudentForm();
            });

            // Add new student form
            document.getElementById('btnAddStudent').addEventListener('click', function() {
                addStudentForm();
                updateLayout();
            });

            function addStudentForm() {
                studentCount++;

                const studentsWrapper = document.getElementById('studentsWrapper');
                const newStudent = document.createElement('div');
                newStudent.className = 'student-container';
                newStudent.innerHTML = `
                    <div class="student-header">
                        <span class="student-title">Student #${studentCount}</span>
                        <button type="button" class="remove-student" onclick="removeStudent(this)">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="student_fname_${studentCount}">First Name *</label>
                            <input type="text" id="student_fname_${studentCount}" name="students[${studentCount-1}][student_fname]" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="student_lname_${studentCount}">Last Name *</label>
                            <input type="text" id="student_lname_${studentCount}" name="students[${studentCount-1}][student_lname]" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="student_sex_${studentCount}">Sex</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" id="student_sex_male_${studentCount}" name="students[${studentCount-1}][student_sex]" value="male">
                                    <label for="student_sex_male_${studentCount}">Male</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="student_sex_female_${studentCount}" name="students[${studentCount-1}][student_sex]" value="female">
                                    <label for="student_sex_female_${studentCount}">Female</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="student_sex_other_${studentCount}" name="students[${studentCount-1}][student_sex]" value="other">
                                    <label for="student_sex_other_${studentCount}">Other</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="student_birthdate_${studentCount}">Birthdate *</label>
                            <input type="date" id="student_birthdate_${studentCount}" name="students[${studentCount-1}][student_birthdate]" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="student_address_${studentCount}">Address *</label>
                            <input type="text" id="student_address_${studentCount}" name="students[${studentCount-1}][student_address]" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="student_contactinfo_${studentCount}">Contact Information *</label>
                            <input type="text" id="student_contactinfo_${studentCount}" name="students[${studentCount-1}][student_contactinfo]" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="parent_search_${studentCount}">Parent *</label>
                            <input type="text" id="parent_search_${studentCount}" class="form-control parent-search-input" placeholder="Search parent by name..." autocomplete="off">
                            <input type="hidden" id="parent_id_${studentCount}" name="students[${studentCount-1}][parent_id]" class="parent-id-input" required>
                            <div class="search-results parent-results" id="parent_results_${studentCount}"></div>
                        </div>

                        <div class="form-group">
                            <label for="adviser_search_${studentCount}">Adviser *</label>
                            <input type="text" id="adviser_search_${studentCount}" class="form-control adviser-search-input" placeholder="Search adviser by name..." autocomplete="off">
                            <input type="hidden" id="adviser_id_${studentCount}" name="students[${studentCount-1}][adviser_id]" class="adviser-id-input" required>
                            <div class="search-results adviser-results" id="adviser_results_${studentCount}"></div>
                        </div>

                        <div class="form-group">
                            <label for="status_${studentCount}">Status</label>
                            <select id="status_${studentCount}" name="students[${studentCount-1}][status]" class="form-control">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="transferred">Transferred</option>
                                <option value="graduated">Graduated</option>
                            </select>
                        </div>
                    </div>
                `;

                studentsWrapper.appendChild(newStudent);

                // Attach search functionality to the new form
                attachSearchListeners(newStudent, studentCount);
            }

            // Attach search functionality to parent and adviser fields
            function attachSearchListeners(container, studentIndex) {
                const parentSearch = container.querySelector('.parent-search-input');
                const parentIdInput = container.querySelector('.parent-id-input');
                const parentResults = container.querySelector('.parent-results');

                const adviserSearch = container.querySelector('.adviser-search-input');
                const adviserIdInput = container.querySelector('.adviser-id-input');
                const adviserResults = container.querySelector('.adviser-results');

                // Parent search functionality
// Parent search functionality - using FormData
parentSearch.addEventListener('input', function() {
    const query = this.value.trim();

    console.log('Parent search query:', query);

    if (query.length < 2) {
        parentResults.innerHTML = '';
        return;
    }

    // Use FormData instead of JSON (more compatible with Laravel)
    const formData = new FormData();
    formData.append('query', query);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(parentSearchUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Parent search results:', data);
        parentResults.innerHTML = '';

        if (!data || data.length === 0) {
            parentResults.innerHTML = '<div class="no-results">No parents found</div>';
            return;
        }

        data.forEach(parent => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.textContent = `${parent.parent_fname} ${parent.parent_lname}`;
            item.dataset.id = parent.parent_id;

            item.addEventListener('click', function() {
                parentSearch.value = `${parent.parent_fname} ${parent.parent_lname}`;
                parentIdInput.value = parent.parent_id;
                parentResults.innerHTML = '';

                parentIdInput.style.borderColor = '#ddd';
                parentSearch.style.borderColor = '#ddd';
            });

            parentResults.appendChild(item);
        });
    })
    .catch(error => {
        console.error('Parent search error:', error);
        parentResults.innerHTML = '<div class="no-results">Search failed. Please try again.</div>';
    });
});

                // Adviser search functionality
                adviserSearch.addEventListener('input', function() {
                    const query = this.value.trim();

                    if (query.length < 2) {
                        adviserResults.innerHTML = '';
                        return;
                    }

                    // AJAX call to search advisers
                    fetch(adviserSearchUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ query: query })
                    })
                    .then(response => response.json())
                    .then(data => {
                        adviserResults.innerHTML = '';

                        if (data.length === 0) {
                            adviserResults.innerHTML = '<div class="no-results">No advisers found</div>';
                            return;
                        }

                        data.forEach(adviser => {
                            const item = document.createElement('div');
                            item.className = 'search-result-item';
                            item.textContent = `${adviser.adviser_fname} ${adviser.adviser_lname}`;
                            item.dataset.id = adviser.adviser_id;

                            item.addEventListener('click', function() {
                                adviserSearch.value = `${adviser.adviser_fname} ${adviser.adviser_lname}`;
                                adviserIdInput.value = adviser.adviser_id;
                                adviserResults.innerHTML = '';

                                // Clear any previous error styling
                                adviserIdInput.style.borderColor = '#ddd';
                                adviserSearch.style.borderColor = '#ddd';
                            });

                            adviserResults.appendChild(item);
                        });
                    })
                    .catch(error => {
                        console.error('Adviser search error:', error);
                        adviserResults.innerHTML = '<div class="no-results">Search failed</div>';
                    });
                });

                // Close search results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!parentSearch.contains(e.target) && !parentResults.contains(e.target)) {
                        parentResults.innerHTML = '';
                    }
                    if (!adviserSearch.contains(e.target) && !adviserResults.contains(e.target)) {
                        adviserResults.innerHTML = '';
                    }
                });
            }

            // Remove student form
            function removeStudent(button) {
                const studentContainers = document.querySelectorAll('.student-container');
                if (studentContainers.length > 1) {
                    button.closest('.student-container').remove();
                    // Update student numbers and layout
                    updateStudentNumbers();
                    updateLayout();
                } else {
                    alert('You need at least one student form.');
                }
            }

            // Update student numbers after removal
            function updateStudentNumbers() {
                const studentContainers = document.querySelectorAll('.student-container');
                studentContainers.forEach((container, index) => {
                    const title = container.querySelector('.student-title');
                    title.textContent = `Student #${index + 1}`;

                    // Update all input names and IDs
                    const inputs = container.querySelectorAll('input, select');
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

                    // Update radio button IDs and labels
                    const radios = container.querySelectorAll('input[type="radio"]');
                    radios.forEach(radio => {
                        const id = radio.getAttribute('id');
                        if (id) {
                            radio.setAttribute('id', id.replace(/\d+$/, index + 1));
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
                studentCount = studentContainers.length;
            }

            // Update layout based on number of student forms
            function updateLayout() {
                const studentContainers = document.querySelectorAll('.student-container');
                const studentsWrapper = document.getElementById('studentsWrapper');

                // Reset all containers to default flex behavior
                studentContainers.forEach(container => {
                    container.style.flex = '1 1 400px';
                    container.style.maxWidth = '600px';
                });

                // Special layout for single student
                if (studentContainers.length === 1) {
                    studentContainers[0].style.maxWidth = '800px';
                    studentsWrapper.style.justifyContent = 'center';
                }
                // For multiple students, let flexbox handle the layout naturally
                else {
                    studentsWrapper.style.justifyContent = 'flex-start';
                }
            }

            // Form validation
            document.getElementById('studentForm').addEventListener('submit', function(e) {
                const studentContainers = document.querySelectorAll('.student-container');
                let isValid = true;

                studentContainers.forEach((container, index) => {
                    const firstName = container.querySelector(`input[name="students[${index}][student_fname]"]`);
                    const lastName = container.querySelector(`input[name="students[${index}][student_lname]"]`);
                    const birthdate = container.querySelector(`input[name="students[${index}][student_birthdate]"]`);
                    const address = container.querySelector(`input[name="students[${index}][student_address]"]`);
                    const contactInfo = container.querySelector(`input[name="students[${index}][student_contactinfo]"]`);
                    const parentId = container.querySelector(`input[name="students[${index}][parent_id]"]`);
                    const parentSearch = container.querySelector('.parent-search-input');
                    const adviserId = container.querySelector(`input[name="students[${index}][adviser_id]"]`);
                    const adviserSearch = container.querySelector('.adviser-search-input');

                    if (!firstName.value || !lastName.value || !birthdate.value || !address.value || !contactInfo.value || !parentId.value || !adviserId.value) {
                        isValid = false;
                        // Highlight empty required fields
                        if (!firstName.value) firstName.style.borderColor = '#e74c3c';
                        if (!lastName.value) lastName.style.borderColor = '#e74c3c';
                        if (!birthdate.value) birthdate.style.borderColor = '#e74c3c';
                        if (!address.value) address.style.borderColor = '#e74c3c';
                        if (!contactInfo.value) contactInfo.style.borderColor = '#e74c3c';
                        if (!parentId.value) {
                            parentId.style.borderColor = '#e74c3c';
                            parentSearch.style.borderColor = '#e74c3c';
                        }
                        if (!adviserId.value) {
                            adviserId.style.borderColor = '#e74c3c';
                            adviserSearch.style.borderColor = '#e74c3c';
                        }
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

                    // Also clear error styling on hidden ID inputs when search input changes
                    if (e.target.classList.contains('parent-search-input')) {
                        const parentIdInput = e.target.closest('.form-group').querySelector('.parent-id-input');
                        parentIdInput.style.borderColor = '#ddd';
                    }
                    if (e.target.classList.contains('adviser-search-input')) {
                        const adviserIdInput = e.target.closest('.form-group').querySelector('.adviser-id-input');
                        adviserIdInput.style.borderColor = '#ddd';
                    }
                }
            });

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const studentContainers = document.querySelectorAll('.student-container');

                studentContainers.forEach(container => {
                    const firstName = container.querySelector('input[name*="[student_fname]"]').value.toLowerCase();
                    const lastName = container.querySelector('input[name*="[student_lname]"]').value.toLowerCase();

                    if (firstName.includes(searchTerm) || lastName.includes(searchTerm)) {
                        container.style.display = 'block';
                    } else {
                        container.style.display = 'none';
                    }
                });
            });
        </script>

@endsection
