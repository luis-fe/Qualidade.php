
from flask import Blueprint, jsonify, request
from functools import wraps
from models import Novo_ProdutividadeWms

Produtividade_routes = Blueprint('Produtividade_routes', __name__)


def token_required(f): # TOKEN FIXO PARA ACESSO AO CONTEUDO
    @wraps(f)
    def decorated_function(*args, **kwargs):
        token = request.headers.get('Authorization')
        if token == 'a40016aabcx9':  # Verifica se o token é igual ao token fixo
            return f(*args, **kwargs)
        return jsonify({'message': 'Acesso negado'}), 401

    return decorated_function
@Produtividade_routes.route('/api/InserirProdutividadeCarregarEndereco', methods=['POST'])
@token_required
def post_InserirProdutividadeCarregarEndereco():
        data = request.get_json()

        # Validação básica
        required_fields = ['codEmpresa', 'codUsuarioCargaEndereco', 'endereco', 'qtdPcs', 'codNatureza']
        missing = [field for field in required_fields if field not in data]

        if missing:
            return jsonify({'status': False, 'mensagem': f'Campos ausentes: {", ".join(missing)}'}), 400

        # Instanciando o objeto com os dados recebidos
        produtividade = Novo_ProdutividadeWms.ProdutividadeWms(
            codEmpresa=data['codEmpresa'],
            codUsuarioCargaEndereco=data['codUsuarioCargaEndereco'],
            endereco=data['endereco'],
            qtdPcs=data['qtdPcs'],
            codNatureza=data['codNatureza']
        )

        # Executa o método e retorna o resultado
        response = produtividade.inserirProducaoCarregarEndereco()
        return jsonify(response), 200


@Produtividade_routes.route('/api/ProdCarregarEndereco', methods=['GET'])
@token_required
def get_ProdCarregarEndereco():
    empresa = request.args.get('empresa','1')
    dataInicio = request.args.get('dataInicio')
    dataFinal = request.args.get('dataFinal')

    produtividade = Novo_ProdutividadeWms.ProdutividadeWms(str(empresa), '','','','',dataInicio, dataFinal).consultaProd_CarregarCaixas()
    # Obtém os nomes das colunas
    column_names = produtividade.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    enderecos_data = []
    for index, row in produtividade.iterrows():
        enderecos_dict = {}
        for column_name in column_names:
            enderecos_dict[column_name] = row[column_name]
        enderecos_data.append(enderecos_dict)
    return jsonify(enderecos_data)


@Produtividade_routes.route('/api/produtividade_peloHorario_colaborador', methods=['GET'])
@token_required
def get_produtividade_peloHorario_colaborador():
    empresa = request.args.get('empresa','1')
    dataInicio = request.args.get('dataInicio')
    dataFinal = request.args.get('dataFinal')
    nome = request.args.get('nome')
    faixaTemporal = request.args.get('faixaTemporal',30)

    produtividade = Novo_ProdutividadeWms.ProdutividadeWms(str(empresa), '','','','',dataInicio, dataFinal,'',nome).produtividade_peloHorario_colaborador(str(faixaTemporal))
    # Obtém os nomes das colunas
    column_names = produtividade.columns
    # Monta o dicionário com os cabeçalhos das colunas e os valores correspondentes
    enderecos_data = []
    for index, row in produtividade.iterrows():
        enderecos_dict = {}
        for column_name in column_names:
            enderecos_dict[column_name] = row[column_name]
        enderecos_data.append(enderecos_dict)
    return jsonify(enderecos_data)




