<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
}
echo '<script>const usuario = "' . $username . '";</script>';

?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height" >
        <button class="btn btn-outline-primary" id="CadastrarChamados" onclick="$('#modalCadChamados').modal('show')">
            Novo Chamado
            <span><i class="fa-solid fa-square-plus"></i></span>
        </button>
        <div class="row col-12">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="Infos">
                <label for="itensPorPagina">Mostrar</label>
                <select class="form-select" id="itensPorPagina">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label for="text">elementos</label>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6" id="Infos" style="justify-content: right;">
                <div id="search-container" class="col-12 col-sm-12 ">
                    <input type="text" id="search" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered " id="TableChamados">
                    <thead>
                        <tr>
                            <th scope="col">Id Chamado</th>
                            <th scope="col">Descrição Chamado</th>
                            <th scope="col">Solicitante</th>
                            <th scope="col">Data Abertura Chamado</th>
                            <th scope="col">Data Finalização Chamado</th>
                            <th scope="col">Tipo de Chamado</th>
                            <th scope="col">Status do Chamado</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="container3" style="margin-top: 1rem; min-width: 100%">
                <div class="col-6 col-md-7 align-items-center text-align-center justify-content-center" id="Paginacao">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCadChamados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TituloModal">Cadastrar Novo Chamado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="FormCadChamados">
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="InputDescricaoChamado" placeholder="Descrição do Chamado" required style="height: 150px; min-height: 150px"></textarea>
                        <label for="InputDescricaoChamado">Descrição do Chamado</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="InputFoto" accept="image/*">
                        <label for="InputFoto">Selecione uma foto</label>
                    </div>
                    <div class="text-center" id="PreviewFoto" style="display: none;">
                        <img id="ImagemPreview" src="#" alt="Preview da Foto" style="max-width: 100%; max-height: 200px;">
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectTipoChamado" required>
                            <option value="" disabled selected>Selecione o Tipo</option>
                            <option value="MANUTENÇÃO">MANUTENÇÃO</option>
                            <option value="MELHORIA">MELHORIA</option>
                        </select>
                        <label for="">Tipo</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectAreaChamado" required>
                            <option value="" disabled selected>Selecione a Área</option>
                            <option value="SERVIDOR">SERVIDOR</option>
                            <option value="APP">APP</option>
                            <option value="PORTAL">PORTAL</option>
                        </select>
                        <label for="">Área</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectPrioridadeChamado" required>
                            <option value="" disabled selected>Defina a Prioridade</option>
                            <option value="URGENTE">URGENTE</option>
                            <option value="MODERADA">MODERADA</option>
                            <option value="NORMAL">NORMAL</option>
                        </select>
                        <label for="">Prioridade</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="BtnRemoverFoto" class="btn btn-danger d-none">Remover Foto</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TituloModal">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="FormEditarUsuario">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputEditMatricula" placeholder=" " readonly>
                        <label for="text">Matrícula do Usuário</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputEditNomeUsuario" placeholder=" " required autocomplete="off">
                        <label for="text">Nome do Usuário</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectEditFuncao" required>
                            <option value="" disabled selected>Selecione a Função</option>
                            <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                            <option value="REPOSITOR">REPOSITOR</option>
                            <option value="SEPARADOR">SEPARADOR</option>
                        </select>
                        <label for="empresa">Função</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectEditSituacao" required>
                            <option value="" disabled selected>Selecione a Situação</option>
                            <option value="ATIVO">ATIVO</option>
                            <option value="INATIVO">INATIVO</option>
                        </select>
                        <label for="empresa">Função</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>