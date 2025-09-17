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

    input.invalid { border-color: red; }

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
    .small-modal { width: 350px; padding: 15px; }
    .small-modal .form-group { margin-bottom: 10px; }
    .small-modal label { font-size: 0.9rem; }
    .small-modal input, .small-modal select { width: 100%; padding: 6px 8px; font-size: 0.9rem; }
    .small-modal button { font-size: 0.85rem; padding: 6px 12px; }
    .small-modal .modal-header { font-size: 1rem; margin-bottom: 8px; }

    .modal-footer { text-align: right; margin-top: 10px; }
    #successMessage { color: green; margin-bottom: 10px; display: none; }

    /* Reset */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; font-weight: bold; transition: all 0.2s ease-in-out; }
body { display: flex; background: #f9f9f9; color: #111; }

/* Sidebar */
.sidebar {
  width: 230px;
  background: linear-gradient(135deg, #001f3f, #003366, #0066cc, #3399ff);
  background-repeat: no-repeat;
  background-attachment: fixed;
  color: #fff;
  height: 100vh;
  position: fixed;
  padding: 25px 15px;
  border-radius: 0 15px 15px 0;
  box-shadow: 2px 0 15px rgba(0,0,0,0.5);
  overflow-y: auto;
}

.sidebar h2 { margin-bottom: 30px; text-align: center; font-size: 22px; letter-spacing: 1px; color: #ffffff; text-transform: uppercase; border-bottom: 2px solid rgba(255, 255, 255, 0.15); padding-bottom: 10px; }
.sidebar ul { list-style: none; }
.sidebar ul li { padding: 12px 14px; display: flex; align-items: center; cursor: pointer; border-radius: 10px; font-size: 15px; color: #e0e0e0; transition: background 0.3s, transform 0.2s; }
.sidebar ul li i { margin-right: 12px; color: #cfcfcf; min-width: 20px; font-size: 16px; }
.sidebar ul li:hover { background: #2d3f55; transform: translateX(5px); color: #fff; }
.sidebar ul li:hover i { color: #00e0ff; }
.sidebar ul li.active { background: #00aaff; color: #fff; border-left: 4px solid #ffffff; }
.sidebar ul li.active i { color: #fff; }
.sidebar ul li a { text-decoration: none; color: inherit; flex: 1; }
.section-title { margin: 20px 10px 8px; font-size: 11px; text-transform: uppercase; font-weight: bold; color: rgba(255, 255, 255, 0.6); letter-spacing: 1px; }

/* Dropdown */
.dropdown-container { display: none; list-style: none; padding-left: 25px; }
.dropdown-container li { padding: 10px; font-size: 14px; border-radius: 8px; color: #ddd; }
.dropdown-container li:hover { background: #3a4c66; color: #fff; }
.dropdown-btn .arrow { margin-left: auto; transition: transform 0.3s; }
.dropdown-btn.active .arrow { transform: rotate(180deg); }

/* Scrollbar */
.sidebar::-webkit-scrollbar { width: 6px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.25); border-radius: 3px; }

/* Main Content */
main, .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }

/* Cards */
.card, .crud-container { background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 25px; }

/* Tables */
.table, table { width: 100%; border-collapse: collapse; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.table thead, table thead { background: linear-gradient(90deg, #007bff, #00aaff); color: #fff; text-transform: uppercase; font-size: 13px; }
.table th, .table td, table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eaeaea; font-weight: 500; }
.table tr:nth-child(even), table tr:nth-child(even) { background-color: #f9f9f9; }
.table tr:hover, table tr:hover { background-color: #eef3fb; }

/* Buttons */
.btn { padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease-in-out; }
.btn i { margin-right: 5px; }
.btn-primary { background-color: #007bff; color: #fff; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }
.btn-warning { background-color: #ffc107; color: #212529; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-warning:hover { background-color: #e0a800; transform: translateY(-2px); }
.btn-danger { background-color: #dc3545; color: #fff; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
.btn-danger:hover { background-color: #bd2130; transform: translateY(-2px); }
.btn-secondary { background-color: #000; color: #fff; }

/* Make search input same height as buttons */
/* Search + Buttons Container */
.search-create {
    display: flex;
    justify-content: flex-end; /* aligns everything to the right */
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.search-create input[type="text"] {
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    height: 40px; /* same as buttons */
    width: 250px;
}

.search-create input[type="text"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 6px rgba(0,123,255,0.2);
    outline: none;
}

/* Buttons next to each other */
.top-buttons {
    display: flex;
    gap: 10px;
}

.top-buttons .btn {
    height: 40px; /* same as search input */
    display: flex;
    align-items: center;
    justify-content: center;
}



/* Modal */
.modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
.modal.show, .show-modal { display: flex; animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
.modal-content { background-color: #fff; margin: 10% auto; padding: 25px; border-radius: 10px; width: 100%; max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
.modal-header { display: flex; justify-content: space-between; font-size: 20px; margin-bottom: 15px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; }

/* Forms */
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ccc; font-weight: normal; font-size: 14px; transition: border-color 0.2s; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #007bff; outline: none; }

/* Responsive */
@media screen and (max-width:768px) {
  main, .main-content { margin-left: 0; padding: 15px; width: 100%; }
  table th, table td, .table th, .table td { font-size: 12px; padding: 8px; }
  .btn { font-size: 12px; padding: 6px 12px; }
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
    
            <h2>List of Advisers</h2>
    <div style="padding:20px;">
      <!-- Search and Create/Delete -->
<div class="search-create">
    <input type="text" id="searchInput" placeholder="Search adviser name...">
    <div class="top-buttons">
        <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Create</button>
        <button class="btn btn-danger" onclick="bulkDelete()"><i class="fas fa-trash"></i> Delete</button>
    </div>
</div>


        <div id="successMessage">Created successfully!</div>

        
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

        fetch('/admin/advisers',{
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
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
            } else { alert('Failed to create adviser'); }
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
                alert(data.success ? "Deleted successfully" : "Failed to delete.");
                if(data.success) location.reload();
            });
        }
    }

    function logout(){
        fetch('/logout',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
        .then(()=>window.location.href='/prefect/login').catch(console.error);
    }

    // Live search for advisers by name
    document.getElementById('searchInput').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#list-advisers tbody tr');
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    });
</script>
</body>
</html>
