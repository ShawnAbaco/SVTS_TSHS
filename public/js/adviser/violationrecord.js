
  document.addEventListener("DOMContentLoaded", function () {
  const archive = []; // store archived rows

  // Trash button (move selected to archive)
  document.querySelector(".btn-trash-small").addEventListener("click", function () {
    const rows = document.querySelectorAll("#violationTable tbody tr");
    rows.forEach(row => {
      const checkbox = row.querySelector(".rowCheckbox");
      if (checkbox && checkbox.checked) {
        archive.push(row.innerHTML); // save row content
        row.remove(); // remove from main table
      }
    });
    alert("Selected records moved to archive.");
  });

  // Open Archive Modal
  document.getElementById("archivesBtn").addEventListener("click", function () {
    const tbody = document.querySelector("#archiveTable tbody");
    tbody.innerHTML = "";

    archive.forEach((rowHtml, index) => {
      const tr = document.createElement("tr");
      tr.innerHTML = rowHtml;

      // replace Actions column with Restore button
      tr.querySelector("td:last-child").innerHTML =
        `<button class="btn-info restoreBtn" data-index="${index}">
           <i class="fas fa-undo"></i> Restore
         </button>`;

      tbody.appendChild(tr);
    });

    openModal("archiveModal");
  });

  // Restore from archive
  document.querySelector("#archiveTable tbody").addEventListener("click", function (e) {
    if (e.target.closest(".restoreBtn")) {
      const btn = e.target.closest(".restoreBtn");
      const index = btn.getAttribute("data-index");

      // restore row to main table
      const tbody = document.querySelector("#violationTable tbody");
      const tr = document.createElement("tr");
      tr.innerHTML = archive[index];
      tbody.appendChild(tr);

      // remove from archive
      archive.splice(index, 1);
      btn.closest("tr").remove();
    }
  });

  // Search inside archive
  document.getElementById("archiveSearch").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#archiveTable tbody tr").forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // --- Keep modal helpers ---
  window.openModal = function(id) {
    document.getElementById(id).classList.add("show");
  };
  window.closeModal = function(id) {
    document.getElementById(id).classList.remove("show");
  };
});
     // Dropdown functionality - auto close others & scroll
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        // close all other dropdowns
        dropdowns.forEach(otherBtn => {
            if (otherBtn !== this) {
                otherBtn.nextElementSibling.classList.remove('show');
                otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
            }
        });

        // toggle clicked dropdown
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
            container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';

        // scroll into view if dropdown is opened
        if(container.classList.contains('show')){
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  // --- Modal controls ---
  function openModal(id) { document.getElementById(id).classList.add("show"); }
  function closeModal(id) { document.getElementById(id).classList.remove("show"); }
  window.onclick = function(event) {
    let addModal = document.getElementById("addModal");
    let editModal = document.getElementById("editModal");
    if (event.target === addModal) closeModal("addModal");
    if (event.target === editModal) closeModal("editModal");
  };

  // --- Open Add Modal ---
  window.openAddModal = function() { openModal("addModal"); };

  // --- Live Search ---
  document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("#violationTable tbody tr").forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // --- Logout ---
  window.logout = function() {
    if (confirm("Are you sure you want to log out?")) {
      window.location.href = "/adviser/login";
    }
  };

  // --- Handle Add (AJAX POST) ---
  document.getElementById("addViolationForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken },
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const v = data.violation;
          let tbody = document.querySelector("#violationTable tbody");

          let newRow = document.createElement("tr");
          newRow.setAttribute("data-id", v.violation_id);
          newRow.setAttribute("data-student-id", v.violator_id);

          newRow.innerHTML = `
            <td>${v.student ? v.student.student_fname + " " + v.student.student_lname : "N/A"}</td>
            <td>${v.offense ? v.offense.offense_type : "N/A"}</td>
            <td>${v.violation_incident}</td>
            <td>${v.violation_date}</td>
            <td>${v.violation_time}</td>
            <td>${v.offense ? v.offense.sanction_consequences : "N/A"}</td>
            <td>
              <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
            </td>
          `;
          tbody.appendChild(newRow);

          closeModal("addModal");
          document.getElementById("addViolationForm").reset();
          alert("Violation added successfully!");
        }
      })
      .catch(err => console.error("Error:", err));
  });

  // --- Event Delegation for Edit & Delete ---
  document.querySelector("#violationTable tbody").addEventListener("click", function(e) {
    const row = e.target.closest("tr");
    if (!row) return;

    // Handle Edit
    if (e.target.closest(".btn-edit")) {
      const id = row.getAttribute("data-id");
      const studentId = row.getAttribute("data-student-id");
      const cells = row.querySelectorAll("td");

      document.getElementById("editViolationId").value = id;
      document.getElementById("editStudent").value = studentId;
      document.getElementById("editIncident").value = cells[2].innerText;
      document.getElementById("editDate").value = cells[3].innerText;
      document.getElementById("editTime").value = cells[4].innerText;

      openModal("editModal");
    }

    // Handle Delete
    if (e.target.closest(".btn-delete")) {
      if (!confirm("Are you sure you want to delete this violation?")) return;
      const id = row.getAttribute("data-id");

      fetch(`/adviser/violations/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": csrfToken }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Violation deleted successfully!");
          row.remove();
        }
      })
      .catch(err => console.error("Error:", err));
    }
  });

  // --- Handle Update (AJAX PUT) ---
  document.getElementById("editViolationForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const id = document.getElementById("editViolationId").value;
    const formData = new FormData(this);

    fetch(`/adviser/violation/${id}`, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken, "X-HTTP-Method-Override": "PUT" },
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Violation updated successfully!");
          location.reload();
        }
      });
  });
});
// --- Select All Checkboxes ---
document.getElementById("selectAll").addEventListener("change", function () {
  const checkboxes = document.querySelectorAll(".rowCheckbox");
  checkboxes.forEach(cb => cb.checked = this.checked);
});

// --- Keep Select All in sync ---
document.addEventListener("change", function (e) {
  if (e.target.classList.contains("rowCheckbox")) {
    const all = document.querySelectorAll(".rowCheckbox");
    const checked = document.querySelectorAll(".rowCheckbox:checked");
    document.getElementById("selectAll").checked = all.length === checked.length;
  }
});
