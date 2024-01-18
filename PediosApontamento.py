import ConexaoCSW
import ConexaoPostgreMPL
import pandas as pd

import numpy as np


def FilaPedidos():
    conn = ConexaoPostgreMPL.conexao()
    pedido = pd.read_sql(
        ' select f.codigopedido , f.vlrsugestao, f.codcliente , f.desc_cliente, f.cod_usuario, f.cidade, f.estado, '
        'datageracao, f.codrepresentante , f.desc_representante, f.desc_tiponota, condicaopgto, agrupamentopedido, situacaopedido  '
        '  from "Reposicao".filaseparacaopedidos f '
         , conn)
    pedidosku = pd.read_sql('select codpedido, sum(qtdesugerida) as qtdesugerida, sum(necessidade) as necessidade   from "Reposicao".pedidossku p  '
                            'group by codpedido ', conn)
    pedidosku.rename(columns={'codpedido': '01-CodPedido', 'qtdesugerida': '15-qtdesugerida','necessidade': '19-necessidade'}, inplace=True)

    usuarios = pd.read_sql(
        'select codigo as cod_usuario , nome as nomeusuario_atribuido  from "Reposicao".cadusuarios c ', conn)
    usuarios['cod_usuario'] = usuarios['cod_usuario'].astype(str)
    pedido = pd.merge(pedido, usuarios, on='cod_usuario', how='left')

    try:
        conn2 = ConexaoCSW.Conexao()
        transporta = pd.read_sql('SELECT  t.cidade , t.siglaEstado as estado, f.fantasia as transportadora  FROM Asgo_Trb.TransPreferencia t'
                                 ' join cad.Transportador  f on  f.codigo  = t.Transportador  '
                                 ' WHERE t.Empresa = 1 ',conn2)
        conn2.close()
        pedido = pd.merge(pedido, transporta, on=["cidade", "estado"], how='left')
    except:

        pedido['transportadora'] = 'Perdeu Conexao Csw'



    pedido.rename(
        columns={'codigopedido': '01-CodPedido', 'datageracao': '02- Data Sugestao', 'desc_tiponota': '03-TipoNota',
                 'codcliente': '04-codcliente',
                 'desc_cliente': '05-desc_cliente', 'cidade': '06-cidade', 'estado': '07-estado',
                 'codrepresentante': '08-codrepresentante', 'desc_representante': '09-Repesentante',
                 'cod_usuario': '10-codUsuarioAtribuido',
                 'nomeusuario_atribuido': '11-NomeUsuarioAtribuido', 'vlrsugestao': '12-vlrsugestao',
                 'condicaopgto': '13-CondPgto', 'agrupamentopedido': '14-AgrupamentosPedido','situacaopedido': '22- situacaopedido',"transportadora":"23-transportadora"}, inplace=True)
    pedido['12-vlrsugestao'] = 'R$ ' + pedido['12-vlrsugestao']
    pedido = pd.merge(pedido, pedidosku, on='01-CodPedido', how='left')
    pedido['15-qtdesugerida'] = pedido['15-qtdesugerida'].fillna(0)
    pedidoskuReposto = pd.read_sql('select codpedido, sum(necessidade) as reposto  from "Reposicao".pedidossku p '
                                   "where endereco <> 'Não Reposto' "
                                   'group by codpedido ', conn)
    pedidoskuReposto.rename(columns={'codpedido': '01-CodPedido', 'reposto': '16-Endereco Reposto'}, inplace=True)
    pedido = pd.merge(pedido, pedidoskuReposto, on='01-CodPedido', how='left')
    pedidoskuReposto2 = pd.read_sql('select codpedido, sum(necessidade) as naoreposto  from "Reposicao".pedidossku p '
                                    "where endereco = 'Não Reposto' "
                                    'group by codpedido ', conn)
    pedidoskuReposto2.rename(columns={'codpedido': '01-CodPedido', 'naoreposto': '17-Endereco NaoReposto'},
                             inplace=True)
    pedido = pd.merge(pedido, pedidoskuReposto2, on='01-CodPedido', how='left')
    pedido['16-Endereco Reposto'] = pedido['16-Endereco Reposto'].fillna(0)
    pedido['17-Endereco NaoReposto'] = pedido['17-Endereco NaoReposto'].fillna(0)
    pedido['19-necessidade'] = pedido['19-necessidade'].fillna(0)

    pedido['18-%Reposto'] = pedido['17-Endereco NaoReposto'] + pedido['16-Endereco Reposto']


    pedido['18-%Reposto'] = pedido['16-Endereco Reposto'] / pedido['18-%Reposto']


    pedido['20-Separado%'] = 1 - (pedido['19-necessidade'] / pedido['15-qtdesugerida'])
    pedido['18-%Reposto'] = (pedido['18-%Reposto'] * 100).round(2)
    pedido['18-%Reposto'] = pedido['18-%Reposto'].fillna(0)
    pedido['20-Separado%'] = (pedido['20-Separado%'] * 100).round(2)
    pedido['20-Separado%'] = pedido['20-Separado%'].fillna(0)
    # obtendo a Marca do Pedido
    marca = pd.read_sql('select codpedido ,  t.engenharia   from "Reposicao".pedidossku p '
                        'join "Reposicao".tagsreposicao t on t.codreduzido = p.produto '
                        'group by codpedido, t.engenharia ',conn)
    marca['engenharia'] = marca['engenharia'].str.slice(1)
    marca['21-MARCA'] =np.where((marca['engenharia'].str[:3] == '102') | (marca['engenharia'].str[:3] == '202') , 'M.POLLO', 'PACO')
    marca.drop('engenharia', axis=1, inplace=True)
    marca.drop_duplicates(subset='codpedido', inplace=True)
    marca.rename(columns={'codpedido': '01-CodPedido'}, inplace=True)
    pedido = pd.merge(pedido, marca, on='01-CodPedido', how='left')
    pedido['21-MARCA'].fillna('-', inplace=True)
    pedido['22- situacaopedido'].fillna('No Retorna', inplace=True)
    pedido.fillna('-', inplace=True)
    pedido.replace([np.inf, -np.inf], 0, inplace=True)

    return pedido




def FilaAtribuidaUsuario(codUsuario):
    x = FilaPedidos()
    codUsuario = str(codUsuario)
    x = x[x['10-codUsuarioAtribuido'] == codUsuario]
    return x


def ApontamentoTagPedido(codusuario, codpedido, codbarra, datahora, enderecoApi, padrao= False):
    # Aqui inplanto uma funcao para verificar qual a validação de onde vem  o **Codigo Barras*** , exemplo vem da: fila ? reposicao? estornar? ....
    validacao, Reduzido, Necessidade, ValorUnit, endereco = VerificacoesApontamento(codbarra, codpedido, enderecoApi)

    if validacao == 1: # 1 Caso a tag venha NORMAL da Prateleira Reposta e também nao for duplicada  no pedido

        if Necessidade <= 0: # 1.1 Caso essa tag ja tenha sido bipado na separacao
            return pd.DataFrame(
                {'Mensagem': [f'o produto {Reduzido} já foi totalmente bipado. Deseja estornar ?']})

        else: #1.2 Caso a tag nunca esteve bipada na separacao
            conn = ConexaoPostgreMPL.conexao()
            insert = 'INSERT INTO "Reposicao".tags_separacao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                     '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                     '"numeroop", "cor", "tamanho", "totalop", "codpedido","dataseparacao", "usuario_rep") ' \
                     'SELECT %s, "codbarrastag", "codreduzido", "Endereco", "engenharia", ' \
                     '"DataReposicao", "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop", ' \
                     "%s, %s, usuario " \
                     'FROM "Reposicao".tagsreposicao t ' \
                     'WHERE "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(insert, (codusuario, 'tagSeparado(v1)', codpedido, datahora, codbarra))
            conn.commit()
            cursor.close()
            delete = 'Delete from "Reposicao"."tagsreposicao"  ' \
                     'where "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(delete
                           , (
                               codbarra,))
            conn.commit()
            cursor.close()
            uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                           ' SET necessidade= %s ' \
                           'where "produto" = %s and codpedido= %s ;'
            Necessidade = Necessidade - 1
            cursor = conn.cursor()
            cursor.execute(uptadePedido
                           , (
                               Necessidade, Reduzido, codpedido))
            conn.commit()
            cursor.close()

            # atualizando a necessidade
            conn.close()
            return pd.DataFrame({'Mensagem': [f'tag {codbarra} apontada!'], 'status': [True]})


    elif validacao == 12: # 12 Caso a TAG venha da prateleira e o endereco seja duplo

        if Necessidade <= 0:
            return pd.DataFrame(
                {'Mensagem': [f'o produto {Reduzido} já foi totalmente bipado. Deseja estornar ?']})
        else:
            conn = ConexaoPostgreMPL.conexao()
            insert = 'INSERT INTO "Reposicao".tags_separacao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                     '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                     '"numeroop", "cor", "tamanho", "totalop", "codpedido","dataseparacao", "usuario_rep") ' \
                     'SELECT %s, "codbarrastag", "codreduzido", %s, "engenharia", ' \
                     '"DataReposicao", "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop", ' \
                     "%s, %s, usuario " \
                     'FROM "Reposicao".tagsreposicao t ' \
                     'WHERE "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(insert, (codusuario, enderecoApi,'tagSeparado(v12)', codpedido, datahora, codbarra))
            conn.commit()
            cursor.close()
            delete = 'Delete from "Reposicao"."tagsreposicao"  ' \
                     'where "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(delete
                           , (
                               codbarra,))
            conn.commit()
            cursor.close()

            if ValorUnit != '0':

                uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                           ' SET necessidade= %s ' \
                           'where "produto" = %s and codpedido= %s and endereco = %s and necessidade >0 and valorunitarioliq is null ;'
                Necessidade = Necessidade - 1
                cursor = conn.cursor()
                cursor.execute(uptadePedido
                           , (
                               Necessidade, Reduzido, codpedido, endereco))
                conn.commit()
                # Obtendo o número de linhas afetadas
                num_linhas_afetadas = cursor.rowcount
                print(f'endereco duplo {codpedido} / e {ValorUnit} , afetando {num_linhas_afetadas} linhas')

            else:
                uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                               ' SET necessidade= %s ' \
                               'where "produto" = %s and codpedido= %s and endereco = %s and necessidade >0 and valorunitarioliq = %s ;'
                Necessidade = Necessidade - 1
                cursor = conn.cursor()
                cursor.execute(uptadePedido
                               , (
                                   Necessidade, Reduzido, codpedido, endereco, ValorUnit))
                conn.commit()
                # Obtendo o número de linhas afetadas
                num_linhas_afetadas = cursor.rowcount
                print(f'endereco duplo {codpedido} / e {ValorUnit} , afetando {num_linhas_afetadas} linhas')


            cursor.close()

            # atualizando a necessidade
            conn.close()
            return pd.DataFrame({'Mensagem': [f'tag {codbarra} apontada!'], 'status': [True]})

    elif validacao == 2:
        if padrao == False:
            return pd.DataFrame(
            {'Mensagem': [f'o produto {Reduzido} já foi totalmente bipado. Deseja estornar ?']})

        else:

            conn = ConexaoPostgreMPL.conexao()
            insert = 'INSERT INTO "Reposicao".tagsreposicao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                         '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                         '"numeroop", "cor", "tamanho", "totalop") ' \
                         'SELECT usuario_rep, "codbarrastag", "codreduzido", "Endereco", "engenharia", ' \
                         '"DataReposicao", "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop"' \
                         'FROM "Reposicao".tags_separacao t ' \
                         'WHERE "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(insert,
                               ( 'Estornado', codbarra))
            conn.commit()
            cursor.close()
            delete = 'Delete from "Reposicao"."tags_separacao" ' \
                         'where "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(delete
                               , (
                                   codbarra,))
            conn.commit()
            cursor.close()
            uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                               ' SET necessidade= %s ' \
                               'where "produto" = %s and codpedido= %s and endereco = %s  ;'
            Necessidade = Necessidade + 1
            cursor = conn.cursor()
            cursor.execute(uptadePedido
                               , (
                                   Necessidade, Reduzido, codpedido, endereco))
            conn.commit()
            cursor.close()

            # atualizando a necessidade
            conn.close()

            return pd.DataFrame({'Mensagem': [f'tag  {codbarra} estornado no pedido'], 'status': [True]})


    elif validacao == 3:
        conn = ConexaoPostgreMPL.conexao()
        insert = 'INSERT INTO "Reposicao".tags_separacao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                 '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                 '"numeroop", "cor", "tamanho", "totalop", "codpedido","dataseparacao", "usuario_rep") ' \
                 'SELECT %s, "codbarrastag", "codreduzido", %s, "engenharia", ' \
                 '%s, "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop", ' \
                 "%s, %s, %s " \
                 'FROM "Reposicao".filareposicaoportag t ' \
                 'WHERE "codbarrastag" = %s;'
        cursor = conn.cursor()
        cursor.execute(insert, (codusuario,'Veio Da Fila',datahora, 'tagSeparado', codpedido, datahora, codbarra, codusuario))
        conn.commit()
        cursor.close()
        delete = 'Delete from "Reposicao"."filareposicaoportag" ' \
                 'where "codbarrastag" = %s;'
        cursor = conn.cursor()
        cursor.execute(delete
                       , (
                           codbarra,))
        conn.commit()
        cursor.close()
        uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                       ' SET necessidade= %s ' \
                       'where "produto" = %s and codpedido= %s and endereco = %s ;'
        Necessidade = Necessidade - 1
        cursor = conn.cursor()
        cursor.execute(uptadePedido
                       , (
                           Necessidade, Reduzido, codpedido, enderecoApi))
        conn.commit()
        cursor.close()

        # atualizando a necessidade
        conn.close()
        return pd.DataFrame({'Mensagem': [f'3- tag {codbarra} apontada, veio da FILA!'], 'status': [True]})

    elif validacao == 32:
        if padrao == False:
            return pd.DataFrame(
                {'Mensagem': [f'a tag {codbarra} já foi  bipado no Pedido. Deseja estornar ?']})
        else:
            if padrao == True:
                conn = ConexaoPostgreMPL.conexao()
                insert = 'INSERT INTO "Reposicao".tagsreposicao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                         '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                         '"numeroop", "cor", "tamanho", "totalop") ' \
                         'SELECT %s, "codbarrastag", "codreduzido", "Endereco", "engenharia", ' \
                         '"DataReposicao", "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop"' \
                         'FROM "Reposicao".tags_separacao t ' \
                         'WHERE "codbarrastag" = %s;'
                cursor = conn.cursor()
                cursor.execute(insert,
                               (codusuario, 'Estornado', codbarra))
                conn.commit()
                cursor.close()
                delete = 'Delete from "Reposicao"."tags_separacao" ' \
                         'where "codbarrastag" = %s;'
                cursor = conn.cursor()
                cursor.execute(delete
                               , (
                                   codbarra,))
                conn.commit()
                cursor.close()
                uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                               ' SET necessidade= %s ' \
                               'where "produto" = %s and codpedido= %s and endereco = %s ;'
                Necessidade = Necessidade + 1
                cursor = conn.cursor()
                cursor.execute(uptadePedido
                               , (
                                   Necessidade, Reduzido, codpedido, enderecoApi))
                conn.commit()
                cursor.close()

                # atualizando a necessidade
                conn.close()

                return pd.DataFrame({'Mensagem': [f'tag  {codbarra} estornado no pedido'], 'status': [True]})

    elif validacao == 33:
        if padrao == False:
            return pd.DataFrame(
                {'Mensagem': [f'o produto {Reduzido} já foi  bipado em outro Pedido. Deseja transferir ?']})
        else:
            conn = ConexaoPostgreMPL.conexao()
            insert = 'UPDATE "Reposicao".tags_separacao ' \
                     'set "Endereco" = %s, codpedido = %s ' \
                     'WHERE "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(insert, (enderecoApi, codpedido,
                                    codbarra))
            conn.commit()
            cursor.close()
            delete = 'Delete from "Reposicao"."filareposicaoportag" ' \
                     'where "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(delete
                           , (
                               codbarra,))
            conn.commit()
            cursor.close()
            uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                           ' SET necessidade= %s ' \
                           'where "produto" = %s and codpedido= %s and endereco = %s ;'
            Necessidade = Necessidade - 1
            cursor = conn.cursor()
            cursor.execute(uptadePedido
                           , (
                               Necessidade, Reduzido, codpedido, enderecoApi))
            conn.commit()
            cursor.close()

            # atualizando a necessidade
            conn.close()
            return pd.DataFrame(
                {'Mensagem': [f'33- tag {codbarra} apontada, TRANSFERIDO na separacao!'], 'status': [True]})

    elif validacao == 4:
        conn = ConexaoPostgreMPL.conexao()
        insert = 'INSERT INTO "Reposicao".tags_separacao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                 '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                 '"numeroop", "cor", "tamanho", "totalop", "codpedido","dataseparacao") ' \
                 'SELECT %s, "codbarrastag", "codreduzido", "Endereco", "engenharia", ' \
                 '"DataReposicao", "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop", ' \
                 "%s, %s " \
                 'FROM "Reposicao".tagsreposicao_inventario t ' \
                 'WHERE "codbarrastag" = %s;'

        cursor = conn.cursor()
        cursor.execute(insert, (codusuario,'Veio Do Inventario',datahora, 'tagSeparado', codpedido, datahora, codbarra))
        conn.commit()
        cursor.close()
        delete = 'Delete from "Reposicao"."tagsreposicao_inventario" ' \
                 'where "codbarrastag" = %s;'
        cursor = conn.cursor()
        cursor.execute(delete
                       , (
                           codbarra,))
        conn.commit()
        cursor.close()
        uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                       ' SET necessidade= %s ' \
                       'where "produto" = %s and codpedido= %s and endereco = %s;'
        Necessidade = Necessidade - 1
        cursor = conn.cursor()
        cursor.execute(uptadePedido
                       , (
                           Necessidade, Reduzido, codpedido, enderecoApi))
        conn.commit()
        cursor.close()

        # atualizando a necessidade
        conn.close()
        return pd.DataFrame({'Mensagem': [f'tag {codbarra} apontada, veio do Inventario!'], 'status': [True]})
    elif validacao == 5 :
        if padrao == False:
            return pd.DataFrame({'Mensagem': [f'tag  {codbarra} já foi apontada, deseja estornar ?'],'status': [False]} )
        if padrao == True:
            conn = ConexaoPostgreMPL.conexao()
            insert = 'INSERT INTO "Reposicao".tagsreposicao ("usuario", "codbarrastag", "codreduzido", "Endereco", ' \
                     '"engenharia", "DataReposicao", "descricao", "epc", "StatusEndereco", ' \
                     '"numeroop", "cor", "tamanho", "totalop") ' \
                     'SELECT %s, "codbarrastag", "codreduzido", "Endereco", "engenharia", ' \
                     '"DataReposicao", "descricao", "epc", %s, "numeroop", "cor", "tamanho", "totalop"' \
                     'FROM "Reposicao".tags_separacao t ' \
                     'WHERE "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(insert,
                           (codusuario, 'Estornado', codbarra))
            conn.commit()
            cursor.close()
            delete = 'Delete from "Reposicao"."tags_separacao" ' \
                     'where "codbarrastag" = %s;'
            cursor = conn.cursor()
            cursor.execute(delete
                           , (
                               codbarra,))
            conn.commit()
            cursor.close()
            uptadePedido = 'UPDATE "Reposicao".pedidossku' \
                           ' SET necessidade= %s ' \
                           'where "produto" = %s and codpedido= %s and endereco = %s ;'
            Necessidade = Necessidade + 1
            cursor = conn.cursor()
            cursor.execute(uptadePedido
                           , (
                               Necessidade, Reduzido, codpedido, enderecoApi))
            conn.commit()
            cursor.close()

            # atualizando a necessidade
            conn.close()

            return pd.DataFrame({'Mensagem': [f'tag  {codbarra} estornado no pedido'],'status': [True]} )

    else:
        return pd.DataFrame({'Mensagem': [f'tag nao pertence ao endereco selecionado']})


# ESSA FUNCAO VERIFICA DE ONDE O CODIGO DE BARRAS ESTÁ SAINDO
def VerificacoesApontamento(codbarra, codpedido, enderecoAPI):
    conn = ConexaoPostgreMPL.conexao()

    # 1. Verificar se o codigobarra veio da Reposição ou se veio da Fila/Inventario
    pesquisaTagReposicao = pd.read_sql(
        'SELECT "codbarrastag", "codreduzido", "Endereco" FROM "Reposicao".tagsreposicao f WHERE codbarrastag = %s',
        conn, params=(codbarra,))

    if not pesquisaTagReposicao.empty: #1.1 Nesse caso o codigo veio normal da Reposicao
        reduzido = pesquisaTagReposicao['codreduzido'][0]

        #1.2 Inicio fazendo uma consulta para verificar se o produto possue mais de 2 reservas de endereco
        pesquisaPedidoSKU1 = pd.read_sql(
            'SELECT p.codpedido, p.produto, p.necessidade, p.valorunitarioliq, p.endereco FROM "Reposicao".pedidossku p '
            'WHERE codpedido = %s AND produto = %s and necessidade > 0',
            conn, params=(codpedido, reduzido))

        tamanhoPesquisa2 = pesquisaPedidoSKU1['codpedido'].size #1.3 Aqui avalio se existe 1 uma ou mais de 1 uma reserva de enderecos diferentes.


        if tamanhoPesquisa2 == 1:
            conn.close()
            return 1, reduzido, pesquisaPedidoSKU1['necessidade'][0], pesquisaPedidoSKU1['valorunitarioliq'][0], pesquisaTagReposicao['Endereco'][0]

        elif tamanhoPesquisa2 > 1:
            pesquisaPedidoSKU2 = pd.read_sql(
                'SELECT p.codpedido, p.produto, p.necessidade, p.valorunitarioliq, p.endereco FROM "Reposicao".pedidossku p '
                'WHERE codpedido = %s AND produto = %s and necessidade > 0 and endereco = %s',
                conn, params=(codpedido, reduzido, enderecoAPI))
            if not pesquisaPedidoSKU2.empty:
                conn.close()
                return 12, reduzido, pesquisaPedidoSKU2['necessidade'][0], pesquisaPedidoSKU2['valorunitarioliq'][0], enderecoAPI
            else:
                return 12, reduzido, pesquisaPedidoSKU1['necessidade'][0], pesquisaPedidoSKU1['valorunitarioliq'][0], pesquisaPedidoSKU1['endereco'][0]
        else:
            pesquisaPedidoSKUEstornado = pd.read_sql(
                'SELECT p.codpedido, p.produto, p.necessidade, p.valorunitarioliq, p.endereco FROM "Reposicao".pedidossku p '
                'WHERE codpedido = %s AND produto = %s',
                conn, params=(codpedido, reduzido))

            print('Pegou o 2 estornar')
            return 2, reduzido, 2, 2, pesquisaPedidoSKUEstornado['endereco'][0]  # Se as condicoes nao forem atendidas

    else:
        # 2 - Else caso a tag NAO seja encontrada na reposicao
        pesquisa3 = pd.read_sql(
            'SELECT "codbarrastag", "codreduzido" AS codreduzido FROM "Reposicao".filareposicaoportag f '
            'WHERE codbarrastag = %s', conn, params=(codbarra,))

            # Pesquisar se a tag ja foi separada
        pesquisaSeparacao = pd.read_sql(
            'SELECT "codbarrastag", "codreduzido" AS codreduzido, codpedido FROM "Reposicao".tags_separacao f '
            'WHERE codbarrastag = %s', conn, params=(codbarra,))

        if not pesquisa3.empty and pesquisaSeparacao.empty:
            # 2.1 - Caso a tag seja encontrada na fila mas nao na separacao
            pesquisa4 = pd.read_sql(
                'SELECT p.codpedido, p.produto, p.necessidade FROM "Reposicao".pedidossku p '
                'WHERE codpedido = %s AND produto = %s and endereco = %s', conn, params=(codpedido, pesquisa3['codreduzido'][0],enderecoAPI))



            if not pesquisa4.empty :
                conn.close()
                return 3, pesquisa3['codreduzido'][0], pesquisa4['necessidade'][0], 3, 3
            else:
                pesquisa4 = pd.read_sql(
                    'SELECT p.codpedido, p.produto, p.necessidade FROM "Reposicao".pedidossku p '
                    'WHERE codpedido = %s AND produto = %s', conn,
                    params=(codpedido, pesquisa3['codreduzido'][0]))

                conn.close()
                return 3, pesquisa3['codreduzido'][0], pesquisa4['necessidade'][0], 3, 3


        elif not pesquisa3.empty and not pesquisaSeparacao.empty and codpedido == pesquisaSeparacao['codpedido'][0] :
            # 2.2 - Caso a tag seja encontrada na separacao e na fila, faz um UPDATE no pedido na separacao
            pesquisa4 = pd.read_sql(
                'SELECT p.codpedido, p.produto, p.necessidade FROM "Reposicao".pedidossku p '
                'WHERE codpedido = %s AND produto = %s', conn, params=(codpedido, pesquisa3['codreduzido'][0]))

            conn.close()

            return 32, pesquisa3['codreduzido'][0], pesquisa4['necessidade'][0], 32, 32

        elif not pesquisa3.empty and not pesquisaSeparacao.empty and codpedido != pesquisaSeparacao['codpedido'][0]:
            # 2.2 - Caso a tag seja encontrada na separacao e na fila, faz um UPDATE no pedido na separacao
            pesquisa4 = pd.read_sql(
                'SELECT p.codpedido, p.produto, p.necessidade FROM "Reposicao".pedidossku p '
                'WHERE codpedido = %s AND produto = %s', conn, params=(codpedido, pesquisa3['codreduzido'][0]))

            conn.close()

            return 32, pesquisa3['codreduzido'][0], pesquisa4['necessidade'][0], 32, 32

        else:
            pesquisarInventario = pd.read_sql(
                'SELECT "codbarrastag", "codreduzido" AS codreduzido FROM "Reposicao".tagsreposicao_inventario f '
                'WHERE codbarrastag = %s', conn, params=(codbarra,))

            if not pesquisarInventario.empty:
                pesquisa4 = pd.read_sql(
                    'SELECT p.codpedido, p.produto, p.necessidade FROM "Reposicao".pedidossku p '
                    'WHERE codpedido = %s AND produto = %s', conn, params=(codpedido, pesquisaTagReposicao['codreduzido'][0]))

                conn.close()
                return 4, pesquisa4['codreduzido'][0], pesquisa4['necessidade'][0], 4, 4
            else:
                pesquisarSeparacao = pd.read_sql(
                    'SELECT "codbarrastag", "codreduzido" AS codreduzido FROM "Reposicao".tags_separacao f '
                    'WHERE codbarrastag = %s', conn, params=(codbarra,))

                if not pesquisarSeparacao.empty:
                    pesquisa5 = pd.read_sql(
                        'SELECT p.codpedido, p.produto AS codreduzido, p.necessidade FROM "Reposicao".pedidossku p '
                        'WHERE codpedido = %s AND produto = %s', conn, params=(codpedido, pesquisarSeparacao['codreduzido'][0]))

                    conn.close()
                    return 5, pesquisa5['codreduzido'][0], pesquisa5['necessidade'][0], 5, 5
                else:
                    conn.close()
                    return 0, 0, 0, 0, 0





def pesquisarSKUxPedido(codpedido, reduzido):
    conn = ConexaoPostgreMPL.conexao()
    pesquisa2 = pd.read_sql(
        ' select p.codpedido, p.produto , p.necessidade  from "Reposicao".pedidossku p    '
        'where codpedido = ' + "'" + codpedido + "' and produto = " + "'" + reduzido + "'", conn)
    conn.close()
    return pesquisa2
def EndereçoTag(codbarra, empresa, natureza):
    conn = ConexaoPostgreMPL.conexao()
    pesquisa = pd.read_sql(
        ' select t."Endereco"  from "Reposicao".tagsreposicao t  '
        'where codbarrastag = ' + "'" + codbarra + "' and natureza = '"+natureza+"'", conn)

    pesquisa['Situacao'] = 'Reposto'
    pesquisa2 = pd.read_sql(
        " select '-' as Endereco  from " + '"Reposicao".filareposicaoportag f   '
                                           'where codbarrastag = ' + "'" + codbarra + "' and codnaturezaatual = '"+natureza+"'", conn)

    pesquisa2['Situacao'] = 'na fila'
    pesquisa3 = pd.read_sql(
        ' select distinct f."Endereco"  from "Reposicao".tagsreposicao_inventario f   '
        'where codbarrastag = ' + "'" + codbarra + "'", conn)

    pesquisa3['Situacao'] = f'em inventario'
    conn.close()

    if not pesquisa2.empty:
        return 'Na fila ', pesquisa2

    if not pesquisa3.empty:
        return 'em inventario ', pesquisa3
    if not pesquisa.empty:

        return pesquisa['Endereco'][0], pesquisa
    else:
        return False, pd.DataFrame({'Mensagem': [f'tag nao encontrada na natureza {natureza}']})

