from Service import FaturamentoCswModel
from flask import Blueprint, jsonify, request
from functools import wraps
import ConexaoCSW

faturamento_routes = Blueprint('faturamento', __name__)

def token_required(f): # TOKEN FIXO PARA ACESSO AO CONTEUDO
    @wraps(f)
    def decorated_function(*args, **kwargs):
        token = request.headers.get('Authorization')
        if token == 'a40016aabcx9':  # Verifica se o token é igual ao token fixo
            return f(*args, **kwargs)
        return jsonify({'message': 'Acesso negado'}), 401

    return decorated_function

@faturamento_routes.route('/api/Faturamento', methods=['GET'])
@token_required
def get_Faturamento():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    empresa = request.args.get('empresa','1')
    dataInicio = request.args.get('dataInicio')
    dataFim = request.args.get('dataFim')
    detalhar = request.args.get('detalhar', False)
    mensagem = request.args.get('mensagem', 'ConexaoPerdida')
    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = FaturamentoCswModel.Faturamento(empresa, dataInicio, dataFim, detalhar, mensagem)



    # Obtém os nomes das colunas
    column_names = TagReposicao.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    pedidos_data = []
    for index, row in TagReposicao.iterrows():
        pedidos_dict = {}
        for column_name in column_names:
            pedidos_dict[column_name] = row[column_name]
        pedidos_data.append(pedidos_dict)
    return jsonify(pedidos_data)

@faturamento_routes.route('/api/ObterNotasCsw', methods=['GET'])
@token_required
def ObterNotasCsw():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    empresa = request.args.get('empresa', '1')
    TagReposicao = ConexaoCSW.obter_notaCsw()


    # Obtém os nomes das colunas
    column_names = TagReposicao.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    pedidos_data = []
    for index, row in TagReposicao.iterrows():
        pedidos_dict = {}
        for column_name in column_names:
            pedidos_dict[column_name] = row[column_name]
        pedidos_data.append(pedidos_dict)
    return jsonify(pedidos_data)

@faturamento_routes.route('/api/TesteConexao', methods=['GET'])
@token_required
def TesteConexao():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    empresa = request.args.get('empresa', '1')
    TagReposicao = ConexaoCSW.VerificarConexao()


    # Obtém os nomes das colunas
    column_names = TagReposicao.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    pedidos_data = []
    for index, row in TagReposicao.iterrows():
        pedidos_dict = {}
        for column_name in column_names:
            pedidos_dict[column_name] = row[column_name]
        pedidos_data.append(pedidos_dict)
    return jsonify(pedidos_data)
@faturamento_routes.route('/api/testeFaturamento', methods=['GET'])
@token_required
def testeFaturamento():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    empresa = request.args.get('empresa','1')
    dataInicio = request.args.get('dataInicio')
    dataFim = request.args.get('dataFim')
    detalhar = request.args.get('detalhar', False)
    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = FaturamentoCswModel.teste(empresa,dataInicio,dataFim)


    # Obtém os nomes das colunas
    column_names = TagReposicao.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    pedidos_data = []
    for index, row in TagReposicao.iterrows():
        pedidos_dict = {}
        for column_name in column_names:
            pedidos_dict[column_name] = row[column_name]
        pedidos_data.append(pedidos_dict)
    return jsonify(pedidos_data)
