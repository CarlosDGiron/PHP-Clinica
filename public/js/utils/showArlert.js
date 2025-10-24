export function showAlert(message, alertType) {
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const closeButton = document.getElementById('closeButton');

    // Restablecer la alerta si fue cerrada manualmente
    errorMessage.classList.remove('fade', 'slide-out');
    errorMessage.classList.add('show', 'alert', `alert-${alertType}`, 'slide-in');
    errorText.textContent = message;

    setTimeout(function() {
        errorMessage.classList.remove('slide-in');
        errorMessage.classList.add('fade', 'slide-out');
        
        setTimeout(() => {
            errorMessage.classList.remove(`alert-${alertType}`);
        }, 1000);
    }, 4500);

    closeButton.addEventListener('click', function() {
        errorMessage.classList.remove('show', 'slide-in');
        errorMessage.classList.add('fade', 'slide-out');

        setTimeout(() => {
            errorMessage.classList.remove(`alert-${alertType}`);
        }, 500);
    });
}