// modalSuccess.js

/**
 * Carrega o modal de sucesso dinamicamente no DOM.
 */
function loadSuccessModal() {
    fetch('successModal.html')
        .then(response => response.text())
        .then(html => {
            const container = document.createElement('div');
            container.innerHTML = html;
            document.body.appendChild(container);
        })
        .catch(error => console.error('Erro ao carregar o modal de sucesso:', error));
}

/**
 * Exibe o modal de sucesso e inicia o contador de redirecionamento.
 */
function showSuccessModal() {
    const modal = document.getElementById("success-modal");
    if (!modal) {
        console.error("Modal de sucesso não foi carregado.");
        return;
    }

    modal.classList.remove("hidden");

    // Inicia o contador de 10 segundos
    let countdownValue = 10;
    const countdownElement = document.getElementById("countdown");
    countdownElement.textContent = countdownValue;

    const countdownInterval = setInterval(() => {
        countdownValue--;
        countdownElement.textContent = countdownValue;

        if (countdownValue <= 0) {
            clearInterval(countdownInterval); // Para o contador
            redirectToHome(); // Redireciona para a tela inicial
        }
    }, 1000); // Atualiza a cada segundo
}

/**
 * Fecha o modal de sucesso e redireciona para a tela inicial.
 */
function closeModal() {
    const modal = document.getElementById("success-modal");
    modal.classList.add("hidden");

    redirectToHome(); // Redireciona imediatamente ao fechar o modal
}

/**
 * Redireciona o usuário para a tela inicial.
 */
function redirectToHome() {
    window.location.href = "Index.html"; // Substitua pelo caminho correto para a tela inicial
}
