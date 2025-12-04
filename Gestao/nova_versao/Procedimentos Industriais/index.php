<?php
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-stack"></i></span> Procedimentos Industriais
</div>

   <div id="div-tabelaInv" class="p-3 border rounded">
                    <h6>**Lista de Procedimentos**</h6>
                    <table class="table table-striped table-hover table-bordered"  id="tabelaTagsInv">
                        <thead>
                            <tr>
                                <th scope="col">Codigo</th>
                                <th scope="col">Data Criacao</th>
                                <th scope="col">Procedimento</th>
                                <th scope="col">Autor</th>
                                <th scope="col">Aprovado por</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="KanbanEmbalagens">PRC-001</td> <td>04/12/2025</td> 
                                <td>Controle Kanban Embalagens</td> 
                                <td>Luis Fernando Gonçalves</td> 
                                <td>Ismael Maricato</td>
                            </tr>
                                <td><a href="Fluxograma Fases Industrial">PRC-002</td> <td>04/12/2025</td> 
                                <td>Fluxograma Fases Industrial"</td> 
                                <td>Odilon</td> 
                                <td>Ismael Maricato</td>
                        </tbody>
                    </table>
                </div>







<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script1.js"></script>

<script>
    // Se o seu código de controle de visibilidade estiver aqui,
    // ele funcionará perfeitamente, pois o $ já estará disponível.
    

</script>
