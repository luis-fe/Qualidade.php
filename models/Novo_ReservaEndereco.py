import pandas as pd
from connection import WmsConnectionClass

class Reserva_endereco():
    '''Classe que gerencia a reserva de endereo no WMS'''

    def __init__(self, codEmpresa = '', codNatureza = '', tipoReserva = 'Retirar Substitutos', ordemDistribuicao = 'asc'):
        '''Contrutor da classe com os atributos:'''

        self.codEmpresa = codEmpresa
        self.codNatureza = codNatureza
        self.tipoReserva = tipoReserva
        '''Esse atributo informa se o cálculo da reserva irá Considerar ou Nao as Tags Armazendas como Substitutas '''

        self.ordemDistribuicao = ordemDistribuicao

    def __selecaoSku_p_distribuicao(self):
        '''Metdo que seleciona os Sku's que serao distribuidos '''

        conn = WmsConnectionClass.WmsConnectionClass(self.codEmpresa).conexaoEngine()


        # 1 Filtra oa reserva de sku somente para os skus em pedidos:
        skuEmPedios = """
                select distinct 
                    produto  
                from 
                    "Reposicao".pedidossku ps
                join 
                	"Reposicao".filaseparacaopedidos f
                left join 
                	"Reposicao".configuracoes.tiponota_nat tn 
                on
                	tn.tiponota = f.codtiponota 
                on
                	f.codigopedido = ps.codpedido 
                where 
                    necessidade > 0 
                    and reservado = 'nao'
                    and natureza = %s
        """
        consulta = pd.read_sql(skuEmPedios,conn, params=(self.codNatureza,))

        return consulta

    def __get_saldoLiquido_Enderecos(self):
        conn = WmsConnectionClass.WmsConnectionClass(self.codEmpresa).conexaoEngine()


        if self.tipoReserva == 'Substitutos' and self.ordemDistribuicao == 'asc':
            calculoEnderecos = """
            select  
                codreduzido as produto, 
                codendereco as codendereco2, 
                "SaldoLiquid"  
            from 
                "Reposicao"."calculoEndereco" c
            where  
                natureza = %s 
                and c.codendereco  
                    in (
                        select 
                            "Endereco" 
                        from 
                            "Reposicao".tagsreposicao t 
                        where 
                            resticao  like %s ) 
                and "SaldoLiquid" >0  
            order by 
                "SaldoLiquid" asc
        """
            enderecosSku = pd.read_sql(calculoEnderecos, conn, params=(self.codNatureza,'%||%'))
