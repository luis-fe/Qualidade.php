<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <button class="btn btn-outline-primary" id="CadastrarUsuario" onclick="$('#modalCadUsuario').modal('show')">
            Novo Usuário
            <span><i class="fa-solid fa-user-plus"></i></span>
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
                    <input type="text" id="searchUsuarios" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="TableUsuarios">
                    <thead>
                        <tr>
                            <th scope="col">Código Usuário</th>
                            <th scope="col">Nome Usuário</th>
                            <th scope="col">Função</th>
                            <th scope="col">Situação</th>
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

<div class="modal fade" id="modalCadUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TituloModal">Cadastrar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="FormCadUsuario">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputMatricula" placeholder=" " required autocomplete="off">
                        <label for="text">Matrícula do Usuário</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputNomeUsuario" placeholder=" " required autocomplete="off">
                        <label for="text">Nome do Usuário</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectFuncao" required>
                            <option value="" disabled selected>Selecione a Função</option>
                            <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                            <option value="REPOSITOR">REPOSITOR</option>
                            <option value="SEPARADOR">SEPARADOR</option>
                        </select>
                        <label for="empresa">Função</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="SelectSituacao" required>
                            <option value="" disabled selected>Selecione a Situação</option>
                            <option value="ATIVO">ATIVO</option>
                            <option value="INATIVO">INATIVO</option>
                        </select>
                        <label for="empresa">Função</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="senha" id="InputSenhaUsuario" placeholder=" " required autocomplete="off">
                        <label for="text">Senha do Usuário</label>
                    </div>
                    <div class="modal-footer">
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