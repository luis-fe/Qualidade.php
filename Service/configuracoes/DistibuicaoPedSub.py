import pandas as pd
import ConexaoPostgreMPL



def PedidosSkuEspecial():
    conn = ConexaoPostgreMPL.conexao()

    consulta = """
select p.codpedido, p.produto, ts.cor, ts.engenharia, p.endereco, p.necessidade  from "Reposicao"."Reposicao".pedidossku p 
inner join "Reposicao"."Reposicao"."Tabela_Sku" ts on ts.codreduzido = p.produto
where p.codpedido||ts.engenharia ||ts.cor in (
select p.codpedido||data2.engenharia||data2.cor from "Reposicao"."Reposicao".pedidossku p
inner join (select "Endereco", max(resticao) as resticao, max(cor) as cor, max(engenharia) as engenharia from "Reposicao"."Reposicao".tagsreposicao t  group by "Endereco")data2 on data2."Endereco" = p.endereco  
where data2.resticao like '%||%'
)
    """

    consulta2 = """
    select t."Endereco" as endereco, max(resticao) as restricao  from "Reposicao"."Reposicao".tagsreposicao t where t.resticao like '%||%'
    group by "Endereco" 
    """

    consulta3= """
    select ce.codendereco as endereco , codreduzido as produto , saldo, "SaldoLiquid"  from "Reposicao"."Reposicao"."calculoEndereco" ce 
where ce."SaldoLiquid" > 0 and 
codendereco in (select t."Endereco" from "Reposicao"."Reposicao".tagsreposicao t where t.resticao like '%||%')
order by "SaldoLiquid" desc 
    """

    consulta = pd.read_sql(consulta,conn)
    consulta2 = pd.read_sql(consulta2,conn)
    consulta3 = pd.read_sql(consulta3,conn)

    conn.close()

    # Adicionando uma coluna de contagem para cada produto
    consulta3['count'] = consulta3.groupby('produto').cumcount() + 1
    pivot_df = consulta3.pivot_table(index='produto', columns='count', values=['endereco', 'SaldoLiquid','saldo'], aggfunc='first')
    pivot_df.columns = [f"{col[0]}{col[1]}" for col in pivot_df.columns]
    pivot_df.reset_index(inplace=True)

    consulta = pd.merge(consulta, consulta2,on='endereco',how='left')
    consulta['restricao'].fillna('-',inplace=True)


    consulta = consulta.sort_values(by=['codpedido','engenharia','cor'], ascending=False,
                                ignore_index=True)

    def avaliar_grupo(df_grupo):
        return len(set(df_grupo)) == 1

    df_resultado = consulta.groupby(['codpedido','engenharia','cor'])['restricao'].apply(avaliar_grupo).reset_index()
    df_resultado.columns = ['codpedido','engenharia','cor', 'Resultado']

    consulta = pd.merge(consulta, df_resultado,on=['codpedido','engenharia','cor'],how='left')
    consulta = pd.merge(consulta, pivot_df,on='produto',how='left')
    consulta.fillna('-',inplace=True)

    consulta = consulta[consulta['Resultado'] == False]

    return consulta

