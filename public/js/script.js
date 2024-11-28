const FileRoute = '../src/Route.php';
var itemMenu = document.querySelectorAll('.itemMenu');
var iSetor;
var sResquest;

function selectLinkColor(){
    itemMenu.forEach((item) => 
            item.classList.remove('ativo'))
    this.classList.add('ativo')
}

itemMenu.forEach((item) =>
    item.addEventListener('click',selectLinkColor));

function loadArrayQuestions(iValue) { 
    requestWebServer(FileRoute+'?route=consult-questions&setor='+iValue)
        .then(() => {
            window.location.href = "Questions.html";
        });
}

function requestWebServer(URL) {
    return fetch(URL, { 
        method: 'GET',
        headers: { 
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na resposta do servidor');
        }
        return response.json();
    })
    .then(data => {
        setArrayQuestions(data);
    })
    .catch(error => {
        alert('Erro na requisição: ' + error.message);
    });
}

function setArrayQuestions(aValue){
    localStorage.setItem("StringQuestion",aValue);
}

function loadInterfaceAdmin(){
    window.location.href = "Admin.html";
}