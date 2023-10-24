import ConexaoCSW
import ConexaoPostgreMPL
import pandas as pd
import psycopg2
from psycopg2 import Error
import datetime
import pytz


def obterHoraAtual():
    fuso_horario = pytz.timezone('America/Sao_Paulo')  # Define o fuso horário do Brasil
    agora = datetime.datetime.now(fuso_horario)
    hora_str = agora.strftime('%Y-%m-%d %H:%M:%S')
    return hora_str


def ApontarTag(codbarras, Ncaixa, empresa, usuario, estornar = False):
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
    pesquisa['DataReposicao'] = obterHoraAtual()

    if estornar == False:
        if pesquisa.empty:
            return pd.DataFrame([{'status': False, 'Mensagem': f'tag {codbarras} nao encontrada !'}])
        else:
            pesquisarSituacao = PesquisarTag(codbarras,Ncaixa)

            if pesquisarSituacao == 1:
                InculirDados(pesquisa)
                return pd.DataFrame([{'status':True , 'Mensagem':'tag inserido !'}])
            elif pesquisarSituacao ==2:
                return pd.DataFrame([{'status': False, 'Mensagem': f'tag {codbarras} ja bipado nessa caixa, deseja estornar ?'}])
            else:
                return pd.DataFrame(
                    [{'status': False, 'Mensagem': f'tag {codbarras} ja bipado em outra  caixa de n°{pesquisarSituacao}, deseja estornar ?'}])
    else:
        estorno = EstornarTag(codbarras)
        return estorno




def InculirDados(dataframe):
        conn = ConexaoPostgreMPL.conexao()

        cursor = conn.cursor()  # Crie um cursor para executar a consulta SQL
        insert =  'insert into off.reposicao_qualidade (codbarrastag, codreduzido, engenharia, descricao, natureza, codempresa, cor, tamanho, numeroop, caixa, usuario, "DataReposicao")' \
                  ' values ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )'

        values = [(row['codbarrastag'], row['codreduzido'], row['engenharia'],row['descricao']
                   ,row['natureza'],row['codempresa'],row['cor'],row['tamanho'],row['numeroop'], row['caixa'],row['usuario'], row['DataReposicao'] ) for index, row in dataframe.iterrows()]

        cursor.executemany(insert, values)
        conn.commit()  # Faça o commit da transação
        cursor.close()  # Feche o cursor



        conn.close()
def EncontrarEPC(caixa,endereco):
    #Passo1: Pesquisar em outra funcao um dataframe que retorna a coluna numeroop
    caixaNova = ConsultaCaixa(caixa)
    caixaNova = caixaNova.drop_duplicates(subset=['codbarrastag'])
    #Passo2: Retirar do dataframe somente a coluna numeroop
    ops1 = caixaNova[['numeroop']]
    ops1 = ops1.drop_duplicates(subset=['numeroop'])

    # Passo 3: Transformar o dataFrame em lista
    resultado = '({})'.format(', '.join(["'{}'".format(valor) for valor in ops1['numeroop']]))

    conn = ConexaoCSW.Conexao()

    # Use parâmetro de substituição na consulta SQL
    epc = pd.read_sql('SELECT t.codBarrasTag AS codbarrastag, numeroOP as numeroop, (SELECT epc.id FROM Tcr_Rfid.NumeroSerieTagEPC epc WHERE epc.codTag = t.codBarrasTag) AS epc '
            "FROM tcr.SeqLeituraFase t WHERE t.codempresa = 1 and t.numeroOP IN "+resultado,conn)

    epc = epc.drop_duplicates(subset=['codbarrastag'])

    result = pd.merge(caixaNova,epc,on=('codbarrastag','numeroop'), how='left')
    result.fillna('-', inplace=True)

    if result['mensagem'][0] == 'caixa vazia':
        return pd.DataFrame({'mensagem':['caixa vazia'],'status':False})
    else:
        #Avaliar se a op da tag foi baixada
        result['mensagem'] = result.apply(lambda row: 'OP em estoque' if row['epc']!='-' else 'OP nao entrou em estoque',axis=1)
        #Filtrar somente as OPs que entraram no estoque, verificar se a prateleira ta livre, inserir na tagsreposicao e excluir da reposicaoqualidade
        inserir = result[result['mensagem']=='OP em estoque']
        inserir = IncrementarCaixa(endereco,inserir)

        QtdtotalCaixa = result['codbarrastag'].count()
        Qtde_noEstoque = inserir['codbarrastag'].count()


        if QtdtotalCaixa == Qtde_noEstoque:
            return pd.DataFrame([{'status':True,'Mensagem':'Endereco carregado com sucesso!'}])
        else:
            NaoEntrou = result[result['mensagem'] == 'OP nao entrou em estoque']

            NaoEntrou = NaoEntrou[['codbarrastag', 'numeroop']]


            data = {
                'status': True,
                'Mensagem': 'Endereco PARCIALMENTE carregado',
                'Tags nao carregadas':NaoEntrou.to_dict(orient='records')
            }
            return pd.DataFrame([data])




def ConsultaCaixa(NCaixa):
    conn = ConexaoPostgreMPL.conexao()
    consultar = pd.read_sql('select rq.codbarrastag , rq.codreduzido, rq.engenharia, rq.descricao, rq.natureza'
                            ', rq.codempresa, rq.cor, rq.tamanho, rq.numeroop, rq.usuario, rq."DataReposicao"  from "off".reposicao_qualidade rq  '
                            "where rq.caixa = %s ",conn,params=(NCaixa,))
    conn.close()

    if consultar.empty :
        return pd.DataFrame({'mensagem':['caixa vazia'],'codbarrastag':'','numeroop':''})
    else:
        consultar['mensagem'] = 'Caixa Cheia'

        return consultar

def IncrementarCaixa(endereco, dataframe):

    try:
        conn = ConexaoPostgreMPL.conexao()
        insert = 'insert into "Reposicao".tagsreposicao ("Endereco","codbarrastag","codreduzido",' \
                 '"engenharia","descricao","natureza","codempresa","cor","tamanho","numeroop","usuario", "proveniencia","DataReposicao") values ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )'
        dataframe['proveniencia'] = 'Veio da Caixa'

        cursor = conn.cursor()  # Crie um cursor para executar a consulta SQL

        values = [(endereco,row['codbarrastag'], row['codreduzido'], row['engenharia'], row['descricao']
                   , row['natureza'], row['codempresa'], row['cor'], row['tamanho'], row['numeroop'],
                   row['usuario'],row['proveniencia'],row['DataReposicao']) for index, row in dataframe.iterrows()]

        cursor.executemany(insert, values)
        conn.commit()  # Faça o commit da transação
        cursor.close()  # Feche o cursor

        return dataframe


    except psycopg2.Error as e:
        if 'duplicate key value violates unique constraint' in str(e):

            dataframe['mensagem'] = "codbarras ja existe em outra prateleira"

            return dataframe

        else:
            # Lidar com outras exceções que não são relacionadas à chave única
            print("Erro inesperado:", e)
            dataframe['mensagem'] = "Erro inesperado:", e

            return dataframe

    finally:
        if conn:
            conn.close()
            return dataframe


def PesquisarTagCsw(codbarras, empresa):
    conn = ConexaoCSW.Conexao()
    codbarras = "'" + codbarras + "'"

    pesquisa = pd.read_sql(
        'select p.codBarrasTag as codbarrastag , p.codReduzido as codreduzido, p.codEngenharia as engenharia, '
        ' (select i.nome from cgi.Item i WHERE i.codigo = p.codReduzido) as descricao, situacao, codNaturezaAtual as natureza, codEmpresa as codempresa, '
        " (select s.corbase||'-'||s.nomecorbase  from tcp.SortimentosProduto s WHERE s.codempresa = 1 and s.codproduto = p.codEngenharia and s.codsortimento = p.codSortimento)"
        ' as cor, (select t.descricao from tcp.Tamanhos t WHERE t.codempresa = 1 and t.sequencia = p.seqTamanho ) as tamanho, p.numeroOP as numeroop '
        ' FROM Tcr.TagBarrasProduto p'
        ' WHERE p.codBarrasTag = ' + codbarras + ' and p.codempresa =' + empresa, conn)
    conn.close()

    return pesquisa


def PesquisarTag(codbarrastag, caixa):
    conn = ConexaoPostgreMPL.conexao()
    consulta = pd.read_sql('select caixa  from "off".reposicao_qualidade rq'
                           ' where rq.codbarrastag = '+codbarrastag, conn )
    conn.close()

    if consulta.empty:
        return 1
    else:
        caixaAntes = consulta['caixa'][0]

        if  caixaAntes == str(caixa):
            return 2
        else:
         return consulta['caixa'][0]

def EstornarTag(codbarrastag):
    conn = ConexaoPostgreMPL.conexao()
    delete = 'delete from "off".reposicao_qualidade ' \
             'where codbarrastag  = '+codbarrastag
    cursor = conn.cursor()
    cursor.execute(delete,)
    conn.commit()
    cursor.close()
    conn.close()

    return pd.DataFrame([{'status':True,'Mensagem':'tag estornada! '}])




