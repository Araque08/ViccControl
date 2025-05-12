
    function toggleDropdown() {
        const dropdown = document.getElementById('perfilDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Cierra el menú si haces clic fuera
    window.onclick = function(e) {
        if (!e.target.closest('.perfil-container')) {
            document.getElementById('perfilDropdown').style.display = 'none';
        }
    }
