@extends('adviser.NewAdviser.layout')

@section('content')

    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div class="success-modal-content">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Success!</h3>
            <p>All parent information has been saved successfully.</p>
            <p><small>Redirecting to parent lists...</small></p>
        </div>
    </div>

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

    <!-- Toolbar -->
    <div class="toolbar">
        <h2>Create Parents</h2>
        <div class="actions">
            <div class="buttons-row">
                <button type="button" class="btn-Add-Violation" id="btnAddViolation">
                    <i class="fas fa-plus-circle"></i> Add Another Parent
                </button>
                <button type="button" class="btn-save" id="btnSave">
                    <i class="fas fa-save"></i> Save All
                </button>
            </div>
        </div>
    </div>

    <!-- Parent Container -->
    <form id="violationForm" method="POST" action="{{ route('adviser.parents.store') }}">
        @csrf
        <div class="parents-wrapper" id="parentsWrapper">
            <!-- Parent forms will be dynamically added here -->
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let parentCount = 0;
    const parentListsUrl = "{{ route('adviser.parent.lists') }}";

    // Function to show success modal and redirect
    function showSuccessModalAndRedirect() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';

        // Redirect after 2 seconds to allow user to see the success message
        setTimeout(function() {
            window.location.href = parentListsUrl;
        }, 2000);
    }

    // Check if we're coming from a successful submission
    document.addEventListener('DOMContentLoaded', function() {
        addParentForm();

        // Check for success flash message (if using Laravel session)
        @if(session('success'))
            showSuccessModalAndRedirect();
        @endif
    });

    // Add new parent form
    document.getElementById('btnAddViolation').addEventListener('click', function() {
        addParentForm();
        updateLayout();
    });

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
                    <input type="text" id="parent_fname_${parentCount}" name="parents[${parentCount-1}][parent_fname]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="parent_lname_${parentCount}">Last Name *</label>
                    <input type="text" id="parent_lname_${parentCount}" name="parents[${parentCount-1}][parent_lname]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="parent_sex_${parentCount}">Sex *</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="parent_sex_male_${parentCount}" name="parents[${parentCount-1}][parent_sex]" value="Male" required>
                            <label for="parent_sex_male_${parentCount}">Male</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="parent_sex_female_${parentCount}" name="parents[${parentCount-1}][parent_sex]" value="Female" required>
                            <label for="parent_sex_female_${parentCount}">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="parent_sex_other_${parentCount}" name="parents[${parentCount-1}][parent_sex]" value="Other" required>
                            <label for="parent_sex_other_${parentCount}">Other</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="parent_birthdate_${parentCount}">Birthdate *</label>
                    <input type="date" id="parent_birthdate_${parentCount}" name="parents[${parentCount-1}][parent_birthdate]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="parent_email_${parentCount}">Email</label>
                    <input type="email" id="parent_email_${parentCount}" name="parents[${parentCount-1}][parent_email]" class="form-control">
                </div>

                <div class="form-group">
                    <label for="parent_contactinfo_${parentCount}">Contact Information *</label>
                    <input type="text" id="parent_contactinfo_${parentCount}" name="parents[${parentCount-1}][parent_contactinfo]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="parent_relationship_${parentCount}">Relationship *</label>
                    <select id="parent_relationship_${parentCount}" name="parents[${parentCount-1}][parent_relationship]" class="form-control" required>
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
    }

    // Remove parent form
    function removeParent(button) {
        const parentContainers = document.querySelectorAll('.parent-container');
        if (parentContainers.length > 1) {
            button.closest('.parent-container').remove();
            // Update parent numbers and layout
            updateParentNumbers();
            updateLayout();
        } else {
            alert('You need at least one parent form.');
        }
    }

    // Update parent numbers after removal
    function updateParentNumbers() {
        const parentContainers = document.querySelectorAll('.parent-container');
        parentContainers.forEach((container, index) => {
            const title = container.querySelector('.parent-title');
            title.textContent = `Parent #${index + 1}`;

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

    // Show confirmation modal before saving
    document.getElementById('btnSave').addEventListener('click', function() {
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
            if (!firstName.value || !lastName.value || !birthdate.value || !contactInfo.value) {
                isValid = false;
                errorMessages.push(`Parent #${index + 1} has missing required fields`);

                if (!firstName.value) firstName.style.borderColor = '#e74c3c';
                if (!lastName.value) lastName.style.borderColor = '#e74c3c';
                if (!birthdate.value) birthdate.style.borderColor = '#e74c3c';
                if (!contactInfo.value) contactInfo.style.borderColor = '#e74c3c';
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

            // Check if relationship is selected
            if (!relationship.value) {
                isValid = false;
                errorMessages.push(`Parent #${index + 1}: Please select a relationship`);
                relationship.style.borderColor = '#e74c3c';
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
        });

        if (!isValid) {
            Swal.fire({
                title: 'Validation Error!',
                html: 'Please fix the following errors:<br><br>' + errorMessages.join('<br>'),
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Show confirmation modal
        Swal.fire({
            title: 'Save Parent Records?',
            html: `You are about to save <b>${parentContainers.length} parent(s)</b>.<br><br>This action cannot be undone.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save All Parents',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    // Submit the form via AJAX to handle the response
                    const formData = new FormData(document.getElementById('violationForm'));

                    fetch("{{ route('adviser.parents.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        // Check if response contains success indicators
                        if (data.includes('success') || data.includes('parent(s) added successfully')) {
                            // Show success modal and redirect
                            showSuccessModalAndRedirect();
                            resolve();
                        } else {
                            // If there's an error, show the form again with errors
                            document.open();
                            document.write(data);
                            document.close();
                            reject(new Error('Save failed - server returned error'));
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
    });

    // Clear error styling on input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('form-control')) {
            e.target.style.borderColor = '#ddd';
        }
    });
</script>
@endsection
