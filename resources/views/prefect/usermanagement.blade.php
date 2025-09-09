<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/USERS.css') }}">
<style>
    /* Inline validation error styling */
    .error-msg {
        color: red;
        font-size: 0.85rem;
        margin-top: 3px;
        display: block;
    }

    input.invalid {
        border-color: red;
    }

    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        width: 500px;
        border-radius: 8px;
        max-width: 95%;
    }

    /* Small modal */
    .small-modal {
        width: 350px;
        padding: 15px;
    }

    .small-modal .form-group {
        margin-bottom: 10px;
    }

    .small-modal label {
        font-size: 0.9rem;
    }

    .small-modal input,
    .small-modal select {
        width: 100%;
        padding: 6px 8px;
        font-size: 0.9rem;
    }

    .small-modal button {
        font-size: 0.85rem;
        padding: 6px 12px;
    }

    .small-modal .modal-header {
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .modal-footer {
        text-align: right;
        margin-top: 10px;
    }

    #successMessage {
        color: green;
        margin-bottom: 10px;
        display: none;
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>PREFECT DASHBOARD</h2>
    <ul>
        <div class="section-title">Main</div>
        <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List</a></li>
        <li><a href="{{ route('parent.lists') }}"><i class="fas fa-users"></i> Parent List</a></li>
        <li class="active"><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
        <li class="dropdown-btn"><i class="fas fa-book"></i> Violations <i class="fas fa-caret-down arrow"></i></li>
        <ul class="dropdown-container">
            <li><a href="{{ route('violation.records') }}">Violation Record</a></li>
            <li><a href="{{ route('violation.appointments') }}">Violation Appointments</a></li>
            <li><a href="{{ route('violation.anecdotals') }}">Violation Anecdotal</a></li>
        </ul>
        <li class="dropdown-btn"><i class="fas fa-comments"></i> Complaints <i class="fas fa-caret-down arrow"></i></li>
        <ul class="dropdown-container">
            <li><a href="{{ route('people.complaints') }}">Complaints</a></li>
            <li><a href="{{ route('complaints.appointments') }}">Complaints Appointments</a></li>
            <li><a href="{{ route('complaints.anecdotals') }}">Complaints Anecdotal</a></li>
        </ul>
        <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense & Sanctions</a></li>
        <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
        <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
</div>

<!-- Main Content -->
<main>
    <div style="padding:20px;">
        <div style="margin-bottom:20px;">
            <button class="btn btn-primary" onclick="openModal()">+ Create Adviser</button>
        </div>

        <div id="successMessage">Created successfully!</div>

        <section id="list-advisers" class="card">
            <h2>List of Advisers</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advisers as $adviser)
                        <tr>
                            <td>{{ $adviser->adviser_fname . ' ' . $adviser->adviser_lname }}</td>
                            <td>{{ $adviser->adviser_email }}</td>
                            <td>{{ $adviser->adviser_contactinfo }}</td>
                            <td>{{ $adviser->adviser_gradelevel }}</td>
                            <td>{{ $adviser->adviser_section }}</td>
                            <td>
                                <button class="btn btn-primary" onclick="showEditModal('{{ $adviser->id }}','{{ $adviser->email }}')"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-warning" onclick="showResetModal('{{ $adviser->id }}')"><i class="fas fa-key"></i> Reset Password</button>
                                <button class="btn btn-danger" onclick="deleteAdviser('{{ $adviser->id }}')"><i class="fas fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<!-- Small Modal -->
<div class="modal" id="createAdviserModal">
    <div class="modal-content small-modal">
        <div class="modal-header">Create Adviser</div>
        <form id="adviserForm" novalidate>
            @csrf
            <div class="form-group">
                <label for="fname">First Name:</label>
                <input type="text" name="fname" id="fname" placeholder="First name" required pattern="[A-Za-z\s]+">
                <small class="error-msg" id="fnameError"></small>
            </div>
            <div class="form-group">
                <label for="lname">Last Name:</label>
                <input type="text" name="lname" id="lname" placeholder="Last name" required pattern="[A-Za-z\s]+">
                <small class="error-msg" id="lnameError"></small>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="example@email.com" required>
                <small class="error-msg" id="emailError"></small>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact:</label>
                <input type="text" name="contact_number" id="contact_number" placeholder="09XXXXXXXXX" required pattern="^09\d{9}$" title="Format: 09XXXXXXXXX (11 digits)">
                <small class="error-msg" id="contactError"></small>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter password" required>
                <button type="button" id="togglePassword" class="btn btn-secondary" style="margin-top:5px;">Show</button>
                <small class="error-msg" id="passwordError"></small>
            </div>
            <div class="form-group">
                <label for="grade">Grade:</label>
                <select name="grade" id="grade" required>
                    <option value="">Select</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <small class="error-msg" id="gradeError"></small>
            </div>
            <div class="form-group">
                <label for="section">Section:</label>
                <input type="text" name="section" id="section" placeholder="e.g. A, STEM-1" required pattern="[A-Za-z0-9\s-]+">
                <small class="error-msg" id="sectionError"></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Sidebar dropdown
    const sidebar = document.querySelector('.sidebar');
    const dropdowns = document.querySelectorAll('.dropdown-btn');

    dropdowns.forEach(btn => {
        btn.addEventListener('click', () => {
            const container = btn.nextElementSibling;
            dropdowns.forEach(otherBtn => {
                const otherContainer = otherBtn.nextElementSibling;
                if (otherBtn !== btn) {
                    otherBtn.classList.remove('active');
                    otherContainer.style.display = 'none';
                }
            });
            btn.classList.toggle('active');
            container.style.display = container.style.display === 'block' ? 'none' : 'block';
            const openDropdowns = document.querySelectorAll('.dropdown-container[style*="block"]').length;
            sidebar.style.overflowY = openDropdowns >= 1 ? 'auto' : 'hidden';
        });
    });

    // Modal
    function openModal() { document.getElementById("createAdviserModal").style.display = "flex"; }
    function closeModal() { document.getElementById("createAdviserModal").style.display = "none"; }

    // Password toggle
    document.getElementById("togglePassword").addEventListener("click", function () {
        const passwordField = document.getElementById("password");
        const type = passwordField.type === "password" ? "text" : "password";
        passwordField.type = type;
        this.textContent = type === "password" ? "Show" : "Hide";
    });

    // Form submission with AJAX
    const form = document.getElementById('adviserForm');
    const fnameInput = document.getElementById('fname');
    const lnameInput = document.getElementById('lname');
    const contactInput = document.getElementById('contact_number');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const gradeSelect = document.getElementById('grade');
    const sectionInput = document.getElementById('section');
    const successMsg = document.getElementById('successMessage');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const namePattern = /^[A-Za-z\s]+$/;

        // Validation (similar as before)
        function validateField(input, pattern=null, errorId, emptyMsg, patternMsg=null){
            if(!input.value.trim()){
                document.getElementById(errorId).textContent = emptyMsg;
                input.classList.add('invalid'); valid=false;
            } else if(pattern && !pattern.test(input.value.trim())){
                document.getElementById(errorId).textContent = patternMsg;
                input.classList.add('invalid'); valid=false;
            } else {
                document.getElementById(errorId).textContent = '';
                input.classList.remove('invalid');
            }
        }

        validateField(fnameInput,namePattern,'fnameError','Please fill out this field','Only letters and spaces allowed');
        validateField(lnameInput,namePattern,'lnameError','Please fill out this field','Only letters and spaces allowed');
        validateField(emailInput,null,'emailError','Please fill out this field');
        validateField(contactInput,/^09\d{9}$/,'contactError','Please fill out this field','Format: 09XXXXXXXXX (11 digits)');
        validateField(passwordInput,null,'passwordError','Please fill out this field');
        validateField(gradeSelect,null,'gradeError','Please select grade');
        validateField(sectionInput,/^[A-Za-z0-9\s-]+$/,'sectionError','Please fill out this field','Invalid format');

        if(!valid) return;

        // Submit via fetch
        fetch('/admin/advisers',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({
                adviser_fname: fnameInput.value,
                adviser_lname: lnameInput.value,
                adviser_email: emailInput.value,
                adviser_contactinfo: contactInput.value,
                adviser_password: passwordInput.value,
                adviser_gradelevel: gradeSelect.value,
                adviser_section: sectionInput.value
            })
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                successMsg.style.display='block';
                setTimeout(()=>successMsg.style.display='none',3000);
                form.reset();
                closeModal();
            } else {
                alert('Failed to create adviser');
            }
        })
        .catch(err=>console.error(err));
    });

    function showEditModal(id,email){
        const newEmail = prompt("Enter new email:", email);
        if(newEmail && newEmail!==email){
            fetch(`/admin/advisers/${id}`,{
                method:"PUT",
                headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}"},
                body: JSON.stringify({email:newEmail})
            }).then(res=>res.json()).then(data=>{
                alert(data.success ? "Updated successfully." : "Failed to update.");
                if(data.success) location.reload();
            });
        }
    }

    function showResetModal(id){
        const newPassword = prompt("Enter a new password:");
        if(newPassword){
            fetch(`/admin/advisers/${id}/reset-password`,{
                method:"PATCH",
                headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}"},
                body: JSON.stringify({password:newPassword})
            }).then(res=>res.json()).then(data=>{
                alert(data.success ? "Password reset successfully." : "Failed to reset.");
            });
        }
    }

    function deleteAdviser(id){
        if(confirm("Are you sure you want to delete this adviser?")){
            fetch(`/admin/advisers/${id}`,{ method:"DELETE", headers:{"X-CSRF-TOKEN":"{{ csrf_token() }}"}})
            .then(res=>res.json()).then(data=>{
                alert(data.success ? "Deleted successfully." : "Failed to delete.");
                if(data.success) location.reload();
            });
        }
    }

    function logout(){
        fetch('/logout',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
        .then(()=>window.location.href='/prefect/login').catch(console.error);
    }
</script>
</body>
</html>
