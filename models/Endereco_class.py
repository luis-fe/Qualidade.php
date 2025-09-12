import ConexaoPostgreMPL
import pandas as pd

class Enderecos():
    '''Classe que registra os item substitutos , que tem restricoes na armazenagem do WMS e na separacao de Pedidos'''

    def __init__(self, codEmpresa = None, codNatureza = None):

        self.codEmpresa = codEmpresa
        self.codNatureza = codNatureza


    def get_informacoes_enderecos(self):
        '''Metodo que levanta a informacoes dos enderecos '''

        conn = ConexaoPostgreMPL.conexaoEngine()

        sql_consultar_enderecos = f"""
            select
	            codempresa,
	            codendereco as "codEndereco",
	            natureza as "codNatureza",
	            substring(codendereco,1,2) as rua
            from
	            "Reposicao"."Reposicao".cadendereco c
            where 
               codempresa = '{str(self.codEmpresa)}' 
            order by 
                substring(codendereco,1,2)::int 
        """

        consulta = pd.read_sql(sql_consultar_enderecos,conn)
        total_enderecos = consulta['codendereco'].count()
        ruas =  consulta.groupby('rua').agg({'rua':'first'}).reset_index()

        data = {

            '1- total_enderecos ': f'{total_enderecos}',
            '2.1- Rua ': ruas.to_dict(orient='records'),
            '3- Detalhamento ': consulta.to_dict(orient='records')
        }
        return pd.DataFrame([data])

