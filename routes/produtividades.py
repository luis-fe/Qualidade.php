import models.Dashboards.Produtividades
from models import produtividadeModel, Novo_ProdutividadeWms
from flask import Blueprint, jsonify, request
from functools import wraps
import pandas as pd

from models.configuracoes import empresaConfigurada

produtividade_routes = Blueprint('produtividade', __name__)

def token_required(f): # TOKEN FIXO PARA ACESSO AO CONTEUDO
    @wraps(f)
    def decorated_function(*args, **kwargs):
        token = request.headers.get('Authorization')
        if token == 'a40016aabcx9':  # Verifica se o token é igual ao token fixo
            return f(*args, **kwargs)
        return jsonify({'message': 'Acesso negado'}), 401

    return decorated_function

@produtividade_routes.route('/api/TagsReposicao/Resumo', methods=['GET'])
@token_required
def get_TagsReposicao():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    data_inicial = request.args.get('DataInicial','0')
    data_final = request.args.get('DataFinal','0')
    horarioInicial = request.args.get('horarioInicial', '01:00:00')
    horarioFinal = request.args.get('horarioFinal', '23:59:00')
    #Relatorios.RelatorioSeparadoresLimite(10)
    codEmpresa = empresaConfigurada.EmpresaEscolhida()

    if codEmpresa == '1':

        consulta = Novo_ProdutividadeWms.ProdutividadeWms(codEmpresa,'','','','',data_inicial, data_final).consultaConsultaProdutividadeRepositorTagCaixa()

    else:
        consulta = produtividadeModel.ProdutividadeRepositores(data_inicial, data_final, horarioInicial,
                                                                   horarioFinal)
        consulta = pd.DataFrame(consulta)

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

@produtividade_routes.route('/api/TagsSeparacao/Resumo', methods=['GET'])
@token_required
def get_TagsSeparacao():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    data_inicial = request.args.get('DataInicial','0')
    data_final = request.args.get('DataFinal','0'),
    horarioInicial = request.args.get('horarioInicial', '01:00:00')
    horarioFinal = request.args.get('horarioFinal', '23:59:00')
    codEmpresa = empresaConfigurada.EmpresaEscolhida()



    if codEmpresa == '1':
        consulta = Novo_ProdutividadeWms.ProdutividadeWms(codEmpresa,'','','','',data_inicial, data_final)#.consultaSeparacaoDiariaPorUsuario()


    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = produtividadeModel.ProdutividadeSeparadores(data_inicial,data_final, horarioInicial, horarioFinal)
    TagReposicao = pd.DataFrame(TagReposicao)

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
@produtividade_routes.route('/api/DetalhaRitmoRepositor', methods=['GET'])
@token_required
def DetalhaRitmoRepositor():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    data_inicial = request.args.get('DataInicial','0')
    data_final = request.args.get('DataFinal','0'),
    horarioInicial = request.args.get('horarioInicial', '01:00:00')
    horarioFinal = request.args.get('horarioFinal', '23:59:00')
    usuario = request.args.get('usuario','-')

    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = produtividadeModel.DetalhaRitmoRepositor(usuario,data_inicial,data_final)
    TagReposicao = pd.DataFrame(TagReposicao)

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

@produtividade_routes.route('/api/RelatorioSeparacao', methods=['GET'])
@token_required
def RelatorioSeparacao():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    data_inicial = request.args.get('DataInicial','0')
    data_final = request.args.get('DataFinal','0')
    usuario = request.args.get('usuario','')

    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = produtividadeModel.RelatorioSeparacao('1',data_inicial,data_final,usuario)
    TagReposicao = pd.DataFrame(TagReposicao)

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


@produtividade_routes.route('/api/ProdutividadeGarantiaEquipe', methods=['GET'])
@token_required
def get_ProdutividadeGarantiaEquipe():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    data_inicial = request.args.get('DataInicial','0')
    data_final = request.args.get('DataFinal','0')
    horarioInicial = request.args.get('horarioInicial', '01:00:00')
    horarioFinal = request.args.get('horarioFinal', '23:59:00')
    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = models.Dashboards.Produtividades.ProdutividadeGarantiaEquipe(data_inicial, data_final, horarioInicial, horarioFinal)
    TagReposicao = pd.DataFrame(TagReposicao)


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

@produtividade_routes.route('/api/ProdutividadeGarantiaIndividual', methods=['GET'])
@token_required
def ProdutividadeGarantiaIndividual():
    # Obtém os valores dos parâmetros DataInicial e DataFinal, se estiverem presentes na requisição
    data_inicial = request.args.get('DataInicial','0')
    data_final = request.args.get('DataFinal','0')
    horarioInicial = request.args.get('horarioInicial', '01:00:00')
    horarioFinal = request.args.get('horarioFinal', '23:59:00')
    #Relatorios.RelatorioSeparadoresLimite(10)
    TagReposicao = models.Dashboards.Produtividades.ProdutividadeGarantiaIndividual(data_inicial, data_final, horarioInicial, horarioFinal)
    TagReposicao = pd.DataFrame(TagReposicao)


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