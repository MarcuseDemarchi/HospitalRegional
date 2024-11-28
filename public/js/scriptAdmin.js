document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("login-form");

    if (!form) {
        console.error("O formulário com o ID 'login-form' não foi encontrado.");
        return;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const inputUsuario = form.querySelector("input[name='usuario']");
        const inputSenha = form.querySelector("input[name='senha']");

        if (!inputUsuario || !inputSenha) {
            console.error("Os campos de entrada 'usuario' ou 'senha' não foram encontrados.");
            return;
        }

        const usuario = inputUsuario.value.trim();
        const senha = inputSenha.value.trim();

        if (usuario === "" || senha === "") {
            alert("Por favor, preencha ambos os campos.");
            return;
        }

        requestWebServer(usuario, senha);
    });
});


function requestWebServer(usuario, senha) {
    fetch('../src/Route.php?route=authenticate', { 
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            usuario: usuario,
            senha: senha
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log("Resposta bruta:", data); 
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.success) {
                alert("Login bem-sucedido!");
                window.location.href = "../public/DashBoardAdm.html";
            } else {
                alert(jsonData.message);
            }
        } catch (error) {
            console.error("Erro ao analisar JSON:", error);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
    });
}

