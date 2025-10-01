@extends('prefect.layout')

@section('content')
<div class="main-container">
    <style>


    </style>
</head>
<body>
    <div class="main-container">
        <!-- Flash Messages -->
        <div class="alert-messages">
            <!-- Flash messages would appear here -->
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <h2>Create Parents</h2>
            <div class="actions">
                <input type="search" placeholder="🔍 Search parent..." id="searchInput">

                <div class="buttons-row">
                    <button type="button" class="btn-Add-Violation" id="btnAddViolation">
                        <i class="fas fa-plus-circle"></i> Add Another Parent
                    </button>
                    <button type="submit" class="btn-save" form="violationForm">
                        <i class="fas fa-save"></i> Save All
                    </button>
                </div>
            </div>
        </div>

        <!-- Parent Container -->
        <form id="violationForm" method="POST"action="{{ route('parents.store') }}">
                @csrf
            <div class="parents-wrapper" id="parentsWrapper">
                <!-- Parent forms will be dynamically added here -->
            </div>
        </form>
    </div>

    <script>
        let parentCount = 0;

        // Initialize with one parent form
        document.addEventListener('DOMContentLoaded', function() {
            addParentForm();
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
                        <label for="parent_sex_${parentCount}">Sex</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="parent_sex_male_${parentCount}" name="parents[${parentCount-1}][parent_sex]" value="male">
                                <label for="parent_sex_male_${parentCount}">Male</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="parent_sex_female_${parentCount}" name="parents[${parentCount-1}][parent_sex]" value="female">
                                <label for="parent_sex_female_${parentCount}">Female</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="parent_sex_other_${parentCount}" name="parents[${parentCount-1}][parent_sex]" value="other">
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
                        <label for="parent_relationship_${parentCount}">Relationship</label>
                        <select id="parent_relationship_${parentCount}" name="parents[${parentCount-1}][parent_relationship]" class="form-control">
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

    document.getElementById('violationForm').addEventListener('submit', function(e) {
    const parentContainers = document.querySelectorAll('.parent-container');
    let isValid = true;

    parentContainers.forEach((container, index) => {
        const firstName = container.querySelector(`input[name="parents[${index}][parent_fname]"]`);
        const lastName = container.querySelector(`input[name="parents[${index}][parent_lname]"]`);
        const birthdate = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`);
        const contactInfo = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`);
        const email = container.querySelector(`input[name="parents[${index}][parent_email]"]`);

        if (!firstName.value || !lastName.value || !birthdate.value || !contactInfo.value) {
            isValid = false;
            if (!firstName.value) firstName.style.borderColor = '#e74c3c';
            if (!lastName.value) lastName.style.borderColor = '#e74c3c';
            if (!birthdate.value) birthdate.style.borderColor = '#e74c3c';
            if (!contactInfo.value) contactInfo.style.borderColor = '#e74c3c';
        }

        // Validate email only if not empty
        if (email.value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                isValid = false;
                email.style.borderColor = '#e74c3c';
                alert(`Please enter a valid email address for Parent #${index + 1}.`);
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
            }
        });
    </script>
@endsection
