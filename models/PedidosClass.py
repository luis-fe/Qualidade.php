import gc
import pandas as pd
from connection import ConexaoCSW
import models.configuracoes.empresaConfigurada


class Pedido():
    '''Classe que gerencia os pedidos enviado ao WMS'''

    def __init__(self, codEmpresa = None, codPedido = None):

        self.codEmpresa = str(codEmpresa)
        self.codPedido = codPedido

    def consultaERPCSW_TipoNota(self):
        '''Metodo  que busca no ERP do CSW os tipo de Notas '''

        sql = """
        select
            n.codigo ,
            n.descricao, 
            t.codNatureza1 as natureza
        from
            Fat.TipoDeNotaPadrao n
        left join
            est.Transacao t
            on t.codEmpresa = 1 
            and t.codTransacao = n.codTransacaoEstoque 
        """

        with ConexaoCSW.Conexao() as conn:
            with conn.cursor() as cursor_csw:
                cursor_csw.execute(sql)
                colunas = [desc[0] for desc in cursor_csw.description]
                # Busca todos os dados
                rows = cursor_csw.fetchall()
                # Cria o DataFrame com as colunas
                consulta = pd.DataFrame(rows, columns=colunas)
                del rows
                gc.collect()
        return  consulta

    def __sugestoesPedidosAberto_ErpCsw(self):
        '''Metodo PRIVADO que busca no ERP do CSW as sugestoes de pedidos'''

        SugestoesAbertos = """
                SELECT 
                    codPedido||'-'||codsequencia as codPedido, 
                    codPedido as codPedido2, 
                    dataGeracao, 
                    priorizar, 
                    vlrSugestao,
                    situacaoSugestao, 
                    dataFaturamentoPrevisto  
                from 
                    ped.SugestaoPed
                WHERE 
                    codEmpresa =""" + self.codEmpresa +""" and situacaoSugestao =2 """


        with ConexaoCSW.Conexao() as conn:
            with conn.cursor() as cursor_csw:
                cursor_csw.execute(SugestoesAbertos)
                colunas = [desc[0] for desc in cursor_csw.description]
                # Busca todos os dados
                rows = cursor_csw.fetchall()
                # Cria o DataFrame com as colunas
                SugestoesAbertos = pd.DataFrame(rows, columns=colunas)
                del rows
                gc.collect()
        return  SugestoesAbertos


    def __sugestoesPedidosMKTo_ErpCsw(self):
        '''Metodo PRIVADO que busca no ERP do CSW as Pedidos de Marketing '''


        PedidosMkt = """
        SELECT 
            codPedido||'-Mkt' as codPedido,
            codPedido as codPedido2,
            dataemissao as dataGeracao,
            '0' as priorizar, 
            vlrPedido as vlrSugestao, 
            '2' as situacaoSugestao,
            dataPrevFat as dataFaturamentoPrevisto
        FROM 
            ped.Pedido e
        WHERE 
            e.codtiponota in (1001 , 38) 
            and situacao = 0 and codEmpresa = """+str(self.codEmpresa)+"""
        and dataEmissao > DATEADD(DAY, -120, GETDATE())"""



        with ConexaoCSW.Conexao() as conn:
            with conn.cursor() as cursor_csw:
                cursor_csw.execute(PedidosMkt)
                colunas = [desc[0] for desc in cursor_csw.description]
                # Busca todos os dados
                rows = cursor_csw.fetchall()
                # Cria o DataFrame com as colunas
                SugestoesAbertos = pd.DataFrame(rows, columns=colunas)
                del rows
                gc.collect()
        return  SugestoesAbertos


    def get_SugestoesPedidosGeral(self):
        '''Metodo que unifica as sugestoes + pedidos de marketing'''

        sugestoes = self.__sugestoesPedidosAberto_ErpCsw()
        sugestoesMkt = self.__sugestoesPedidosMKTo_ErpCsw()

        PedidosSituacao = pd.concat([sugestoes, sugestoesMkt])

        return PedidosSituacao

    def __situacaoPedidoGeral(self):


        PedidosSituacao = """
                select 
                    DISTINCT p.codPedido||'-'||p.codSequencia as codPedido , 
                    'Em Conferencia' as situacaopedido  
                FROM 
                    ped.SugestaoPedItem p
                join 
                    ped.SugestaoPed s 
                    on s.codEmpresa = p.codEmpresa 
                    and s.codPedido = p.codPedido 
                    and s.codsequencia = p.codSequencia 
                WHERE 
                    p.codEmpresa ="""+self.codEmpresa +"""and s.situacaoSugestao = 2
            UNION
                SELECT 
                    codPedido||'-Mkt' as codPedido,
                    'Em Conferencia' as situacaopedido 
                FROM 
                    ped.Pedido e
                WHERE 
                    e.codtiponota in (1001 , 38) 
                    and situacao = 0 
                    and codEmpresa = """ +str(self.codEmpresa)+""" 
                    and dataEmissao > DATEADD(DAY, -120, GETDATE()) """


        with ConexaoCSW.Conexao() as conn:
            with conn.cursor() as cursor_csw:
                cursor_csw.execute(PedidosSituacao)
                colunas = [desc[0] for desc in cursor_csw.description]
                # Busca todos os dados
                rows = cursor_csw.fetchall()
                # Cria o DataFrame com as colunas
                PedidosSituacao = pd.DataFrame(rows, columns=colunas)
                del rows
                gc.collect()
        return  PedidosSituacao




