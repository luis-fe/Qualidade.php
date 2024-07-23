
    // Inicializa a sidebar como fechada ao carregar a página
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector("#sidebar").classList.add("collapsed");
    });

    // Toggle da sidebar
    const sidebarToggle = document.querySelector("#sidebar-toggle");
    sidebarToggle.addEventListener("click", function () {
        document.querySelector("#sidebar").classList.toggle("collapsed");
    });

    // Adiciona classe active ao item clicado
    document.querySelectorAll('#sidebar .sidebar-item').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('#sidebar .sidebar-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Adiciona classe selected ao link clicado
    $(document).ready(function () {
        $('.sidebar-link').on('click', function () {
            $('.sidebar-link').removeClass('selected');
            $(this).addClass('selected');
        });
    });

    // Função de mensagem
    const Mensagem = async (mensagem, icon) => {
        try {
            Swal.fire({
                title: mensagem,
                icon: icon,
                showConfirmButton: false,
                timer: 3000,
            });
        } catch (err) {
            console.log(err);
        }
    }
