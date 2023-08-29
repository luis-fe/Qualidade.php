import ConexaoPostgreMPL
import pandas as pd

import PediosApontamento
import datetime
import pytz
import locale


def obterHoraAtual():
    fuso_horario = pytz.timezone('America/Sao_Paulo')
    agora = datetime.datetime.now(fuso_horario)
    hora_str = agora.strftime('%Y-%m-%d %H:%M:%S')
    return hora_str

def AtribuirPedido(usuario, pedidos, dataAtribuicao):
    tamanho = len(pedidos)
    pedidosNovo = []
    for i in range(tamanho):
       incr =  str(pedidos[i])
       pedidosNovo.append(incr)

    pedidosNovo = [p.replace(',', '/') for p in pedidosNovo]



    if tamanho >= 0:
        conn = ConexaoPostgreMPL.conexao()
        for i in range(tamanho):
            pedido_x = str(pedidosNovo[i])
            query = 'update "Reposicao".filaseparacaopedidos '\
                    'set cod_usuario = %s '\
                    'where codigopedido = %s'
            cursor = conn.cursor()
            cursor.execute(query, (usuario,pedido_x, ))
            conn.commit()
            cursor.close()
            consulta1 = pd.read_sql('select datahora, vlrsugestao  from "Reposicao".filaseparacaopedidos '
                                   ' where codigopedido = %s ', conn, params=(pedido_x,))

            consulta2 = pd.read_sql('select * from "Reposicao".finalizacao_pedido '
                                   ' where codpedido = %s ', conn, params=(pedido_x,))

            consulta3 = pd.read_sql('select sum(qtdesugerida) as qtdepcs  from "Reposicao".pedidossku '
                                   ' where codpedido = %s group by codpedido ', conn, params=(pedido_x,))
            dataatual = obterHoraAtual()
            try:
                if consulta2.empty and not consulta1.empty:
                    datahora = consulta1["datahora"][0]
                    vlrsugestao = consulta1["vlrsugestao"][0]
                    qtdepcs = consulta3["qtdepcs"][0]
                    cursor2 = conn.cursor()

                    insert = 'insert into "Reposicao".finalizacao_pedido (usuario, codpedido, datageracao, dataatribuicao, vlrsugestao, qtdepçs) values (%s , %s , %s , %s, %s, %s)'
                    cursor2.execute(insert, (usuario, pedido_x, datahora, dataatual, vlrsugestao,qtdepcs))
                    conn.commit()


                    cursor2.close()
                    print(f'Insert Pedido Finalizacao {usuario} e {datahora}')
                else:

                    cursor2 = conn.cursor()
                    vlrsugestao = consulta1["vlrsugestao"][0]
                    qtdepcs = consulta3["qtdepcs"][0]

                    update = 'update "Reposicao".finalizacao_pedido ' \
                             'set datageracao = %s , dataatribuicao = %s , usuario = %s, vlrsugestao = %s, qtdepçs= %s ' \
                             'where codpedido = %s'
                    cursor2.execute(update, (consulta1['datahora'][0], dataatual,usuario, vlrsugestao,qtdepcs, pedido_x))
                    conn.commit()
                    cursor2.close()

                    print(f'update {consulta3}')

            except:
                print('segue o baile')
        conn.close()
    else:
        print('sem pedidos')

    data = {
        '1- Usuario:': usuario,
        '2- Pedidos para Atribuir:': pedidos,
        '3- dataAtribuicao:': dataAtribuicao
    }
    return [data]

def ClassificarFila(coluna, tipo):
    fila = PediosApontamento.FilaPedidos()
    fila['12-vlrsugestao'] = fila['12-vlrsugestao'].str.replace("R\$", "").astype(float)

    if tipo == 'desc':
        fila = fila.sort_values(by=coluna, ascending=False)
        fila['12-vlrsugestao'] = fila['12-vlrsugestao'] .astype(str)
        fila['12-vlrsugestao'] = 'R$ ' + fila['12-vlrsugestao']

        return fila

    else:
        fila['12-vlrsugestao'] = fila['12-vlrsugestao'] .astype(str)
        fila['12-vlrsugestao'] = 'R$ ' + fila['12-vlrsugestao']
        fila = fila.sort_values(by=coluna, ascending=True)
        return fila

def AtribuicaoDiaria():
    conn = ConexaoPostgreMPL.conexao()

    query = pd.read_sql('select usuario, qtdepçs, vlrsugestao from "Reposicao".finalizacao_pedido '
                        'WHERE CAST(dataatribuicao AS DATE) = current_date;',conn)
    query['qtdPedidos'] =   query['usuario'].count()
    query ['qtdepçs'] = query ['qtdepçs'].astype(float)
    query['qtdepçs'] = query['qtdepçs'].astype(int)
    query['vlrsugestao'] = query['vlrsugestao'].astype(float)


    query = query.groupby('usuario').agg({
        'qtdepçs': 'sum',
        'vlrsugestao': 'sum',
        'qtdPedidos': 'count'})

    def format_with_separator(value):
        return locale.format('%0.0f', value, grouping=True)

    query['vlrsugestao'] = query['vlrsugestao'].apply(format_with_separator)

    query['vlrsugestao'] = query['vlrsugestao'].round(2)
    query['vlrsugestao'] = query['vlrsugestao'].astype(str)
    query['vlrsugestao'] = 'R$ ' + query['vlrsugestao']
    query['Méd. pç/pedido'] = query['qtdepçs']/query['qtdPedidos']
    query['Méd. pç/pedido'] = query['Méd. pç/pedido'].round(2)
    query = query.sort_values(by='qtdPedidos', ascending=False)
    Usuarios = pd.read_sql('Select codigo as usuario, nome from "Reposicao".cadusuarios ', conn)
    Usuarios['usuario'] = Usuarios['usuario'].astype(str)
    query = pd.merge(query, Usuarios, on='usuario', how='left')
    query.rename(columns={'nome': '1- nome', "qtdPedidos": '2- qtdPedidos', "qtdepçs":"3- qtdepçs"}, inplace=True)

    return query






