import pandas as pd
from psycopg2 import sql
from connection import ConexaoCSW
import ConexaoPostgreMPL
from sqlalchemy.sql import text



def detalhaFila(empresa, natureza):
    ValidandoTracoOP()
    IdentificandoDevolucoes(str(empresa))
    CorrigindoDuplicatas()

    detalalhaTags_query = """
    SELECT f.numeroop, codreduzido, descricao, COUNT(codbarrastag) AS pcs
    FROM "Reposicao"."Reposicao".filareposicaoportag f 
    WHERE f.codempresa = %s AND f.codnaturezaatual = %s AND (status_fila IS NULL or status_fila = 'Devolucao' )
    GROUP BY numeroop, codreduzido, descricao  
    ORDER BY COUNT(codbarrastag) DESC
    """

    caixa_query = """
    SELECT rq.caixa, rq.numeroop, rq.codreduzido, COUNT(rq.codbarrastag) AS pc
    FROM "Reposicao"."off".reposicao_qualidade rq 
    INNER JOIN "Reposicao"."Reposicao".filareposicaoportag f ON f.codbarrastag = rq.codbarrastag
    GROUP BY rq.caixa, rq.numeroop, rq.codreduzido
    """

    sqlCsw_query = """
    SELECT TOP 100000 numeroOP AS numeroop, dataFim, L.descricao AS descOP 
    FROM tco.OrdemProd o 
    INNER JOIN tcl.Lote L ON L.codempresa = o.codempresa AND L.codlote = o.codlote
    WHERE o.codEmpresa = 1 AND o.situacao = 2 
    ORDER BY numeroOP DESC
    """

    sqlEstoqueCSW = """
SELECT e.codItem as codreduzido , e.estoqueAtual as estoqueCsw  FROM est.DadosEstoque e
WHERE e.codNatureza = %s and e.codEmpresa = 1
    """%natureza


    query_SaldoEnderecos ="""
    select t.codreduzido as codreduzido , count(codbarrastag) as  "SaldoEnderecos" from "Reposicao"."Reposicao".tagsreposicao t 
    where t."natureza"= %s 
    group by codreduzido 
    """

    with ConexaoCSW.Conexao() as conn:
        with conn.cursor() as cursor_csw:
            cursor_csw.execute(sqlCsw_query)
            colunas = [desc[0] for desc in cursor_csw.description]
            rows = cursor_csw.fetchall()
            dadosOP = pd.DataFrame(rows, columns=colunas)

            cursor_csw.execute(sqlEstoqueCSW)
            colunas = [desc[0] for desc in cursor_csw.description]
            rows = cursor_csw.fetchall()
            estoqueCsw = pd.DataFrame(rows, columns=colunas)

        ultima_atualizacao_Fila = """
        select substring(max(fim),1,17) as "Ultima Atualizacao" from "Reposicao".configuracoes.controle_requisicao_csw 
    where rotina = 'AtualizarTagsEstoque'
        """

        devolucoes = """
        SELECT distinct numeroop, codreduzido, "status_fila" FROM "Reposicao"."filareposicaoportag"
        where "status_fila" = 'Devolucao' and codnaturezaatual = %s
        """

        conn2 = ConexaoPostgreMPL.conexaoEngine()
        detalalhaTags = pd.read_sql(detalalhaTags_query, conn2, params=(empresa, natureza))
        caixapd = pd.read_sql(caixa_query, conn2)
        ultima_atualizacao_Fila = pd.read_sql(ultima_atualizacao_Fila, conn2)
        devolucoes = pd.read_sql(devolucoes, conn2, params=(natureza,))
        query_SaldoEnderecos = pd.read_sql(query_SaldoEnderecos, conn2, params=(natureza,))


    caixa = caixapd.groupby(['numeroop', 'codreduzido']).apply(
        lambda x: ', '.join(x['caixa'].astype(str) + ':' + x['pc'].astype(str))).reset_index(name='caixas')

    detalalhaTags = pd.merge(detalalhaTags, caixa, on=['numeroop', 'codreduzido'], how='left')
    detalalhaTags = pd.merge(detalalhaTags, dadosOP, on='numeroop', how='left')
    detalalhaTags = pd.merge(detalalhaTags, estoqueCsw, on='codreduzido', how='left')
    detalalhaTags = pd.merge(detalalhaTags, query_SaldoEnderecos, on='codreduzido', how='left')
    detalalhaTags = pd.merge(detalalhaTags, devolucoes, on=['numeroop', 'codreduzido'], how='left')


    detalalhaTags.fillna('-', inplace=True)
    detalalhaTags['descOP'] = detalalhaTags.apply(lambda r : '***DEVOLUCAO '+r['descOP'] if r['status_fila'] == 'Devolucao' else r['descOP'], axis=1)


    ultima_atualizacao_Fila = ultima_atualizacao_Fila['Ultima Atualizacao'][0]

    data = {
        '1.0- Total Peças Fila': f'{detalalhaTags["pcs"].sum()} pcs',
        '1.1- Total Caixas na Fila': f'{caixapd["caixa"].count()}',
        '1.2- Ultima Atualizacao':f'{ultima_atualizacao_Fila}',
        '2.0- Detalhamento': detalalhaTags.to_dict(orient='records')
    }

    return pd.DataFrame([data])

def DetalharCaixa(numeroCaixa):

    DetalharCaixa = """
    select rq.usuario, c.nome  , rq."DataReposicao",  rq.codbarrastag , f.epc ,rq.numeroop , rq.codreduzido from "Reposicao"."off".reposicao_qualidade rq 
left join "Reposicao"."Reposicao".filareposicaoportag f on rq.codbarrastag = f.codbarrastag 
inner join "Reposicao"."Reposicao".cadusuarios c on c.codigo = rq.usuario ::int
where rq.caixa = %s 
    """

    with ConexaoPostgreMPL.conexao() as conn:
        DetalharCaixa = pd.read_sql(DetalharCaixa, conn, params=(numeroCaixa,))

    return DetalharCaixa

def TagsFilaConferencia():
    sql = """
    select codbarrastag, engenharia , codreduzido , epc , dataseparacao, codpedido, "Endereco" as "EnderecoOrigem" , c.nome as usuario_Separou
from "Reposicao"."Reposicao".tags_separacao ts 
inner join "Reposicao"."Reposicao".cadusuarios c on c.codigo = ts.usuario :: int
where ts.codbarrastag in (select codbarrastag  from "Reposicao"."Reposicao".filareposicaoportag )

    """
    with ConexaoPostgreMPL.conexao() as conn:
        detalhaTags = pd.read_sql(sql, conn)


    # Agrupando pelo codpedido e contando o número de ocorrências
    pedidos = detalhaTags.groupby('codpedido').size().reset_index(name='count')
    total_pedidos_na_fila = pedidos['codpedido'].nunique()

    detalhaTags.fillna('-',inplace=True)

    data = {
        '1.0- Total Peças': f'{detalhaTags["codbarrastag"].nunique()} pcs',
        '1.1- Total Pedidos na Fila': f'{total_pedidos_na_fila}',
        '2.0- Detalhamento': detalhaTags.to_dict(orient='records')
    }


    return pd.DataFrame([data])



def ValidandoTracoOP():
    # Criando conexão com o banco
    engine = ConexaoPostgreMPL.conexaoEngine()

    sql1 = """
    SELECT codbarrastag, f.numeroop  
    FROM "Reposicao"."Reposicao".filareposicaoportag f 
    WHERE f.numeroop NOT LIKE '%-001'
    """

    sql2 = """
    SELECT rq.codbarrastag   
    FROM "Reposicao"."off".reposicao_qualidade rq
    """

    # Lendo os dados com uma conexão de streaming
    with engine.connect() as conn:
        with conn.begin():  # Inicia transação

            c1 = pd.read_sql(sql1, conn)
            c2 = pd.read_sql(sql2, conn)

            # Fazendo merge entre os dois DataFrames
            c = pd.merge(c1, c2, on='codbarrastag')

            update_sql = text("""
                UPDATE "Reposicao"."off".reposicao_qualidade
                SET numeroop = :numeroop
                WHERE codbarrastag = :codbarrastag
            """)

            delete_sql = text("""
                DELETE FROM "Reposicao"."Reposicao".filareposicaoportag
                WHERE codbarrastag IN (
                    SELECT codbarrastag FROM "Reposicao"."Reposicao".tagsreposicao
                )
            """)

            # Iniciando a transação
            transaction = conn.begin()
            for _, row in c.iterrows():
                conn.execute(update_sql, {
                    "numeroop": row["numeroop"],
                    "codbarrastag": row["codbarrastag"]
                })

            conn.execute(delete_sql)  # Executa DELETE
            transaction.commit()  # Confirma transação





def DetalhaTagsNumeroOPReduzido(numeroop, codreduzido, codempresa, natureza):
    sql = """
    select distinct f.codbarrastag , f.epc, f.numeroop, f."DataHora", f.codreduzido  from "Reposicao"."Reposicao".filareposicaoportag f 
where numeroop = %s and codreduzido = %s and (status_fila is null or status_fila = 'Devolucao' ) and f.codempresa =  %s and f.codnaturezaatual =  %s
    """

    conn = ConexaoPostgreMPL.conexaoEngine()
    c1 = pd.read_sql(sql,conn,params=(numeroop, codreduzido, str(codempresa), str(natureza)))

    return c1

def IdentificandoDevolucoes(empresa):
    sqlCsw = """
    SELECT numDocto as codbarrastag  FROM est.Movimento m
WHERE m.codEmpresa = %s and codTransacao = 1426 and numDocto in (SELECT codbarrastag from tcr.TagBarrasProduto t WHERE t.codempresa = 1 and situacao = 3)
    """%empresa

    with ConexaoCSW.Conexao() as conn:
        with conn.cursor() as cursor_csw:
            cursor_csw.execute(sqlCsw)
            colunas = [desc[0] for desc in cursor_csw.description]
            rows = cursor_csw.fetchall()
            sqlCsw = pd.DataFrame(rows, columns=colunas)

    #Transformando em lista
    lista = sqlCsw['codbarrastag'].tolist()

    query1 = sql.SQL('update  "Reposicao"."filareposicaoportag" set "status_fila" ='+"""'Devolucao'"""+' WHERE codbarrastag not in (SELECT codbarrastag FROM "Reposicao"."tags_separacao" ts WHERE ts.dataseparacao::date > now() - INTERVAL '+"'15 days'"+') and codbarrastag IN ({})').format(
            sql.SQL(',').join(map(sql.Literal, lista)))

        # Executar a consulta update
    with ConexaoPostgreMPL.conexao() as conn2:
            cursor = conn2.cursor()
            cursor.execute(query1)
            conn2.commit()



def CorrigindoDuplicatas():

    sql = """
    insert into "Reposicao"."Reposicao".filareposicaoportag 
select   distinct codbarrastag , codnaturezaatual , engenharia , codreduzido , descricao , numeroop , cor , tamanho, 
usuario, "Situacao" , epc, "DataHora" , totalop,  '1' as dataentrada, codempresa, resticao, considera , "status_fila"   from "Reposicao"."Reposicao".filareposicaoportag f 
where codbarrastag in (select codbarrastag from "Reposicao"."Reposicao".filareposicaoportag f2 group by codbarrastag having count(codbarrastag)> 1
)
    """

    delete = """
    delete  from"Reposicao"."Reposicao".filareposicaoportag 
where codbarrastag in (select codbarrastag from "Reposicao"."Reposicao".filareposicaoportag f2 group by codbarrastag having count(codbarrastag)> 1)
and dataentrada is null
    """

    update="""
    update  "Reposicao"."Reposicao".filareposicaoportag 
    set dataentrada = null
    """

        # Executar a consulta update
    with ConexaoPostgreMPL.conexao() as conn2:
            cursor = conn2.cursor()
            cursor.execute(sql)
            conn2.commit()
            cursor.execute(delete)
            conn2.commit()
            cursor.execute(update)
            conn2.commit()


def ExplodindoLocalizacaoReposicao(natureza,codreduzido ):

    sql = """
    select natureza , "Endereco" , count(codbarrastag) as saldo  from "Reposicao"."Reposicao".tagsreposicao t 
    where t.codreduzido = %s and natureza = %s
    group by natureza , "Endereco" 
    """

    conn = ConexaoPostgreMPL.conexaoEngine()
    c1 = pd.read_sql(sql,conn,params=(codreduzido,natureza))

    return c1

