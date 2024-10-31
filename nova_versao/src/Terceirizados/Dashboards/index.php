    <?php
    include_once("requests.php");
    include_once("../../../templates/Loading.php");
    include_once("../../../templates/header.php");
    ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <div class="titulo">
        <i class="ph ph-speedometer"></i> Dashboards
    </div>
    <div class="corpo container mt-4">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center d-flex flex-row align-items-center p-3">
                    <div class="icon">
                        <i class="icon ph-bold ph-t-shirt fa-2x"></i>
                    </div>
                    <div class="justify-content-center" style="width: 100%">
                        <h5 class="card-title">Total de Peças</h5>
                        <p class="card-text" id="total_pecas"></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center d-flex flex-row align-items-center p-3">
                    <div class="icon">
                        <i class="icon ph-bold ph-t-shirt fa-2x"></i>
                    </div>
                    <div class="justify-content-center" style="width: 100%">
                        <h5 class="card-title">Total de Ops</h5>
                        <p class="card-text" id="total_ops"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-3" style="max-height: 70vh; overflow: auto;">
            <div class="col-12">
                <div class="card p-3">
                    <h4 class="card-title" style="position: sticky; top: 0; min-width: 100%; background-color: white; z-index: 1">Carga por Faccionistas</h4>
                    <div id="barChart"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-3">
            <div class="col-md-6 d-flex align-items-stretch mb-3">
                <div class="card p-3 flex-fill">
                    <h4 class="card-title">Carga por Categorias</h4>
                    <div id="donutChart"></div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-stretch mb-3">
                <div class="card p-3 flex-fill">
                    <h4 class="card-title">Carga por Status</h4>
                    <div id="pieChart"></div>
                </div>
            </div>
        </div>
        <button id="btn-filtros" onclick="$('#modal-filtros').modal('show')">
            <i class="icon ph-bold ph-funnel" style="margin-left: 9px;"></i>
        </button>

    </div>

    <div class="modal fade modal-custom" id="modal-filtros" tabindex="-1" aria-labelledby="customModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customModalLabel" style="color: black;">Filtros</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex" style="align-items: start; text-align: left;">
                    <div class="dropdown mb-4 mr-4" id="dropdown-faccionistas">
                        <button class="btn dropdown-toggle" type="button" id="btn-dropdown-faccionistas" data-bs-toggle="dropdown"
                            aria-expanded="false" style="border: 1px solid black; border-radius: 10px; padding: 10px 10px;">
                            Faccionistas
                        </button>
                        <ul class="dropdown-menu p-3 dropdown-checkbox" aria-labelledby="dropdown-faccionistas" style="width: 300px;">
                            <!-- Campo de Pesquisa -->
                            <li>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="search-faccionistas" class="form-control" placeholder="Pesquisar..." aria-label="Pesquisar Faccionistas">
                                </div>
                            </li>
                            <hr>
                            <!-- Aqui as opções serão inseridas dinamicamente -->
                            <div id="faccionistas-options"></div>
                        </ul>
                    </div>
                    <div class="dropdown" id="dropdown-categorias">
                        <button class="btn dropdown-toggle" type="button" id="btn-dropdown-categorias" data-bs-toggle="dropdown"
                            aria-expanded="false" style="border: 1px solid black; border-radius: 10px; padding: 10px 10px;">
                            Categorias
                        </button>
                        <ul class="dropdown-menu p-3 dropdown-checkbox" aria-labelledby="dropdown-categorias" style="width: 300px;">
                            <!-- Campo de Pesquisa -->
                            <li>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="search-categorias" class="form-control" placeholder="Pesquisar..." aria-label="Pesquisar Faccionistas">
                                </div>
                            </li>
                            <hr>
                            <!-- Aqui as opções serão inseridas dinamicamente -->
                            <div id="categorias-options"></div>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn" style="background-color: #002955; color: white" onclick="AtualizarDados()">Aplicar</button>
                </div>
            </div>
        </div>

        <?php include_once("../../../templates/footer.php"); ?>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="script.js"></script>
