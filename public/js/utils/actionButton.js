import { apiService } from '../services/apiService.js';
import { showAlert } from './showArlert.js';

export function createButton(btnClass, title, dataInfo, targetModal, iconClass) {
    return `
        <button
            type="button"
            class="btn btn-sm ${btnClass} me-1"
            title="${title}"
            data-info="${dataInfo}"
            data-bs-toggle="modal"
            data-bs-target="#${targetModal}">
            <i class="bi ${iconClass}"></i>
        </button>
    `;
}

export async function assignModalEvent(selector, handler) {
    document.querySelectorAll(selector).forEach(button => {
        const newButton = button.outerHTML;
        button.outerHTML = newButton;
    });

    document.querySelectorAll(selector).forEach(newButtonElement => {
        newButtonElement.addEventListener('click', async () => {
            const url = `${urlBase}/login/token`;
            
            try {
                await apiService.fetchData(url, 'GET');
                const dataInfo = JSON.parse(newButtonElement.getAttribute('data-info'));
                handler(dataInfo);
            } catch (error) {
                if (error.message.includes('401')) {
                    localStorage.setItem('tokenExpired', 'true');
                    window.location.href = urlBase;
                } else {
                    showAlert('Error de conexión.', 'danger');
                }
            }
        });
    });
}


export async function assignFormSubmitEvent(id, handler) {
    const form = document.getElementById(id);

    if (form) {
        // Reemplaza el contenido del formulario por su HTML para remover cualquier evento anterior
        const newForm = form.outerHTML;
        form.outerHTML = newForm;

        // Obtener de nuevo el nuevo formulario
        const newFormElement = document.getElementById(id);

        // Asignar el nuevo evento de submit
        newFormElement.addEventListener('submit', async (event) => {
            event.preventDefault();
            const url = `${urlBase}/login/token`;

            try {
                await apiService.fetchData(url, 'GET');
                handler();
            } catch (error) {
                if (error.message.includes('401')) {
                    localStorage.setItem('tokenExpired', 'true');
                    window.location.href = urlBase;
                } else {
                    showAlert('Error de conexión.', 'danger');
                }
            }
        });
    }
}

export async function assignSearchEvent(inputSelector, tableSelector, columns) {
    const searchInput = document.getElementById(inputSelector);
    const tableBody = document.getElementById(tableSelector);

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', async () => {
            const input = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let shouldDisplay = false;

                for (let col of columns) {
                    const cell = rows[i].getElementsByTagName('td')[col];
                    if (cell && cell.textContent.toLowerCase().includes(input)) {
                        shouldDisplay = true;
                        break;
                    }
                }

                rows[i].style.display = shouldDisplay ? '' : 'none';
            }
        });
    }
}

export const closeModal = (modalId) => {
    const modalElement = document.getElementById(modalId);
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
};

export const resetModal = (modalId, formId) => {
    const modalElement = document.getElementById(modalId);
    const formElement = document.getElementById(formId);
    modalElement.addEventListener('hidden.bs.modal', () => {
        formElement.reset();
        formElement.querySelectorAll('.input-error').forEach(input => {
            input.classList.remove('input-error');
        });
    });
};

export function togglePassword(toggleSelector, passwordInputId) {
    const toggleButton = document.getElementById(toggleSelector);

    toggleButton.addEventListener('click', function () {
        const passwordInput = document.getElementById(passwordInputId);
        const icon = toggleButton.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
}