
// Dropdown functionality
const dropdowns = document.querySelectorAll('.dropdown-btn');
dropdowns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        dropdowns.forEach(otherBtn => {
            if (otherBtn !== this) {
                otherBtn.nextElementSibling.classList.remove('show');
                otherBtn.querySelector('.fa-caret-down').style.transform = 'rotate(0deg)';
            }
        });
        const container = this.nextElementSibling;
        container.classList.toggle('show');
        this.querySelector('.fa-caret-down').style.transform =
            container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        if(container.classList.contains('show')) container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});

// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(){
        document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

// Search filter
document.getElementById("searchInput").addEventListener("keyup", function () {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#offenseTable tbody tr");
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
});

// Print functionality
document.getElementById("printBtn").addEventListener("click", () => {
    const table = document.getElementById("offenseTable").cloneNode(true);
    const style = `<style>body{font-family:Arial;padding:16px;}h2{margin-bottom:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #000;padding:8px;text-align:left;}thead th{background:#f1f1f1;}</style>`;
    const win = window.open("", "", "height=800,width=1000");
    win.document.write("<html><head><title>Offenses & Sanctions</title>" + style + "</head><body>");
    win.document.write("<h2>Offenses & Sanctions</h2>");
    win.document.body.appendChild(table);
    win.document.write("</body></html>");
    win.document.close();
    win.focus();
    win.print();
});

// Export CSV
document.getElementById("exportBtn").addEventListener("click", () => {
    const table = document.getElementById("offenseTable");
    const csv = Array.from(table.querySelectorAll("tr")).map((row, idx) =>
        Array.from(row.querySelectorAll(idx===0?"th":"td")).map(c=>`"${c.textContent.replace(/"/g,'""')}"`).join(",")
    ).join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "offenses_sanctions.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});

// Logout
function logout() {
    fetch('/logout', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => window.location.href='/adviser/login')
      .catch(error => console.error('Logout failed:', error));
}
