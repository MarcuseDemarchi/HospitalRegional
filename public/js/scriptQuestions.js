const FileRoute = '../src/Route.php';
var iQuestion = 0;
var aNoteQuestions= [];
var aResponseQuestions = [];
var iSetor = getSetor();

function loadComponents(){
    loadButtons();
    loadQuestion();
}

function loadButtons(){
    const btnsQuestions = document.querySelector('.btnsQuestions');

    for (let iIdx = 1;iIdx < 11;iIdx++){
        const button = document.createElement('button');
        button.textContent = iIdx;
        button.classList.add('btnSatisfation');
        button.id = 'btnSatinsfation' + iIdx;
        button.addEventListener("click", function(event){            
            registerNote(parseInt(button.textContent));
        });
        btnsQuestions.appendChild(button);
    };
}

function loadQuestion(){
    const Question = document.getElementById("Question");
    let StringQuestion = getArrayQuestions();
    if (StringQuestion) {
        let arrayQuestions = StringQuestion.split(',');
        Question.textContent = iQuestion+1 + " ." + arrayQuestions[iQuestion];
    } else {
        console.error("Nenhuma pergunta disponível no localStorage");
    }
}

function getArrayQuestions(){
    return localStorage.getItem("StringQuestion");
}

function incQuestion(){
    if (validNote()){
        const inputResponse = document.getElementById('reponseTxt');
        const ButtonProx = document.getElementById("prox");    
        aResponseQuestions[iQuestion] = inputResponse.value;    
        inputResponse.value = '';
            
        if (iQuestion == 9){
            ButtonProx.textContent = 'Enviar';
            registerReponseDataBase();
        }
        else if (iQuestion < 9){
            ++iQuestion;
        }
        loadQuestion();
    }
    else{
        alert("Insira uma nota!")
    }
};

function decQuestion(){
    const ButtonProx = document.getElementById("prox");
    if (ButtonProx.textContent == 'Enviar'){
        ButtonProx.textContent = 'Proximo';
    }

    if (iQuestion > 0){
        --iQuestion;
        ajustNote();
    }
    loadQuestion();
}

function registerNote(iNote){
    aNoteQuestions[iQuestion] = iNote;
}

function registerReponseDataBase(){
    fetch(FileRoute + '?route=insert-response', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            setor: 1,
            notas: aNoteQuestions,
            feedback: aResponseQuestions,
        }),
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Erro ao inserir dados no servidor');
        }
        return response.json();
    })
    .then((data) => {
        alert(data.message || 'Dados inseridos com sucesso!');
    })
    .catch((error) => {
        console.error('Erro ao enviar dados:', error);
    });
    loadSucess();
}

function loadSucess(){
    window.location.href = "../public/Sucess.html";   
}

function validNote(){
    if (!aNoteQuestions[iQuestion]){
        return false;
    }
    else{
        return true;
    }
}

function getSetor(){
   return localStorage.getItem("SessionSetor");
}

function ajustNote(){
    let idBtn = 'btnSatinsfation1';
    const button = document.getElementById(idBtn);
    button.click();
}