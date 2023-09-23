import jaydebeapi
import pandas as pd


def Conexao():
    conn = jaydebeapi.connect(
    'com.intersys.jdbc.CacheDriver',
    'jdbc:Cache://192.168.0.25:1972/CONSISTEM',
    {'user': '_SYSTEM', 'password': 'ccscache'},
    'CacheDB.jar'
)
    return conn

def Conexao2():
    conn = jaydebeapi.connect(
    'com.intersys.jdbc.CacheDriver',
    'jdbc:Cache://192.168.0.25:1972/CONSISTEM',
    {'user': 'root', 'password': 'ccscache'},
    'CacheDB.jar'
)
    return conn

def obter_notaCsw():
    try:
        conn = Conexao()
    except:
        conn = Conexao2()
        print('usado a conexao 2 root')

    data = pd.read_sql(" select t.codigo ,t.descricao  from Fat.TipoDeNotaPadrao t ", conn)
    conn.close()

    return data


try:
    teste = obter_notaCsw()
    print(f'{teste}')
except:
    print('caiu a conexao')