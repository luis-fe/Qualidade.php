import pandas as pd
import ConexaoPostgreMPL

def Obter_chamados(status_chamado, solicitante, atribuido_para, tipo_chamado):
    conn = ConexaoPostgreMPL.conexao()
    if status_chamado != '':
        solicitante = '%'+solicitante+'%'
        atribuido_para = '%' + atribuido_para + '%'
        tipo_chamado = '%' + tipo_chamado + '%'

        query = pd.read_sql('select id_chamado, solicitante, data_chamado, '
                            ' tipo_chamado, atribuido_para, descricao_chamado, status_chamado, '
                            'data_finalizacao_chamado from "chamado"."registro_chamados" where status_chamado = %s and solicitante like %s and atribuido_para like %s'
                            ' and tipo_chamado like %s '
                            'order by data_chamado', conn, params=(status_chamado,solicitante,atribuido_para, tipo_chamado))
        query.fillna('-', inplace=True)

        conn.close()
    else:
        query = pd.read_sql('select id_chamado, solicitante, data_chamado, '
                            ' tipo_chamado, atribuido_para, descricao_chamado, status_chamado, '
                            'data_finalizacao_chamado from "chamado"."registro_chamados" '
                            'order by data_chamado', conn)

        query.fillna('-', inplace=True)

        conn.close()


    return query

def novo_chamados(solicitante, data_chamado, tipo_chamado, atribuido_para, descricao_chamado, status_chamado, data_finalizacao_chamado):
    conn = ConexaoPostgreMPL.conexao()
    try:
        cursor = conn.cursor()
        cursor.execute('INSERT INTO "chamado"."registro_chamados" (solicitante, data_chamado, tipo_chamado, atribuido_para, '
                       'descricao_chamado, status_chamado, data_finalizacao_chamado) '
                       'VALUES (%s, %s, %s, %s, %s, %s, %s)',(solicitante, data_chamado, tipo_chamado, atribuido_para, descricao_chamado, status_chamado, data_finalizacao_chamado))

        conn.commit()
        cursor.close()
        conn.close()
        return True
    except:
        return False

def encerrarchamado(id_chamado, data_finalizacao_chamado):
    conn = ConexaoPostgreMPL.conexao()
    try:
        cursor = conn.cursor()
        cursor.execute('UPDATE "chamado"."registro_chamados" '
                       "SET data_finalizacao_chamado = %s, status_chamado = 'finalizado' "
                       ' WHERE id_chamado = %s',( data_finalizacao_chamado,id_chamado,))
        conn.commit()
        cursor.close()
        conn.close()
        return True
    except:
        return False

def ultimoId():
    conn = ConexaoPostgreMPL.conexao()
    queue = pd.read_sql('select id_chamado from "chamado"."registro_chamados" ',conn)
    id_chamado = queue['id_chamado'].max()
    conn.close()
    return id_chamado