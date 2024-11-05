from models import ReposicaoViaOFF
from flask import Blueprint, jsonify, request
from functools import wraps

ReposicaoViaOFF_routes = Blueprint('ReposicaoViaOFF_routes', __name__)


def token_required(f):
    # TOKEN FIXO PARA ACESSO AO CONTEUDO
    @wraps(f)
    def decorated_function(*args, **kwargs):
        token = request.headers.get('Authorization')
        if token == 'a40016aabcx9':  # Verifica se o token é igual ao token fixo
            return f(*args, **kwargs)
        return jsonify({'message': 'Acesso negado'}), 401

    return decorated_function

@ReposicaoViaOFF_routes.route('/api/consultaTagOFFWMS', methods=['GET'])
@token_required
def get_consultaTagOFFWMS():
    # Obtém os dados do corpo da requisição (JSON)

    codbarrastag = request.args.get('codbarrastag')
    empresa = request.args.get('empresa','1')

    consulta = ReposicaoViaOFF.ReposicaoViaOFF(codbarrastag,'',empresa).consultaTagOFFWMS()
    # Obtém os nomes das colunas
    column_names = consulta.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    pedidos_data = []
    for index, row in consulta.iterrows():
        pedidos_dict = {}
        for column_name in column_names:
            pedidos_dict[column_name] = row[column_name]
        pedidos_data.append(pedidos_dict)
    return jsonify(pedidos_data)
