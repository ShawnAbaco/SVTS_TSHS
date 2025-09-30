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
  <h2>Create Complaint Record</h2>
  <div class="actions">
    <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}">
      @csrf
      <div class="buttons-row">
        <button type="button" class="btn-Add-Complaint" id="btnAddComplaint">
          <i class="fas fa-plus-circle"></i> Add Another Complaint
        </button>
        <button type="submit" class="btn-save">
          <i class="fas fa-save"></i> Save All Records
        </button>
      </div>
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

              <label>Complainant(s) <span class="note">(comma-separated)</span></label>
              <input type="text" class="complainant-input" placeholder="e.g. Shawn Abaco, Kent Zyrone" data-ids="">
              <div class="results"></div>

              <label>Respondent(s) <span class="note">(comma-separated)</span></label>
              <input type="text" class="respondent-input" placeholder="e.g. John Doe, Jane Smith" data-ids="">
              <div class="results"></div>

              <h3 class="section-title"><i class="fas fa-info-circle"></i> Complaint Information</h3>
              <div class="row-fields">
                  <div class="field">
                      <label>Offense</label>
                      <input type="text" class="offense-input" placeholder="Type offense...">
                      <input type="hidden" class="offense-id">
                      <div class="results"></div>
                  </div>

                  <div class="field small">
                      <label>Date</label>
                      <input type="date" class="date-input">
                  </div>

                  <div class="field small">
                      <label>Time</label>
                      <input type="time" class="time-input">
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

function attachListeners(box, id) {
  const complainantInput = box.querySelector(".complainant-input");
  const respondentInput  = box.querySelector(".respondent-input");
  const offenseInput     = box.querySelector(".offense-input");
  const offenseId        = box.querySelector(".offense-id");
  const results          = box.querySelectorAll(".results");
  const showAllBtn       = box.querySelector(".btn-show-all");
  const allGroupsWrapper = document.getElementById("allComplaintGroups");

  box.dataset.groupId = id;

  function allFieldsFilled() {
    return complainantInput.value.trim() &&
           respondentInput.value.trim() &&
           offenseInput.value.trim() &&
           box.querySelector(".date-input").value &&
           box.querySelector(".time-input").value &&
           box.querySelector(".incident-input").value.trim();
  }

  function toggleAddComplaintButton() {
    document.getElementById("btnAddComplaint").disabled = !allFieldsFilled();
  }

  box.querySelectorAll("input, textarea").forEach(input => input.addEventListener("input", toggleAddComplaintButton));

  // Student search
  [complainantInput, respondentInput].forEach((inputField, idx) => {
    inputField.addEventListener("keyup", function() {
      let parts = this.value.split(",");
      let query = parts[parts.length - 1].trim();
      const resultDiv = results[idx];
      if (query.length < 2) { resultDiv.innerHTML = ""; return; }

      $.post(studentSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data) {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = data;
        const selectedIds = (inputField.dataset.ids || "").split(",").filter(id => id.trim() !== "");
        resultDiv.innerHTML = "";
        Array.from(tempDiv.querySelectorAll(".student-item"))
          .filter(item => !selectedIds.includes(item.dataset.id))
          .forEach(item => {
            resultDiv.appendChild(item);
            item.addEventListener("click", () => {
              const currentIds = (inputField.dataset.ids || "").split(",").filter(id => id.trim() !== "");
              if (!currentIds.includes(item.dataset.id)) {
                parts[parts.length - 1] = " " + item.textContent;
                inputField.value = parts.join(",").replace(/^,/, "");
                currentIds.push(item.dataset.id);
                inputField.dataset.ids = currentIds.join(",");
              }
              resultDiv.innerHTML = "";
              toggleAddComplaintButton();
            });
          });
      });
    });
  });

  // Offense search
  offenseInput.addEventListener("keyup", function() {
    let query = this.value;
    if (query.length < 2){ results[2].innerHTML = ""; return; }

    $.post(offenseSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data){
      results[2].innerHTML = data;
      results[2].querySelectorAll(".offense-item").forEach(item => {
        item.onclick = () => {
          offenseInput.value = item.textContent;
          offenseId.value = item.dataset.id;
          results[2].innerHTML = "";
          toggleAddComplaintButton();
        };
      });
    });
  });

  // Show all button
  showAllBtn.onclick = () => {
    if (!allFieldsFilled()) {
      Swal.fire("Incomplete!", "Please fill all fields before showing summary.", "warning");
      return;
    }

    const complainants = complainantInput.value.split(",").map(c => c.trim()).filter(c => c !== "");
    const complainantIds = (complainantInput.dataset.ids || "").split(",").map(i => i.trim()).filter(i => i !== "");
    const respondents = respondentInput.value.split(",").map(r => r.trim()).filter(r => r !== "");
    const respondentIds = (respondentInput.dataset.ids || "").split(",").map(i => i.trim()).filter(i => i !== "");
    const offense = offenseInput.value.trim();
    const offenseVal = offenseId.value;
    const date = box.querySelector(".date-input").value;
    const time = box.querySelector(".time-input").value;
    const incident = box.querySelector(".incident-input").value.trim();
    const groupId = box.dataset.groupId;

    // ✅ Create group container
    let groupContainer = document.querySelector(`#group-${groupId}`);
    if (!groupContainer) {
      groupContainer = document.createElement("div");
      groupContainer.classList.add("complaint-group");
      groupContainer.id = `group-${groupId}`;
      groupContainer.innerHTML = `<div class="complaint-group-title">Violation Group #${groupId}</div>`;
      allGroupsWrapper.appendChild(groupContainer);
    }

    // ✅ Clear existing cards before regenerating
    groupContainer.querySelectorAll(".complaint-card").forEach(card => card.remove());

    // ✅ Generate each pair
    complainants.forEach((compName, i) => {
      const compId = complainantIds[i];
      if (!compId) return;
      respondents.forEach((respName, j) => {
        const respId = respondentIds[j];
        if (!respId) return;

        const card = document.createElement("div");
        card.classList.add("complaint-card");
        card.dataset.groupId = groupId;
        card.innerHTML = `
          <div class="btn-remove">&times;</div>
          <p><b>Complainant:</b> ${compName}
            <input type="hidden" name="complainant_id[${groupId}][]" value="${compId}">
          </p>
          <p><b>Respondent:</b> ${respName}
            <input type="hidden" name="respondent_id[${groupId}][]" value="${respId}">
          </p>
          <p style="color: orange;"><b>Offense:</b> ${offense}
            <input type="hidden" name="offense[${groupId}]" value="${offenseVal}">
          </p>
          <p><b>Date:</b> ${date}
            <input type="hidden" name="date[${groupId}]" value="${date}">
          </p>
          <p><b>Time:</b> ${time}
            <input type="hidden" name="time[${groupId}]" value="${time}">
          </p>
          <p><b>Incident:</b> ${incident}
            <input type="hidden" name="incident[${groupId}]" value="${incident}">
          </p>
        `;
        groupContainer.appendChild(card);
        card.querySelector(".btn-remove").onclick = () => card.remove();
      });
    });

    toggleAddComplaintButton();
  };

  // Remove form
  box.querySelector(".btn-remove-form").addEventListener("click", () => {
    if (document.querySelectorAll(".complaint-form").length > 1) {
      const group = document.querySelector(`#group-${id}`);
      if (group) group.remove();
      box.remove();
      toggleAddComplaintButton();
    } else {
      Swal.fire("Warning", "At least one complaint form is required.", "warning");
    }
  });
}

// Add another complaint form
document.getElementById("btnAddComplaint").onclick = () => {
  const lastForm = document.querySelector(".complaint-form:last-child");
  if ([...lastForm.querySelectorAll("input, textarea")].some(input => !input.value.trim())) {
    Swal.fire("Incomplete!", "Please fill all fields in the current form first.", "warning");
    return;
  }

  complaintCount++;
  const originalBox = document.querySelector(".complaint-form");
  const clone = originalBox.cloneNode(true);
  clone.querySelectorAll("input, textarea").forEach(input => input.value = "");
  clone.querySelectorAll(".complainant-input, .respondent-input").forEach(input => input.dataset.ids = "");
  clone.querySelectorAll(".results").forEach(div => div.innerHTML = "");
  clone.querySelector(".section-title").innerHTML = `<i class="fas fa-user"></i> Complaint Details (Form #${complaintCount})`;
  document.querySelector(".forms-container").appendChild(clone);
  attachListeners(clone, complaintCount);
};

// Initialize first form
attachListeners(document.querySelector(".complaint-form"), complaintCount);
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('btnAddComplaint');
    const formsContainer = document.querySelector('.forms-container');

    addBtn.addEventListener('click', function() {
        // Find the first form-box to clone
        const firstForm = document.querySelector('.complaint-form');
        if (!firstForm) return;

        // Clone the form
        const newForm = firstForm.cloneNode(true);

        // Clear all inputs/textareas in the cloned form
        newForm.querySelectorAll('input, textarea').forEach(input => {
            input.value = '';
            if (input.dataset.ids) input.dataset.ids = ''; // clear custom data
        });

        // Reattach the remove button event
        const removeBtn = newForm.querySelector('.btn-remove-form');
        removeBtn.addEventListener('click', () => newForm.remove());

        // Append new form below existing ones
        formsContainer.appendChild(newForm);
    });

    // Also attach remove function to the first form
    document.querySelectorAll('.btn-remove-form').forEach(btn => {
        btn.addEventListener('click', function() {
            const parentForm = btn.closest('.complaint-form');
            if (document.querySelectorAll('.complaint-form').length > 1) {
                parentForm.remove();
            } else {
                alert('At least one complaint form is required.');
            }
        });
    });
});
</script>

@endsection
