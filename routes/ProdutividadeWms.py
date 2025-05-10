
from flask import Blueprint, jsonify, request
from functools import wraps
from models import ProdutividadeWms

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
    try:
        data = request.get_json()

        # Validação básica
        required_fields = ['codEmpresa', 'codUsuarioCargaEndereco', 'endereco', 'qtdPcs', 'codNatureza']
        missing = [field for field in required_fields if field not in data]

        if missing:
            return jsonify({'status': False, 'mensagem': f'Campos ausentes: {", ".join(missing)}'}), 400

        # Instanciando o objeto com os dados recebidos
        produtividade = ProdutividadeWms.ProdutividadeWms(
            codEmpresa=data['codEmpresa'],
            codUsuarioCargaEndereco=data['codUsuarioCargaEndereco'],
            endereco=data['endereco'],
            qtdPcs=data['qtdPcs'],
            codNatureza=data['codNatureza']
        )

        # Executa o método e retorna o resultado
        response = produtividade.inserirProducaoCarregarEndereco()
        return jsonify(response), 200

    except Exception as e:
        return jsonify({'status': False, 'mensagem': f'Erro interno: {str(e)}'}), 500
