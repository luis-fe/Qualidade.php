import gc
import psutil
from colorama import Fore
import pandas as pd
import ConexaoPostgreMPL
import ConexaoCSW
import fastparquet as fp
from models.AutomacaoWMS_CSW import controle
from gevent import os

def memory_usage():
    process = psutil.Process(os.getpid())
    return process.memory_info().rss  # Retorna o uso de memória em bytes
def IncrementarPediosProdutos():
    consulta = """SELECT top 1000000 codItem as seqCodItem, p.codPedido, p.codProduto , p.qtdePedida ,  p.qtdeFaturada, p.qtdeCancelada  FROM ped.PedidoItemGrade p
WHERE p.codEmpresa = 1 and p.codProduto  not like '8601000%' and p.codProduto  not like '83060062%'  and p.codProduto  not like '8306000%' and
p.codProduto not like '8302003%' and p.codProduto not like '8306003%' and p.codProduto not like '8306006%' and p.codProduto not like '8306007%'
order by codPedido desc"""

    return consulta

def CapaPedido2():
    consulta = """SELECT top 100000 p.codPedido , p.codTipoNota, p.dataemissao, p.dataPrevFat  FROM ped.Pedido p
WHERE p.codEmpresa = 1
order by p.codPedido desc """

    return consulta

def ValorDosItensPedido():
    consulta = """select top 350000 item.codPedido, 
    item.CodItem as seqCodItem, 
    item.precoUnitario, item.tipoDesconto, item.descontoItem, 
    case when tipoDesconto = 1 then ( (item.qtdePedida * item.precoUnitario) - item.descontoItem)/item.qtdePedida when item.tipoDesconto = 0 then (item.precoUnitario * (1-(item.descontoItem/100))) else item.precoUnitario end  PrecoLiquido 
    from ped.PedidoItem as item WHERE item.codEmpresa = 1 order by item.codPedido desc """

    return consulta

def SugestaoItemAberto():
    consulta = """SELECT p.codPedido , p.produto as codProduto , p.qtdeSugerida , p.qtdePecasConf,
case when (situacaoSugestao = 2 and dataHoraListagem>0) then 'Sugerido(Em Conferencia)' WHEN situacaoSugestao = 0 then 'Sugerido(Gerado)' WHEN situacaoSugestao = 2 then 'Sugerido(A listar)' else '' end StatusSugestao
FROM ped.SugestaoPedItem p
inner join ped.SugestaoPed c on c.codEmpresa = p.codEmpresa and c.codPedido = p.codPedido and c.codSequencia = p.codSequencia 
WHERE p.codEmpresa = 1"""

    return consulta


def IncrementarPedidos(rotina,datainico ):
    with ConexaoCSW.Conexao() as conn:
        with conn.cursor() as cursor_csw:
            # Executa a primeira consulta e armazena os resultados
            cursor_csw.execute(IncrementarPediosProdutos())
            colunas = [desc[0] for desc in cursor_csw.description]
            rows = cursor_csw.fetchall()
            pedidos = pd.DataFrame(rows, columns=colunas)

            # Executa a segunda consulta e armazena os resultados
            cursor_csw.execute(ValorDosItensPedido())
            colunas2 = [desc[0] for desc in cursor_csw.description]
            rows2 = cursor_csw.fetchall()
            pedidosValores = pd.DataFrame(rows2, columns=colunas2)

            pedidos = pd.merge(pedidos, pedidosValores, on=['codPedido', 'seqCodItem'], how='left')
            del pedidosValores

            # Executa a terceira consulta e armazena os resultados
            cursor_csw.execute(SugestaoItemAberto())
            colunas3 = [desc[0] for desc in cursor_csw.description]
            rows3 = cursor_csw.fetchall()
            sugestoes = pd.DataFrame(rows3, columns=colunas3)
            pedidos = pd.merge(pedidos, sugestoes, on=['codPedido', 'codProduto'], how='left')
            del sugestoes

            # Executa a quarta consulta e armazena os resultados
            cursor_csw.execute(CapaPedido2())  # Verifique se a consulta é correta
            colunas4 = [desc[0] for desc in cursor_csw.description]
            rows4 = cursor_csw.fetchall()
            capaPedido = pd.DataFrame(rows4, columns=colunas4)
            pedidos = pd.merge(pedidos, capaPedido, on='codPedido', how='left')
            # Limpeza de memória
            del rows, rows2, rows3, rows4, capaPedido
            gc.collect()

    etapa1 = controle.salvarStatus_Etapa1(rotina,'automacao', datainico,'from ped.pedidositemgrade')


    etapa2 = controle.salvarStatus_Etapa2(rotina,'automacao', etapa1,'realizar o mergem entre pedidos+pedidositemgrade ')


    pedidos['codTipoNota'] = pedidos['codTipoNota'].astype(str)
    pedidos = pedidos[(pedidos['codTipoNota'] != '38') & (pedidos['codTipoNota'] != '239') & (pedidos['codTipoNota'] != '223')]
    etapa3 = controle.salvarStatus_Etapa3(rotina,'automacao', etapa2,'filtrando tipo de notas')

    # Escolha o diretório onde deseja salvar o arquivo Parquet
    fp.write('pedidos.parquet', pedidos)
    etapa4 = controle.salvarStatus_Etapa4(rotina,'automacao', etapa3,'Salvando o DataFrame em formato fast')
    del pedidos, etapa4, etapa1, etapa2, conn , cursor_csw
    gc.collect()
    memoria_antes = memory_usage()
    print(Fore.MAGENTA + f'A MEMORIA apos de IncrementarPedidos  {round(memoria_antes / 1000000)}')