import pandas as pd
import jaydebeapi

from sqlalchemy import create_engine
import datetime
import numpy

import ConexaoPostgreMPL


# Função para criar os agrupamentos
def criar_agrupamentos(grupo):
    return '/'.join(sorted(set(grupo)))
def obterHoraAtual():
    agora = datetime.datetime.now()
    hora_str = agora.strftime('%d/%m/%Y %H:%M')
    return hora_str

def Funcao_Inserir (df_tags, tamanho,tabela, metodo):
    # Configurações de conexão ao banco de dados
    database = "Reposicao"
    user = "postgres"
    password = "Master@100"
    host = "127.0.0.1"
    port = "5432"

# Cria conexão ao banco de dados usando SQLAlchemy
    engine = create_engine(f'postgresql://{user}:{password}@{host}:{port}/{database}')


    # Inserir dados em lotes
    chunksize = tamanho
    for i in range(0, len(df_tags), chunksize):
        df_tags.iloc[i:i + chunksize].to_sql(tabela, engine, if_exists=metodo, index=False , schema='Reposicao')




def FilaTags():
    conn = jaydebeapi.connect(
    'com.intersys.jdbc.CacheDriver',
    'jdbc:Cache://192.168.0.25:1972/SISTEMAS',
    {'user': 'root', 'password': 'ccscache'},
    'CacheDB.jar'
)
    conn2 = ConexaoPostgreMPL.conexao()
    df_tags = pd.read_sql(
        "SELECT  codBarrasTag as codbarrastag, codNaturezaAtual , codEngenharia , codReduzido as codreduzido,(SELECT i.nome  FROM cgi.Item i WHERE i.codigo = t.codEngenharia) as descricao , numeroop as numeroop,"
        " (SELECT i2.codCor  FROM cgi.Item2  i2 WHERE i2.Empresa = 1 and  i2.codItem  = t.codReduzido) as cor,"
        " (SELECT tam.descricao  FROM cgi.Item2  i2 join tcp.Tamanhos tam on tam.codEmpresa = i2.Empresa and tam.sequencia = i2.codSeqTamanho  WHERE i2.Empresa = 1 and  i2.codItem  = t.codReduzido) as tamanho"
        " from tcr.TagBarrasProduto t WHERE codEmpresa = 1 and codNaturezaAtual = 5 and situacao = 3", conn)

    df_opstotal = pd.read_sql('SELECT top 200000 numeroOP as numeroop , totPecasOPBaixadas as totalop  '
                              'from tco.MovimentacaoOPFase WHERE codEmpresa = 1 and codFase = 236  '
                              'order by numeroOP desc ',conn)

    df_tags = pd.merge(df_tags, df_opstotal, on='numeroop', how='left')
    df_tags['totalop'] = df_tags['totalop'].replace('', numpy.nan).fillna('0')
    df_tags['codNaturezaAtual'] = df_tags['codNaturezaAtual'].astype(str)
    df_tags['totalop'] = df_tags['totalop'].astype(int)
    # CRIANDO O DATAFRAME DO QUE JA FOI REPOSTO E USANDO MERGE
       # Verificando as tag's que ja foram repostas
    TagsRepostas = pd.read_sql('select "codbarrastag" as codbarrastag, "usuario" as usuario_  from "Reposicao"."tagsreposicao" tr ',conn2)
    df_tags = pd.merge(df_tags, TagsRepostas, on='codbarrastag', how='left')
    df_tags = df_tags.loc[df_tags['usuario_'].isnull()]
    df_tags.drop('usuario_', axis=1, inplace=True)
        # Verificando as tag's que ja estam na fila
    ESTOQUE = pd.read_sql('select "usuario", "codbarrastag" as codbarrastag, "Situacao" as sti_aterior  from "Reposicao"."filareposicaoportag" ',conn2)
    df_tags = pd.merge(df_tags,ESTOQUE,on='codbarrastag',how='left')
    df_tags['Situacao'] = df_tags.apply(lambda row: 'Reposto' if not pd.isnull(row['usuario']) else 'Reposição não Iniciada', axis=1)
    epc = LerEPC()
    df_tags = pd.merge(df_tags, epc, on='codbarrastag', how='left')
    df_tags.rename(columns={'codbarrastag': 'codbarrastag','codEngenharia':'engenharia'
                            , 'numeroop':'numeroop'}, inplace=True)
    conn2.close()
    df_tags = df_tags.loc[df_tags['sti_aterior'].isnull()]
    df_tags.drop_duplicates(subset='codbarrastag', inplace=True)
    # Excluir a coluna 'B' inplace
    df_tags.drop('sti_aterior', axis=1, inplace=True)
    df_tags.drop_duplicates(subset='codbarrastag', inplace=True)
    df_tags['epc'] = df_tags['epc'].str.extract('\|\|(.*)').squeeze()
    print(df_tags.dtypes)
    print(df_tags['codbarrastag'].size)
    tamanho = df_tags['codbarrastag'].size
    dataHora = obterHoraAtual()
    df_tags['DataHora'] = dataHora
    df_tags.to_csv('planilha.csv')
    #try:
    Funcao_Inserir(df_tags, tamanho,'filareposicaoportag', 'append')
    hora = obterHoraAtual()
    return tamanho, hora
   # except:
    #    print('falha na funçao Inserir')
     #   hora = obterHoraAtual()
      #  return tamanho, hora


def LerEPC():
    conn = jaydebeapi.connect(
        'com.intersys.jdbc.CacheDriver',
        'jdbc:Cache://192.168.0.25:1972/SISTEMAS',
        {'user': 'root', 'password': 'ccscache'},
        'CacheDB.jar'
    )


    consulta = pd.read_sql('select epc.id as epc, t.codBarrasTag as codbarrastag from tcr.SeqLeituraFase  t '
                           'join Tcr_Rfid.NumeroSerieTagEPC epc on epc.codTag = t.codBarrasTag '
                           'WHERE t.codEmpresa = 1 and (t.codTransacao = 3500 or t.codTransacao = 501) '
                           'and (codLote like "23%" or  codLote like "24%" or codLote like "25%" '
                           'or codLote like "22%" )',conn)
    conn.close()

    print(consulta)
    return consulta
FilaTags()