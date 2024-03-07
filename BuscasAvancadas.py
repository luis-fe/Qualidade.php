##################################### ARQUIVO.py UTILIZADO PARA CATALOGAR OS CODIGOS SQL DE BUSCA NO CSW: #########################################################

#Elaborado por : Luis Fernando Gonçalves de Lima Machado

## SQL BUSCANDO AS ORDEM DE PRODUCAO EM ABERTO - velocidade media da consulta : 0,850 s (otima)

def OP_Aberto():


    OP_emAberto = 'SELECT (select pri.descricao  FROM tcp.PrioridadeOP pri WHERE pri.Empresa = 1 and o.codPrioridadeOP = pri.codPrioridadeOP ) as prioridade, dataInicio as startOP, codProduto  , numeroOP , codTipoOP , codFaseAtual as codFase , codSeqRoteiroAtual as seqAtual, ' \
                  'codPrioridadeOP, codLote , codEmpresa, (SELECT f.nome from tcp.FasesProducao f WHERE f.codempresa = 1 and f.codfase = o.codFaseAtual) as nomeFase, ' \
                  '(select e.descricao from tcp.Engenharia e WHERE e.codempresa = o.codEmpresa and e.codengenharia = o.codProduto) as descricao' \
                  ' FROM tco.OrdemProd o '\
                   ' WHERE o.codEmpresa = 1 and o.situacao = 3'

    return OP_emAberto



## SQL BUSCANDO AS " DATA/HORA DE MOVIMENTACAO DAS ORDEM DE PRODUCAO EM ABERTO " - velocidade media: 4,500 s (regular)

def DataMov(AREA):

    if AREA == 'PRODUCAO':
        DataMov = 'SELECT numeroOP, dataMov as data_entrada , horaMov , seqRoteiro, (seqRoteiro + 1) as seqAtual FROM tco.MovimentacaoOPFase mf '\
            ' WHERE  numeroOP in (SELECT o.numeroOP from  tco.OrdemProd o' \
            ' having o.codEmpresa = 1 and o.situacao = 3 and o.codtipoop <> 13) and mf.codempresa = 1 order by codlote desc'
    else:
        DataMov = 'SELECT numeroOP, dataMov as data_entrada , horaMov , seqRoteiro, (seqRoteiro + 1) as seqAtual FROM tco.MovimentacaoOPFase mf '\
            ' WHERE  numeroOP in (SELECT o.numeroOP from  tco.OrdemProd o' \
            ' having o.codEmpresa = 1 and o.situacao = 3 and o.codtipoop = 13) and mf.codempresa = 1 order by codlote desc'

    return DataMov

# SQL BUSCAR OS TIPO's DE OP DO CSW
def TipoOP():

    TipoOP = 'SELECT t.codTipo as codTipoOP, t.nome as nomeTipoOp  FROM tcp.TipoOP t WHERE t.Empresa = 1'

    return TipoOP


# Sql Buscando Pedidos Bloqueados NO CREDITO tempo 0,100 ms (otimo)
def BloqueiosCredito():

    BloqueiosCredito = "SELECT codPedido, 'BqCredito' as situacao  FROM Cre.PedidoCreditoBloq WHERE Empresa = 1 and situacao = 1 "

    return BloqueiosCredito

# Sql Buscando Pedidos Bloqueados NO COMERCIAL tempo 0,050 ms (otimo)
def bloqueioComerical():
    bloqueioComerical = 'SELECT codPedido, situacaoBloq as situacao from ped.PedidoBloqComl c WHERE codEmpresa = 1 and situacaoBloq = 1 '

    return bloqueioComerical

# SQL CAPA DOS PEDIDOS: Velocidade media : 1,5 s (ótimo - para o intervalo de 1 ano de pedidos)
def CapaPedido (iniVenda, finalVenda, tiponota):

    CapaPedido = "SELECT dataEmissao, codPedido, "\
    "(select c.nome as nome_cli from fat.cliente c where c.codCliente = p.codCliente) as nome_cli, "\
    " codTipoNota, dataPrevFat, codCliente, codRepresentante, descricaoCondVenda, vlrPedido as vlrSaldo,qtdPecasFaturadas "\
    " FROM Ped.Pedido p"\
    " where codEmpresa = 1 and  dataEmissao >= '" + iniVenda + "' and dataEmissao <= '" + finalVenda + "' and codTipoNota in (" + tiponota + ")"\
    " order by codPedido desc "

    return CapaPedido


#SQL DE PEDIDOS NO NIVEL SKU - Velocidade Media 5 s para dados de 1 ano (regular)
def pedidosNivelSKU (iniVenda, finalVenda, tiponota):
    pedidosNivelSKU = 'select codPedido, codProduto as reduzido, qtdeCancelada, qtdeFaturada, qtdePedida '\
                        'from ped.PedidoItemGrade  p where codEmpresa = 1 and p.codPedido in '\
                        "(select p.codPedido FROM Ped.Pedido p where codEmpresa = 1 and dataEmissao >= '" + iniVenda + "' and dataEmissao <= '" + finalVenda + ")"

    return pedidosNivelSKU
#SQL DE BUSCA DE TERCEIRIZADOS POR OP E FASE - Velocidade Média: 0,700 s

def OPporTecerceirizado():
    OpTercerizados = 'SELECT CONVERT(VARCHAR(10), R.codOP) AS numeroOP, R.codFase as codFase, R.codFac,'\
  ' (SELECT nome  FROM tcg.Faccionista  f WHERE f.empresa = 1 and f.codfaccionista = r.codfac) as nome'\
 ' FROM TCT.RemessaOPsDistribuicao R'\
' INNER JOIN tco.OrdemProd op on'\
    ' op.codempresa = r.empresa and op.numeroop = CONVERT(VARCHAR(10), R.codOP)'\
    ' WHERE R.Empresa = 1 and op.situacao = 3 and r.situac = 2'

    return OpTercerizados

#SQL DEPARA DA ENGENHARIA PAI X FILHO: velocidade Média : 0,20 segundos

def DeParaFilhoPaiCategoria():

    dePara = "SELECT e.codEngenharia as codProduto,"\
     " (SELECT ep.descricao from tcp.Engenharia ep WHERE ep.codempresa = 1 and ep.codengenharia like '%-0' and '01'||SUBSTRING(e.codEngenharia, 3,9) = ep.codEngenharia) as descricaoPai"\
" FROM tcp.Engenharia e"\
" WHERE e.codEmpresa = 1 and e.codEngenharia like '6%' and e.codEngenharia like '%-0' and e.codEngenharia not like '65%'"

    return dePara

#SQL DE BUSCA DAS REQUISICOES DAS OPS : velocidade Média : 1,20 segundos

def RequisicoesOPs():

    requisicoes = ' SELECT numero,numOPConfec as numeroOP ,  seqRoteiro as fase, sitBaixa, codNatEstoque  ' \
                  ' FROM tcq.Requisicao r WHERE r.codEmpresa = 1 and ' \
                  ' r.numOPConfec in (SELECT op.numeroop from tco.OrdemProd op WHERE op.codempresa = 1 and op.situacao = 3)'

    return requisicoes

#SQL DE BUSCA DAS PARTES DAS OPS : velocidade Média : 0,35 segundos (OTIMO)

def LocalizarPartesOP():

    partes = "SELECT p.codlote as numero, codopconjunto as numeroOP , '425' as fase, op.situacao as sitBaixa, codOPParte as codNatEstoque,"\
             " (SELECT e.descricao from tcp.Engenharia e WHERE e.codempresa = 1 and e.codengenharia = op.codProduto) as nomeParte"\
             " FROM tco.RelacaoOPsConjuntoPartes p"\
             " inner join tco.OrdemProd op on op.codEmpresa = p.Empresa and op.numeroOP = p.codOPParte "\
             " WHERE codopconjunto in (SELECT op.numeroop from tco.OrdemProd op WHERE op.codempresa = 1 and op.situacao = 3 and op.codfaseatual = 426 )"

    return partes

#SQL DE BUSCA DAs cores : velocidade Média : 0,07 segundos (OTIMO)
def CoresVariantesCSW():
    cores = "SELECT codigoCor as sortimentosCores , descricao  FROM Ppcpt_Gen_Ttg.TabGenClasseCor c "\
                        "WHERE c.codEmpresa = 1 "
    return cores

#SQL DE BUSCA DAs transportadoras cadastras no csw : velocidade Média : 0,200 segundos (OTIMO)
def tranportadora(empresa):
    tranportadora = 'SELECT  t.cidade , t.siglaEstado as estado, f.fantasia as transportadora  FROM Asgo_Trb.TransPreferencia t'\
        ' join cad.Transportador  f on  f.codigo  = t.Transportador  '\
        ' WHERE t.Empresa = '+empresa

    return tranportadora