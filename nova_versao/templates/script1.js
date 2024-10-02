$(document).ready(function () {
    // Manipulador de clique para itens do menu
    $(".menu > ul > li").click(function (e) {
        // Remove a classe 'active' de todos os itens irmãos
        $(this).siblings().removeClass("active");
        // Adiciona a classe 'active' ao item clicado
        $(this).toggleClass("active");
        // Alterna a visibilidade do submenu para o item clicado
        $(this).find("ul").slideToggle();
        // Fecha os submenus dos outros itens irmãos
        $(this).siblings().find("ul").slideUp();
        // Remove a classe 'active' dos itens de submenu dos irmãos
        $(this).siblings().find("ul").find("li").removeClass("active");
    });

    // Manipulador de clique para o botão de menu e o botão de toggle da sidebar
    $(".menu-btn, #sidebar-toggle").click(function () {
        // Alterna a classe 'active' na sidebar
        $("#sidebar").toggleClass("active");
    });


});

function toggleButton(button) {
    // Remove a classe btn-menu-clicado de todos os botões
    const buttons = document.querySelectorAll('.btn-menus');
    buttons.forEach(btn => btn.classList.remove('btn-menu-clicado'));

    // Adiciona a classe btn-menu-clicado ao botão clicado
    button.classList.add('btn-menu-clicado');
}