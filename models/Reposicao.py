

class Reposicao():
    '''classe criado para a entidade reposicao, utilizada no processo de Reposicao de Pçs na prateleira no WMS'''

    def __init__(self, codbarrastag = None, endereco = None, empresa = None, usuario = None, natureza = None, Ncaixa = None ):
        '''Contrutor da classe'''

        self.codbarrastag = codbarrastag
        self.endereco = endereco
        self.empresa = empresa
        self.usuario = usuario
        self.natureza = natureza
        self.Ncaixa = Ncaixa


    def avalicaoOcupacaoEndereco(self):
        '''Metodo utilizado para avaliar '''