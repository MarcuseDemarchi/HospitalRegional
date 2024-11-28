document.addEventListener("DOMContentLoaded", () => {
    loadSectors(); 
    loadQuestions(); 
    loadUsers(); 
    loadChart(); 
    
    document.getElementById("filter-sector").addEventListener("change", () => {
        loadQuestions(); 
        loadChart();
    });

    document.getElementById("question-form").addEventListener("submit", (event) => {
        event.preventDefault();
        addQuestion();
    });

    document.getElementById("user-form").addEventListener("submit", (event) => {
        event.preventDefault();
        addUser();
    });
});

function loadSectors() {
    fetch('../src/Route.php?route=get-sectors')
        .then(response => response.json())
        .then(data => {
            const setorSelects = document.querySelectorAll("#setor, #user-sector, #filter-sector");
            setorSelects.forEach(select => {
                select.innerHTML = ""; 
                data.forEach(setor => {
                    const option = document.createElement("option");
                    option.value = setor.idsetor;
                    option.textContent = setor.nome;
                    select.appendChild(option);
                });
            });
        })
        .catch(error => console.error("Erro ao carregar setores:", error));
}

function loadQuestions() {
    const setor = document.getElementById("filter-sector").value; 
    let url = '../src/Route.php?route=get-questions';
    if (setor) {
        url += `&setor=${setor}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("questions-table").querySelector("tbody");
            tableBody.innerHTML = ""; 
            data.forEach(question => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${question.idquestao}</td>
                    <td>${question.nome_setor}</td>
                    <td>${question.pergunta}</td>
                    <td>${question.statuspergunta ? "Ativo" : "Inativo"}</td>
                    <td>
                        <button onclick="editQuestion(${question.idquestao}, '${question.pergunta}')">Editar</button>
                        <button onclick="deleteQuestion(${question.idquestao})">Excluir</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Erro ao carregar perguntas:", error));
}

function addQuestion() {
    const setor = document.getElementById("setor").value;
    const pergunta = document.getElementById("pergunta").value;

    fetch('../src/Route.php?route=add-question', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ setor, pergunta })
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadQuestions(); 
        })
        .catch(error => console.error("Erro ao adicionar pergunta:", error));
}

function editQuestion(idquestao, perguntaAtual) {
    const novaPergunta = prompt("Edite a pergunta:", perguntaAtual);
    if (novaPergunta === null || novaPergunta === "") return;

    fetch('../src/Route.php?route=edit-question', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idquestao, pergunta: novaPergunta })
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadQuestions(); 
        })
        .catch(error => console.error("Erro ao editar pergunta:", error));
}

function deleteQuestion(idquestao) {
    if (!confirm("Tem certeza que deseja excluir esta pergunta?")) return;

    fetch(`../src/Route.php?route=delete-question&idquestao=${idquestao}`, {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadQuestions(); 
        })
        .catch(error => console.error("Erro ao excluir pergunta:", error));
}

function loadUsers() {
    fetch('../src/Route.php?route=get-users')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("users-table").querySelector("tbody");
            tableBody.innerHTML = ""; 
            data.forEach(user => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${user.iduser}</td>
                    <td>${user.nome}</td>
                    <td>${user.setor}</td>
                    <td>
                        <button onclick="deleteUser(${user.iduser})">Excluir</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Erro ao carregar usuários:", error));
}

function addUser() {
    const nome = document.getElementById("user-name").value;
    const senha = document.getElementById("user-password").value;
    const setor = document.getElementById("user-sector").value;

    fetch('../src/Route.php?route=add-user', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ nome, senha, setor })
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadUsers(); 
        })
        .catch(error => console.error("Erro ao adicionar usuário:", error));
}

function deleteUser(iduser) {
    if (!confirm("Tem certeza que deseja excluir este usuário?")) return;

    fetch(`../src/Route.php?route=delete-user&iduser=${iduser}`, {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            loadUsers();
        })
        .catch(error => console.error("Erro ao excluir usuário:", error));
}

document.getElementById("filter-sector").addEventListener("change", () => {
    const setorSelecionado = document.getElementById("filter-sector").value;
    loadChart(setorSelecionado);
});

let chartInstance = null; 

function loadChart(setor = null) {
    let url = '../src/Route.php?route=get-evaluations';
    if (setor) {
        url += `&setor=${setor}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.nome_setor);
            const values = data.map(item => parseFloat(item.media));

            const ctx = document.getElementById("evaluation-chart").getContext("2d");

            // Verifica se há um gráfico existente e destrói
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Cria um novo gráfico
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Média Geral por Setor',
                        data: values,
                        backgroundColor: '#FF6384',
                        borderColor: '#FFFFFF',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        title: {
                            display: true,
                            text: setor ? `Média de Avaliações (${labels[0]})` : 'Média de Avaliações por Setor',
                        }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Setores' } },
                        y: { title: { display: true, text: 'Média das Notas' }, beginAtZero: true }
                    }
                }
            });
        })
        .catch(error => console.error("Erro ao carregar dados do gráfico:", error));
}
