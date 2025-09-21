
// Sidebar dropdown
document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault();
    const container = btn.nextElementSibling;
    container.classList.toggle('show');
    btn.querySelector('.fa-caret-down').style.transform = container.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
    document.querySelectorAll('.dropdown-container').forEach(dc => {
      if(dc !== container) dc.classList.remove('show');
    });
  });
});

// Sidebar active link
document.querySelectorAll('.sidebar a').forEach(link => {
  link.addEventListener('click', function(){
    document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
  });
});

// Profile dropdown toggle
function toggleProfileDropdown() {
  const dropdown = document.getElementById('profileDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown if clicked outside
document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('profileDropdown');
  const userInfo = document.querySelector('.user-info');
  if (!userInfo.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});

// Logout
function logout() {
  if(confirm('Are you sure you want to log out?')) alert('Logged out!'); // placeholder
}

// Pie Chart
new Chart(document.getElementById('pieChart').getContext('2d'), {
  type:'doughnut',
  data:{
    labels:['overall','Complaints','Violations'],
    datasets:[{
      data:[12,8,3],
      backgroundColor:['#FFD700','#1E3A8A','#EF4444'],
      borderColor:'#fff',
      borderWidth:2,
      hoverOffset:10
    }]
  },
  options:{
    responsive:true,
    plugins:{ legend:{ position:'bottom', labels:{ font:{ size:12 }, padding:10 } } },
    cutout:'40%'
  }
});

// Bar Chart
new Chart(document.getElementById('barChart').getContext('2d'), {
  type:'bar',
  data:{
    labels:['Jan','Feb','Mar','Apr','May','Jun'],
    datasets:[{
      label:'Monthly Violations',
      data:[15,18,12,20,25,32],
      backgroundColor:'#000',
      borderRadius:3,
      barPercentage:0.45
    }]
  },
  options:{
    responsive:true,
    plugins:{ legend:{ display:false } },
    scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true, ticks:{ stepSize:5 } } }
  }
});
