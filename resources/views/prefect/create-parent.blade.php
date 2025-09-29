@extends('prefect.layout')

@section('content')
<div class="main-container">

    {{-- ✅ Flash Messages --}}
    @if(session('messages'))
        <div class="alert-messages">
            @foreach(session('messages') as $msg)
                <div class="alert-item">{!! $msg !!}</div>
            @endforeach
        </div>
    @endif

  <div class="toolbar">
    <h2>Create Parents</h2>
    <div class="actions">
<input type="search" placeholder="🔍 Search parent..." id="searchInput">
      {{-- <button class="btn-primary" id="createBtn">➕ Add Student</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button> --}}

         <!-- ======= ACTION BUTTONS ======= -->
    <form id="violationForm" method="POST" action="{{ route('parent.store') }}">
        @csrf
        <div class="buttons-row">
            <button type="button" class="btn-Add-Violation" id="btnAddViolation">
                <i class="fas fa-plus-circle"></i> Add Another Parent
            </button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save All
            </button>
        </div>
    </form>

    </div>
  </div>


            <!-- Parent Container -->
            <div class="parent-container" id="parentContainer">
                <div class="parent-header">
                    <span class="parent-title">Parent #1</span>
                    <button type="button" class="remove-parent" onclick="removeParent(this)">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="parent_fname">First Name *</label>
                        <input type="text" id="parent_fname" name="parents[0][parent_fname]" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_lname">Last Name *</label>
                        <input type="text" id="parent_lname" name="parents[0][parent_lname]" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_sex">Sex</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="parent_sex_male" name="parents[0][parent_sex]" value="male">
                                <label for="parent_sex_male">Male</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="parent_sex_female" name="parents[0][parent_sex]" value="female">
                                <label for="parent_sex_female">Female</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="parent_sex_other" name="parents[0][parent_sex]" value="other">
                                <label for="parent_sex_other">Other</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_birthdate">Birthdate *</label>
                        <input type="date" id="parent_birthdate" name="parents[0][parent_birthdate]" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_email">Email</label>
                        <input type="email" id="parent_email" name="parents[0][parent_email]" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_contactinfo">Contact Information *</label>
                        <input type="text" id="parent_contactinfo" name="parents[0][parent_contactinfo]" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_relationship">Relationship</label>
                        <select id="parent_relationship" name="parents[0][parent_relationship]" class="form-control">
                            <option value="">Select Relationship</option>
                            <option value="father">Father</option>
                            <option value="mother">Mother</option>
                            <option value="guardian">Guardian</option>
                            <option value="grandparent">Grandparent</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
    <script>
        let parentCount = 1;

        // Add new parent form
        document.getElementById('btnAddViolation').addEventListener('click', function() {
            parentCount++;
            
            const parentContainer = document.getElementById('parentContainer');
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
            
            parentContainer.parentNode.insertBefore(newParent, parentContainer.nextSibling);
        });

        // Remove parent form
        function removeParent(button) {
            const parentContainers = document.querySelectorAll('.parent-container');
            if (parentContainers.length > 1) {
                button.closest('.parent-container').remove();
                // Update parent numbers
                updateParentNumbers();
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
            });
            parentCount = parentContainers.length;
        }

        // Form validation
        document.getElementById('violationForm').addEventListener('submit', function(e) {
            const parentContainers = document.querySelectorAll('.parent-container');
            let isValid = true;
            
            parentContainers.forEach((container, index) => {
                const firstName = container.querySelector(`input[name="parents[${index}][parent_fname]"]`);
                const lastName = container.querySelector(`input[name="parents[${index}][parent_lname]"]`);
                const birthdate = container.querySelector(`input[name="parents[${index}][parent_birthdate]"]`);
                const contactInfo = container.querySelector(`input[name="parents[${index}][parent_contactinfo]"]`);
                
                if (!firstName.value || !lastName.value || !birthdate.value || !contactInfo.value) {
                    isValid = false;
                    // Highlight empty required fields
                    if (!firstName.value) firstName.style.borderColor = '#e74c3c';
                    if (!lastName.value) lastName.style.borderColor = '#e74c3c';
                    if (!birthdate.value) birthdate.style.borderColor = '#e74c3c';
                    if (!contactInfo.value) contactInfo.style.borderColor = '#e74c3c';
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
