@extends('adviser.NewAdviser.layout')

@section('content')
    <div class="main-container">

        <!-- Flash Messages -->
        <div class="alert-messages">
            <!-- Flash messages would appear here -->
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Create Adviser</h2>
            <div class="actions">

                <div class="buttons-row">
                    <button type="button" class="btn-Add-Violation" id="btnAddViolation">
                        <i class="fas fa-plus-circle"></i> Add Another Adviser
                    </button>
                    <button type="submit" class="btn-save" form="violationForm">
                        <i class="fas fa-save"></i> Save All
                    </button>
                </div>
            </div>
        </div>

        <!-- Adviser Form -->
        <form id="violationForm" method="POST" action="{{ route('adviser.advisers.store') }}">
            @csrf
            <div class="parents-wrapper" id="parentsWrapper">
                <!-- Adviser forms will be dynamically added here -->
            </div>
        </form>

    </div>

    <script>
        let adviserCount = 0;

        // Initialize with one adviser form
        document.addEventListener('DOMContentLoaded', function() {
            addAdviserForm();
        });

        // Add new adviser form
        document.getElementById('btnAddViolation').addEventListener('click', function() {
            addAdviserForm();
            updateLayout();
        });

        function addAdviserForm() {
            adviserCount++;

            const parentsWrapper = document.getElementById('parentsWrapper');
            const newAdviser = document.createElement('div');
            newAdviser.className = 'parent-container';
            newAdviser.innerHTML = `
            <div class="parent-header">
                <span class="parent-title">Adviser #${adviserCount}</span>
                <button type="button" class="remove-parent" onclick="removeAdviser(this)">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="adviser_fname_${adviserCount}">First Name *</label>
                    <input type="text" id="adviser_fname_${adviserCount}" name="advisers[${adviserCount-1}][adviser_fname]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="adviser_lname_${adviserCount}">Last Name *</label>
                    <input type="text" id="adviser_lname_${adviserCount}" name="advisers[${adviserCount-1}][adviser_lname]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Sex</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="adviser_sex_male_${adviserCount}" name="advisers[${adviserCount-1}][adviser_sex]" value="male">
                            <label for="adviser_sex_male_${adviserCount}">Male</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="adviser_sex_female_${adviserCount}" name="advisers[${adviserCount-1}][adviser_sex]" value="female">
                            <label for="adviser_sex_female_${adviserCount}">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="adviser_sex_other_${adviserCount}" name="advisers[${adviserCount-1}][adviser_sex]" value="other">
                            <label for="adviser_sex_other_${adviserCount}">Other</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="adviser_email_${adviserCount}">Email *</label>
                    <input type="email" id="adviser_email_${adviserCount}" name="advisers[${adviserCount-1}][adviser_email]" class="form-control" required>
                </div>

                <div class="form-group password-wrapper">
                    <label for="adviser_password_${adviserCount}">Password *</label>
                    <input type="password" id="adviser_password_${adviserCount}" name="advisers[${adviserCount-1}][adviser_password]" class="form-control" required>
                    <img src="{{ asset('images/hide.png') }}"
                        class="toggle-password"
                        alt="Toggle Password"
                        data-target="adviser_password_${adviserCount}">
                    </div>


                <div class="form-group">
                    <label for="adviser_contactinfo_${adviserCount}">Contact Information *</label>
                    <input type="text" id="adviser_contactinfo_${adviserCount}" name="advisers[${adviserCount-1}][adviser_contactinfo]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="adviser_section_${adviserCount}">Section *</label>
                    <input type="text" id="adviser_section_${adviserCount}" name="advisers[${adviserCount-1}][adviser_section]" class="form-control" required>
                </div>

                    <div class="form-group">
                    <label for="adviser_gradelevel_${adviserCount}">Grade Level *</label>
                    <select id="adviser_gradelevel_${adviserCount}"
                            name="advisers[${adviserCount-1}][adviser_gradelevel]"
                            class="form-control"
                            required>
                        <option value="" disabled selected>Select Grade Level</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>
            </div>
        `;

            parentsWrapper.appendChild(newAdviser);
        }

        // Remove adviser form
        function removeAdviser(button) {
            const adviserContainers = document.querySelectorAll('.parent-container');
            if (adviserContainers.length > 1) {
                button.closest('.parent-container').remove();
                updateAdviserNumbers();
                updateLayout();
            } else {
                alert('You need at least one adviser form.');
            }
        }

       // Update numbering
function updateAdviserNumbers() {
    const adviserContainers = document.querySelectorAll('.parent-container');
    adviserContainers.forEach((container, index) => {
        container.querySelector('.parent-title').textContent = `Adviser #${index + 1}`;

        // Update all form elements (both input and select)
        const formElements = container.querySelectorAll('input, select, textarea');
        formElements.forEach(element => {
            const name = element.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                element.setAttribute('name', newName);

                // Also update ID if it exists
                const oldId = element.getAttribute('id');
                if (oldId) {
                    const newId = oldId.replace(/_(\d+)$/, `_${index + 1}`);
                    element.setAttribute('id', newId);

                    // Update label "for" attribute if it exists
                    const label = container.querySelector(`label[for="${oldId}"]`);
                    if (label) {
                        label.setAttribute('for', newId);
                    }
                }
            }
        });
    });
    adviserCount = adviserContainers.length;
}
        // Layout adjustments
        function updateLayout() {
            const containers = document.querySelectorAll('.parent-container');
            const wrapper = document.getElementById('parentsWrapper');
            containers.forEach(c => {
                c.style.flex = '1 1 400px';
                c.style.maxWidth = '600px';
            });
            if (containers.length === 1) {
                containers[0].style.maxWidth = '800px';
                wrapper.style.justifyContent = 'center';
            } else {
                wrapper.style.justifyContent = 'flex-start';
            }
        }

        // Validation
        document.getElementById('violationForm').addEventListener('submit', function(e) {
            const adviserContainers = document.querySelectorAll('.parent-container');
            let isValid = true;

            adviserContainers.forEach((container, index) => {
                const firstName = container.querySelector(
                `input[name="advisers[${index}][adviser_fname]"]`);
                const lastName = container.querySelector(`input[name="advisers[${index}][adviser_lname]"]`);
                const email = container.querySelector(`input[name="advisers[${index}][adviser_email]"]`);
                const password = container.querySelector(
                    `input[name="advisers[${index}][adviser_password]"]`);
                const contactInfo = container.querySelector(
                    `input[name="advisers[${index}][adviser_contactinfo]"]`);
                const section = container.querySelector(
                `input[name="advisers[${index}][adviser_section]"]`);
                const gradelevel = container.querySelector(
                    `select[name="advisers[${index}][adviser_gradelevel]"]`); // CHANGED THIS LINE

                // Check all required fields
                const requiredFields = [firstName, lastName, email, password, contactInfo, section,
                    gradelevel
                ];

                requiredFields.forEach(input => {
                    if (!input || !input.value) {
                        isValid = false;
                        if (input) {
                            input.style.borderColor = '#e74c3c';
                        }
                    }
                });
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields (marked with *) before submitting.');
            }
        });

        // Clear red border on input
        document.addEventListener('input', e => {
            if (e.target.classList.contains('form-control')) {
                e.target.style.borderColor = '#ddd';
            }
        });



        // Password toggle logic
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('toggle-password')) {
                const targetId = e.target.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;

                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');

                // Change icon
                e.target.src = isPassword ?
                    "{{ asset('images/show.png') }}" :
                    "{{ asset('images/hide.png') }}";
            }
        });
    </script>
@endsection
