import pandas as pd
import ConexaoPostgreMPL
from psycopg2 import sql
import datetime
import pytz
def obterHoraAtual():
    fuso_horario = pytz.timezone('America/Sao_Paulo')  # Define o fuso horário do Brasil
    agora = datetime.datetime.now(fuso_horario)
    hora_str = agora.strftime('%d/%m/%Y %H:%M')
    return hora_str



def EstornarReservasNaoAtribuidas():
    conn = ConexaoPostgreMPL.conexao()

    # Acessando os pedidos com enderecos reservados
    queue = pd.read_sql('select * from "Reposicao".pedidossku '
                        "where necessidade > 0 and reservado = 'sim' ",conn)

    # Acessando os pedidos NAO atribuidos
    queue2 = pd.read_sql('select codigopedido as codpedido, f.cod_usuario  from "Reposicao".filaseparacaopedidos f  '
                        "where cod_usuario is null ",conn)

    # Obtendo somente os enderecos reservados com pedidos nao atribuidos
    queue = pd.merge(queue,queue2,on='codpedido')
    tamanho = queue['codpedido'].size
    # transformando o codigo do pedido em lista
    # Obter os valores para a cláusula WHERE do DataFrame
    lista = queue['codpedido'].tolist()
    # Construir a consulta DELETE usando a cláusula WHERE com os valores do DataFrame

    query = sql.SQL('UPDATE  "Reposicao"."pedidossku" '
                    "set reservado = 'nao', endereco = 'Não Reposto' "
                    'WHERE codpedido IN ({})').format(
        sql.SQL(',').join(map(sql.Literal, lista))
    )

    if tamanho != 0:
        # Executar a consulta DELETE
        with conn.cursor() as cursor:
            cursor.execute(query)
            conn.commit()

    cursor.close()
    conn.close()

    return pd.DataFrame([{'Mensagem': f'foram estornado a reserva de {tamanho} endereços'}])
def LimparReservaPedido(pedido):
    conn = ConexaoPostgreMPL.conexao()
    # Acessando os pedidos com enderecos reservados
    queue = pd.read_sql('update "Reposicao".pedidossku '
                        " set reservado = 'nao', endereco = 'Não Reposto' "
                        " where codpedido = %s",conn, params=(pedido))

    cursor = conn.cursor()
    cursor.execute(queue,(pedido))
    conn.commit()

    conn.close()
    return pd.DataFrame([{'Mensagem': f'As reservas para o pedido {pedido} foram limpadas'}])

def AtribuirReserva(pedido, natureza):
    conn = ConexaoPostgreMPL.conexao()
    total = 0  # Para Totalizar o numer de atualizcoes
    inseridosDuplos = 0

    # Passo 1 :  obter os skus do pedido
    LimparReservaPedido(pedido)

    queue = pd.read_sql('select produto, necessidade from "Reposicao".pedidossku '
                        "where necessidade > 0 and codpedido = %s ",conn,params=(pedido,))

    enderecosSku = pd.read_sql(
        ' select  codreduzido as produto, codendereco as codendereco2, "SaldoLiquid"  from "Reposicao"."calculoEndereco"  '
        ' where  natureza = %s  order by "SaldoLiquid" asc', conn, params=(natureza,))

    enderecosSku = pd.merge(enderecosSku,queue, on= 'produto')

    # Calculando a necessidade de cada endereco

    enderecosSku['repeticoessku'] = enderecosSku.groupby('produto').cumcount() + 1
    for i in range(0):
        pedidoskuIteracao = enderecosSku[enderecosSku['repeticoessku'] == (i + 1)]

        tamanho = pedidoskuIteracao['produto'].size
        pedidoskuIteracao = pedidoskuIteracao.reset_index(drop=False)

        for i in range(tamanho):
            necessidade = pedidoskuIteracao['necessidade'][i]
            saldoliq = pedidoskuIteracao['SaldoLiquid'][i]
            endereco = pedidoskuIteracao['codendereco2'][i]
            produto = pedidoskuIteracao['produto'][i]
            pedido = pedidoskuIteracao['codpedido'][i]

            if necessidade<= saldoliq:
                    update = 'UPDATE "Reposicao".pedidossku '\
                             'SET endereco = %s , reservado = %s'\
                             'WHERE codpedido = %s AND produto = %s and reservado = %s '

                    # Filtrar e atualizar os valores "a" para "aa"
                    pedidoskuIteracao.loc[(pedidoskuIteracao['codendereco2'] == endereco) &
                                          (pedidoskuIteracao['produto'] == produto), 'SaldoLiquid'] \
                        = pedidoskuIteracao['SaldoLiquid'][i] - pedidoskuIteracao['necessidade'][i]

                    cursor = conn.cursor()

                    # Executar a atualização na tabela "Reposicao.pedidossku"
                    cursor.execute(update,
                                   (endereco,'sim',
                                    pedido, produto,'nao')
                                    )

                    # Confirmar as alterações
                    conn.commit()

                    total = total + 1

            elif saldoliq >0 and necessidade > saldoliq:
                qtde_sugerida = pd.read_sql('select qtdesugerida from "Reposicao".pedidossku '
                                            "where reservado = 'nao' and codpedido = "+"'"+pedido+"' and produto ="
                                                                                                  " '"+produto+"'",conn)
                if not qtde_sugerida.empty:
                    qtde_sugerida = qtde_sugerida['qtdesugerida'][0]
                    qtde_sugerida2 = qtde_sugerida - saldoliq

                    insert = 'insert into "Reposicao".pedidossku (codpedido, datahora, endereco, necessidade, produto, qtdepecasconf, ' \
                             'qtdesugerida, reservado, status, valorunitarioliq) ' \
                             'select codpedido, datahora, %s, %s, produto, qtdepecasconf, ' \
                             '%s, %s, status, valorunitarioliq from "Reposicao".pedidossku ' \
                             'WHERE codpedido = %s AND produto = %s and reservado = %s ' \
                             ' limit 1;'
                    cursor = conn.cursor()

                    # Executar a atualização na tabela "Reposicao.pedidossku"
                    cursor.execute(insert,
                                   ('Não Reposto', qtde_sugerida2, qtde_sugerida2, 'nao',
                                    pedido, produto, 'nao')
                                   )

                    # Confirmar as alterações
                    conn.commit()

                    update = 'UPDATE "Reposicao".pedidossku ' \
                             'SET endereco = %s , qtdesugerida = %s , reservado = %s, necessidade = %s ' \
                             'WHERE codpedido = %s AND produto = %s and reservado = %s and qtdesugerida = %s'

                    # Filtrar e atualizar os valores "a" para "aa"
                    pedidoskuIteracao.loc[(pedidoskuIteracao['codendereco2'] == endereco) &
                                          (pedidoskuIteracao['codreduzido'] == produto), 'SaldoLiquid'] = 0
                    cursor = conn.cursor()

                    # Executar a atualização na tabela "Reposicao.pedidossku"
                    cursor.execute(update,
                                   (endereco, saldoliq, 'sim', saldoliq,
                                    pedido, produto, 'nao', qtde_sugerida)
                                   )

                    # Confirmar as alterações
                    conn.commit()

                    inseridosDuplos = 1 + inseridosDuplos

            else:
                encerra = i
    datahora = obterHoraAtual()
    print(f'{total} atualizacoes realizadas, as {datahora}')
    return pd.DataFrame([{'Mensagem': f'foram reservados  {total} pçs e incrementado {inseridosDuplos}'}])










