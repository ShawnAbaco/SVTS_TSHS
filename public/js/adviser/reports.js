
  // Dropdown toggle
  document.querySelectorAll('.dropdown-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const container = btn.nextElementSibling;
      container.classList.toggle('show');
    });
  });

  function openReportModal(reportId) {
    const modal = document.getElementById('modal' + reportId);
    const title = document.querySelector(`.report-box[data-modal="modal${reportId}"] h3`).textContent;
    modal.querySelector('.modal-title').textContent = title;

    fetch(`/adviser/reports/data/${reportId}`)
      .then(res => res.ok ? res.json() : Promise.reject('Fetch failed'))
      .then(data => {
        const thead = modal.querySelector('thead');
        const tbody = modal.querySelector('tbody');
        thead.innerHTML = '';
        tbody.innerHTML = '';

        // Build table header manually
        let headers = [];
        switch (parseInt(reportId)) {
          case 1: headers = ['Complainant Name','Respondent Name','Solution','Recommendation','Date Recorded','Time Recorded']; break;
          case 2: headers = ['Student Name','Solution','Recommendation','Date','Time']; break;
          case 3: headers = ['Complainant Name','Respondent Name','Appointment Date','Appointment Status']; break;
          case 4: headers = ['Student Name','Appointment Date','Appointment Time','Appointment Status']; break;
          case 5: headers = ['Complainant Name','Respondent Name','Incident Description','Complaint Date','Complaint Time']; break;
          case 6: headers = ['Complainant Name','Respondent Name','Type of Offense','Complaint Date','Complaint Time']; break;
          case 7: headers = ['Offense Type','Description','Total Occurrences']; break;
          case 8: headers = ['Student Name','Section','Grade Level','Total Violations','First Violation Date','Most Recent Violation Date']; break;
          case 9: headers = ['Offense Type','Offense Description','Sanction Consequences']; break;
          case 10: headers = ['Student Name','Parent Name','Parent Contact Info','Violation Date','Violation Time','Violation Status']; break;
          case 11: headers = ['Offense Type','Sanction Consequences','Month and Year','Number of Sanctions Given']; break;
          case 12: headers = ['Student Name','Parent Name','Parent Contact Info']; break;
          case 13: headers = ['First Name','Last Name','Violation Count','Complaint Involvement Count']; break;
          case 14: headers = ['Student Name','Adviser Section','Grade Level','Total Violations']; break;
          case 15: headers = ['Student Name','Offense Type','Sanction','Incident Description','Violation Date','Violation Time']; break;
        }

        // Insert header row
        thead.innerHTML = `<tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr>`;

        // Build table body manually (match controller column names)
        if (!data.length) {
          tbody.innerHTML = `<tr><td colspan="${headers.length}" style="text-align:center;">No records found.</td></tr>`;
          modal.style.display = 'block';
          return;
        }


        data.forEach(row => {
          let tr = document.createElement('tr');
          switch (parseInt(reportId)) {
            case 1:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.solution}</td>
                              <td>${row.recommendation}</td>
                              <td>${row.date_recorded}</td>
                              <td>${row.time_recorded}</td>`;
              break;
            case 2:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.solution}</td>
                              <td>${row.recommendation}</td>
                              <td>${row.date}</td>
                              <td>${row.time}</td>`;
              break;
            case 3:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.appointment_date}</td>
                              <td>${row.appointment_status}</td>`;
              break;
            case 4:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.appointment_date}</td>
                              <td>${row.appointment_time}</td>
                              <td>${row.appointment_status}</td>`;
              break;
            case 5:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.incident_description}</td>
                              <td>${row.complaint_date}</td>
                              <td>${row.complaint_time}</td>`;
              break;
            case 6:
              tr.innerHTML = `
                              <td>${row.complainant_name}</td>
                              <td>${row.respondent_name}</td>
                              <td>${row.offense_type}</td>
                              <td>${row.complaint_date}</td>
                              <td>${row.complaint_time}</td>`;
              break;
            case 7:
              tr.innerHTML = `
                              <td>${row.offense_type}</td>
                              <td>${row.offense_description}</td>
                              <td>${row.total_occurrences}</td>`;
              break;
            case 8:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.section}</td>
                              <td>${row.grade_level}</td>
                              <td>${row.total_violations}</td>
                              <td>${row.first_violation_date}</td>
                              <td>${row.most_recent_violation_date}</td>`;
              break;
            case 9:
              tr.innerHTML = `<td>${row.offense_type}</td>
                              <td>${row.offense_description}</td>
                              <td>${row.sanction_consequences}</td>`;
              break;
            case 10:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.parent_name}</td>
                              <td>${row.parent_contactinfo}</td>
                              <td>${row.violation_date}</td>
                              <td>${row.violation_time}</td>
                              <td>${row.violation_status}</td>`;
              break;
            case 11:
            tr.innerHTML = `
                            <td>${row.offense_type}</td>
                            <td>${row.sanction_consequences}</td>
                            <td>${row.month_and_year}</td>
                            <td>${row.number_of_sanctions_given}</td>`;
            break;

            case 12:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.parent_name}</td>
                              <td>${row.parent_contactinfo}</td>`;
              break;
            case 13:
              tr.innerHTML = `<td>${row.first_name}</td>
                              <td>${row.last_name}</td>
                              <td>${row.violation_count}</td>
                              <td>${row.complaint_involvement_count}</td>`;
              break;
            case 14:
              tr.innerHTML = `<td>${row.student_name}</td>
                              <td>${row.adviser_section}</td>
                              <td>${row.grade_level}</td>
                              <td>${row.total_violations}</td>`;
              break;

            case 15:
              tr.innerHTML = `
                              <td>${row.student_name}</td>
                              <td>${row.offense_type}</td>
                              <td>${row.sanction}</td>
                              <td>${row.incident_description}</td>
                              <td>${row.violation_date}</td>
                              <td>${row.violation_time}</td>`;
              break;
          }
          tbody.appendChild(tr);
        });

        modal.style.display = 'block';
      })
      .catch(err => {
      console.error(err);
      alert('Failed to load report data. Check console for details.');
      modal.style.display = 'block'; // still open so user sees something
    });
  }

  // Attach click listeners
  document.querySelectorAll('.report-box').forEach(box => {
    box.addEventListener('click', () => {
      openReportModal(box.dataset.modal.replace('modal',''));
    });
  });

  // Close modal
  document.querySelectorAll('.modal .close').forEach(btn => {
    btn.addEventListener('click', e => {
      e.target.closest('.modal').style.display = 'none';
    });
  });
  window.onclick = e => {
    if (e.target.classList.contains('modal')) e.target.style.display = 'none';
  }
    // Search
  function liveSearch(modalId, query){
    const tbody = document.querySelector('#'+modalId+' tbody');
    query = query.toLowerCase();
    tbody.querySelectorAll('tr').forEach(tr=>{
      tr.style.display = Array.from(tr.cells).some(td=>td.textContent.toLowerCase().includes(query)) ? '' : 'none';
    });
  }

  // Print modal
  function printModal(modalId){
    const modal = document.getElementById(modalId);
    const clone = modal.querySelector('.modal-content').cloneNode(true);
    clone.querySelectorAll('input,button,.close').forEach(el=>el.remove());
    const w = window.open('','','width=900,height=700');
    w.document.write('<html><head><title>Print</title></head><body>');
    w.document.write(clone.innerHTML);
    w.document.write('</body></html>');
    w.document.close(); w.focus(); w.print(); w.close();
  }

  // Export CSV
  function exportCSV(modalId){
    const table = document.querySelector('#'+modalId+' table');
    const rows = Array.from(table.querySelectorAll('tr')).filter(r=>r.style.display!=='none');
    const csv = rows.map((row,i)=>{
      const cells = Array.from(row.querySelectorAll(i===0?'th':'td'));
      return cells.map(c=>`"${(c.textContent||'').replace(/"/g,'""')}"`).join(',');
    }).join('\n');
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = modalId+'.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(a.href);
  }
