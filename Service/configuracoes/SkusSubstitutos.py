import ConexaoPostgreMPL
import pandas as pd


def SubstitutosPorOP(filtro = ''):
   if  filtro == '':
        conn = ConexaoPostgreMPL.conexao()

        consultar = pd.read_sql('Select categoria as "1-categoria", numeroop as "2-numeroOP", codproduto as "3-codProduto", cor as "4-cor", databaixa_req as "5-databaixa", '
                                '"coodigoPrincipal" as "6-codigoPrinc", '
                                'nomecompontente as "7-nomePrinc",'
                                '"coodigoSubs" as "8-codigoSub",'
                                'nomesub as "9-nomeSubst", aplicacao as "10-aplicacao", considera from "Reposicao"."SubstitutosSkuOP" ', conn)

        conn.close()

        consultar.fillna('-',inplace=True)

        # Fazer a ordenacao
        consultar = consultar.sort_values(by=['considera','5-databaixa'], ascending=False)  # escolher como deseja classificar

        return consultar
   else:
       conn = ConexaoPostgreMPL.conexao()

       consultar = pd.read_sql('Select categoria as "1-categoria", numeroop as "2-numeroOP", codproduto as "3-codProduto", cor as "4-cor", databaixa_req as "5-databaixa", '
                               '"coodigoPrincipal" as "6-codigoPrinc", '
                               'nomecompontente as "7-nomePrinc",'
                               '"coodigoSubs" as "8-codigoSub",'
                               'nomesub as "9-nomeSubst",aplicacao as "10-aplicacao",  considera from "Reposicao"."SubstitutosSkuOP" where categoria = %s ', conn, params=(filtro,))

       conn.close()

       # Fazer a ordenacao
       consultar = consultar.sort_values(by=['considera', '5-databaixa'],
                                         ascending=False)  # escolher como deseja classificar
       consultar.fillna('-', inplace=True)

       consultar = consultar.drop_duplicates()

       return consultar

def ObterCategorias():
    conn = ConexaoPostgreMPL.conexao()

    consultar = pd.read_sql('Select distinct categoria from "Reposicao"."SubstitutosSkuOP" ', conn)

    conn.close()


    return consultar

def UpdetaConsidera(arrayOP , arrayCompSub, arrayconsidera):
    conn = ConexaoPostgreMPL.conexao()

    indice = 0
    for i in range(len(arrayOP)):
        indice = 1 + indice
        op = arrayOP[i]
        compSub = arrayCompSub[i]
        considera = arrayconsidera[i]

        update = 'update "Reposicao"."SubstitutosSkuOP" set considera = %s where numeroop = %s and "coodigoSubs" = %s'

        cursor = conn.cursor()
        cursor.execute(update,(considera, op, compSub,))
        conn.commit()
        cursor.close()





    conn.close()
    return pd.DataFrame([{'Mensagem':'Salvo com sucesso'}])
