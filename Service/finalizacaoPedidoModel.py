import pandas as pd
import ConexaoPostgreMPL
import datetime
import pytz


def obterHoraAtual():
    fuso_horario = pytz.timezone('America/Sao_Paulo')
    agora = datetime.datetime.now(fuso_horario)
    hora_str = agora.strftime('%Y-%m-%d %H:%M:%S')
    return hora_str


def VerificarExisteApontamento(codpedido, usuario):
    conn = ConexaoPostgreMPL.conexao()
    codpedido = str(codpedido)
    query = pd.read_sql('select codpedido from "Reposicao".tags_separacao '
                        ' where codpedido = %s '
                        , conn, params=(codpedido,))
    conn.close()

    if query.empty:
        conn = ConexaoPostgreMPL.conexao()
        select = pd.read_sql('select * from "Reposicao".finalizacao_pedido fp'
                             ' where codpedido = %s ', conn, params=(codpedido,))
        if select.empty:
            print('teste')
            insert = 'insert into "Reposicao".finalizacao_pedido (codpedido, usuario, "dataInicio") values (%s , %s , %s)'
            datahora = obterHoraAtual()
            cursor = conn.cursor()
            cursor.execute(insert, (codpedido, usuario, datahora))
            conn.commit()
            conn.close()

        else:
            update = 'update "Reposicao".finalizacao_pedido' \
                     ' set usuario = %s , "dataInicio" = %s ' \
                     ' where codpedido = %s'
            datahora = obterHoraAtual()
            cursor = conn.cursor()
            cursor.execute(update, (usuario, datahora, codpedido))
            conn.commit()
            conn.close()

    else:
        print('ok')
def Buscar_Caixas():
    conn = ConexaoPostgreMPL.conexao()
    query = pd.read_sql('select tamanhocaixa as TamCaixa from "Reposicao".caixas',conn)
    conn.close()

    # Selecione a coluna 'coluna b' e converta em uma lista
    query = query['tamcaixa'].tolist()

    return query
def finalizarPedido(pedido, TamCaixa, quantidade):
    conn = ConexaoPostgreMPL.conexao()
    datafinalizacao = obterHoraAtual()

    # Certifique-se de que TamCaixa e quantidade tenham pelo menos 4 elementos
    while len(TamCaixa) < 4:
        TamCaixa.append(0)

    while len(quantidade) < 4:
        quantidade.append(0)

    # Atribua os valores de TamCaixa e quantidade às variáveis correspondentes
    TamCaixa1 = TamCaixa[0]
    quantidade1 = quantidade[0]
    TamCaixa2 = TamCaixa[1]
    quantidade2 = quantidade[1]
    TamCaixa3 = TamCaixa[2]
    quantidade3 = quantidade[2]
    TamCaixa4 = TamCaixa[3]
    quantidade4 = quantidade[3]

    query = 'update "Reposicao".finalizacao_pedido ' \
            'set "tamCaixa" = %s, qtdcaixa= %s, datafinalizacao= %s,' \
            ' "tamcaixa2" = %s, qtdcaixa2= %s,' \
            '"tamcaixa3" = %s, qtdcaixa3= %s, ' \
            '"tamcaixa4" = %s, qtdcaixa4= %s,' \
            "baixacsw = 'nao' " \
            'where codpedido = %s'

    cursor = conn.cursor()
    cursor.execute(query, (TamCaixa1, quantidade1, datafinalizacao, TamCaixa2, quantidade2, TamCaixa3, quantidade3, TamCaixa4, quantidade4, pedido,))
    conn.commit()
    conn.close()

    data = {
        'Status':
        True,
        'Mensagem': f'Pedido {pedido} finalizado com sucesso!',
    }

    return [data]


def RelatorioConsumoCaixa(dataInico, DataFim):
    conn = ConexaoPostgreMPL.conexao()

    query1 = pd.read_sql("select TO_CHAR(datafinalizacao, 'YYYY-MM-DD') as data ,"
                                                                     ' "tamCaixa" as tamcaixa, qtdcaixa1 as quantidade  from "Reposicao".relatorio_caixas '
                        'where datafinalizacao >= %s and datafinalizacao <= %s ',conn,params=(dataInico,DataFim))

    query2 = pd.read_sql("select TO_CHAR(datafinalizacao, 'YYYY-MM-DD') as data ,"
                         ' tamcaixa2 as tamcaixa , qtdcaixa2 as quantidade  from "Reposicao".relatorio_caixas '
                        'where datafinalizacao >= %s and datafinalizacao <= %s ',conn,params=(dataInico,DataFim))

    query3 = pd.read_sql("select TO_CHAR(datafinalizacao, 'YYYY-MM-DD') as data ,"
                         ' tamcaixa3 as tamcaixa , qtdcaixa3 as quantidade  from "Reposicao".relatorio_caixas '
                        'where datafinalizacao >= %s and datafinalizacao <= %s ',conn,params=(dataInico,DataFim))

    query4 = pd.read_sql("select TO_CHAR(datafinalizacao, 'YYYY-MM-DD') as data ,"
                         ' tamcaixa4 as tamcaixa , qtdcaixa4 as quantidade   from "Reposicao".relatorio_caixas '
                        'where datafinalizacao >= %s and datafinalizacao <= %s ',conn,params=(dataInico,DataFim))




    result = pd.concat([query1, query2,query3,query4])

    result.fillna('-', inplace=True)
    result = result[result['tamcaixa'] != '-']
    result = result[result['tamcaixa'] != '0']

    result = result.groupby('tamcaixa').agg({
        'tamcaixa':'first',
        'quantidade': 'sum'
    })

    result['quantidade'] = result['quantidade'].astype(int)
    result['tamanhocaixa'] = result['tamcaixa']

    caixasCadastros = pd.read_sql('select tamanhocaixa , codcaixa , nomecaixa  from "Reposicao".caixas c ',conn)
    result = pd.merge(result,caixasCadastros, on='tamanhocaixa', how= 'left')
    result.drop('tamanhocaixa', axis=1, inplace=True)

    conn.close()
    return result

def GetCaixas():
    conn = ConexaoPostgreMPL.conexao()
    query = pd.read_sql('select * from "Reposicao".caixas',conn)
    conn.close()


    return query

def InserirNovaCaixa(codcaixa, nomecaixa, tamanhocaixa):
    conn = ConexaoPostgreMPL.conexao()
    # SQL statement para inserir os dados
    insert = 'INSERT INTO "Reposicao".caixas (codcaixa, nomecaixa, tamanhocaixa) VALUES (%s, %s, %s)'

    cursor = conn.cursor()
    # Execute o comando SQL
    cursor.execute(insert, (codcaixa, nomecaixa, tamanhocaixa))

    # Confirme as alterações no banco de dados
    conn.commit()

    # Feche o cursor e a conexão
    cursor.close()
    conn.close()

    # Retorne uma mensagem de sucesso
    return pd.DataFrame([{'Mensagem': 'Inserido com sucesso!'}])

def AtualizarCaixa(codcaixa, codcaixa2, nomecaixa2, tamanhocaixa2):
    conn = ConexaoPostgreMPL.conexao()
    consulta = GetCaixas()


    consulta = consulta[consulta['codcaixa'] == codcaixa]


    if codcaixa2 == '0':
        codcaixa2 = codcaixa

    if nomecaixa2== '0':
        nomecaixa2 = consulta['nomecaixa'][0]

    if tamanhocaixa2== '0':
        tamanhocaixa2 = consulta['tamanhocaixa'][0]


    update = 'update "Reposicao".caixas ' \
                 'set codcaixa = %s, nomecaixa = %s, tamanhocaixa = %s ' \
                 'where codcaixa = %s '

    cursor = conn.cursor()
    # Execute o comando SQL
    cursor.execute(update, (codcaixa2, nomecaixa2, tamanhocaixa2, codcaixa))

    # Confirme as alterações no banco de dados
    conn.commit()

    # Feche o cursor e a conexão
    cursor.close()
    conn.close()

    # Retorne uma mensagem de sucesso
    return pd.DataFrame([{'Mensagem': 'Atualizado com sucesso!'}])










