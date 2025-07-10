import gc
from connection import ConexaoCSW
import pandas as pd


class Pedidos_Csw():
    '''Classe utilizado para obter informacoes referente a Pedidos junto ao CSW,
        obtendo a informacao para o WMS '''
    def __init__(self, codEmpresa = '1', codCliente = '',
                 desc_cliente = '', codRepresent = '',desc_representante='', cidade = '', estado = ''):

        self.codEmpresa = str(codEmpresa)
        self.codCliente = str(codCliente)
        self.desc_cliente = desc_cliente
        self.codRepresent = str(codRepresent)
        self.desc_representante = desc_representante
        self.cidade = cidade
        self.estado = estado


    def obter_fila_pedidos_nivel_capa(self):
        '''Metodo que obtem do ERP CSW os pedidos a nivel de capa'''



        sql = f"""
            select top 100000 
                codPedido as codPedido2, 
                convert(varchar(10),codCliente ) as codCliente,
                (select c.nome from fat.Cliente c WHERE c.codEmpresa = {self.codEmpresa } and p.codCliente = c.codCliente) as desc_cliente,
                (select r.nome from fat.Representante  r WHERE r.codEmpresa = {self.codEmpresa } and r.codRepresent = p.codRepresentante) as desc_representante, 
                (select c.nomeCidade from fat.Cliente  c WHERE c.codEmpresa = {self.codEmpresa } and c.codCliente = p.codCliente) as cidade,
                (select c.nomeEstado from fat.Cliente  c WHERE c.codEmpresa = {self.codEmpresa } and c.codCliente = p.codCliente) as estado,
                codRepresentante , 
                codTipoNota,
                CondicaoDeVenda as condvenda  
            from 
                ped.Pedido p
            WHERE 
                p.codEmpresa = {self.codEmpresa }
            order by 
                codPedido desc
        """

        with ConexaoCSW.ConexaoInternoMPL() as conn:
            with conn.cursor() as cursor:
                cursor.execute(sql)
                colunas = [desc[0] for desc in cursor.description]
                rows = cursor.fetchall()
                consulta = pd.DataFrame(rows, columns=colunas)

            del rows




            return consulta


    def get_clientes_Pedidos_revisar(self):
        '''Metodo que busca do ERP Csw os clientes com pedidos para revisar'''

        sql = f"""
            select
                c.codCliente, 'REVISAR' as obs
            from
                fat.CliComplemento2 c
            where
                (c.observacao4 like '%REVISAR%'
                OR c.observacao3 like '%REVISAR%'
                )
                and c.codEmpresa = 1
        """

        with ConexaoCSW.ConexaoInternoMPL() as conn:
            with conn.cursor() as cursor:
                cursor.execute(sql)
                colunas = [desc[0] for desc in cursor.description]
                rows = cursor.fetchall()
                consulta = pd.DataFrame(rows, columns=colunas)

            del rows
            return consulta



    def sugestoesPedidosAberto_ErpCsw(self):
        '''Metodo  que busca no ERP do CSW as sugestoes de pedidos'''

        SugestoesAbertos = f"""
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
                    codEmpresa ={self.codEmpresa} and situacaoSugestao = 2 
                    """


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


