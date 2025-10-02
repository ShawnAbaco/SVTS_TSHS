@extends('prefect.layout')

@section('content')
<div class="main-container">

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

  <div class="toolbar">
    <h2>Create Complaint Record</h2>
    <div class="actions">
      <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}">
        @csrf
        <div class="buttons-row">
          <button type="button" class="btn-Add-Complaint" id="btnAddComplaint" disabled>
            <i class="fas fa-plus-circle"></i> Add Another Complaint
          </button>
          <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Save All Records
          </button>
        </div>

        <!-- ✅ Hidden fields container for form submission -->
        <div id="hiddenFieldsContainer"></div>
      </form>
    </div>
  </div>

  <!-- ======= CONTENT WRAPPER (FORM + SUMMARY) ======= -->
  <div class="content-wrapper">
      <!-- Left: Multiple Forms -->
      <div class="forms-container">
          <div class="form-box shadow-card complaint-form">
              <div class="form-header">
                  <h3 class="section-title"><i class="fas fa-user"></i> Complaint Details</h3>
                  <button type="button" class="btn-remove-form">&times;</button>
              </div>

              <label>Complainant(s) <span class="note">(single or comma-separated for multiple)</span></label>
              <input type="text" class="complainant-input" placeholder="e.g. Kent Zyrone, Shawn Laurence" data-ids="">
              <div class="results complainant-results"></div>

              <label>Respondent(s) <span class="note">(comma-separated for multiple)</span></label>
              <input type="text" class="respondent-input" placeholder="e.g. Jonathan, Junald, Jayvee Charles" data-ids="">
              <div class="results respondent-results"></div>

              <h3 class="section-title"><i class="fas fa-info-circle"></i> Complaint Information</h3>
              <div class="row-fields">
                  <div class="field">
                      <label>Offense</label>
                      <input type="text" class="offense-input" placeholder="Type offense (e.g., Tardiness, Bullying, Cheating)...">
                      <input type="hidden" class="offense-id">
                      <div class="results offense-results"></div>
                  </div>

                  <div class="field small">
                      <label>Date</label>
                      <input type="date" class="date-input" value="{{ date('Y-m-d') }}">
                  </div>

                  <div class="field small">
                      <label>Time</label>
                      <input type="time" class="time-input" value="{{ date('H:i') }}">
                  </div>

                  <div class="field large">
                      <label>Incident Details</label>
                      <textarea rows="3" class="incident-input" placeholder="Briefly describe the incident..."></textarea>
                  </div>
              </div>

              <button type="button" class="btn-show-all">
                  <i class="fas fa-eye"></i> Show All
              </button>
          </div>
      </div>

      <!-- Right: Complaints Summary -->
      <section class="complaints-section">
          <div class="summary-header">
              <h3 class="section-title"><i class="fas fa-list"></i> Complaints Summary</h3>
              <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
          </div>

          <!-- Summary container (all groups appended here) -->
          <div id="allComplaintGroups" class="complaintsWrapper"></div>
      </section>
  </div>

</div>

{{-- ✅ JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const studentSearchUrl = "{{ route('complaints.search-students') }}";
const offenseSearchUrl = "{{ route('complaints.search-offenses') }}";

let complaintCount = 1;
let allComplaintsData = {};
let complaintCounter = 1;

function attachListeners(box, id) {
  const complainantInput = box.querySelector(".complainant-input");
  const respondentInput  = box.querySelector(".respondent-input");
  const offenseInput     = box.querySelector(".offense-input");
  const offenseId        = box.querySelector(".offense-id");
  const dateInput        = box.querySelector(".date-input");
  const timeInput        = box.querySelector(".time-input");
  const incidentInput    = box.querySelector(".incident-input");
  const complainantResults = box.querySelector(".complainant-results");
  const respondentResults  = box.querySelector(".respondent-results");
  const offenseResults     = box.querySelector(".offense-results");
  const showAllBtn       = box.querySelector(".btn-show-all");

  box.dataset.groupId = id;

  function allFieldsFilled() {
    return complainantInput.value.trim() &&
           respondentInput.value.trim() &&
           offenseInput.value.trim() &&
           offenseId.value.trim() &&
           dateInput.value &&
           timeInput.value &&
           incidentInput.value.trim();
  }

  function toggleAddComplaintButton() {
    const addBtn = document.getElementById("btnAddComplaint");
    addBtn.disabled = !allFieldsFilled();
  }

  box.querySelectorAll("input, textarea").forEach(input => input.addEventListener("input", toggleAddComplaintButton));

  // Student search for complainant - UPDATED FOR MULTIPLE SELECTION
  complainantInput.addEventListener("keyup", function() {
    let parts = this.value.split(",");
    let query = parts[parts.length - 1].trim();

    if (query.length < 2) {
        complainantResults.innerHTML = "";
        return;
    }

    $.post(studentSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data) {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = data;
        const selectedIds = (complainantInput.dataset.ids || "").split(",").filter(id => id.trim() !== "");
        complainantResults.innerHTML = "";

        Array.from(tempDiv.querySelectorAll(".student-item"))
          .filter(item => !selectedIds.includes(item.dataset.id))
          .forEach(item => {
            const clonedItem = item.cloneNode(true);
            complainantResults.appendChild(clonedItem);
            clonedItem.addEventListener("click", () => {
              const currentIds = (complainantInput.dataset.ids || "").split(",").filter(id => id.trim() !== "");
              if (!currentIds.includes(clonedItem.dataset.id)) {
                const fullName = clonedItem.textContent.trim();
                const currentNames = complainantInput.value.split(",").map(n => n.trim()).filter(n => n !== "");

                // Replace the last incomplete name with the full selected name
                if (currentNames.length > 0 && query.length > 0) {
                  const lastIndex = currentNames.length - 1;
                  if (currentNames[lastIndex].toLowerCase().includes(query.toLowerCase())) {
                    currentNames[lastIndex] = fullName;
                  } else {
                    currentNames.push(fullName);
                  }
                } else {
                  currentNames.push(fullName);
                }

                complainantInput.value = currentNames.join(", ");
                currentIds.push(clonedItem.dataset.id);
                complainantInput.dataset.ids = currentIds.join(",");
              }
              complainantResults.innerHTML = "";
              toggleAddComplaintButton();
            });
          });
    }).fail(function(xhr, status, error) {
        console.error('Complainant search failed:', error);
        complainantResults.innerHTML = '<div class="no-results">Search failed</div>';
    });
  });

  // Student search for respondent - SAME LOGIC AS COMPLAINANT
  respondentInput.addEventListener("keyup", function() {
    let parts = this.value.split(",");
    let query = parts[parts.length - 1].trim();

    if (query.length < 2) {
        respondentResults.innerHTML = "";
        return;
    }

    $.post(studentSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data) {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = data;
        const selectedIds = (respondentInput.dataset.ids || "").split(",").filter(id => id.trim() !== "");
        respondentResults.innerHTML = "";

        Array.from(tempDiv.querySelectorAll(".student-item"))
          .filter(item => !selectedIds.includes(item.dataset.id))
          .forEach(item => {
            const clonedItem = item.cloneNode(true);
            respondentResults.appendChild(clonedItem);
            clonedItem.addEventListener("click", () => {
              const currentIds = (respondentInput.dataset.ids || "").split(",").filter(id => id.trim() !== "");
              if (!currentIds.includes(clonedItem.dataset.id)) {
                const fullName = clonedItem.textContent.trim();
                const currentNames = respondentInput.value.split(",").map(n => n.trim()).filter(n => n !== "");

                if (currentNames.length > 0 && query.length > 0) {
                  const lastIndex = currentNames.length - 1;
                  if (currentNames[lastIndex].toLowerCase().includes(query.toLowerCase())) {
                    currentNames[lastIndex] = fullName;
                  } else {
                    currentNames.push(fullName);
                  }
                } else {
                  currentNames.push(fullName);
                }

                respondentInput.value = currentNames.join(", ");
                currentIds.push(clonedItem.dataset.id);
                respondentInput.dataset.ids = currentIds.join(",");
              }
              respondentResults.innerHTML = "";
              toggleAddComplaintButton();
            });
          });
    }).fail(function(xhr, status, error) {
        console.error('Respondent search failed:', error);
        respondentResults.innerHTML = '<div class="no-results">Search failed</div>';
    });
  });

  // Offense search
  offenseInput.addEventListener("keyup", function() {
    let query = this.value;

    if (query.length < 2){
        offenseResults.innerHTML = "";
        return;
    }

    $.post(offenseSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data){
        offenseResults.innerHTML = data;

        offenseResults.querySelectorAll(".offense-item").forEach(item => {
            item.onclick = () => {
                offenseInput.value = item.textContent;
                offenseId.value = item.dataset.id;
                offenseResults.innerHTML = "";
                toggleAddComplaintButton();
            };
        });
    }).fail(function(xhr, status, error) {
        console.error('Offense search failed:', error);
        offenseResults.innerHTML = '<div class="no-results">Search failed</div>';
    });
  });

  // Show all button - ENHANCED FOR ALL COMBINATIONS
  showAllBtn.onclick = () => {
    if (!allFieldsFilled()) {
      Swal.fire("Incomplete!", "Please fill all fields before showing summary.", "warning");
      return;
    }

    const complainantNames = complainantInput.value.split(",").map(c => c.trim()).filter(c => c !== "");
    const complainantIds = (complainantInput.dataset.ids || "").split(",").map(i => i.trim()).filter(i => i !== "");
    const respondentNames = respondentInput.value.split(",").map(r => r.trim()).filter(r => r !== "");
    const respondentIds = (respondentInput.dataset.ids || "").split(",").map(i => i.trim()).filter(i => i !== "");
    const offense = offenseInput.value.trim();
    const offenseVal = offenseId.value;
    const date = dateInput.value;
    const time = timeInput.value;
    const incident = incidentInput.value.trim();
    const groupId = box.dataset.groupId;

    // Validate selections
    if (complainantNames.length !== complainantIds.length) {
        Swal.fire("Error!", "Some complainant selections are invalid. Please reselect complainants.", "error");
        return;
    }

    if (respondentNames.length !== respondentIds.length) {
        Swal.fire("Error!", "Some respondent selections are invalid. Please reselect respondents.", "error");
        return;
    }

    if (complainantNames.length === 0 || respondentNames.length === 0) {
        Swal.fire("Error!", "Please select at least one complainant and one respondent.", "error");
        return;
    }

    // ✅ Determine pairing strategy
    let pairs = [];

    if (complainantNames.length === 1 && respondentNames.length >= 1) {
        // SINGLE complainant → MULTIPLE respondents (1:N)
        const singleComplainant = complainantNames[0];
        const singleComplainantId = complainantIds[0];

        respondentNames.forEach((respName, index) => {
            pairs.push({
                complainantName: singleComplainant,
                complainantId: singleComplainantId,
                respondentName: respName,
                respondentId: respondentIds[index]
            });
        });

    } else if (complainantNames.length >= 1 && respondentNames.length === 1) {
        // MULTIPLE complainants → SINGLE respondent (N:1)
        const singleRespondent = respondentNames[0];
        const singleRespondentId = respondentIds[0];

        complainantNames.forEach((compName, index) => {
            pairs.push({
                complainantName: compName,
                complainantId: complainantIds[index],
                respondentName: singleRespondent,
                respondentId: singleRespondentId
            });
        });

    } else if (complainantNames.length === respondentNames.length) {
        // MULTIPLE complainants → MULTIPLE respondents (1:1 pairing)
        complainantNames.forEach((compName, index) => {
            pairs.push({
                complainantName: compName,
                complainantId: complainantIds[index],
                respondentName: respondentNames[index],
                respondentId: respondentIds[index]
            });
        });

    } else {
        // UNEQUAL multiple complainants & respondents - create all combinations
        complainantNames.forEach((compName, compIndex) => {
            respondentNames.forEach((respName, respIndex) => {
                pairs.push({
                    complainantName: compName,
                    complainantId: complainantIds[compIndex],
                    respondentName: respName,
                    respondentId: respondentIds[respIndex]
                });
            });
        });
    }

    // ✅ Store complaint data
    allComplaintsData[groupId] = {
        pairs: pairs,
        offense: offense,
        offenseVal: offenseVal,
        date: date,
        time: time,
        incident: incident
    };

    // ✅ Update hidden fields for form submission
    updateHiddenFields();

    // ✅ Create group container
    let groupContainer = document.querySelector(`#group-${groupId}`);
    if (!groupContainer) {
      groupContainer = document.createElement("div");
      groupContainer.classList.add("complaint-group");
      groupContainer.id = `group-${groupId}`;
      groupContainer.innerHTML = `<div class="complaint-group-title">Complaint Group #${groupId}</div>`;
      document.getElementById("allComplaintGroups").appendChild(groupContainer);
    }

    // ✅ Clear existing cards before regenerating
    groupContainer.querySelectorAll(".complaint-card").forEach(card => card.remove());

    // ✅ Generate cards - ONE card per pair
    pairs.forEach((pair, index) => {
        const uniqueComplaintId = complaintCounter++;

        const card = document.createElement("div");
        card.classList.add("complaint-card");
        card.dataset.complaintId = uniqueComplaintId;
        card.dataset.groupId = groupId;
        card.dataset.complainantId = pair.complainantId;
        card.dataset.respondentId = pair.respondentId;
        card.innerHTML = `
          <div class="btn-remove">&times;</div>
          <p><b>Complaint ID:</b> #${uniqueComplaintId}</p>
          <p><b>Complainant:</b> ${pair.complainantName} (ID: ${pair.complainantId})</p>
          <p><b>Respondent:</b> ${pair.respondentName} (ID: ${pair.respondentId})</p>
          <p style="color: orange;"><b>Offense:</b> ${offense} (ID: ${offenseVal})</p>
          <p><b>Date:</b> ${new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })}</p>
          <p><b>Time:</b> ${new Date("1970-01-01T" + time).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            })}</p>
          <p><b>Incident:</b> ${incident}</p>
        `;
        groupContainer.appendChild(card);

        // Remove card functionality
        card.querySelector(".btn-remove").onclick = () => {
            card.remove();
            removeComplaintFromGroup(groupId, pair.complainantId, pair.respondentId);
        };
    });

    toggleAddComplaintButton();
    Swal.fire("Success!", `Added ${pairs.length} complaint(s) to summary.`, "success");
  };

  // Remove form
  box.querySelector(".btn-remove-form").addEventListener("click", () => {
    if (document.querySelectorAll(".complaint-form").length > 1) {
      const groupId = box.dataset.groupId;
      delete allComplaintsData[groupId];
      updateHiddenFields();

      const group = document.querySelector(`#group-${groupId}`);
      if (group) group.remove();
      box.remove();
      toggleAddComplaintButton();
    } else {
      Swal.fire("Warning", "At least one complaint form is required.", "warning");
    }
  });
}

// Update hidden fields for form submission - ENHANCED FOR ALL SCENARIOS
function updateHiddenFields() {
    const container = document.getElementById('hiddenFieldsContainer');
    container.innerHTML = '';

    Object.keys(allComplaintsData).forEach(groupId => {
        const group = allComplaintsData[groupId];

        if (!group.pairs || group.pairs.length === 0) return;

        // For EACH pair, create a separate complaint entry
        group.pairs.forEach((pair, index) => {
            const complaintIndex = `${groupId}_${index}`;

            // Add complainant ID
            const compInput = document.createElement('input');
            compInput.type = 'hidden';
            compInput.name = `complaints[${complaintIndex}][complainant_id]`;
            compInput.value = pair.complainantId;
            container.appendChild(compInput);

            // Add respondent ID
            const respInput = document.createElement('input');
            respInput.type = 'hidden';
            respInput.name = `complaints[${complaintIndex}][respondent_id]`;
            respInput.value = pair.respondentId;
            container.appendChild(respInput);

            // Add offense, date, time, incident
            const offenseInput = document.createElement('input');
            offenseInput.type = 'hidden';
            offenseInput.name = `complaints[${complaintIndex}][offense_sanc_id]`;
            offenseInput.value = group.offenseVal;
            container.appendChild(offenseInput);

            const dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = `complaints[${complaintIndex}][date]`;
            dateInput.value = group.date;
            container.appendChild(dateInput);

            const timeInput = document.createElement('input');
            timeInput.type = 'hidden';
            timeInput.name = `complaints[${complaintIndex}][time]`;
            timeInput.value = group.time;
            container.appendChild(timeInput);

            const incidentInput = document.createElement('input');
            incidentInput.type = 'hidden';
            incidentInput.name = `complaints[${complaintIndex}][incident]`;
            incidentInput.value = group.incident;
            container.appendChild(incidentInput);
        });
    });

    console.log('Hidden fields updated. Total complaints:', getTotalComplaints());
}

// Remove specific complaint from group
function removeComplaintFromGroup(groupId, complainantId, respondentId) {
    const group = allComplaintsData[groupId];
    if (!group || !group.pairs) return;

    // Find and remove the specific pair
    const pairIndex = group.pairs.findIndex(pair =>
        pair.complainantId === complainantId && pair.respondentId === respondentId
    );

    if (pairIndex > -1) {
        group.pairs.splice(pairIndex, 1);

        // If no pairs left, remove the entire group
        if (group.pairs.length === 0) {
            delete allComplaintsData[groupId];
            const groupElement = document.querySelector(`#group-${groupId}`);
            if (groupElement) groupElement.remove();
        }

        updateHiddenFields();
    }
}

// Get total complaints count
function getTotalComplaints() {
    let total = 0;
    Object.keys(allComplaintsData).forEach(groupId => {
        const group = allComplaintsData[groupId];
        total += group.pairs ? group.pairs.length : 0;
    });
    return total;
}

// Add another complaint form
document.getElementById("btnAddComplaint").onclick = () => {
  const lastForm = document.querySelector(".complaint-form:last-child");
  const allFilled = [...lastForm.querySelectorAll("input, textarea")].every(input => input.value.trim());

  if (!allFilled) {
    Swal.fire("Incomplete!", "Please fill all fields in the current form first.", "warning");
    return;
  }

  complaintCount++;
  const originalBox = document.querySelector(".complaint-form");
  const clone = originalBox.cloneNode(true);

  clone.querySelectorAll("input, textarea").forEach(input => input.value = "");
  clone.querySelectorAll(".complainant-input, .respondent-input").forEach(input => input.dataset.ids = "");
  clone.querySelectorAll(".results").forEach(div => div.innerHTML = "");

  clone.querySelector(".date-input").value = "{{ date('Y-m-d') }}";
  clone.querySelector(".time-input").value = "{{ date('H:i') }}";

  clone.querySelector(".section-title").innerHTML = `<i class="fas fa-user"></i> Complaint Details (Form #${complaintCount})`;
  document.querySelector(".forms-container").appendChild(clone);
  attachListeners(clone, complaintCount);

  document.getElementById("btnAddComplaint").disabled = true;
};

// Form submission handler - ENHANCED DEBUGGING
document.getElementById('complaintForm').addEventListener('submit', function(e) {
  const totalComplaints = getTotalComplaints();

  if (totalComplaints === 0) {
    e.preventDefault();
    Swal.fire("No Complaints!", "Please add at least one complaint to the summary before saving.", "warning");
    return;
  }

  console.log('=== FORM SUBMISSION DEBUG ===');
  console.log('Total complaints to save:', totalComplaints);

  // Log all hidden fields
  const hiddenFields = document.getElementById('hiddenFieldsContainer').querySelectorAll('input');
  console.log('Hidden fields count:', hiddenFields.length);

  hiddenFields.forEach((field, index) => {
    console.log(`Field ${index}:`, field.name, '=', field.value);
  });

  // Create a FormData object to see what will be sent
  const formData = new FormData(this);
  console.log('FormData entries:');
  for (let [key, value] of formData.entries()) {
    console.log(key, ':', value);
  }

  // Show loading state
  const submitBtn = this.querySelector('button[type="submit"]');
  submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Saving ${totalComplaints} Complaint(s)...`;
  submitBtn.disabled = true;

  // Allow form to submit normally
});

// Initialize first form
document.addEventListener('DOMContentLoaded', function() {
  attachListeners(document.querySelector(".complaint-form"), complaintCount);
});
</script>

@endsection
