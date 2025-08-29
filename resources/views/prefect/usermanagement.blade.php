<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/USERS.css') }}">
</head>
<body>

 <!-- Sidebar -->
    <div class="sidebar">
        <p>PREFECT DASHBOARD</p>
        <ul>
          <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
            <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List </a></li>
            <li><a href="{{ route('parent.lists') }}"><i class="fas fa-user-graduate"></i> Parent List </a></li>
            <li><a href="{{ route('user.management') }}"><i class="fas fa-users"></i> Adviser</a></li>
            <li><a href="{{ route('violation.records') }}"><i class="fas fa-gavel"></i> Violation Record </a></li>
            <li><a href="{{ route('violation.appointments') }}"><i class="fas fa-bell"></i> Violation Appointments </a></li>
            <li><a href="{{ route('violation.anecdotals') }}"><i class="fas fa-chart-line"></i> Violation Anecdotal </a></li>
            <li><a href="{{ route('people.complaints') }}"><i class="fas fa-users"></i> Complaints</a></li>
            <li><a href="{{ route('complaints.appointments') }}"><i class="fas fa-cogs"></i> Complaints Appointments</a></li>
            <li><a href="{{ route('complaints.anecdotals') }}"><i class="fas fa-book"></i> Complaints Anecdotal</a></li>
            <li><a href="{{ route('offenses.sanctions') }}"><i class="fas fa-exclamation-triangle"></i> Offense&Sanctions </a></li>
             <li><a href="{{ route('report.generate') }}"><i class="fas fa-chart-line"></i> Reports </a></li>
            <li onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</li>
        </ul>
    </div>

<!-- Main Content -->
<main>
    <div style="padding:20px;">
        <div style="margin-bottom:20px;">
            <button class="btn btn-primary" onclick="openModal()">+ Create Adviser</button>
        </div>

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

<!-- Modal -->
<div class="modal" id="createAdviserModal">
    <div class="modal-content">
        <div class="modal-header">Create Adviser</div>
        <form action="/admin/advisers" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Adviser Name:</label>
                <input type="text" name="name" id="name" placeholder="Enter adviser's name" required>
            </div>
            <div class="form-group">
                <label for="email">Adviser Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter adviser's email" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number" placeholder="Enter contact number" required>
            </div>
            <div class="form-group">
                <label for="password">Adviser Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter a secure password" required>
                <button type="button" id="togglePassword" class="btn btn-secondary" style="margin-top:5px;">Show Password</button>
            </div>
            <div class="form-group">
                <label for="grade">Grade Level:</label>
                <select name="grade" id="grade" required>
                    <option value="">-- Select Grade --</option>
                    <option value="11">Grade 11</option>
                    <option value="12">Grade 12</option>
                </select>
            </div>
            <div class="form-group">
                <label for="section">Section:</label>
                <select name="section" id="section" required>
                    <option value="">-- Select Section --</option>
                    <option value="A">Section A</option>
                    <option value="B">Section B</option>
                    <option value="C">Section C</option>
                    <option value="D">Section D</option>
                    <option value="E">Section E</option>
                    <option value="F">Section F</option>
                    <option value="G">Section G</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button type="submit" class="btn btn-primary">Create Adviser</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById("createAdviserModal").style.display = "flex"; }
function closeModal() { document.getElementById("createAdviserModal").style.display = "none"; }

document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordField = document.getElementById("password");
    const type = passwordField.type === "password" ? "text" : "password";
    passwordField.type = type;
    this.textContent = type === "password" ? "Show Password" : "Hide Password";
});

function showEditModal(id, email) {
    const newEmail = prompt("Enter new email:", email);
    if(newEmail && newEmail !== email){
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

function showResetModal(id) {
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
