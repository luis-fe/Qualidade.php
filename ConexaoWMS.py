import os

import psycopg2
from sqlalchemy import create_engine
from dotenv import load_dotenv, dotenv_values



def conexaoEngine():
    load_dotenv('/home/grupompl/Wms_Teste/Wms_InternoMPL/ambiente.env')

    db_name = os.getenv('POSTGRE_NAME')
    db_user = os.getenv('POSTGRE_USER')
    db_password = os.getenv('POSTGRE_PASSWORD')
    host = 'localhost'
    portbanco = "5432"

    connection_string = f"postgresql://{db_user}:{db_password}@{host}:{portbanco}/{db_name}"
    return create_engine(connection_string)


def conexaoInsercao():
    load_dotenv('/home/grupompl/Wms_Teste/Wms_InternoMPL/ambiente.env')

    db_name = os.getenv('POSTGRE_NAME')
    db_user = os.getenv('POSTGRE_USER')
    db_password = os.getenv('POSTGRE_PASSWORD')
    db_host = 'localhost'

    portbanco = "5432"

    return psycopg2.connect(dbname=db_name, user=db_user, password=db_password, host=db_host, port=portbanco)