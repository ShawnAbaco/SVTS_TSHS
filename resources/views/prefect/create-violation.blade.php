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
        <h2>Create Violation Record</h2>
        <div class="actions">
            <form id="violationForm" method="POST" action="{{ route('violations.store') }}">
                @csrf
                <div class="buttons-row">
                    <button type="button" class="btn-Add-Violation" id="btnAddViolation">
                        <i class="fas fa-plus-circle"></i> Add Another Violation
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
        <div class="form-box shadow-card violation-form">
            <div class="form-header">
                <h3 class="section-title"><i class="fas fa-user"></i> Violator Details</h3>
                <button type="button" class="btn-remove-form">&times;</button>
            </div>

            <label>Violator(s) <span class="note">(comma-separated)</span></label>
            <input type="text" class="student-input" placeholder="e.g. Shawn Abaco, Kent Zyrone" data-ids="">
            <div class="results"></div>

            <h3 class="section-title"><i class="fas fa-info-circle"></i> Violation Information</h3>
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

    <!-- Right: Violations Summary -->
    <section class="violations-section">
        <div class="summary-header">
            <h3 class="section-title"><i class="fas fa-list"></i> Violations Summary</h3>
            <input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
        </div>
<div id="violationsContainer-1" class="violationsWrapper">

    <!-- Violation cards go here -->
</div>

    </section>
</div>


    {{-- <!-- ======= Preview (Bottom) ======= -->
    <section class="preview-section">
        <h3><i class="fas fa-search"></i> Preview Records</h3>
        <pre id="output"></pre>
    </section> --}}
</div>



{{-- ==================== SCRIPTS ==================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const studentSearchUrl = "{{ route('violations.search-students') }}";
const offenseSearchUrl = "{{ route('violations.search-offenses') }}";
const getSanctionUrl   = "{{ route('violations.get-sanction') }}";

let violationCount = 1;

// Keep track of all students already in summary
let addedStudentIds = new Set();

function attachListeners(box, id) {
    const studentInput  = box.querySelector(".student-input");
    const studentResults = box.querySelectorAll(".results")[0];
    const offenseInput  = box.querySelector(".offense-input");
    const offenseId     = box.querySelector(".offense-id");
    const offenseResults= box.querySelectorAll(".results")[1];
    const showAllBtn    = box.querySelector(".btn-show-all");

    box.dataset.groupId = id;

    function allFieldsFilled() {
        return studentInput.value.trim() && offenseInput.value.trim() && box.querySelector(".date-input").value && box.querySelector(".time-input").value && box.querySelector(".incident-input").value.trim();
    }

    // Enable/disable "Add Another Violation" button
    function toggleAddViolationButton() {
        const btnAdd = document.getElementById("btnAddViolation");
        btnAdd.disabled = !allFieldsFilled();
    }

    // Attach input events to check if all fields filled
    box.querySelectorAll("input, textarea").forEach(input => {
        input.addEventListener("input", toggleAddViolationButton);
    });

    // Student search
studentInput.addEventListener("keyup", function() {
    let fullInput = this.value;
    let parts = fullInput.split(",");
    let query = parts[parts.length - 1].trim();
    if (query.length < 2) { studentResults.innerHTML = ""; return; }

    $.post(studentSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data){
        // Create a temporary container to parse returned HTML
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = data;

        // Get the list of already selected student IDs for this form
        const selectedIds = (studentInput.dataset.ids || "").split(",").filter(id => id.trim() !== "");

        // Filter out already selected students
        const filteredItems = Array.from(tempDiv.querySelectorAll(".student-item")).filter(item => !selectedIds.includes(item.dataset.id));

        // Clear and append filtered results
        studentResults.innerHTML = "";
        filteredItems.forEach(item => {
            studentResults.appendChild(item);

            // Attach click event
            item.addEventListener("click", () => {
                const currentIds = (studentInput.dataset.ids || "").split(",").filter(id => id.trim() !== "");
                if (!currentIds.includes(item.dataset.id)) {
                    parts[parts.length - 1] = " " + item.textContent;
                    studentInput.value = parts.join(",").replace(/^,/, "");
                    currentIds.push(item.dataset.id);
                    studentInput.dataset.ids = currentIds.join(",");
                }
                studentResults.innerHTML = "";
                toggleAddViolationButton();
            });
        });
    });
});

    // Offense search
    offenseInput.addEventListener("keyup", function() {
        let query = this.value;
        if(query.length < 2){ offenseResults.innerHTML = ""; return; }

        $.post(offenseSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data){
            offenseResults.innerHTML = data;
            offenseResults.querySelectorAll(".offense-item").forEach(item=>{
                item.onclick = () => {
                    offenseInput.value = item.textContent;
                    offenseId.value = item.dataset.id;
                    offenseResults.innerHTML = "";
                    toggleAddViolationButton();
                };
            });
        });
    });

    // Show All Button
   showAllBtn.onclick = () => {
    if(!allFieldsFilled()) { alert("Please complete all fields"); return; }

    const students = studentInput.value.split(",");
    const studentIds = (studentInput.dataset.ids || "").split(",");
    const offense  = offenseInput.value.trim();
    const offenseVal = offenseId.value;
    const date = box.querySelector(".date-input").value;
    const time = box.querySelector(".time-input").value;
    const incident = box.querySelector(".incident-input").value.trim();
    const groupId = box.dataset.groupId;

    let container = document.querySelector(".violationsWrapper");
    let groupContainer = document.querySelector(`#group-${groupId}`);
    if(!groupContainer){
        groupContainer = document.createElement("div");
        groupContainer.classList.add("violation-group");
        groupContainer.id = `group-${groupId}`;
        groupContainer.innerHTML = `<div class="violation-group-title">Violation Group #${groupId}</div>`;
        container.appendChild(groupContainer);
    }

    // Keep track of student IDs **only for this group**
    let groupStudentIds = new Set();

    students.forEach((name, index) => {
        name = name.trim();
        const id = studentIds[index]?.trim();
        if(!name || !id || groupStudentIds.has(id)) return;  // check only within this group

        groupStudentIds.add(id);

        const card = document.createElement("div");
        card.classList.add("violation-card");
        card.dataset.groupId = groupId;
        card.innerHTML = `
            <div class="btn-remove">&times;</div>
            <p><b>Student:</b> ${name}<input type="hidden" name="student_id[]" value="${id}"></p>
            <p style="color: orange;"><b>Offense:</b> ${offense}<input type="hidden" name="offense[]" value="${offenseVal}" ></p>
            <p style="color: red;"><b>Sanction:</b> <input type="text" name="sanction[]" class="sanction" readonly></p>
            <p><b>Date:</b> ${date}<input type="hidden" name="date[]" value="${date}"></p>
            <p><b>Time:</b> ${time}<input type="hidden" name="time[]" value="${time}"></p>
            <p><b>Incident:</b> ${incident}<input type="hidden" name="incident[]" value="${incident}"></p>
        `;
        groupContainer.appendChild(card);

        card.querySelector(".btn-remove").onclick = () => {
            groupStudentIds.delete(id);
            card.remove();
        };

        $.get(getSanctionUrl, { student_id: id, offense_id: offenseVal }, function(data){
            card.querySelector(".sanction").value = data;
        });
    });

    toggleAddViolationButton();
    updatePreview();
};

    // Remove Form
    box.querySelector(".btn-remove-form").addEventListener("click", () => {
        if(document.querySelectorAll(".violation-form").length > 1){
            const group = document.querySelector(`#group-${id}`);
            if(group) {
                // Remove all students in this group from addedStudentIds
                group.querySelectorAll("input[name='student_id[]']").forEach(input => addedStudentIds.delete(input.value));
                group.remove();
            }
            box.remove();
            toggleAddViolationButton();
        } else alert("At least one violation form is required.");
    });
}

// Add another violation form
document.getElementById("btnAddViolation").onclick = () => {
    if(document.querySelectorAll(".violation-form").length > 0){
        const lastForm = document.querySelector(".violation-form:last-child");
        if([...lastForm.querySelectorAll("input, textarea")].some(input => !input.value.trim())) {
            alert("Please fill all fields in the current form first.");
            return;
        }
    }

    violationCount++;
    const originalBox = document.querySelector(".violation-form");
    const clone = originalBox.cloneNode(true);
    clone.querySelectorAll("input, textarea").forEach(input => input.value="");
    clone.querySelector(".student-input").dataset.ids = "";
    clone.querySelectorAll(".results").forEach(div=>div.innerHTML="");
    clone.querySelector(".section-title").innerHTML = `<i class="fas fa-user"></i> Violator Details (Form #${violationCount})`;
    document.querySelector(".forms-container").appendChild(clone);
    attachListeners(clone, violationCount);
};

// Preview
function updatePreview(){
    const data = $("#violationForm").serializeArray();
    $("#output").text(JSON.stringify(data, null, 2));
}

attachListeners(document.querySelector(".violation-form"), violationCount);
$("#violationForm").on("submit", function(){ updatePreview(); });





// ======= Violations Summary Search =======
const summarySearchInput = document.getElementById("searchInput");
const violationsWrapper = document.querySelector(".violationsWrapper");

summarySearchInput.addEventListener("input", function() {
    const query = this.value.trim().toLowerCase();
    let anyVisible = false;

    // Check each violation card
    document.querySelectorAll(".violationsWrapper .violation-card").forEach(card => {
        const studentName = card.querySelector("p b")?.parentElement?.textContent.replace("Student:", "").trim().toLowerCase() || "";
        const offense = card.querySelector("p[style*='orange']")?.textContent.replace("Offense:", "").trim().toLowerCase() || "";
        const sanction = card.querySelector("p[style*='red']")?.textContent.replace("Sanction:", "").trim().toLowerCase() || "";

        if(studentName.includes(query) || offense.includes(query) || sanction.includes(query)) {
            card.style.display = "";
            anyVisible = true;
        } else {
            card.style.display = "none";
        }
    });

    // Remove any existing "No records found" div
    const existing = violationsWrapper.querySelector(".no-records");
    if(existing) existing.remove();

    // If nothing is visible, append "No records found" at the end
    if(!anyVisible) {
        const noRecordsDiv = document.createElement("div");
        noRecordsDiv.classList.add("no-records");
        noRecordsDiv.textContent = "No records found.";
        noRecordsDiv.style.textAlign = "center";
        noRecordsDiv.style.color = "gray";
        noRecordsDiv.style.marginTop = "1rem";
        violationsWrapper.appendChild(noRecordsDiv); // append at the end
    }
});


</script>

@endsection
