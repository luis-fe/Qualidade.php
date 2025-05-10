import pandas as pd
import pytz
from datetime import datetime
from connection import WmsConnectionClass


class ProdutividadeWms:
    '''Classe responsável pela interação com a produtividade do WMS'''

    def __init__(self, codEmpresa=None, codUsuarioCargaEndereco=None,
                 endereco=None, qtdPcs=0, codNatureza=None,
                 dataInicio='', dataFim = ''):
        self.codEmpresa = codEmpresa
        self.codUsuarioCargaEndereco = codUsuarioCargaEndereco
        self.endereco = endereco
        self.qtdPcs = qtdPcs
        self.codNatureza = codNatureza
        self.dataHora = self.__obterDataHoraSystem()
        self.dataInicio = dataInicio
        self.dataFim = dataFim

    def inserirProducaoCarregarEndereco(self):
        '''Método que insere a produtividade na ação de recarregar endereço do WMS'''

        sql = """
            INSERT INTO 
                "Reposicao"."ProducaoRecarregarEndereco"
                ("codEmpresa", "usuario_carga", "dataHoraCarga", "endereco", "qtdPcs", "codNatureza")
            VALUES 
                (%s, %s, %s, %s, %s, %s)
        """

        with WmsConnectionClass.WmsConnectionClass(self.codEmpresa).conexao() as conn:
            with conn.cursor() as curr:
                curr.execute(
                    sql,
                    (self.codEmpresa, self.codUsuarioCargaEndereco, self.dataHora, self.endereco, self.qtdPcs,
                     self.codNatureza)
                )
                conn.commit()

        return {'Status': True, 'Mensagem': 'Produtividade inserida com sucesso!'}

    def __obterDataHoraSystem(self):
        '''Método privado que obtém a data e hora do sistema com fuso horário'''
        fuso_horario = pytz.timezone('America/Sao_Paulo')
        agora = datetime.now(fuso_horario)
        return agora.strftime('%Y-%m-%d %H:%M:%S')


    def consultaProd_CarregarCaixas(self):
        '''Método que consulta a produtividade da atividade de Carregar Caixa no endereco '''


        sql = """
            select
                e."codEmpresa",
                e."usuario_carga",
                c.nome ,
                count("endereco") as "qtdCaixas",
                sum("qtdPcs") as "qtdPcs"
            from
                "Reposicao"."ProducaoRecarregarEndereco" e
            inner join
                "Reposicao"."Reposicao".cadusuarios c 
                on c.codigo::Varchar = e."usuario_carga"
            where 
                e."dataHoraCarga"::Date >= %s
                and e."dataHoraCarga"::Date <= %s
                and e."codEmpresa" = %s
            group by 
                e."codEmpresa", e."usuario_carga", c.nome
            order by 
                "qtdCaixas" desc 
        """


        conn = WmsConnectionClass.WmsConnectionClass(self.codEmpresa).conexaoEngine()

        consulta = pd.read_sql(sql, conn, params=(self.dataInicio, self.dataFim, self.codEmpresa))


        return consulta






