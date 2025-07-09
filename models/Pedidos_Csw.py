from connection import ConexaoCSW
import pandas as pd


class Pedidos_Csw():
    '''Classe utilizado para obter informacoes referente a Pedidos junto ao CSW'''
    def __init__(self, codEmpresa = '1'):

        self.codEmpresa = str(codEmpresa)

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