import ConexaoPostgreMPL
import pandas as pd
from datetime import datetime
import os
from models import imprimirEtiquetaModel


def ObeterEnderecos():
    conn = ConexaoPostgreMPL.conexao()
    endercos = pd.read_sql(
        ' select * from "Reposicao"."cadendereco" ce   ', conn)
    return endercos
def CadEndereco (rua, modulo, posicao):
    inserir = 'insert into "Reposicao".cadendereco ("codendereco","rua","modulo","posicao")' \
          ' VALUES (%s,%s,%s,%s);'
    codenderco = rua+"-"+modulo+"-"+posicao

    conn = ConexaoPostgreMPL.conexao()
    cursor = conn.cursor()
    cursor.execute(inserir
                   , (codenderco, rua, modulo, posicao))

    # Obter o número de linhas afetadas
    numero_linhas_afetadas = cursor.rowcount
    conn.commit()
    cursor.close()
    conn.close()
    return codenderco

def Deletar_Endereco(Endereco):
    conn = ConexaoPostgreMPL.conexao()
    # Validar se existe Restricao Para excluir o endereo
    Validar = pd.read_sql(
        'select "Endereco" from "Reposicao".tagsreposicao '
        'where "Endereco" = '+"'"+Endereco+"'", conn)
    if not Validar.empty:
         return pd.DataFrame({'Mensagem': [f'Endereco com saldo, nao pode ser excluido'], 'Status':False})
    else:
        delatar = 'delete from "Reposicao".cadendereco ' \
                  'where codendereco = %s '
        # Execute a consulta usando a conexão e o cursor apropriados
        cursor = conn.cursor()
        cursor.execute(delatar, (Endereco,))
        conn.commit()
        return pd.DataFrame({'Mensagem': [f'Endereco excluido!'], 'Status':True})

def EnderecosDisponiveis(natureza, empresa):
    conn = ConexaoPostgreMPL.conexaoEngine()

    # 1. Aqui carrego os enderecos do banco de dados, por uma view chamado enderecosReposicao,
    # essa view mostra o saldo de cada endereco cadastrado na plataforma, por empresa e por natureza

    # 1.1 Carregando somente os enderecos com saldo = 0
    relatorioEndereco = pd.read_sql(
        """
            select 
                codendereco, 
                contagem as saldo 
            from 
                "Reposicao"."enderecosReposicao"
            where 
                contagem = 0 
                and natureza = %s 
                """, conn, params=(natureza,))


    #1.2 Carregando todos os enderecos
    relatorioEndereco2 = pd.read_sql(
        """
            select 
                codendereco, 
                contagem as saldo 
            from 
                "Reposicao"."enderecosReposicao" 
            where natureza = %s 
        """
        , conn, params=(natureza,))


    # Calculando a Taxa de Ocupacao
    TaxaOcupacao = 1-(relatorioEndereco["codendereco"].size/relatorioEndereco2["codendereco"].size)
    TaxaOcupacao = round(TaxaOcupacao, 2) * 100


    # Calculando o "tamanho"  de cada uma das consultas
    tamanho = relatorioEndereco["codendereco"].size
    tamanho2 = relatorioEndereco2["codendereco"].size
    tamanho2 = "{:,.0f}".format(tamanho2)
    tamanho2 = str(tamanho2)
    tamanho2 = tamanho2.replace(',', '.')
    tamanho = "{:,.0f}".format(tamanho)
    tamanho = str(tamanho)
    tamanho = tamanho.replace(',', '.')

    relatorioEndereco = relatorioEndereco.head(1000)

    # Exibindo as informacoes para o Json
    data = {

        '1- Total de Enderecos Natureza ': tamanho2,
        '2- Total de Enderecos Disponiveis': tamanho,
        '3- Taxa de Oculpaçao dos Enderecos': f'{TaxaOcupacao} %',
        '4- Enderecos disponiveis ': relatorioEndereco.to_dict(orient='records')
    }
    return [data]


# Codigo para incluir enderecos por atacado ou fazer um update por atacado
def ImportEndereco(rua, ruaLimite, modulo, moduloLimite, posicao, posicaoLimite,
                   tipo, codempresa, natureza, imprimir, enderecoReservado=''):

    conn = ConexaoPostgreMPL.conexao()
    cursor = conn.cursor()

    query = '''
        insert into "Reposicao".cadendereco (codendereco, rua, modulo, posicao, tipo, codempresa, natureza, endereco_subst)
        values (%s, %s, %s, %s, %s, %s, %s, %s)
    '''
    update = '''
        update "Reposicao".cadendereco
        set codendereco = %s, rua = %s, modulo = %s, posicao = %s, tipo = %s, codempresa = %s, natureza = %s, endereco_subst = %s
        where codendereco = %s
    '''

    etiquetas_para_impressao = []

    r = int(rua)
    ruaLimite = int(ruaLimite) + 1
    m0 = int(modulo)
    moduloLimite = int(moduloLimite) + 1
    p0 = int(posicao)
    posicaoLimite = int(posicaoLimite) + 1

    while r < ruaLimite:
        ruaAtual = Acres_0(r)
        m = m0
        while m < moduloLimite:
            moduloAtual = Acres_0(m)
            p = p0
            while p < posicaoLimite:
                posicaoAtual = Acres_0(p)
                codendereco = f"{ruaAtual}-{moduloAtual}-{posicaoAtual}"

                select = pd.read_sql(
                    'select codendereco from "Reposicao".cadendereco where codendereco = %s',
                    conn, params=(codendereco,)
                )

                if imprimir == True and codempresa == '1':
                    etiquetas_para_impressao.append((codendereco, ruaAtual, moduloAtual, posicaoAtual, natureza))
                    nome_pdf2 = 'teste1.pdf'
                    caminho_pdf2 = os.path.join('/home/grupompl/Wms_InternoMPL/static', nome_pdf2)
                    imprimirEtiquetaModel.EtiquetaPrateleira(caminho_pdf2, codendereco, ruaAtual,moduloAtual,posicaoAtual, natureza)

                    imprimirEtiquetaModel.imprimir_pdf(caminho_pdf2)

                if select.empty:
                    cursor.execute(query, (
                        codendereco, ruaAtual, moduloAtual, posicaoAtual, tipo, codempresa, natureza, enderecoReservado
                    ))
                else:
                    cursor.execute(update, (
                        codendereco, ruaAtual, moduloAtual, posicaoAtual, tipo, codempresa, natureza,
                        enderecoReservado, codendereco
                    ))

                conn.commit()
                p += 1
            m += 1
        r += 1

    cursor.close()
    conn.close()

    # Impressão em lote depois do loop
    if imprimir == True and codempresa=='4':
        # Gera nome dinâmico do PDF
        nome_pdf = f"etiquetas_{datetime.now().strftime('%Y%m%d_%H%M%S')}.pdf"
        nome_pdf = 'teste.pdf'
        caminho_pdf = os.path.join('/home/grupompl/Wms_InternoMPL/static', nome_pdf)

        # Gera o PDF
        imprimirEtiquetaModel.gerar_etiquetas_pdf(caminho_pdf, etiquetas_para_impressao)

        # Imprime direto se for empresa 1


        # Retorna a URL pública do PDF
        return f"http://10.162.0.191:5000/static/{nome_pdf}"

    return None


def Acres_0(valor):
    if valor < 10:
        valor = str(valor)
        valor = '0'+valor
        return valor
    else:
        valor = str(valor)
        return valor


def ImportEnderecoDeletar(rua, ruaLimite, modulo, moduloLimite, posicao, posicaoLimite, tipo, codempresa, natureza):

    conn = ConexaoPostgreMPL.conexao()
    query = 'delete from "Reposicao".cadendereco ' \
            'where rua = %s and modulo = %s and posicao = %s'



    r = int(rua)
    ruaLimite = int(ruaLimite) + 1

    m = int(modulo)
    moduloLimite = int(moduloLimite) +1

    p = int(posicao)
    posicaoLimite = int(posicaoLimite)+1

    while r < ruaLimite:
        ruaAtual = Acres_0(r)
        while m < moduloLimite:
            moduloAtual = Acres_0(m)
            while p < posicaoLimite:
                posicaoAtual = Acres_0(p)
                codendereco = ruaAtual + '-' + moduloAtual +"-"+posicaoAtual
                cursor = conn.cursor()
                select = pd.read_sql('select "Endereco" from "Reposicao".tagsreposicao where "Endereco" = %s ', conn,
                                     params=(codendereco,))
                if  select.empty:
                    cursor.execute(query, ( ruaAtual, moduloAtual, posicaoAtual,))
                    conn.commit()
                    cursor.close()
                else:
                    cursor.close()
                    print(f'{codendereco} nao pode ser excluido ')
                p += 1
            p = int(posicao)
            m +=1
        m = int(modulo)
        r += 1


def ObterTipoPrateleira():
    conn = ConexaoPostgreMPL.conexao()
    qurey = pd.read_sql('select tipo from "Reposicao"."configuracaoTipo" ',conn)

    return qurey


def ObterEnderecosEspeciais():
    conn = ConexaoPostgreMPL.conexao()

    consulta = """
    select c.codendereco, ce.saldo , ce."SaldoLiquid"  from "Reposicao"."Reposicao".cadendereco c 
left join "Reposicao"."Reposicao"."calculoEndereco" ce on ce.endereco = c.codendereco 
where c.endereco_subst = 'sim'
    """
    consulta = pd.read_sql(consulta,conn)

    conn.close()

    consulta.fillna(0,inplace = True)

    return consulta