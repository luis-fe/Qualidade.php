<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
}
echo '<script>const empresa = "' . $empresa . '";</script>';

?>
<link rel="stylesheet" href="style.css">




<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <form id="FormCadEnderecos">
            <div class="corpo-enderecos mb-5" style="overflow: auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 10px; margin-bottom: 10px">
                <div class="opcao">
                    <label class="form-check">
                        <input type="radio" id="radioIncluir" name="acaoEndereco" value="Incluir" class="form-check-input"> Incluir
                    </label><br>
                    <label class="form-check">
                        <input type="radio" id="radioExcluir" name="acaoEndereco" value="Excluir" class="form-check-input"> Excluir
                    </label>
                </div>

                <div class="form-group">
                    <label for="OpcoesNaturezas">Natureza de Estoque:</label>
                    <select class="form-select" id="SelectNatureza" required>
                        <option value="" disabled selected>Selecione a Natureza</option>
                        <option value="5">5 - P.A ATACADO</option>
                        <option value="7">7 - SALDO</option>
                        <option value="54">54 - BONIFICAÇÃO MKT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="OpcoesEstoque">Tipo de Estoque:</label>
                    <select class="form-select" id="OpcoesEstoque" required>
                        <option value="" disabled selected>Selecione a Opção</option>
                        <option value="COLECAO">COLEÇÃO</option>
                        <option value="SALDO">SALDO</option>
                    </select>
                </div>
            </div>

            <div class="form-group col-12 mb-5" style="justify-content: center; align-items: center; text-align: center">
                <button type="submit" class="btn btn-primary">Selecionar Endereços</button>
                <button type="button" id="ButtonReservaOp" class="btn btn-secondary">Endereços Reservados</button>
            </div>

        </form>
        <form id="FormCadEnderecos2">
        <h2 class="selecao-enderecos2 d-none">Cadastrar/Excluir Endereços</h2>
            <div class="selecao-enderecos2 col-12 mb-5 d-none" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 10px; margin-bottom: 10px; display: flex">
                <div class="form-row col-md-6">
                    <div class="form-group col-md-12">
                        <label for="inputRuaInicial">Rua Inicial</label>
                        <input type="text" id="inputRuaInicial" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputModuloInicial">Módulo Inicial</label>
                        <input type="text" id="InputModuloInicial" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputPosicaoInicial">Posição Inicial</label>
                        <input type="text" id="InputPosicaoInicial" class="form-control" required>
                    </div>
                </div>
                <div class="form-row col-md-6">
                    <div class="form-group col-md-12">
                        <label for="inputRuaFinal">Rua Final</label>
                        <input type="text" id="inputRuaFinal" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputModuloFinal">Módulo Final</label>
                        <input type="text" id="InputModuloFinal" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputPosicaoFinal">Posição Final</label>
                        <input type="text" id="InputPosicaoFinal" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="div-botoes2 col-12 mb-5 d-none" style="justify-content: center; align-items: center; text-align: center">
                <button type="submit" id="BotaoPersistir" class="btn btn-success"></button>
                <button type="button" id="BotaoCancelarEnderecos" class="btn btn-danger">CANCELAR</button>
            </div>
        </form>
        <form id="FormCadEnderecos3">
            <h2 class="selecao-enderecos3 d-none">Reservar Endereços</h2>
            <div class="selecao-enderecos3 col-12 mb-5 d-none" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 10px; margin-bottom: 10px; display: flex">
                <div class="form-row col-md-6">
                    <div class="form-group col-md-12">
                        <label for="inputRuaInicial">Rua Inicial</label>
                        <input type="text" id="inputRuaInicialReserva" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputModuloInicial">Módulo Inicial</label>
                        <input type="text" id="InputModuloInicialReserva" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputPosicaoInicial">Posição Inicial</label>
                        <input type="text" id="InputPosicaoInicialReserva" class="form-control" required>
                    </div>
                </div>
                <div class="form-row col-md-6">
                    <div class="form-group col-md-12">
                        <label for="inputRuaFinal">Rua Final</label>
                        <input type="text" id="inputRuaFinalReserva" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputModuloFinal">Módulo Final</label>
                        <input type="text" id="InputModuloFinalReserva" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="InputPosicaoFinal">Posição Final</label>
                        <input type="text" id="InputPosicaoFinalReserva" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="div-botoes3 col-12 mb-5 d-none" style="justify-content: center; align-items: center; text-align: center">
                <button type="submit" id="BotaoPersistir" class="btn btn-success">RESERVAR ENDEREÇO</button>
                <button type="button" id="BotaoCancelarReserva" class="btn btn-danger">CANCELAR</button>
            </div>
        </form>

    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>

