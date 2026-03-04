$(document).ready(function () {
    // Manipulador de clique para itens do menu
    $(".menu > ul > li").click(function () {
        $(this).siblings().removeClass("active");
        $(this).toggleClass("active");
        $(this).find("ul").slideToggle();
        $(this).siblings().find("ul").slideUp();
        $(this).siblings().find("ul li").removeClass("active");
    });
    $("#sidebar-toggle").click(function () {
        $("#sidebar").toggleClass("active");

        // Verifica se a tela é menor que 769px
        if (window.matchMedia("(max-width: 768px)").matches) {
            if ($("#sidebar").hasClass("active")) {
                $("#sidebar").css("display", "flex");
            } else {
                $("#sidebar").css("display", "none");
            }
        }
    });

    // Manipulador para ajuste de tamanho da janela
    $(window).resize(function () {
        if (window.matchMedia("(min-width: 769px)").matches) {
            // Garante que a sidebar esteja visível em telas maiores
            $("#sidebar").css("display", "flex");
            $("#sidebar").addClass("active"); // Se necessário, adicione a classe 'active'
        }
    });


    $(".menu-btn").click(function () {
        $("#sidebar").toggleClass("active");

    });

});
