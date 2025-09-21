
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




  document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', function(){
          document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
          this.classList.add('active');
      });// Sidebar active link
  });

  const openModalBtn = document.getElementById('openModalBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const modal = document.getElementById('createStudentModal');

  const editModal = document.getElementById('editStudentModal');
  const closeEditModalBtn = document.getElementById('closeEditModalBtn');

  const infoModal = document.getElementById('infoModal');
  const closeInfoModalBtn = document.getElementById('closeInfoModalBtn');
  const sendSMSBtn = document.getElementById('sendSMSBtn');

  // TABLE SEARCH (LIVE) - filters student table rows only
  const tableSearch = document.getElementById('tableSearch');
  if (tableSearch) {
    tableSearch.addEventListener('input', function() {
      const q = this.value.trim().toLowerCase();
      document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        // only check cell text, not attributes — matches visible table content
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // OPEN / CLOSE MODALS
  openModalBtn.addEventListener('click', () => modal.style.display = 'flex');
  closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
  closeEditModalBtn.addEventListener('click', () => editModal.style.display = 'none');
  closeInfoModalBtn.addEventListener('click', () => infoModal.style.display = 'none');

  // Close modals when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
    if (e.target === editModal) editModal.style.display = 'none';
    if (e.target === infoModal) infoModal.style.display = 'none';
  });

  // EDIT MODAL
  document.querySelectorAll('.edit').forEach(button => {
    button.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const studentId = row.dataset.id;

      document.getElementById('editStudentId').value = studentId;
      document.getElementById('editStudentFname').value = row.dataset.fname;
      document.getElementById('editStudentLname').value = row.dataset.lname;
      document.getElementById('editStudentBirthdate').value = row.dataset.birthdate;
      document.getElementById('editStudentAddress').value = row.dataset.address;
      document.getElementById('editStudentContact').value = row.dataset.contact;

      document.getElementById('edit_parent_search').value = row.dataset.parentName;
      document.getElementById('edit_parent_id').value = row.dataset.parentId || '';

      document.getElementById('editStudentForm').action = `/adviser/students/${studentId}`;
      editModal.style.display = 'flex';
    });
  });

  // INFO BUTTON
  document.querySelectorAll('.info').forEach(button => {
    button.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const guardianName = row.dataset.parentName;
      const guardianContact = row.dataset.parentContact;

      document.getElementById('infoGuardianName').innerText = guardianName;
      document.getElementById('infoGuardianContact').innerText = guardianContact;

      infoModal.style.display = 'flex';

      sendSMSBtn.onclick = () => {
        alert(`📩 SMS will be sent to ${guardianName} (${guardianContact})`);
      };
    });
  });

  // LIVE SEARCH PARENT (Create)
  document.getElementById('parent_search').addEventListener('keyup', function() {
    let query = this.value;
    if(query.length < 2){
      document.getElementById('parentList').style.display = 'none';
      document.getElementById('parentList').innerHTML = '';
      return;
    }
    fetch(`{{ route('adviser.parentsearch') }}?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        let results = '';
        data.forEach(parent => {
          // sanitize name when injecting
          const name = parent.parent_name.replace(/'/g, "\\'");
          results += `<div class="dropdown-results-item" onclick="selectParent(${parent.parent_id}, '${name}')">${parent.parent_name}</div>`;
        });
        document.getElementById('parentList').innerHTML = results;
        document.getElementById('parentList').style.display = 'block';
      });
  });

  function selectParent(id, name){
    document.getElementById('parent_id').value = id;
    document.getElementById('parent_search').value = name;
    document.getElementById('parentList').style.display = 'none';
  }

  // LIVE SEARCH for EDIT MODAL
  document.getElementById('edit_parent_search').addEventListener('keyup', function() {
    let query = this.value;
    if(query.length < 2){
      document.getElementById('editParentList').style.display = 'none';
      document.getElementById('editParentList').innerHTML = '';
      return;
    }
    fetch(`{{ route('adviser.parentsearch') }}?query=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        let results = '';
        data.forEach(parent => {
          const name = parent.parent_name.replace(/'/g, "\\'");
          results += `<div class="dropdown-results-item" onclick="selectEditParent(${parent.parent_id}, '${name}')">${parent.parent_name}</div>`;
        });
        document.getElementById('editParentList').innerHTML = results;
        document.getElementById('editParentList').style.display = 'block';
      });
  });

  function selectEditParent(id, name){
    document.getElementById('edit_parent_id').value = id;
    document.getElementById('edit_parent_search').value = name;
    document.getElementById('editParentList').style.display = 'none';
  }

  function logout(){
    if(confirm('Are you sure you want to log out?')){
      window.location.href = '/adviser/login';
    }
  }
