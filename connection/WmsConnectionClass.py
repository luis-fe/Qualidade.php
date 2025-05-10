import gc
import os
import psycopg2
from sqlalchemy import create_engine
from models.configuracoes import  empresaConfigurada
from dotenv import load_dotenv, dotenv_values

class WmsConnectionClass():
    '''Class que faz a conexao com o banco WMS'''
    def __init__(self, empresa = None):
        self.empresa = empresa


    def conexao(self):
        load_dotenv('/home/grupompl/Wms_InternoMPL/ambiente.env')

        db_name = os.getenv('POSTGRE_NAME')
        db_user = os.getenv('POSTGRE_USER')
        db_password = os.getenv('POSTGRE_PASSWORD')

        if empresaConfigurada.EmpresaEscolhida() == '1':
            host = "localhost"
        else:
            host = "localhost"

        portbanco = "5432"

        return psycopg2.connect(dbname=db_name, user=db_user, password=db_password, host=host, port=portbanco)