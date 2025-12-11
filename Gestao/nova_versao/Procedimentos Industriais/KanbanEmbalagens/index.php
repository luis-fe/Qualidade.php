<?php
include_once("../../../../templates/LoadingGestao.php");
include_once('../../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
</style>

<div class="titulo-tela">
            <a href="../" class="text-decoration-none text-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
</div>
    <div class="row m-2 align-items-center">


        <div class="col">
            <h5 class="mb-0">
                <strong>Consumo via Kanban: Embalagens</strong>
            </h5>
        </div>
    </div>

    <div class="col-md-0 m-2  d-flex flex-column justify-content-center">
        <p class="mb-1"><strong>Objetivo:</strong>
        Garantir a Gestão Estoque das embalagens utilizadas nos produtos, 
    </br>entregando acurácia de estoque, controle de desperdicios e apurando o custo de forma correta.
    </p>
    </div>


<div class="col-12  bg-light p-0 border-start "> 
    <a href="URL_DESTINO_DO_ANUNCIO" target="_blank">
        <img src="Diagrama em branco.png" class="img-fluid rounded shadow-sm" alt="Descrição do Anúncio" style="width: 2080px; height: 900px;">
    </a>
</div>

<div class="bg-light p-0 border-start d-flex g-0">
    <div class="col-md-6 ps-3 py-2 pe-0"> 
        <h5>Diagrama de Causas - **Processo Kanban Embalagem**</h5>
        <a href="URL_DESTINO_DO_DIAGRAMA" target="_blank">
            <img src="causa_efeito.png"
                class="img-fluid rounded shadow-sm"
                alt="Diagrama de Causa e Efeito (Ishikawa) para o processo X"
                style="height: 400px;">
        </a>
    </div>

    <div class="d-flex flex-column justify-content-start">
        <p>
            **Causas que podem levar à não conformidade neste processo:**
            <br>
            01 - **Cadastro de Engenharia: Analista de Cadastro**
        </p>

        <a href="URL_DESTINO_DO_DETALHE" target="_blank" class="mt-2">
            <img src="1_cad engenharia.png"
                class="img-fluid rounded shadow-sm"
                alt="Detalhe da não conformidade em Cadastro de Engenharia"
                style="height: 380px; width: 100%;"> 
        </a>
    </div>
</div>

<div class="bg-light p-0 border-start d-flex g-0">
    <div class="col-md-6 ps-3 py-2 pe-0"> 
        <p>
            <br>
            02 - **⚠️Configuracao no ERP CSW(CCTCG090) -Area de Processos**
        </p>
        <a href="URL_DESTINO_DO_DETALHE" target="_blank" class="mt-2">
            <img src="2ConfiguracaoKanban.png"
                class="img-fluid rounded shadow-sm"
                alt="Detalhe da não conformidade em Cadastro de Engenharia"
                style="height: 190px; width: 95%;"> 
        </a>


    </div>

    <div class="d-flex flex-column justify-content-start">
        <p>
            <br>
            03 - **Rotina de Geração de Requisicao Independente - CCSERQ010:**
        </p>

        <a href="URL_DESTINO_DO_DETALHE" target="_blank" class="mt-2">
            <img src="ReqTransf.png"
                class="img-fluid rounded shadow-sm"
                alt="Detalhe da não conformidade em Cadastro de Engenharia"
                style="height: 380px; width: 100%;"> 
        </a>
    </div>
</div>


<div class="bg-light p-0 border-start d-flex g-0">
    <div class="col-md-6 ps-3 py-2 pe-0"> 
        <p>
            <br>
            04 - **Baixa da Requisicao Independente -CCSERQ050 **
        </p>
        <a href="URL_DESTINO_DO_DETALHE" target="_blank" class="mt-2">
            <img src="ReqBaixa.png"
                class="img-fluid rounded shadow-sm"
                alt="Detalhe da não conformidade em Cadastro de Engenharia"
                style="height: 250px; width: 92%;"> 
        </a>


    </div>

    <div class="d-flex flex-column justify-content-start">
        <p>
            <br>
            05 - **Dados de Base para a Visao de Power BI:**
            <br>responsabilidade Analista de Processos
            <h6>Regras a serem consideradas</h6>
            <br><p><strong>I</strong>:Será fornecedido query do banco de dados do ERP para o desenvolvimento
            <br>da visao de BI.</p>
        </p>
        <h6>Sql requisicao de transferencia:</h6>
        <p class="mt-0">
            <small class="text-muted fw-light">
                SELECT
                    *
                FROM
                    Serq.Requisicao r
                WHERE
                    r.codEmpresa = 1
                    and r.centroCusto in (21120110); --21120110: EXPEDICAO DE TERCEIRIZADOS
            </small>
        </p>
                <h6>Sql baixa de requisicao nivel item:</h6>
        <p class="mt-0">
            <small>nessa consulta é obtida o que foi transferido da nat.3 para nat61</small>
            </br>
            <small class="text-muted fw-light">
                SELECT
               		r.dtEmissao,
                	r.dtBaixa,
                    ri.codMaterial,
                    ri.nomeMaterial,
                    ri.codRequisicao,
                    ri.codNatureza,
                    r.codTransBaixa,
                    r.codCCusto,
                    r.observacao1 as faccionistaTransf
                FROM
                    Serq.RequisicaoItem ri
                inner join Serq.Requisicao r
                on r.codEmpresa = ri.codEmpresa 
                and r.numero = ri.codRequisicao 
                WHERE
                    ri.codEmpresa = 1
                    and ri.codMaterial in (
                    339344,
					339348,
					342487,
					344023
                    ) and ri.codNatureza = 3
            </small>
            </br>
            <small>***Obs.: para deixar a consulta mais rapido é preciso filtrar os codigos reduzido dos itens Kanban</small>
                    </br>
                    <small>nessa consulta é obtida o que foi consumido da nat61</small>
            </br>
            <small class="text-muted fw-light">
                SELECT
               		r.dtEmissao,
                	r.dtBaixa,
                    r.numOPConfec as OP,
                    ri.codMaterial,
                    ri.nomeMaterial,
                    ri.codRequisicao,
                    ri.codNatureza,
                    r.codTransBaixa,
                    r.codCCusto
                FROM
                    Serq.RequisicaoItem ri
                inner join Serq.Requisicao r
                on r.codEmpresa = ri.codEmpresa 
                and r.numero = ri.codRequisicao 
                WHERE
                    ri.codEmpresa = 1
                    and ri.codMaterial in (
                    339344,
					339348,
					342487,
					344023
                    ) and ri.codNatureza = 61
            </small>
            </br>
        </p>
        
 <br><p><strong>II</strong>:No controle de estoque por faccionista a saida será baixa kanban sistemico, 
    ao movimentar a fase 429; 
            <br>A regra é: caso nao tenha a fase revisao externa considera o faccionista da fase 429. 
            caso contenha a fase "Revisao Externa" ou "Acabamento Externo" o faccionista é o da ultima fase externa
        </p>
        <h6>Sql Obter faccionistas da OP x codFase:</h6>
        <p class="mt-0">
            <small class="text-muted fw-light">
    select
        CONVERT(varchar(11),d.codOP) as OP,
        d.codFac,
        d.codFase,
        o.idroteiro,
        op.datageracao,
        (select f.nome FROM tcg.Faccionista f
        WHERE f.Empresa = 1 
        and f.codfaccionista = d.codFac) as nomeFac
    FROM
        tct.RemessaOPsDistribuicao d
    inner join tco.RoteiroOP o 
        on o.codempresa = 1
        and o.numeroop = CONVERT(varchar(11),d.codOP)
        and o.codfase = d.codFase
    inner join tco.ordemprod op 
        on op.codEmpresa = 1
        and op.numeroOP = o.numeroop
    WHERE
        d.Empresa = 1
        and op.dataProgramacao >'2025-01-01'
            </small>
        </p>


</div>






<?php
include_once('../../../../templates/footerGestao.php');
?>
<script src="script1.js"></script>

<script>
    // Se o seu código de controle de visibilidade estiver aqui,
    // ele funcionará perfeitamente, pois o $ já estará disponível.
    

</script>
