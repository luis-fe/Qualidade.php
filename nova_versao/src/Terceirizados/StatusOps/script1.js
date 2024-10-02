$(document).ready(() => {
    const dataAtual = new Date();
    const dataFormatada = getdataFormatada(dataAtual);
    $('#data-inicio-pedido').val(dataFormatada);
    $('#data-fim-pedido').val(dataFormatada);
    $('#data-emissao-inicial').val(dataFormatada);
    $('#data-emissao-final').val(dataFormatada);
    $('#data-inicio-ops').val(dataFormatada);
    $('#data-fim-ops').val(dataFormatada);
})