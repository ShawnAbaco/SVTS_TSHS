@extends('prefect.layout')

@section('content')
<div class="container">

    {{-- ✅ Flash Messages --}}
    @if(session('messages'))
        <div class="alert-messages">
            @foreach(session('messages') as $msg)
                <div class="alert-item">{!! $msg !!}</div>
            @endforeach
        </div>
    @endif

    <div class="main-container">

        <!-- ======= HEADER ======= -->
        <header class="main-header">
            <div class="header-left">
                <h2>Create Complaint Records</h2>
                {{-- <p class="subtitle">Log multiple complaints quickly and efficiently.</p> --}}
            </div>
            <div class="header-right">
                <div class="user-info" onclick="toggleProfileDropdown()">
                    <img src="/images/user.jpg" alt="User">
                    <i class="fas fa-caret-down"></i>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="{{ route('profile.settings') }}">Profile</a>
                </div>
            </div>
        </header>

        <!-- ======= FORM BOX ======= -->
        <div class="form-box shadow-card">
            <h3 class="section-title"><i class="fas fa-user"></i> Complainant Details</h3>
            <label>Complainant(s) <span class="note">(comma-separated)</span></label>
            <input type="text" id="studentsInput" placeholder="e.g. Shawn Abaco, Kent Zyrone" autocomplete="off">
            <div id="studentResults" class="results"></div>

            <h3 class="section-title"><i class="fas fa-info-circle"></i> Complaint Information</h3>
            <div class="row-fields">
                <div class="field">
                    <label>Complaint Type</label>
                    <input type="text" id="complaintInput" placeholder="Type complaint..." autocomplete="off">
                    <input type="hidden" id="complaint_id">
                    <div id="complaintResults" class="results"></div>
                </div>

                <div class="field small">
                    <label>Date</label>
                    <input type="date" id="dateInput">
                </div>

                <div class="field small">
                    <label>Time</label>
                    <input type="time" id="timeInput">
                </div>

                <div class="field large">
                    <label>Incident Details</label>
                    <textarea id="incidentInput" rows="3" placeholder="Briefly describe the incident..."></textarea>
                </div>
            </div>

            <button type="button" class="btn-show-all" id="btnShowAll"><i class="fas fa-eye"></i> Show All</button>
        </div>
    </div>

    <!-- ======= COMPLAINT CARDS ======= -->
    <section class="violations-section">
        <h3 class="section-title"><i class="fas fa-list"></i> Complaints Summary</h3>
        <div id="complaintsContainer" class="violationsWrapper"></div>
    </section>

    <!-- ======= ACTION BUTTONS ======= -->
    <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}">
        @csrf
        <div class="buttons-row">
            <button type="button" class="btn-Add-Violation" id="btnAddComplaint">
                <i class="fas fa-plus-circle"></i> Add Another Complaint
            </button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save All Complaints
            </button>
        </div>
    </form>

    <!-- ======= PREVIEW ======= -->
    <section class="preview-section">
        <h3><i class="fas fa-search"></i> Preview Records</h3>
        <pre id="output"></pre>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const studentSearchUrl = "{{ route('complaints.search-students') }}";
    const complaintSearchUrl = "{{ route('complaints.search-types') }}";

    function attachListeners(box) {
        const studentInput = box.querySelector("input[placeholder^='e.g.']");
        const studentResults = box.querySelector(".results");

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

        const complaintInput = box.querySelector("input[placeholder='Type complaint...']");
        const complaintId = box.querySelector("input[type='hidden']");
        const complaintResults = box.querySelectorAll(".results")[1];

        complaintInput.addEventListener("keyup", function() {
            let query = this.value;
            if(query.length < 2){ complaintResults.innerHTML = ""; return; }
            $.post(complaintSearchUrl, { query, _token: "{{ csrf_token() }}" }, function(data){
                complaintResults.innerHTML = data;
                complaintResults.querySelectorAll(".complaint-item").forEach(item=>{
                    item.onclick = () => {
                        complaintInput.value = item.textContent;
                        complaintId.value = item.dataset.id;
                        complaintResults.innerHTML = "";
                    };
                });
            });
        });

        const showAllBtn = box.querySelector(".btn-show-all");
        showAllBtn.onclick = () => {
            const students = studentInput.value.split(",");
            const complaint = complaintInput.value.trim();
            const complaintVal = complaintId.value;
            const date = box.querySelector("input[type='date']").value;
            const time = box.querySelector("input[type='time']").value;
            const incident = box.querySelector("textarea").value.trim();

            if(!students || !complaint || !date || !time || !incident){
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
                    <p><b>Complainant:</b> ${name}<input type="hidden" name="student_id[]" value="${id}"></p>
                    <p><b>Complaint Type:</b> ${complaint}<input type="hidden" name="complaint[]" value="${complaintVal}"></p>
                    <p><b>Date:</b> ${date}<input type="hidden" name="date[]" value="${date}"></p>
                    <p><b>Time:</b> ${time}<input type="hidden" name="time[]" value="${time}"></p>
                    <p><b>Incident:</b> ${incident}<input type="hidden" name="incident[]" value="${incident}"></p>
                `;
                document.getElementById("complaintsContainer").appendChild(card);
                card.querySelector(".btn-remove").onclick = () => card.remove();
            });
        };
    }

    attachListeners(document.querySelector(".form-box"));

    document.getElementById("btnAddComplaint").onclick = () => {
        const originalBox = document.querySelector(".form-box");
        const clone = originalBox.cloneNode(true);
        clone.querySelectorAll("input, textarea").forEach(input=>input.value="");
        clone.querySelectorAll(".results").forEach(div=>div.innerHTML="");
        document.querySelector(".main-container").appendChild(clone);
        attachListeners(clone);
    };

    $("#complaintForm").on("submit", function(e){
        $("#output").text(JSON.stringify($(this).serializeArray(), null, 2));
    });

    function toggleProfileDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
</script>

<style>
/* ===== General ===== */
.container { width:100%; max-width:100%; margin:0; padding:25px 40px; box-sizing:border-box; }
.main-container { width:100%; }
.form-box, .violations-section, .preview-section { width:100%; }

/* ===== Typography ===== */
h2, h3 { margin-bottom:10px; color:#222; }
.subtitle { font-size:14px; color:#666; margin-top:-5px; }

/* ===== Header ===== */
.main-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; width:100%; }
.header-left h2 { font-size:1.6rem; }
.user-info { display:flex; align-items:center; gap:8px; cursor:pointer; }
.user-info img { width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #ddd; }

/* ===== Form Box ===== */
.shadow-card { width:100%; box-shadow:0 4px 12px rgba(0,0,0,0.05); background:#fff; border-radius:8px; padding:20px 25px; box-sizing:border-box; }
.section-title { margin:15px 0 10px; font-size:1.1rem; color:#0056b3; display:flex; align-items:center; gap:6px; }

/* ===== Fields ===== */
label { font-weight:bold; display:block; margin-bottom:5px; }
input, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; margin-bottom:14px; font-size:14px; }
input:focus, textarea:focus { outline:none; border-color:#007bff; box-shadow:0 0 0 2px rgba(0,123,255,0.1); }
textarea { resize:vertical; }

/* ===== Row Fields ===== */
.row-fields { display:flex; flex-wrap:wrap; gap:15px; width:100%; }
.field { flex:1; min-width:180px; }
.field.small { max-width:200px; }
.field.large { flex:2; }

/* ===== Buttons ===== */
button { padding:10px 18px; border:none; border-radius:6px; cursor:pointer; font-weight:600; transition: background 0.2s ease; }
.buttons-row { display:flex; justify-content:center; flex-wrap:wrap; gap:15px; margin:30px 0; }
.btn-show-all { background-color:#28a745; color:#fff; display:block; margin:20px auto 0; }
.btn-show-all:hover { background-color:#218838; }
.btn-Add-Violation { background-color:#ffc107; color:#000; }
.btn-Add-Violation:hover { background-color:#e0a800; }
.btn-save { background-color:#007bff; color:#fff; }
.btn-save:hover { background-color:#0069d9; }

/* ===== Complaints Section ===== */
.violations-section { margin-top:40px; width:100%; }
.violationsWrapper { display:flex; flex-wrap:wrap; gap:20px; margin-top:10px; }
.violation-card { border:1px solid #ccc; border-radius:8px; padding:15px; background:#fafafa; flex:1 1 320px; position:relative; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
.violation-card p { margin-bottom:8px; font-size:14px; }
.violation-card .btn-remove { background-color:#dc3545; color:white; border-radius:50%; width:24px; height:24px; line-height:22px; text-align:center; font-weight:bold; position:absolute; top:6px; right:6px; cursor:pointer; }

/* ===== Results Dropdown ===== */
.results div { padding:6px 10px; cursor:pointer; }
.results div:hover { background:#f0f0f0; }

/* ===== Alerts ===== */
.alert-messages { margin-bottom:20px; }
.alert-item { background-color:#d4edda; color:#155724; padding:10px 15px; border-radius:6px; margin-bottom:8px; border:1px solid #c3e6cb; }
</style>
@endsection
