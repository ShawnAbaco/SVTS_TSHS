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
                <p>All parent records have been saved successfully.</p>
                <p><small>Redirecting to parents list...</small></p>
            </div>
        </div>

        <!-- Duplicate Summary -->
        <div id="duplicateSummary" class="duplicate-summary">
            <h4><i class="fas fa-exclamation-triangle"></i> Duplicate Parents Detected</h4>
            <div id="duplicateList"></div>
            <small>Please remove or modify duplicate parents before saving.</small>
        </div>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Display Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Create Parents</h2>
            <div class="actions">
                <div class="buttons-row">
                    <button type="button" class="btn-Add-Parent" id="btnAddParent">
                        <i class="fas fa-plus-circle"></i> Add Another Parent
                    </button>
                    <button type="button" class="btn-save" id="btnSave">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Parent Container -->
        <form id="parentForm" method="POST" action="{{ route('adviser.parents.store') }}">
            @csrf
            <div class="parents-wrapper" id="parentsWrapper">
                <!-- Parent forms will be dynamically added here -->
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let parentCount = 0;
        const parentsListUrl = "{{ route('adviser.parentlist') }}";
        const checkDuplicateUrl = "{{ route('adviser.parents.check-duplicate') }}";

        // Function to show success modal and redirect
        function showSuccessModalAndRedirect() {
            const modal = document.getElementById('successModal');
            modal.style.display = 'flex';

            // Redirect after 2 seconds to allow user to see the success message
            setTimeout(function () {
                window.location.href = parentsListUrl;
            }, 2000);
        }

        // Check if we're coming from a successful submission
        document.addEventListener('DOMContentLoaded', function () {
            // Check for success flash message (if using Laravel session)
            @if(session('success'))
                showSuccessModalAndRedirect();
            @endif

            // Initialize with one parent form
            addParentForm();
            setupRealTimeDuplicateChecking();
        });

        // Add new parent form
        document.getElementById('btnAddParent').addEventListener('click', function () {
            addParentForm();
            updateLayout();
        });

        // Save button click handler
        document.getElementById('btnSave').addEventListener('click', function () {
            const totalParents = document.querySelectorAll('.parent-container').length;

            if (totalParents === 0) {
                Swal.fire("No Parents!", "Please add at least one parent before saving.", "warning");
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
            const parentData = collectParentData();

            try {
                const response = await fetch(checkDuplicateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ parents: parentData })
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

        // Collect parent data for duplicate checking
        function collectParentData() {
            const parentContainers = document.querySelectorAll('.parent-container');
            const parentData = [];

            parentContainers.forEach((container, index) => {
                const firstName = container.querySelector(`input[name="parents[${index}][parent_fname]"]`).value.trim();
                const lastName = container.querySelector(`input[name="parents[${index}][parent_lname]"]`).value.trim();
                const birthdate = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`).value;
                const contact = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`).value.trim();

                if (firstName && lastName && birthdate && contact) {
                    parentData.push({
                        index: index,
                        parent_fname: firstName,
                        parent_lname: lastName,
                        parent_birthdate: birthdate,
                        parent_contactinfo: contact
                    });
                }
            });

            return parentData;
        }

        // Show duplicate warning
        function showDuplicateWarning(duplicates, type) {
            let duplicateMessage = "";
            let title = "";

            if (type === 'form') {
                title = 'Duplicate Parents in Form!';
                duplicateMessage = "The following parents are duplicates within this form:<br><br>";
                duplicates.forEach(dup => {
                    duplicateMessage += `• Parent #${dup.index1} and Parent #${dup.index2}: <strong>${dup.name}</strong> (Birthdate: ${dup.birthdate}, Contact: ${dup.contact})<br>`;
                });
                duplicateMessage += "<br>Please remove duplicates before saving.";
            } else {
                title = 'Parent Already Exists!';
                duplicateMessage = "The following parents already exist in the database:<br><br>";
                duplicates.forEach(dup => {
                });
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
            const totalParents = document.querySelectorAll('.parent-container').length;

            Swal.fire({
                title: 'Save Parent Records?',
                html: `You are about to save <b>${totalParents} parent(s)</b>.<br><br>This action cannot be undone.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save All Records',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                        // Collect all parent data as JSON
                        const parentsData = [];
                        const parentContainers = document.querySelectorAll('.parent-container');

                        parentContainers.forEach((container, index) => {
                            const firstName = container.querySelector(`input[name="parents[${index}][parent_fname]"]`).value;
                            const lastName = container.querySelector(`input[name="parents[${index}][parent_lname]"]`).value;
                            const sexRadio = container.querySelector(`input[name="parents[${index}][parent_sex]"]:checked`);
                            const birthdate = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`).value;
                            const contact = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`).value;
                            const relationship = container.querySelector(`select[name="parents[${index}][parent_relationship]"]`).value;
                            const email = container.querySelector(`input[name="parents[${index}][parent_email]"]`).value;

                            // Convert sex to lowercase for backend consistency
                            const sex = sexRadio ? sexRadio.value.toLowerCase() : '';

                            parentsData.push({
                                parent_fname: firstName,
                                parent_lname: lastName,
                                parent_sex: sex,
                                parent_birthdate: birthdate,
                                parent_contactinfo: contact,
                                parent_relationship: relationship,
                                parent_email: email || null
                            });
                        });

                        console.log('Sending data:', parentsData); // Debug log

                        // Send as JSON
                        fetch("{{ route('adviser.parents.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ parents: parentsData })
                        })
                            .then(response => {
                                console.log('Response status:', response.status); // Debug log
                                if (!response.ok) {
                                    return response.text().then(text => {
                                        console.log('Error response:', text); // Debug log
                                        try {
                                            const errorData = JSON.parse(text);
                                            throw new Error(errorData.message || 'Server error');
                                        } catch (e) {
                                            throw new Error(text || 'Server error');
                                        }
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Success response:', data); // Debug log
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
            const parentContainers = document.querySelectorAll('.parent-container');
            const duplicates = [];
            const parentMap = new Map();

            // Reset all duplicate styling first
            resetDuplicateStyling();

            parentContainers.forEach((container, index) => {
                const firstName = container.querySelector(`input[name="parents[${index}][parent_fname]"]`).value.trim().toLowerCase();
                const lastName = container.querySelector(`input[name="parents[${index}][parent_lname]"]`).value.trim().toLowerCase();
                const birthdate = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`).value;
                const contact = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`).value.trim();

                if (firstName && lastName && birthdate && contact) {
                    const parentKey = `${firstName}-${lastName}-${birthdate}-${contact}`;

                    if (parentMap.has(parentKey)) {
                        const existingIndex = parentMap.get(parentKey);
                        duplicates.push({
                            index1: existingIndex + 1,
                            index2: index + 1,
                            name: `${firstName} ${lastName}`,
                            birthdate: birthdate,
                            contact: contact
                        });

                        // Highlight both duplicate parents
                        highlightDuplicateFields(existingIndex);
                        highlightDuplicateFields(index);
                    } else {
                        parentMap.set(parentKey, index);
                    }
                }
            });

            return duplicates;
        }

        // Highlight duplicate fields
        function highlightDuplicateFields(index) {
            const container = document.querySelectorAll('.parent-container')[index];
            if (!container) return;

            const firstNameInput = container.querySelector(`input[name="parents[${index}][parent_fname]"]`);
            const lastNameInput = container.querySelector(`input[name="parents[${index}][parent_lname]"]`);
            const birthdateInput = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`);
            const contactInput = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`);

            const fnameWarning = container.querySelector(`#duplicate_fname_${index + 1}`);
            const lnameWarning = container.querySelector(`#duplicate_lname_${index + 1}`);
            const birthdateWarning = container.querySelector(`#duplicate_birthdate_${index + 1}`);
            const contactWarning = container.querySelector(`#duplicate_contact_${index + 1}`);

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
            if (contactInput) {
                contactInput.classList.add('duplicate-field');
                if (contactWarning) contactWarning.style.display = 'block';
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
            document.addEventListener('input', function (e) {
                if (e.target.name && (e.target.name.includes('[parent_fname]') ||
                    e.target.name.includes('[parent_lname]') ||
                    e.target.name.includes('[parent_birthdate]') ||
                    e.target.name.includes('[parent_contactinfo]'))) {
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
                                        <strong>Parent #${dup.index1} & Parent #${dup.index2}</strong><br>
                                        <small>${dup.name} (Birthdate: ${dup.birthdate}, Contact: ${dup.contact})</small>
                                    `;
                    duplicateList.appendChild(item);
                });
            } else {
                summary.classList.remove('show');
            }
        }

        function addParentForm() {
            parentCount++;

            const parentsWrapper = document.getElementById('parentsWrapper');
            const newParent = document.createElement('div');
            newParent.className = 'parent-container';
            newParent.innerHTML = `
                                <div class="parent-header">
                                    <span class="parent-title">Parent #${parentCount}</span>
                                    <button type="button" class="remove-parent" onclick="removeParent(this)">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="parent_fname_${parentCount}">First Name *</label>
                                        <input type="text" id="parent_fname_${parentCount}" name="parents[${parentCount - 1}][parent_fname]" class="form-control" required>
                                        <div class="duplicate-warning" id="duplicate_fname_${parentCount}">This parent may be a duplicate</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_lname_${parentCount}">Last Name *</label>
                                        <input type="text" id="parent_lname_${parentCount}" name="parents[${parentCount - 1}][parent_lname]" class="form-control" required>
                                        <div class="duplicate-warning" id="duplicate_lname_${parentCount}">This parent may be a duplicate</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_sex_${parentCount}">Sex</label>
                                        <div class="radio-group">
                                            <div class="radio-option">
                                                <input type="radio" id="parent_sex_male_${parentCount}" name="parents[${parentCount - 1}][parent_sex]" value="male" required>
                                                <label for="parent_sex_male_${parentCount}">Male</label>
                                            </div>
                                            <div class="radio-option">
                                                <input type="radio" id="parent_sex_female_${parentCount}" name="parents[${parentCount - 1}][parent_sex]" value="female" required>
                                                <label for="parent_sex_female_${parentCount}">Female</label>
                                            </div>
                                            <div class="radio-option">
                                                <input type="radio" id="parent_sex_other_${parentCount}" name="parents[${parentCount - 1}][parent_sex]" value="other" required>
                                                <label for="parent_sex_other_${parentCount}">Other</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_birthdate_${parentCount}">Birthdate *</label>
                                        <input type="date" id="parent_birthdate_${parentCount}" name="parents[${parentCount - 1}][parent_birthdate]" class="form-control" required>
                                        <div class="duplicate-warning" id="duplicate_birthdate_${parentCount}">This parent may be a duplicate</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_email_${parentCount}">Email</label>
                                        <input type="email" id="parent_email_${parentCount}" name="parents[${parentCount - 1}][parent_email]" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_contactinfo_${parentCount}">Contact Information *</label>
                                        <input type="text" id="parent_contactinfo_${parentCount}" name="parents[${parentCount - 1}][parent_contactinfo]" class="form-control" required>
                                        <div class="duplicate-warning" id="duplicate_contact_${parentCount}">This parent may be a duplicate</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_relationship_${parentCount}">Relationship *</label>
                                        <select id="parent_relationship_${parentCount}" name="parents[${parentCount - 1}][parent_relationship]" class="form-control" required>
                                            <option value="">Select Relationship</option>
                                            <option value="father">Father</option>
                                            <option value="mother">Mother</option>
                                            <option value="guardian">Guardian</option>
                                            <option value="grandparent">Grandparent</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            `;

            parentsWrapper.appendChild(newParent);

            // Setup real-time duplicate checking for new form
            setTimeout(() => {
                checkAndHighlightDuplicates();
            }, 100);
        }

        // Check and highlight duplicates
        function checkAndHighlightDuplicates() {
            const duplicates = checkForDuplicates();
            updateDuplicateSummary(duplicates);
        }

        // Remove parent form
        function removeParent(button) {
            const parentContainers = document.querySelectorAll('.parent-container');
            if (parentContainers.length > 1) {
                button.closest('.parent-container').remove();
                // Update parent numbers and layout
                updateParentNumbers();
                updateLayout();
                checkAndHighlightDuplicates();
            } else {
                Swal.fire("Warning", "You need at least one parent form.", "warning");
            }
        }

        // Update parent numbers after removal
        function updateParentNumbers() {
            const parentContainers = document.querySelectorAll('.parent-container');
            parentContainers.forEach((container, index) => {
                const newNumber = index + 1;
                const title = container.querySelector('.parent-title');
                title.textContent = `Parent #${newNumber}`;

                // Update all input names and IDs
                const inputs = container.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const oldName = input.getAttribute('name');
                    if (oldName) {
                        const newName = oldName.replace(/parents\[\d+\]/, `parents[${index}]`);
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

                // Update duplicate warning IDs
                const warnings = container.querySelectorAll('.duplicate-warning');
                warnings.forEach(warning => {
                    const oldId = warning.getAttribute('id');
                    if (oldId) {
                        const newId = oldId.replace(/\d+$/, newNumber);
                        warning.setAttribute('id', newId);
                    }
                });
            });
            parentCount = parentContainers.length;
        }

        // Update layout based on number of parent forms
        function updateLayout() {
            const parentContainers = document.querySelectorAll('.parent-container');
            const parentsWrapper = document.getElementById('parentsWrapper');

            // Reset all containers to default flex behavior
            parentContainers.forEach(container => {
                container.style.flex = '1 1 400px';
                container.style.maxWidth = '600px';
            });

            // Special layout for single parent
            if (parentContainers.length === 1) {
                parentContainers[0].style.maxWidth = '800px';
                parentsWrapper.style.justifyContent = 'center';
            }
            // For multiple parents, let flexbox handle the layout naturally
            else {
                parentsWrapper.style.justifyContent = 'flex-start';
            }
        }

        // Validate all forms
        function validateAllForms() {
            const parentContainers = document.querySelectorAll('.parent-container');
            let isValid = true;
            let errorMessages = [];

            parentContainers.forEach((container, index) => {
                const firstName = container.querySelector(`input[name="parents[${index}][parent_fname]"]`);
                const lastName = container.querySelector(`input[name="parents[${index}][parent_lname]"]`);
                const birthdate = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`);
                const contactInfo = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`);
                const email = container.querySelector(`input[name="parents[${index}][parent_email]"]`);
                const relationship = container.querySelector(`select[name="parents[${index}][parent_relationship]"]`);
                const sexRadios = container.querySelectorAll(`input[name="parents[${index}][parent_sex]"]`);

                // Reset borders
                [firstName, lastName, birthdate, contactInfo, email, relationship].forEach(field => {
                    if (field) field.style.borderColor = '#ddd';
                });

                // Check required fields
                if (!firstName.value.trim() || !lastName.value.trim() || !birthdate.value || !contactInfo.value.trim() || !relationship.value) {
                    isValid = false;
                    errorMessages.push(`Parent #${index + 1} has missing required fields`);

                    if (!firstName.value.trim()) firstName.style.borderColor = '#e74c3c';
                    if (!lastName.value.trim()) lastName.style.borderColor = '#e74c3c';
                    if (!birthdate.value) birthdate.style.borderColor = '#e74c3c';
                    if (!contactInfo.value.trim()) contactInfo.style.borderColor = '#e74c3c';
                    if (!relationship.value) relationship.style.borderColor = '#e74c3c';
                }

                // Check if sex is selected
                let sexSelected = false;
                sexRadios.forEach(radio => {
                    if (radio.checked) sexSelected = true;
                });

                if (!sexSelected) {
                    isValid = false;
                    errorMessages.push(`Parent #${index + 1}: Please select a gender`);
                }

                // Validate email only if not empty
                if (email.value) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(email.value)) {
                        isValid = false;
                        email.style.borderColor = '#e74c3c';
                        errorMessages.push(`Parent #${index + 1}: Please enter a valid email address`);
                    }
                }

                // Validate birthdate (should not be in future)
                if (birthdate.value) {
                    const selectedDate = new Date(birthdate.value);
                    const today = new Date();
                    if (selectedDate > today) {
                        isValid = false;
                        birthdate.style.borderColor = '#e74c3c';
                        errorMessages.push(`Parent #${index + 1}: Birthdate cannot be in the future`);
                    }
                }

                // Validate contact info format (basic validation)
                if (contactInfo.value.trim() && !/^[\d\s\-\+\(\)]+$/.test(contactInfo.value.trim())) {
                    isValid = false;
                    contactInfo.style.borderColor = '#e74c3c';
                    errorMessages.push(`Parent #${index + 1}: Please enter a valid contact number`);
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
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('form-control')) {
                e.target.style.borderColor = '#ddd';
                e.target.classList.remove('duplicate-field');

                // Clear duplicate warnings
                const warning = e.target.parentElement.querySelector('.duplicate-warning');
                if (warning) {
                    warning.style.display = 'none';
                }
            }
        });

        // Clear radio button styling on change
        document.addEventListener('change', function (e) {
            if (e.target.type === 'radio') {
                const radioGroup = e.target.closest('.radio-group');
                if (radioGroup) {
                    radioGroup.style.borderColor = '#ddd';
                }
            }
        });
    </script>
@endsection