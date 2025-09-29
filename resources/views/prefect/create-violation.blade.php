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
<input type="search" placeholder="🔍 Search by student name or ID..." id="searchInput">
      {{-- <button class="btn-primary" id="createBtn">➕ Add Student</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button> --}}

         <!-- ======= ACTION BUTTONS ======= -->
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


        <!-- ======= FORM BOX ======= -->
       <div class="form-box shadow-card">
    <h3 class="section-title"><i class="fas fa-user"></i> Violator Details</h3>
    <label>Violator(s) <span class="note">(comma-separated)</span></label>
    <input type="text" id="studentsInput" placeholder="e.g. Shawn Abaco, Kent Zyrone" autocomplete="off">
    <div id="studentResults" class="results"></div>

    <h3 class="section-title"><i class="fas fa-info-circle"></i> Violation Information</h3>
    <div class="row-fields">
        <div class="field">
            <label>Offense</label>
            <input type="text" id="offenseInput" placeholder="Type offense..." autocomplete="off">
            <input type="hidden" id="offense_id">
            <div id="offenseResults" class="results"></div>
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

    <!-- ======= VIOLATION CARDS ======= -->
    <section class="violations-section">
        <h3 class="section-title"><i class="fas fa-list"></i> Violations Summary</h3>
        <div id="violationsContainer" class="violationsWrapper"></div>
    </section>



    <!-- ======= PREVIEW ======= -->
    <section class="preview-section">
        <h3><i class="fas fa-search"></i> Preview Records</h3>
        <pre id="output"></pre>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const studentSearchUrl = "{{ route('violations.search-students') }}";
    const offenseSearchUrl = "{{ route('violations.search-offenses') }}";
    const getSanctionUrl = "{{ route('violations.get-sanction') }}";

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

        const offenseInput = box.querySelector("input[placeholder='Type offense...']");
        const offenseId = box.querySelector("input[type='hidden']");
        const offenseResults = box.querySelectorAll(".results")[1];

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

        const showAllBtn = box.querySelector(".btn-show-all");
        showAllBtn.onclick = () => {
            const students = studentInput.value.split(",");
            const offense = offenseInput.value.trim();
            const offenseVal = offenseId.value;
            const date = box.querySelector("input[type='date']").value;
            const time = box.querySelector("input[type='time']").value;
            const incident = box.querySelector("textarea").value.trim();

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
                    <p><b>Offense:</b> ${offense}<input type="hidden" name="offense[]" value="${offenseVal}"></p>
                    <p><b>Sanction:</b> <input type="text" name="sanction[]" class="sanction" readonly></p>
                    <p><b>Date:</b> ${date}<input type="hidden" name="date[]" value="${date}"></p>
                    <p><b>Time:</b> ${time}<input type="hidden" name="time[]" value="${time}"></p>
                    <p><b>Incident:</b> ${incident}<input type="hidden" name="incident[]" value="${incident}"></p>
                `;
                document.getElementById("violationsContainer").appendChild(card);
                card.querySelector(".btn-remove").onclick = () => card.remove();

                // fetch sanction
                $.get(getSanctionUrl, { student_id: id, offense_id: offenseVal }, function(data){
                    card.querySelector(".sanction").value = data;
                });
            });
        };
    }

    attachListeners(document.querySelector(".form-box"));

    document.getElementById("btnAddViolation").onclick = () => {
        const originalBox = document.querySelector(".form-box");
        const clone = originalBox.cloneNode(true);
        clone.querySelectorAll("input, textarea").forEach(input=>input.value="");
        clone.querySelectorAll(".results").forEach(div=>div.innerHTML="");
        document.querySelector(".main-container").appendChild(clone);
        attachListeners(clone);
    };

    $("#violationForm").on("submit", function(e){
        $("#output").text(JSON.stringify($(this).serializeArray(), null, 2));
    });
</script>

{{-- old script --}}
<script>
  // Chart.js Doughnut
  const ctx = document.getElementById('violationChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Attendance', 'Behavior', 'Dress Code', 'Other'],
      datasets: [{
        data: [40, 25, 20, 15],
        backgroundColor: ['#00ff00', '#ff0000', '#0000ff', '#ffff00'],
        borderWidth: 1
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });

  // Recent Violations Line Chart
  const recentCtx = document.getElementById('recentChart').getContext('2d');
  new Chart(recentCtx, {
    type: 'line',
    data: {
        labels: ['Jan 1','Jan 5','Jan 10','Jan 15','Jan 20','Jan 25','Jan 30'],
        datasets: [
            { label: 'Violations', data: [5,8,6,10,7,9,12], borderColor: '#FF0000', backgroundColor: 'rgba(255,0,0,0.2)', fill: true, tension: 0.3 },
            { label: 'Complaints', data: [2,3,4,3,5,4,6], borderColor: '#0000FF', backgroundColor: 'rgba(0,0,255,0.2)', fill: true, tension: 0.3 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
            x: { display: true, title: { display: true, text: 'Date' } },
            y: { display: true, title: { display: true, text: 'Count' }, beginAtZero: true }
        }
    }
  });

  // Dropdown
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
    });
  });

  // Profile image & name
  function changeProfileImage() { document.getElementById('imageInput').click(); }
  document.getElementById('imageInput').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
      const reader = new FileReader();
      reader.onload = function(ev){ document.getElementById('profileImage').src = ev.target.result; }
      reader.readAsDataURL(file);
    }
  });
  function changeProfileName() {
    const newName = prompt("Enter new name:");
    if(newName) document.querySelector('.user-info span').innerText = newName;
  }

  // Logout
  function logout() {
    const confirmLogout = confirm("Are you sure you want to logout?");
    if (!confirmLogout) return;
    fetch("{{ route('adviser.logout') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(response => { if(response.ok){ window.location.href = "{{ route('auth.login') }}"; } })
    .catch(error => console.error('Logout failed:', error));
  }

  // Info modal logic
  const modal = document.getElementById("infoModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalBody = document.getElementById("modalBody");
  const closeBtn = document.querySelector(".close");
  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (event) => { if(event.target === modal) modal.style.display = "none"; }

</script>
@endsection
