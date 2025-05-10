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

        Atualizado = self.__obterDataHoraSystem()

        record = self.__recordHistorico_CarregarEndereco()
        record1 = record["qtdCaixas"][0]
        total = consulta['qtdCaixas'].sum()
        totalPcs = consulta['qtdPcs'].sum()

        data = {
            '0- Atualizado:':f'{Atualizado}',
            '1- Record': f'{record["nome"][0]}',
            '1.1- Record qtdCaixas': f'{record1}',
            '1.2- Record data': f'{record["dataRecord"][0]}',
            '2 Total Caixas':f'{total}',
            '2.1 Total Pcs': f'{totalPcs}',
            '3- Ranking Carregar Endereco': consulta.to_dict(orient='records')
        }
        return pd.DataFrame([data])




    def __recordHistorico_CarregarEndereco(self):
        '''Método que busca o record Historico da Atividade de Carregar Endereco'''

        sql = """
            select 
                e."codEmpresa",
                e."usuario_carga",
                c.nome ,
                count("endereco") as "qtdCaixas",
                sum("qtdPcs") as "qtdPcs",
                e."dataHoraCarga"::Date as "dataRecord"
            from
                "Reposicao"."ProducaoRecarregarEndereco" e
            inner join
                "Reposicao"."Reposicao".cadusuarios c 
                on c.codigo::Varchar = e."usuario_carga"
            where 
                 e."codEmpresa" = %s
            group by 
                e."codEmpresa", e."usuario_carga", c.nome, e."dataHoraCarga"::Date
            order by 
                "qtdCaixas" desc 
           	limit 1
        """

        conn = WmsConnectionClass.WmsConnectionClass(self.codEmpresa).conexaoEngine()

        consulta = pd.read_sql(sql, conn, params=(self.codEmpresa, ))

        return consulta







