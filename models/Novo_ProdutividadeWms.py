import pandas as pd
import pytz
from datetime import datetime

import ConexaoPostgreMPL
from connection import WmsConnectionClass


class ProdutividadeWms:
    '''Classe responsável pela interação com a produtividade do WMS, com as regras e consultas '''

    def __init__(self, codEmpresa=None, codUsuarioCargaEndereco=None,
                 endereco=None, qtdPcs=0, codNatureza=None,
                 dataInicio='', dataFim = '', tempoAtualizacao = None):
        self.codEmpresa = codEmpresa
        self.codUsuarioCargaEndereco = codUsuarioCargaEndereco
        self.endereco = endereco
        self.qtdPcs = qtdPcs
        self.codNatureza = codNatureza
        self.dataHora = self.__obterDataHoraSystem()
        self.dataInicio = dataInicio
        self.dataFim = dataFim
        self.tempoAtualizacao = tempoAtualizacao

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

        self.tempoAtualizacao = 5 * 60
        self.temporizadorConsultaProdutividadeRepositorTagCaixa()

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


    def __inserir_produtividadeRepositorTagCaixa(self):
        '''Metodo que armazena a produtividade do repositor de tags inserida na caixa '''


        sql = """
            INSERT INTO 
                "Reposicao"."ProducaoeRepositorTagCaixa"
                ("codEmpresa", "usuario_repositorTAG", "dataHora", "NCaixa", "qtdPcs", "codNatureza")
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

    def temporizadorConsultaProdutividadeRepositorTagCaixa(self):
        '''Metodo que carrega e insere na tabela a Produtividade RepositorTagCaixa a cada nSegundo '''
        verificaAtualizacao = self.__atualizaInformacaoAtualizacao('temporizadorConsultaProdutividadeRepositorTagCaixa')
        if verificaAtualizacao == True:
            self.__exclusaoDadosProdutividadeBiparTagCaixa()

            sql = """
                        SELECT
                            rq.usuario,
                            rq."Ncarrinho",
                            rq.caixa,
                            rq."DataReposicao"::date AS data,
                            date_trunc('hour', rq."DataReposicao"::timestamp) + 
                                INTERVAL '1 minute' * floor(date_part('minute', rq."DataReposicao"::timestamp) / 5) * 5 AS hora_intervalo,
                            count(rq.codbarrastag) AS "qtdPcs"
                        FROM
                            "Reposicao"."off".reposicao_qualidade rq
                        where 
                            rq."DataReposicao"::date = CURRENT_DATE
                        GROUP BY
                            rq.usuario,
                            rq."Ncarrinho",
                            rq.caixa,
                            rq."DataReposicao"::date,
                            hora_intervalo
                        ORDER BY
                            data, hora_intervalo
            """

            conn = ConexaoPostgreMPL.conexaoEngine()

            consulta = pd.read_sql(sql,conn)
            consulta['data'] =consulta['data'].astype(str)
            consulta['id'] = (consulta.groupby('data').cumcount() + 1).astype(str) + '|'+consulta['data']



            ConexaoPostgreMPL.Funcao_Inserir(consulta,consulta['Ncarrinho'].size,'ProdutividadeBiparTagCaixa','append')


    def __exclusaoDadosProdutividadeBiparTagCaixa(self):
        '''Metodo que exclui os dados do dia na tabela ProdutividadeBiparTagCaixa '''


        delete = """
        delete 
            FROM "Reposicao"."Reposicao"."ProdutividadeBiparTagCaixa" pbtc
            WHERE pbtc."data"::date = CURRENT_DATE;
        """

        with ConexaoPostgreMPL.conexao() as conn2:
            with conn2.cursor() as curr:
                curr.execute(delete,)
                conn2.commit()




    def __atualizaInformacaoAtualizacao(self, nomeRotina = ''):
        '''Metodo que atualiza no banco de Dados Postgres a data da atualizacao '''

        sqlConsulta = """
        select 
            * 
        from 
            "Produtividade"."ControleAutomacaoProdutividade"
        where
            "Rotina" = %s
        """

        sqlInsert = """
        update 
            "Produtividade"."ControleAutomacaoProdutividade" 
        set
             "DataHora" = %s
        where
            "Rotina" = %s
        """

        conn = ConexaoPostgreMPL.conexaoEngine()
        consultaSql1 = pd.read_sql(sqlConsulta, conn ,params=(nomeRotina,))
        data_hora_atual = self.__obterHoraAtual()

        if not consultaSql1.empty:
            utimaAtualizacao = consultaSql1['DataHora'][0]

            # Converte as strings para objetos datetime
            data1_obj = datetime.strptime(data_hora_atual, "%Y-%m-%d  %H:%M:%S")
            data2_obj = datetime.strptime(utimaAtualizacao, "%Y-%m-%d  %H:%M:%S")

            # Calcula a diferença entre as datas
            diferenca = data1_obj - data2_obj

            # Obtém a diferença em dias como um número inteiro
            diferenca_em_dias = diferenca.days

            # Obtém a diferença total em segundos
            diferenca_total_segundos = diferenca.total_seconds()

            if diferenca_total_segundos >= self.tempoAtualizacao:

                with ConexaoPostgreMPL.conexao() as conn2:
                    with conn2.cursor() as curr:

                        curr.execute(sqlInsert,(data_hora_atual, nomeRotina))
                        conn2.commit()

                return True

            else:
                return False


        else :

            with ConexaoPostgreMPL.conexao() as conn2:
                with conn2.cursor() as curr:
                    curr.execute(sqlInsert, (data_hora_atual, nomeRotina))
                    conn2.commit()

            return True



    def __obterHoraAtual(self):
        '''Metodo Privado que retorna a Data Hora do Sistema Operacional'''
        fuso_horario = pytz.timezone('America/Sao_Paulo')  # Define o fuso horário do Brasil
        agora = datetime.now(fuso_horario)
        agora = agora.strftime('%Y-%m-%d %H:%M:%S')
        return agora


    def consultaConsultaProdutividadeRepositorTagCaixa(self):
        '''Método que consulta a Produtivdade de RepositorTag'''

        sqlMax = """
        select
	        max(hora_intervalo) as "Atualizado"
        from
	        "Reposicao"."Reposicao"."ProdutividadeBiparTagCaixa" pbtc
        where
	        "data"::Date = CURRENT_DATE        
	        """

        conn = ConexaoPostgreMPL.conexaoEngine()
        max = pd.read_sql(sqlMax,conn)

        if max.empty:
            Atualizado = self.__obterHoraAtual()
        else:
            Atualizado = max['Atualizado'][0]

        sqlConsultaRecord= """
        select
	        pbtc."data", "usuario", sum("qtdPcs")as producao, c.nome
        from
	        "Reposicao"."Reposicao"."ProdutividadeBiparTagCaixa" pbtc
	    join 	
	    	"Reposicao"."Reposicao".cadusuarios c 
	    	on c.codigo::varchar = pbtc.usuario 
	    group by 
	    	pbtc."data", "usuario", c.nome
	    order by 
	    	producao desc limit 1
        """

        consultaRecord = pd.read_sql(sqlConsultaRecord,conn)


        sql = """
                   select
				*
			from
				"Reposicao"."Reposicao"."ProdutividadeBiparTagCaixa" pbtc
			join 	
                "Reposicao"."Reposicao".cadusuarios c 
                on c.codigo::varchar = pbtc.usuario 
			where
				pbtc."data" >= %s
				and pbtc."data" <= %s
        """

        consulta = pd.read_sql(sql,conn, params=(self.dataInicio, self.dataFim,))
        total = consulta['qtdPcs'].sum()


        consulta = consulta.groupby(['nome','usuario','hora_intervalo']).agg({
            'qtdPcs':"sum"
        }).reset_index()

        consulta['ritmo'] =  round(((60*5)/ consulta['qtdPcs']),2)
        consulta['ritimoAcum'] = consulta.groupby('usuario')['ritmo'].cumsum()

        consulta['parcial'] = consulta.groupby(['usuario']).cumcount() + 1

        # ritmoApurado: média parcial acumulada do ritmo
        consulta['ritmoApurado'] = consulta['ritimoAcum'] / consulta['parcial']

        # apuradoGeral: média final do ritmo por usuário
        media_geral = round(consulta.groupby('usuario')['ritmo'].transform('mean'))
        consulta['Ritmo'] = media_geral


        consulta = consulta.groupby(['nome','usuario']).agg({
            'qtdPcs':"sum",
            'Ritmo':"first"
        }).reset_index()
        consulta = consulta.sort_values(by=['qtdPcs'],
                                        ascending=False)  # escolher como deseja classificar

        consulta.rename(columns={'qtdPcs': 'qtde',"Ritmo":"ritmo"},
                                 inplace=True)

        data = {
            '0- Atualizado:':f'{Atualizado}',
            '1- Record Repositor': f'{consultaRecord["nome"][0]}',
            '1.1- Record qtd': f'{consultaRecord["producao"][0]}',
            '1.2- Record data': f'{consultaRecord["data"][0]}',
            '2 Total Periodo':f'{total}',
            '3- Ranking Repositores': consulta.to_dict(orient='records')
        }

        return pd.DataFrame([data])
























