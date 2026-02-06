@extends('adviser.layout')

@section('content')
<div class="main-container">
    <style>
        /* Success Modal Styles */
        .success-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .success-modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }

        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }

        .success-modal h3 {
            color: #28a745;
            margin-bottom: 10px;
        }

        .success-modal p {
            margin-bottom: 20px;
            color: #666;
        }

        /* Duplicate warning style */
        .duplicate-warning {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .duplicate-field {
            border-color: #e74c3c !important;
            background-color: #fff5f5;
        }

        .duplicate-summary {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }

        .duplicate-summary.show {
            display: block;
        }

        .duplicate-item {
            padding: 8px;
            margin: 5px 0;
            background-color: #fff;
            border-radius: 4px;
            border-left: 4px solid #e74c3c;
        }
    </style>

    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div class="success-modal-content">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Success!</h3>
            <p>All student records have been saved successfully.</p>
            <p><small>Redirecting to students list...</small></p>
        </div>
    </div>

    <!-- Duplicate Summary -->
    <div id="duplicateSummary" class="duplicate-summary">
        <h4><i class="fas fa-exclamation-triangle"></i> Duplicate Students Detected</h4>
        <div id="duplicateList"></div>
        <small>Please remove or modify duplicate students before saving.</small>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
        <h2>Create Student</h2>
        <div class="actions">
            <div class="buttons-row">
                <button type="button" class="btn-Add-Student" id="btnAddStudent">
                    <i class="fas fa-plus-circle"></i> Add Another Student
                </button>
                <button type="button" class="btn-save" id="btnSave">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>

    <!-- Student Container -->
    <form id="studentForm" method="POST" action="{{ route('adviser.students.store') }}">
        @csrf
        <!-- Hidden field for adviser_id -->
        <input type="hidden" name="adviser_id" value="{{ Auth::guard('adviser')->id() }}">

        <div class="students-wrapper" id="studentsWrapper">
            <!-- Student forms will be dynamically added here -->
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let studentCount = 0;
    const parentSearchUrl = "{{ route('adviser.students.search-parents') }}";
    const studentsListUrl = "{{ route('student.list') }}";
    const checkDuplicateUrl = "{{ route('adviser.students.check-duplicate') }}";

    // Function to show success modal and redirect
    function showSuccessModalAndRedirect() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
        
        // Redirect after 2 seconds to allow user to see the success message
        setTimeout(function() {
            window.location.href = studentsListUrl;
        }, 2000);
    }

    // Check if we're coming from a successful submission
    document.addEventListener('DOMContentLoaded', function() {
        // Check for success flash message (if using Laravel session)
        @if(session('success'))
            showSuccessModalAndRedirect();
        @endif
        
        // Initialize with one student form
        addStudentForm();
        setupRealTimeDuplicateChecking();
    });

    // Add new student form
    document.getElementById('btnAddStudent').addEventListener('click', function() {
        addStudentForm();
        updateLayout();
    });

    // Save button click handler
    document.getElementById('btnSave').addEventListener('click', function() {
        const totalStudents = document.querySelectorAll('.student-container').length;
        
        if (totalStudents === 0) {
            Swal.fire("No Students!", "Please add at least one student before saving.", "warning");
            return;
        }

        // Validate all forms first
        if (!validateAllForms()) {
            Swal.fire("Validation Error!", "Please fill in all required fields before saving.", "warning");
            return;
        }

        // Check for duplicates within the form
        const formDuplicates = checkForDuplicates();
        if (formDuplicates.length > 0) {
            showDuplicateWarning(formDuplicates, 'form');
            return;
        }

        // Check for duplicates in database before saving
        checkDatabaseDuplicates().then(hasDuplicates => {
            if (!hasDuplicates) {
                submitForm();
            }
        }).catch(error => {
            console.error('Error checking duplicates:', error);
            Swal.fire('Error!', 'An error occurred while checking for duplicates. Please try again.', 'error');
        });
    });

    // Check for duplicates in database
    async function checkDatabaseDuplicates() {
        const studentData = collectStudentData();
        
        try {
            const response = await fetch(checkDuplicateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ students: studentData })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            if (data.duplicates && data.duplicates.length > 0) {
                showDuplicateWarning(data.duplicates, 'database');
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Error checking database duplicates:', error);
            throw error;
        }
    }

    // Collect student data for duplicate checking
    function collectStudentData() {
        const studentContainers = document.querySelectorAll('.student-container');
        const studentData = [];
        
        studentContainers.forEach((container, index) => {
            const firstName = container.querySelector(`input[name="students[${index}][student_fname]"]`).value.trim();
            const lastName = container.querySelector(`input[name="students[${index}][student_lname]"]`).value.trim();
            const birthdate = container.querySelector(`input[name="students[${index}][student_birthdate]"]`).value;
            
            if (firstName && lastName && birthdate) {
                studentData.push({
                    index: index,
                    student_fname: firstName,
                    student_lname: lastName,
                    student_birthdate: birthdate
                });
            }
        });
        
        return studentData;
    }

    // Show duplicate warning
    function showDuplicateWarning(duplicates, type) {
        let duplicateMessage = "";
        let title = "";
        
        if (type === 'form') {
            title = 'Duplicate Students in Form!';
            duplicateMessage = "The following students are duplicates within this form:<br><br>";
            duplicates.forEach(dup => {
                duplicateMessage += `• Student #${dup.index1} and Student #${dup.index2}: <strong>${dup.name}</strong> (Birthdate: ${dup.birthdate})<br>`;
            });
            duplicateMessage += "<br>Please remove duplicates before saving.";
        } else {
            title = 'Student existed!';
            duplicateMessage = "The following students already exist in the database:<br><br>";
        }
        
        Swal.fire({
            title: title,
            html: duplicateMessage,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    }

    // Submit form after all checks
    function submitForm() {
        const totalStudents = document.querySelectorAll('.student-container').length;
        
        Swal.fire({
            title: 'Save Student Records?',
            html: `You are about to save <b>${totalStudents} student(s)</b>.<br><br>This action cannot be undone.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save All Records',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve, reject) => {
                    const formData = new FormData(document.getElementById('studentForm'));
                    
                    fetch("{{ route('adviser.students.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error');
                            });
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            showSuccessModalAndRedirect();
                            resolve();
                        } else {
                            Swal.fire('Error!', data.message || 'An error occurred while saving.', 'error');
                            reject(new Error(data.message || 'Save failed'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'An error occurred while saving. Please try again.', 'error');
                        reject(error);
                    });
                });
            }
        });
    }

    // Enhanced duplicate checking within form
    function checkForDuplicates() {
        const studentContainers = document.querySelectorAll('.student-container');
        const duplicates = [];
        const studentMap = new Map();
        
        // Reset all duplicate styling first
        resetDuplicateStyling();
        
        studentContainers.forEach((container, index) => {
            const firstName = container.querySelector(`input[name="students[${index}][student_fname]"]`).value.trim().toLowerCase();
            const lastName = container.querySelector(`input[name="students[${index}][student_lname]"]`).value.trim().toLowerCase();
            const birthdate = container.querySelector(`input[name="students[${index}][student_birthdate]"]`).value;
            
            if (firstName && lastName && birthdate) {
                const studentKey = `${firstName}-${lastName}-${birthdate}`;
                
                if (studentMap.has(studentKey)) {
                    const existingIndex = studentMap.get(studentKey);
                    duplicates.push({
                        index1: existingIndex + 1,
                        index2: index + 1,
                        name: `${firstName} ${lastName}`,
                        birthdate: birthdate
                    });
                    
                    // Highlight both duplicate students
                    highlightDuplicateFields(existingIndex);
                    highlightDuplicateFields(index);
                } else {
                    studentMap.set(studentKey, index);
                }
            }
        });
        
        return duplicates;
    }

    // Highlight duplicate fields
    function highlightDuplicateFields(index) {
        const container = document.querySelectorAll('.student-container')[index];
        if (!container) return;
        
        const firstNameInput = container.querySelector(`input[name="students[${index}][student_fname]"]`);
        const lastNameInput = container.querySelector(`input[name="students[${index}][student_lname]"]`);
        const birthdateInput = container.querySelector(`input[name="students[${index}][student_birthdate]"]`);
        
        const fnameWarning = container.querySelector(`#duplicate_fname_${index + 1}`);
        const lnameWarning = container.querySelector(`#duplicate_lname_${index + 1}`);
        const birthdateWarning = container.querySelector(`#duplicate_birthdate_${index + 1}`);
        
        if (firstNameInput) {
            firstNameInput.classList.add('duplicate-field');
            if (fnameWarning) fnameWarning.style.display = 'block';
        }
        if (lastNameInput) {
            lastNameInput.classList.add('duplicate-field');
            if (lnameWarning) lnameWarning.style.display = 'block';
        }
        if (birthdateInput) {
            birthdateInput.classList.add('duplicate-field');
            if (birthdateWarning) birthdateWarning.style.display = 'block';
        }
    }

    // Reset duplicate styling
    function resetDuplicateStyling() {
        document.querySelectorAll('.duplicate-field').forEach(field => {
            field.classList.remove('duplicate-field');
        });
        
        document.querySelectorAll('.duplicate-warning').forEach(warning => {
            warning.style.display = 'none';
        });
    }

    // Real-time duplicate checking
    function setupRealTimeDuplicateChecking() {
        document.addEventListener('input', function(e) {
            if (e.target.name && (e.target.name.includes('[student_fname]') || 
                                  e.target.name.includes('[student_lname]') || 
                                  e.target.name.includes('[student_birthdate]'))) {
                // Debounce the duplicate check
                clearTimeout(window.duplicateCheckTimeout);
                window.duplicateCheckTimeout = setTimeout(() => {
                    const duplicates = checkForDuplicates();
                    updateDuplicateSummary(duplicates);
                }, 500);
            }
        });
    }

    // Update duplicate summary
    function updateDuplicateSummary(duplicates) {
        const summary = document.getElementById('duplicateSummary');
        const duplicateList = document.getElementById('duplicateList');
        
        if (duplicates.length > 0) {
            summary.classList.add('show');
            duplicateList.innerHTML = '';
            
            duplicates.forEach(dup => {
                const item = document.createElement('div');
                item.className = 'duplicate-item';
                item.innerHTML = `
                    <strong>Student #${dup.index1} & Student #${dup.index2}</strong><br>
                    <small>${dup.name} (Birthdate: ${dup.birthdate})</small>
                `;
                duplicateList.appendChild(item);
            });
        } else {
            summary.classList.remove('show');
        }
    }

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
                    <div class="duplicate-warning" id="duplicate_fname_${studentCount}">This student may be a duplicate</div>
                </div>

                <div class="form-group">
                    <label for="student_lname_${studentCount}">Last Name *</label>
                    <input type="text" id="student_lname_${studentCount}" name="students[${studentCount-1}][student_lname]" class="form-control" required>
                    <div class="duplicate-warning" id="duplicate_lname_${studentCount}">This student may be a duplicate</div>
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
                    <div class="duplicate-warning" id="duplicate_birthdate_${studentCount}">This student may be a duplicate</div>
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

                <!-- Hidden adviser field - kept for form structure but hidden from view -->
                <div class="form-group" style="display: none;">
                    <label for="adviser_search_${studentCount}">Adviser *</label>
                    <input type="text" id="adviser_search_${studentCount}" class="form-control adviser-search-input" placeholder="Search adviser by name..." autocomplete="off" value="{{ Auth::guard('adviser')->user()->adviser_fname }} {{ Auth::guard('adviser')->user()->adviser_lname }}" readonly>
                    <input type="hidden" id="adviser_id_${studentCount}" name="students[${studentCount-1}][adviser_id]" class="adviser-id-input" value="{{ Auth::guard('adviser')->id() }}" required>
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

        // Attach search functionality to the new form (only for parent now)
        attachSearchListeners(newStudent, studentCount);
        
        // Setup real-time duplicate checking for new form
        setTimeout(() => {
            checkAndHighlightDuplicates();
        }, 100);
    }

    // [Rest of your existing functions remain the same: attachSearchListeners, removeStudent, updateStudentNumbers, updateLayout, validateAllForms]

    // Attach search functionality to parent fields only
    function attachSearchListeners(container, studentIndex) {
        const parentSearch = container.querySelector('.parent-search-input');
        const parentIdInput = container.querySelector('.parent-id-input');
        const parentResults = container.querySelector('.parent-results');

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

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!parentSearch.contains(e.target) && !parentResults.contains(e.target)) {
                parentResults.innerHTML = '';
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
            checkAndHighlightDuplicates();
        } else {
            Swal.fire("Warning", "You need at least one student form.", "warning");
        }
    }

    // Update student numbers after removal
    function updateStudentNumbers() {
        const studentContainers = document.querySelectorAll('.student-container');
        studentContainers.forEach((container, index) => {
            const newNumber = index + 1;
            const title = container.querySelector('.student-title');
            title.textContent = `Student #${newNumber}`;

            // Update all input names and IDs
            const inputs = container.querySelectorAll('input, select');
            inputs.forEach(input => {
                const oldName = input.getAttribute('name');
                if (oldName) {
                    const newName = oldName.replace(/students\[\d+\]/, `students[${index}]`);
                    input.setAttribute('name', newName);
                }

                const oldId = input.getAttribute('id');
                if (oldId) {
                    const newId = oldId.replace(/\d+$/, newNumber);
                    input.setAttribute('id', newId);
                }
            });

            // Update radio button IDs and labels
            const radios = container.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                const oldId = radio.getAttribute('id');
                if (oldId) {
                    const newId = oldId.replace(/\d+$/, newNumber);
                    radio.setAttribute('id', newId);
                }
            });

            const labels = container.querySelectorAll('label');
            labels.forEach(label => {
                const forAttr = label.getAttribute('for');
                if (forAttr) {
                    const newFor = forAttr.replace(/\d+$/, newNumber);
                    label.setAttribute('for', newFor);
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

    // Validate all forms
    function validateAllForms() {
        const studentContainers = document.querySelectorAll('.student-container');
        let isValid = true;
        let errorMessages = [];

        studentContainers.forEach((container, index) => {
            const firstName = container.querySelector(`input[name="students[${index}][student_fname]"]`);
            const lastName = container.querySelector(`input[name="students[${index}][student_lname]"]`);
            const birthdate = container.querySelector(`input[name="students[${index}][student_birthdate]"]`);
            const address = container.querySelector(`input[name="students[${index}][student_address]"]`);
            const contactInfo = container.querySelector(`input[name="students[${index}][student_contactinfo]"]`);
            const parentId = container.querySelector(`input[name="students[${index}][parent_id]"]`);
            const parentSearch = container.querySelector('.parent-search-input');

            // Reset borders
            [firstName, lastName, birthdate, address, contactInfo, parentId, parentSearch].forEach(field => {
                if (field) field.style.borderColor = '#ddd';
            });

            // Check required fields
            if (!firstName.value.trim() || !lastName.value.trim() || !birthdate.value || !address.value.trim() || !contactInfo.value.trim() || !parentId.value) {
                isValid = false;
                errorMessages.push(`Student #${index + 1} has missing required fields`);

                if (!firstName.value.trim()) firstName.style.borderColor = '#e74c3c';
                if (!lastName.value.trim()) lastName.style.borderColor = '#e74c3c';
                if (!birthdate.value) birthdate.style.borderColor = '#e74c3c';
                if (!address.value.trim()) address.style.borderColor = '#e74c3c';
                if (!contactInfo.value.trim()) contactInfo.style.borderColor = '#e74c3c';
                if (!parentId.value) {
                    parentId.style.borderColor = '#e74c3c';
                    parentSearch.style.borderColor = '#e74c3c';
                }
            }

            // Validate birthdate (should not be in future)
            if (birthdate.value) {
                const selectedDate = new Date(birthdate.value);
                const today = new Date();
                if (selectedDate > today) {
                    isValid = false;
                    birthdate.style.borderColor = '#e74c3c';
                    errorMessages.push(`Student #${index + 1}: Birthdate cannot be in the future`);
                }
            }

            // Validate contact info format (basic validation)
            if (contactInfo.value.trim() && !/^[\d\s\-\+\(\)]+$/.test(contactInfo.value.trim())) {
                isValid = false;
                contactInfo.style.borderColor = '#e74c3c';
                errorMessages.push(`Student #${index + 1}: Please enter a valid contact number`);
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Validation Error!',
                html: errorMessages.join('<br>'),
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }

        return isValid;
    }

    // Clear error styling on input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('form-control')) {
            e.target.style.borderColor = '#ddd';
            e.target.classList.remove('duplicate-field');

            // Also clear error styling on hidden ID inputs when search input changes
            if (e.target.classList.contains('parent-search-input')) {
                const parentIdInput = e.target.closest('.form-group').querySelector('.parent-id-input');
                parentIdInput.style.borderColor = '#ddd';
            }

            // Clear duplicate warnings
            const warning = e.target.parentElement.querySelector('.duplicate-warning');
            if (warning) {
                warning.style.display = 'none';
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