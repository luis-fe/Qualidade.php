import ConexaoCSW
import ConexaoPostgreMPL
import pandas as pd

def ApontarTag(codbarras, Ncaixa, empresa, usuario):
    conn = ConexaoCSW.Conexao()
    codbarras = "'"+codbarras+"'"

    pesquisa = pd.read_sql('select p.codBarrasTag as codbarrastag , p.codReduzido as codreduzido, p.codEngenharia as engenharia, '
                           ' (select i.nome from cgi.Item i WHERE i.codigo = p.codReduzido) as descricao, situacao, codNaturezaAtual as natureza, codEmpresa as codempresa, '
                           " (select s.corbase||'-'||s.nomecorbase  from tcp.SortimentosProduto s WHERE s.codempresa = 1 and s.codproduto = p.codEngenharia and s.codsortimento = p.codSortimento)" 
                           ' as cor, (select t.descricao from tcp.Tamanhos t WHERE t.codempresa = 1 and t.sequencia = p.seqTamanho ) as tamanho, p.numeroOP as numeroop '
                           ' FROM Tcr.TagBarrasProduto p'
                           ' WHERE p.codBarrasTag = '+codbarras+' and p.codempresa ='+empresa,conn)
    conn.close()
    pesquisa['usuario'] = usuario
    pesquisa['caixa'] = Ncaixa
    InculirDados(pesquisa)
    return pesquisa




def InculirDados(dataframe):
        conn = ConexaoPostgreMPL.conexao()

        cursor = conn.cursor()  # Crie um cursor para executar a consulta SQL
        insert =  'insert into off.reposicao_qualidade (codbarrastag, codreduzido, engenharia, descricao, natureza, codempresa, cor, tamanho, numeroop, caixa, usuario)' \
                  ' values ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )'


        values = [(row['codbarrastag'], row['codreduzido'], row['engenharia'],row['descricao']
                   ,row['natureza'],row['codempresa'],row['cor'],row['tamanho'],row['numeroop'], row['caixa'],row['usuario']) for index, row in dataframe.iterrows()]

        cursor.executemany(insert, values)
        conn.commit()  # Faça o commit da transação
        cursor.close()  # Feche o cursor



        conn.close()
def EncontrarEPC(caixa):
    caixaNova = ConsultaCaixa(caixa)

    ops1 = caixaNova.drop_duplicates(subset=['numeroop'])
    ops = ops1['numeroop'].tolist()

    conn = ConexaoCSW.Conexao()

    epc = pd.read_sql('select t.codBarrasTag as codbarrastag, numeroOP,' 
        ' (select epc.id from Tcr_Rfid.NumeroSerieTagEPC epc WHERE  epc.codTag = t.codBarrasTag) as epc'
        "from tcr.SeqLeituraFase  t WHERE t.numeroOP  = '124962-001'", conn)

    return ops1

def ConsultaCaixa(NCaixa):
    conn = ConexaoPostgreMPL.conexao()
    consultar = pd.read_sql('select rq.codbarrastag , rq.numeroop  from "off".reposicao_qualidade rq  '
                            "where rq.caixa = %s ",conn,params=(NCaixa,))
    conn.close()

    return consultar
