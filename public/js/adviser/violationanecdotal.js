
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

    function openModal() {
      document.getElementById("recordModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("recordModal").style.display = "none";
    }
    window.onclick = function(event) {
      const modal = document.getElementById("recordModal");
      if (event.target === modal) {
        modal.style.display = "none";
      }
    }
