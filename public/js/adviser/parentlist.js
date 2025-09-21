// Dropdown functionality - auto close others & scroll
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        // close all other dropdowns
        dropdowns.forEach(otherBtn => {
            if (otherBtn !== this && otherBtn.nextElementSibling) {
                otherBtn.nextElementSibling.classList.remove('show');
                const icon = otherBtn.querySelector('.fa-caret-down');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        });

        // toggle clicked dropdown
        const container = this.nextElementSibling;
        if (container) {
            container.classList.toggle('show');
            const icon = this.querySelector('.fa-caret-down');
            if (icon) icon.style.transform =
                container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';

            // scroll into view if dropdown is opened
            if (container.classList.contains('show')) {
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    });
});

// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

// Live search
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("#parentTable tbody tr");
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

function openAddModal() {
    document.getElementById('addParentForm').reset();
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitle').innerText = 'Add Parent/Guardian';
    document.querySelector('#addParentForm button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Save';
    document.getElementById('addModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function showInfo(name, birthdate, contact, studentsJson) {
    const loggedAdviserId = "{{ optional(auth()->guard('adviser')->user())->adviser_id }}";
    const adviserId = loggedAdviserId ? parseInt(loggedAdviserId) : null;

    document.getElementById('infoName').innerText = name;
    document.getElementById('infoBirthdate').innerText = birthdate;
    document.getElementById('infoContact').innerText = contact;

    const childrenList = document.getElementById('infoChildren');
    childrenList.innerHTML = '';
    let students = [];
    try { students = JSON.parse(studentsJson); } catch(e) { students = []; }
    const filtered = adviserId ? students.filter(s => s.adviser_id == adviserId) : students;

    if(filtered.length > 0) {
        filtered.forEach(student => {
            const li = document.createElement('li');
            li.textContent = `${student.name} - Contact: ${student.contact}`;
            childrenList.appendChild(li);
        });
    } else {
        childrenList.innerHTML = '<li>No children under your supervision</li>';
    }

    document.getElementById('smsBtn').onclick = function() {
        const msg = `Hello ${name}, regarding your child(ren): ${filtered.map(s => s.name).join(', ')}.`;
        alert(`SMS to ${contact}: ${msg}`);
    };
    document.getElementById('infoModal').style.display = 'flex';
}

function editGuardian(button) {
    const row = button.closest('tr');
    const cells = row.cells;
    const fullName = cells[0].innerText.trim();
    const lastSpaceIndex = fullName.lastIndexOf(' ');
    const firstName = fullName.slice(0, lastSpaceIndex).trim();
    const lastName = fullName.slice(lastSpaceIndex + 1).trim();

    document.getElementById('parent_id').value = row.dataset.id;
    document.getElementById('parent_fname').value = firstName;
    document.getElementById('parent_lname').value = lastName;
    document.getElementById('parent_birthdate').value = cells[1].innerText.trim();
    document.getElementById('parent_contactinfo').value = cells[2].innerText.trim();

    const form = document.getElementById('addParentForm');
    form.action = `/adviser/adviser/parents/${row.dataset.id}`;
    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Update';
    document.getElementById('modalTitle').innerText = 'Edit Parent/Guardian';
    document.getElementById('addModal').style.display = 'flex';
}

function logout() {
    if(confirm('Are you sure you want to log out?')){
        window.location.href = '/adviser/login';
    }
}
