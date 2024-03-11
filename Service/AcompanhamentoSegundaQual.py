import BuscasAvancadas
import ConexaoCSW
import ConexaoPostgreMPL
import pandas as pd


def TagSegundaQualidade(iniVenda, finalVenda):
    iniVenda = iniVenda[6:] + "-" + iniVenda[3:5] + "-" + iniVenda[:2]
    finalVenda = finalVenda[6:] + "-" + finalVenda[3:5] + "-" + finalVenda[:2]
    conn = ConexaoCSW.Conexao()

    tags = pd.read_sql(BuscasAvancadas.TagsSegundaQualidadePeriodo(iniVenda,finalVenda), conn)
    motivos = pd.read_sql(BuscasAvancadas.Motivos(),conn)
    tags['motivo2Qualidade'] = tags['motivo2Qualidade'].astype(str)
    motivos['motivo2Qualidade'] = motivos['motivo2Qualidade'].astype(str)

    tags = pd.merge(tags,motivos,on='motivo2Qualidade', how='left')

    tags['motivo2Qualidade'] = tags['nome']

    conn.close()





    return tags


def MotivosAgrupado(iniVenda, finalVenda):

    tags = TagSegundaQualidade(iniVenda,finalVenda)
    #Agrupamento do quantitativo
    tags['qtde'] = 1
    Agrupamento = tags.groupby('motivo2Qualidade')['qtde'].sum().reset_index()
    Agrupamento = Agrupamento.sort_values(by='qtde', ascending=False,
                        ignore_index=True)  # escolher como deseja classificar

    return Agrupamento




