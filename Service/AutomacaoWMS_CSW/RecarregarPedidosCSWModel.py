import ConexaoCSW
import ConexaoPostgreMPL
import pandas as pd
import pytz
import datetime
from psycopg2 import sql

def obterHoraAtual():
    fuso_horario = pytz.timezone('America/Sao_Paulo')  # Define o fuso horário do Brasil
    agora = datetime.datetime.now(fuso_horario)
    hora_str = agora.strftime('%d/%m/%Y %H:%M')
    return hora_str

def criar_agrupamentos(grupo):
    return '/'.join(sorted(set(grupo)))

def obter_notaCsw():
    conn = ConexaoCSW.Conexao()
    data = pd.read_sql(" select t.codigo ,t.descricao  from Fat.TipoDeNotaPadrao t ", conn)
    conn.close()

    return data

def RecarregarPedidos(empresa):
    conn = ConexaoCSW.Conexao()
    SugestoesAbertos = pd.read_sql("SELECT codPedido||'-'||codsequencia as codPedido, codPedido as codPedido2, dataGeracao,  "
                                   "priorizar, vlrSugestao,situacaosugestao, dataFaturamentoPrevisto  from ped.SugestaoPed  "
                                   'WHERE codEmpresa ='+empresa+
                                   ' and situacaoSugestao =2',conn)

    PedidosSituacao = pd.read_sql("select DISTINCT p.codPedido||'-'||p.codSequencia as codPedido , 'Em Conferencia' as situacaopedido  FROM ped.SugestaoPedItem p "
                                  'join ped.SugestaoPed s on s.codEmpresa = p.codEmpresa and s.codPedido = p.codPedido '
                                  'WHERE p.codEmpresa ='+empresa+
                                  ' and s.situacaoSugestao = 2', conn)

    SugestoesAbertos = pd.merge(SugestoesAbertos, PedidosSituacao, on='codPedido', how='left')

    CapaPedido = pd.read_sql('select top 100000 codPedido as codPedido2, codCliente, '
                             '(select c.nome from fat.Cliente c WHERE c.codEmpresa = 1 and p.codCliente = c.codCliente) as desc_cliente, '
                             '(select r.nome from fat.Representante  r WHERE r.codEmpresa = 1 and r.codRepresent = p.codRepresentante) as desc_representante, '
                             '(select c.nomeCidade from fat.Cliente  c WHERE c.codEmpresa = 1 and c.codCliente = p.codCliente) as cidade, '
                             '(select c.nomeEstado from fat.Cliente  c WHERE c.codEmpresa = 1 and c.codCliente = p.codCliente) as estado, '
                             ' codRepresentante , codTipoNota, CondicaoDeVenda as condvenda  from ped.Pedido p  '
                                   ' WHERE p.codEmpresa ='+empresa+
                             ' order by codPedido desc ',conn)
    SugestoesAbertos = pd.merge(SugestoesAbertos,CapaPedido,on= 'codPedido2', how = 'left')
    SugestoesAbertos.rename(columns={'codPedido': 'codigopedido', 'vlrSugestao': 'vlrsugestao'
        , 'dataGeracao': 'datageracao','situacaoSugestao':'situacaosugestao','dataFaturamentoPrevisto':'datafaturamentoprevisto',
        'codCliente':'codcliente', 'codRepresentante':'codrepresentante','codTipoNota':'codtiponota'}, inplace=True)
    tiponota = obter_notaCsw()
    tiponota['codigo'] = tiponota['codigo'].astype(str)
    tiponota.rename(columns={'codigo': 'codtiponota'}, inplace=True)
    SugestoesAbertos = pd.merge(SugestoesAbertos, tiponota, on='codtiponota', how='left')

    return SugestoesAbertos