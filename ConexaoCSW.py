import jaydebeapi
import pandas as pd


def Conexao():
    conn = jaydebeapi.connect(
    'com.intersys.jdbc.CacheDriver',
    'jdbc:Cache://192.168.0.25:1972/CONSISTEM',
    {'user': 'root', 'password': 'ccscache'},
    'CacheDB.jar'
)
    return conn

try:
    conn = Conexao()
    teste = pd.read_sql('select * from tcp.tamanhos',conn)
    print(teste)
except:
    print('caiu a conexao')