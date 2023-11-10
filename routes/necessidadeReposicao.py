from Service import necessidadeReposicaoModel
from flask import Blueprint, jsonify, request
from functools import wraps
import pandas as pd

necessidadeRepos_routes = Blueprint('necessidadeRepos', __name__)
def token_required(f): # TOKEN FIXO PARA ACESSO AO CONTEUDO
    @wraps(f)
    def decorated_function(*args, **kwargs):
        token = request.headers.get('Authorization')
        if token == 'a40016aabcx9':  # Verifica se o token é igual ao token fixo
            return f(*args, **kwargs)
        return jsonify({'message': 'Acesso negado'}), 401

    return decorated_function

@necessidadeRepos_routes.route('/api/NecessidadeReposicao', methods=['GET'])
@token_required
def get_RelatorioNecessidadeReposicao():
    # Obtém os dados do corpo da requisição (JSON)

    Endereco_det = necessidadeReposicaoModel.RelatorioNecessidadeReposicao()
    Endereco_det = pd.DataFrame(Endereco_det)
    # Obtém os nomes das colunas
    column_names = Endereco_det.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    end_data = []
    for index, row in Endereco_det.iterrows():
        end_dict = {}
        for column_name in column_names:
            end_dict[column_name] = row[column_name]
        end_data.append(end_dict)
    return jsonify(end_data)
@necessidadeRepos_routes.route('/api/NecessidadeReposicaoDisponivel', methods=['GET'])
@token_required
def NecessidadeReposicaoDisponivel():
    # Obtém os dados do corpo da requisição (JSON)

    natureza = request.args.get('natureza', '5')
    empresa = request.args.get('empresa', '1')

    Endereco_det = necessidadeReposicaoModel.RelatorioNecessidadeReposicaoDisponivel(empresa, natureza)
    Endereco_det = pd.DataFrame(Endereco_det)
    # Obtém os nomes das colunas
    column_names = Endereco_det.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    end_data = []
    for index, row in Endereco_det.iterrows():
        end_dict = {}
        for column_name in column_names:
            end_dict[column_name] = row[column_name]
        end_data.append(end_dict)
    return jsonify(end_data)

@necessidadeRepos_routes.route('/api/RedistribuirPedido', methods=['GET'])
@token_required
def RedistribuirPedido():
    # Obtém os dados do corpo da requisição (JSON)

    pedido = request.args.get('pedido')
    produto = request.args.get('produto')
    natureza = request.args.get('natureza')

    Endereco_det = necessidadeReposicaoModel.Redistribuir(pedido,produto,natureza)
    # Obtém os nomes das colunas
    column_names = Endereco_det.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    pedidos_data = []
    for index, row in Endereco_det.iterrows():
        pedidos_dict = {}
        for column_name in column_names:
            pedidos_dict[column_name] = row[column_name]
        pedidos_data.append(pedidos_dict)
    return jsonify(pedidos_data)