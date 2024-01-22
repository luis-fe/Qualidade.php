import datetime
import pandas as pd
import ConexaoPostgreMPL
from psycopg2 import errors


def obterHoraAtual():
    agora = datetime.datetime.now()
    hora_str = agora.strftime('%d/%m/%Y %H:%M')
    return hora_str

def PesquisarLinhaPadrao():
    conn = ConexaoPostgreMPL.conexao()

    linhas = pd.read_sql('select * from "Reposicao".off."linhapadrado" c order by "Linha" asc',conn)

    conn.close()
    return linhas
def RetornarNomeLinha(linha):
    conn = ConexaoPostgreMPL.conexao()

    linhas = pd.read_sql('select * from "Reposicao".off."linhapadrado" c where "Linha" = %s ', conn, params=(linha,))
    conn.close()

    if not linhas.empty:
        linhas.fillna('-', inplace=True)
        return pd.DataFrame([{'OperadoresLinha':linhas['operador1'][0]+'/'+linhas['operador2'][0]+'/'+linhas['operador3'][0],'status':'1',
                              'Mensagem':'Ja Possui Linha Cadastrada', 'operador1':linhas['operador1'][0],
                              'operador2':linhas['operador2'][0],'operador3':linhas['operador3'][0]}])
    else:
        return pd.DataFrame([{'Mensagem':'nao existe essa linha', 'status':'2'}])

def CadastrarLinha(nomeLinha, operador1, operador2, operador3):

    consularLinha = RetornarNomeLinha(nomeLinha)
    print(consularLinha)
    if consularLinha['status'][0] == '2':
        conn = ConexaoPostgreMPL.conexao()


        if operador3 != '-':
            insertInto = 'insert into "Reposicao".off."linhapadrado"' \
                     ' ("Linha",operador1, operador2, operador3) values (%s, %s, %s , %s)'
            cursor = conn.cursor()
            try:
                cursor.execute(insertInto, (nomeLinha, operador1, operador2, operador3))
                conn.commit()
                mensagem = pd.DataFrame([{'Mensagem': 'Linha cadastrado com sucesso'}])

            except errors.ForeignKeyViolation as e:
                # Se uma exceção de violação de chave estrangeira ocorrer, imprima a mensagem de erro ou faça o que for necessário
                cursor.close()
                conn.close()
                print(f"Erro inesperado nome operador1: {e}")

                mensagem = pd.DataFrame(
                    [{'Mensagem': f'{e}'}])


            except Exception as e:
                # Lide com outras exceções aqui, se necessário
                cursor.close()
                conn.close()
                print(f"Erro inesperado: {e}")
                mensagem = pd.DataFrame(
                    [{'Mensagem': f'NÃO EXISTE O NOME DO OPERADOR 1: {operador1} no cadastro de usuarios do portal !'}])

            cursor.close()
            conn.close()
            return mensagem


        elif operador3 == '-' and operador2 != '-':
            insertInto = 'insert into "Reposicao".off."linhapadrado" ' \
                         '("Linha", operador1, operador2) values (%s, %s, %s)'
            cursor = conn.cursor()
            try:
                cursor.execute(insertInto, (nomeLinha, operador1, operador2))
                conn.commit()
                mensagem = pd.DataFrame([{'Mensagem': 'Linha cadastrado com sucesso'}])

            except errors.ForeignKeyViolation as e:
                # Se uma exceção de violação de chave estrangeira ocorrer, imprima a mensagem de erro ou faça o que for necessário
                cursor.close()
                conn.close()
                print(f"Erro inesperado nome operador1: {e}")

                mensagem = pd.DataFrame(
                    [{'Mensagem': f'{e}'}])


            except Exception as e:
                # Lide com outras exceções aqui, se necessário
                cursor.close()
                conn.close()
                print(f"Erro inesperado: {e}")
                mensagem = pd.DataFrame(
                    [{'Mensagem': f'NÃO EXISTE O NOME DO OPERADOR 1: {operador1} no cadastro de usuarios do portal !'}])

            cursor.close()
            conn.close()
            return mensagem



        elif operador2 == '-':
            insertInto = 'insert into "Reposicao".off."linhapadrado" ' \
                         '("Linha", operador1) values (%s, %s)'
            cursor = conn.cursor()
            try:
                cursor.execute(insertInto, (nomeLinha, operador1))
                conn.commit()
                mensagem = pd.DataFrame([{'Mensagem': 'Linha cadastrado com sucesso'}])

            except errors.ForeignKeyViolation as e:
                # Se uma exceção de violação de chave estrangeira ocorrer, imprima a mensagem de erro ou faça o que for necessário
                cursor.close()
                conn.close()
                print(f"Erro inesperado nome operador1: {e}")

                mensagem = pd.DataFrame([{'Mensagem': f'NÃO EXISTE O NOME DO OPERADOR 1: {operador1} no cadastro de usuarios do portal !'}])


            except Exception as e:
                # Lide com outras exceções aqui, se necessário
                cursor.close()
                conn.close()
                print(f"Erro inesperado: {e}")
                mensagem = pd.DataFrame(
                    [{'Mensagem': f'NÃO EXISTE O NOME DO OPERADOR 1: {operador1} no cadastro de usuarios do portal !'}])

            cursor.close()
            conn.close()
            return mensagem
    else:
        return consularLinha

def AlterarLinha(nomeLinha, operador1, operador2, operador3):

    obterLinha = RetornarNomeLinha(nomeLinha)
    print(obterLinha)
    conn = ConexaoPostgreMPL.conexao()
    if operador3 == '-' and operador2 == '-' and obterLinha['status'][0] == '1':

        uptade = 'update "Reposicao".off."linhapadrado" ' \
             'set operador1 = %s ' \
             'where "Linha" = %s'
        cursor = conn.cursor()
        cursor.execute(uptade, (operador1, nomeLinha))
        conn.commit()
        cursor.close()
        conn.close()
        mensagem = 'Atualizado com sucesso !'

    elif operador3 == '-' and obterLinha['status'][0] == '1' and operador2 != '-':
        uptade = 'update "Reposicao".off."linhapadrado" ' \
             'set operador1 = %s, operador2 ' \
             'where "Linha" = %s'
        cursor = conn.cursor()
        cursor.execute(uptade, (operador1,operador2, nomeLinha))
        conn.commit()
        cursor.close()
        conn.close()
        mensagem = 'Atualizado com sucesso !'

    elif operador2 !='-' and obterLinha['status'][0] == '1':
        uptade = 'update "Reposicao".off."linhapadrado" ' \
             'set operador1 = %s, operador2 = %s , operador3 = %s ' \
             'where "Linha" = %s'
        cursor = conn.cursor()
        cursor.execute(uptade, (operador1, operador2, operador3, nomeLinha))
        conn.commit()
        cursor.close()
        conn.close()
        mensagem = 'Atualizado com sucesso !'



    else:
        mensagem = 'A Linha informada nao possui cadastro'


    return pd.DataFrame([{'Mensagem':mensagem}])


