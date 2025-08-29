<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parent List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  
 <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', Arial, sans-serif;
      background-color: #f4f6f9;
      color: #333;
      line-height: 1.6;
    }

    a { text-decoration: none; color: inherit; }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background: linear-gradient(135deg,rgb(7, 184, 228),rgb(13, 141, 205));
        color: #000;
        box-shadow: 2px 0 8px rgba(0,0,0,0.15);
        padding: 10px;
        position: fixed;
        height: 100%;
        overflow-y: auto;
        font-weight: bold;
    }

    .sidebar img {
        display: block;
        width: 150px;
        margin: 20px auto 10px;
        border-radius: 50%;
        background: #fff;
        padding: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .sidebar p {
        text-align: center;
        color: #000;
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: bold;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin-bottom: 8px;
    }

    .sidebar ul li a,
    .sidebar ul li {
        display: block;
        padding: 10px 15px;
        font-size: 16px;
        border-radius: 6px;
        color: #000;
        transition: background 0.3s ease;
        font-weight: bold;
    }

    .sidebar ul li:hover,
    .sidebar ul li.active {
        background: rgb(246, 246, 246);
    }

    .main-content {
      margin-left: 260px;
      padding: 20px;
    }

    .crud-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .crud-container h2 {
      margin-bottom: 20px;
      font-size: 24px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      text-align: center;
      border: 1px solid #ccc;
    }

    thead {
      background-color: #343a40;
      color: white;
    }

    .btn {
      padding: 5px 10px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-info {
      background-color: #17a2b8;
      color: white;
    }

    .btn-warning {
      background-color: #ffc107;
      color: black;
    }

    .btn-danger {
      background-color: #dc3545;
      color: white;
    }

    .btn i {
      margin-right: 5px;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background: #fff;
      padding: 20px;
      width: 100%;
      max-width: 500px;
      border-radius: 8px;
      position: relative;
    }

    .modal-content h5 {
      margin-bottom: 15px;
    }

    .modal-content .close {
      position: absolute;
      top: 10px;
      right: 15px;
      cursor: pointer;
      font-size: 18px;
    }

    .info-box p {
      margin: 8px 0;
      font-size: 16px;
    }
</style>
</head>
<body>
   <!-- Sidebar -->
    <div class="sidebar">
        <p>PREFECT DASHBOARD</p>
        <ul>
          <li><a href="{{ route('prefect.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
          <li><a href="{{ route('student.management') }}"><i class="fas fa-user-graduate"></i> Student List </a></li>
          <li><a href="{{ route('parent.list') }}"><i class="fas fa-users"></i> Parent List </a></li>
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
  <div class="main-content">
    <div class="crud-container">
      <h2>Parent List</h2>

      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Parent Fullname</th>
            <th>Address</th>
            <th>Contact Number</th>
            <th>Sex</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="parentTableBody">
          <tr>
            <td>1</td>
            <td>John Doe</td>
            <td>123 Main St</td>
            <td>09123456789</td>
            <td>Male</td>
            <td>
              <button class="btn btn-info info-btn"><i class="fas fa-info-circle"></i> Info</button>
              <button class="btn btn-warning edit-btn"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn btn-danger delete-btn"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Jane Smith</td>
            <td>456 Elm Ave</td>
            <td>09876543210</td>
            <td>Female</td>
            <td>
              <button class="btn btn-info info-btn"><i class="fas fa-info-circle"></i> Info</button>
              <button class="btn btn-warning edit-btn"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn btn-danger delete-btn"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Info Modal -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('infoModal').classList.remove('show')">&times;</span>
      <h5>Student Information</h5>
      <div class="info-box">
        <p><strong>Student Name:</strong> <span id="studentName">N/A</span></p>
        <p><strong>Adviser:</strong> <span id="adviserName">N/A</span></p>
        <p><strong>Relationship:</strong> <span id="relationship">N/A</span></p>
      </div>
    </div>
  </div>

  <script>
    const tableBody = document.getElementById("parentTableBody");
    const infoModal = document.getElementById("infoModal");

    // Sample student details data for demo
    const studentDetails = {
      1: { studentName: "Michael Doe", adviserName: "Mr. Santos", relationship: "Father" },
      2: { studentName: "Anna Smith", adviserName: "Mrs. Cruz", relationship: "Mother" }
    };

    tableBody.addEventListener("click", function(e) {
      if (e.target.classList.contains("info-btn") || e.target.closest(".info-btn")) {
        const row = e.target.closest("tr");
        const id = row.querySelector("td").textContent;

        // Populate modal with data
        document.getElementById("studentName").textContent = studentDetails[id].studentName;
        document.getElementById("adviserName").textContent = studentDetails[id].adviserName;
        document.getElementById("relationship").textContent = studentDetails[id].relationship;

        infoModal.classList.add("show");
      }

      if (e.target.classList.contains("delete-btn") || e.target.closest(".delete-btn")) {
        e.target.closest("tr").remove();
      }

      if (e.target.classList.contains("edit-btn") || e.target.closest(".edit-btn")) {
        alert("Edit functionality will be implemented later.");
      }
    });

    function logout() {
      fetch('/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      }).then(() => window.location.href='/prefect/login')
        .catch(error => console.error('Logout failed:', error));
    }
  </script>
</body>
</html>
