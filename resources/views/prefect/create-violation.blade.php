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
        <div id="violationsContainer" class="violationsWrapper">
            {{-- Cards will be added here --}}
        </div>
    </section>
</div>


    <!-- ======= Preview (Bottom) ======= -->
    <section class="preview-section">
        <h3><i class="fas fa-search"></i> Preview Records</h3>
        <pre id="output"></pre>
    </section>
</div>



{{-- ==================== SCRIPTS ==================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const studentSearchUrl = "{{ route('violations.search-students') }}";
const offenseSearchUrl = "{{ route('violations.search-offenses') }}";
const getSanctionUrl   = "{{ route('violations.get-sanction') }}";

function attachListeners(box) {
    const studentInput  = box.querySelector(".student-input");
    const studentResults = box.querySelectorAll(".results")[0];
    const offenseInput  = box.querySelector(".offense-input");
    const offenseId     = box.querySelector(".offense-id");
    const offenseResults= box.querySelectorAll(".results")[1];

    // 🔍 Student Search
    studentInput.addEventListener("keyup", function() {
        let fullInput = this.value;
        let parts = fullInput.split(",");
        let query = parts[parts.length - 1].trim();
        if (query.length < 2) { studentResults.innerHTML = ""; return; }

        $.post(studentSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data){
            studentResults.innerHTML = data;
            studentResults.querySelectorAll(".student-item").forEach(item => {
                item.onclick = () => {
                    parts[parts.length-1] = " " + item.textContent;
                    studentInput.value = parts.join(",").replace(/^,/, "");
                    let ids = studentInput.dataset.ids || "";
                    if(ids) ids += ",";
                    ids += item.dataset.id;
                    studentInput.dataset.ids = ids;
                    studentResults.innerHTML = "";
                };
            });
        });
    });

    // 🔍 Offense Search
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
                };
            });
        });
    });

    // 👁️ Show All Button
    const showAllBtn = box.querySelector(".btn-show-all");
    showAllBtn.onclick = () => {
        const students = studentInput.value.split(",");
        const offense  = offenseInput.value.trim();
        const offenseVal = offenseId.value;
        const date = box.querySelector(".date-input").value;
        const time = box.querySelector(".time-input").value;
        const incident = box.querySelector(".incident-input").value.trim();

        if(!students || !offense || !date || !time || !incident){
            alert("Please complete all fields");
            return;
        }

        const studentIds = (studentInput.dataset.ids || "").split(",");
        students.forEach((name, index) => {
            name = name.trim();
            const id = studentIds[index]?.trim();
            if(!name || !id) return;

            const card = document.createElement("div");
            card.classList.add("violation-card");
            card.innerHTML = `
                <div class="btn-remove">&times;</div>
                <p><b>Student:</b> ${name}<input type="hidden" name="student_id[]" value="${id}"></p>
                <p style="color: orange;"><b>Offense:</b> ${offense}<input type="hidden" name="offense[]" value="${offenseVal}" ></p>
                <p style="color: red;"><b>Sanction:</b> <input type="text" name="sanction[]" class="sanction" readonly></p>
                <p><b>Date:</b> ${date}<input type="hidden" name="date[]" value="${date}"></p>
                <p><b>Time:</b> ${time}<input type="hidden" name="time[]" value="${time}"></p>
                <p><b>Incident:</b> ${incident}<input type="hidden" name="incident[]" value="${incident}"></p>
            `;
            document.getElementById("violationsContainer").appendChild(card);

            // ❌ Remove card
            card.querySelector(".btn-remove").onclick = () => card.remove();

            // ⚖️ Fetch sanction
            $.get(getSanctionUrl, { student_id: id, offense_id: offenseVal }, function(data){
                card.querySelector(".sanction").value = data;
            });
        });

        updatePreview();
    };

    // ❌ Remove Form
    box.querySelector(".btn-remove-form").addEventListener("click", () => {
        if(document.querySelectorAll(".violation-form").length > 1){
            box.remove();
        } else {
            alert("At least one violation form is required.");
        }
    });
}

// ➕ Add another violation form
document.getElementById("btnAddViolation").onclick = () => {
    const originalBox = document.querySelector(".violation-form");
    const clone = originalBox.cloneNode(true);

    // Clear inputs
    clone.querySelectorAll("input, textarea").forEach(input => input.value="");
    clone.querySelector(".student-input").dataset.ids = "";
    clone.querySelectorAll(".results").forEach(div=>div.innerHTML="");

    document.querySelector(".content-wrapper").prepend(clone);
    attachListeners(clone);
};

// 📝 Update Preview JSON
function updatePreview(){
    const data = $("#violationForm").serializeArray();
    $("#output").text(JSON.stringify(data, null, 2));
}

// Initial attach
attachListeners(document.querySelector(".violation-form"));

// Form submit preview
$("#violationForm").on("submit", function(e){
    updatePreview();
});

// ➕ Add another violation form
document.getElementById("btnAddViolation").onclick = () => {
    const originalBox = document.querySelector(".violation-form");
    const clone = originalBox.cloneNode(true);

    // Clear inputs
    clone.querySelectorAll("input, textarea").forEach(input => input.value="");
    clone.querySelector(".student-input").dataset.ids = "";
    clone.querySelectorAll(".results").forEach(div=>div.innerHTML="");

    // 👉 Append at bottom instead of prepend
    document.querySelector(".forms-container").appendChild(clone);
    attachListeners(clone);
};

</script>


@endsection
