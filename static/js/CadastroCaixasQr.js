const Empresa = localStorage.getItem('CodEmpresa');

const rootElement = document.documentElement;

if (Empresa === "1") {
    rootElement.classList.add('palheta-empresa-a');
} else if (Empresa === "4") {
    rootElement.classList.add('palheta-empresa-b');
} else {
    window.location.href = '/Login_Teste';
}

const Api = 'http://10.162.0.190:5000/api/GerarCaixa';
const Token = 'a40016aabcx9';
const InputQuantidade = document.getElementById('InputQuantidade')


document.getElementById('BotaoImprimir').addEventListener('click', () =>{
    if(InputQuantidade.value === '') {
        alert("O campo de Quantidade não pode ser vazio!")
    } else {
        CadastrarCaixas(Api);
    }
})


async function CadastrarCaixas(Api) {
    dados = {
        "QuantidadeImprimir": InputQuantidade.value
    }
  
    try {
        const response = await fetch(Api, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(dados),
        });

        if (response.ok) {
            const data = await response.json();
            console.log(data)
            alert('Ok')
            
        } else {
            throw new Error('Erro ao obter os dados da API');
        
        }
    } catch (error) {
        console.error(error);
        alert('Procure o Administrador')
    
    }
}

window.addEventListener('load', async () => {
    const NomeUsuario = localStorage.getItem('nomeUsuario');
    const VerificaLogin = localStorage.getItem('Login');
    const linkUsuario = document.querySelector('.right-menu-item a')

    if (VerificaLogin !== "Logado") {
        // Se não houver token, redirecione para a página de login
        window.location.href = '/Login_Teste';
    } else {
        linkUsuario.textContent = NomeUsuario;
    }
});

const linkSair = document.querySelector('.right-menu-item li a[href="/Login_Teste"]');

linkSair.addEventListener("click" , async () => {
  localStorage.clear();
});

