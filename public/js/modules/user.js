import { apiService } from '../services/apiService.js';
import { isValidPassword, arePasswordsMatching, isEmpty } from '../utils/validation.js';
import { showAlert } from '../utils/showArlert.js';
import {
    createButton, 
    assignModalEvent, 
    assignFormSubmitEvent, 
    assignSearchEvent,
    closeModal,
    resetModal,
    togglePassword
} from '../utils/actionButton.js';

let currentData;
let currentModule;

export async function initModule(data, module) {
    currentData = data;
    currentModule = module;
    const url = `${urlBase}/${currentModule}/mostrar`;
    const response = await apiService.fetchData(url, 'GET');
    const tableBody = document.getElementById('tableBody');
    const tableHead = document.getElementById('tableHead');
    const addButton = document.getElementById('addButton');
    const moduleData = currentData.modules.find(moduleData => moduleData.link === currentModule);

    const canCreate = moduleData.create_operation === 1;
    const canUpdate = moduleData.update_operation === 1;
    const canDelete = moduleData.delete_operation === 1;
    const hasActions = canUpdate || canDelete;
    let rows = '';

    if (canCreate) {
        addButton.innerHTML = `
            <div class="rounded ps-4">
                <button type="button" class="btn btn-primary fw-bold btn-insert" data-bs-toggle="modal" data-bs-target="#insertModal">Agregar</button>
            </div>
        `;

        const insertModal = document.getElementById('insertModal');
        insertModal.addEventListener('show.bs.modal', () => {
            populateSelectRole('insModRole', 'rol');
            populateSelectEmployee('insModEmployee', 'empleado');
            togglePassword('insModTogglePassword', 'insModPassword');
            togglePassword('insModToggleConfirmPassword', 'insModConfirmPassword');
        });

    } else {
        addButton.innerHTML = ''; // Si no hay permisos, limpiar el contenedor
    }

    if (hasActions) {
        if (!document.querySelector('th.action-column')) {
            const actionHeader = document.createElement('th');
            actionHeader.scope = 'col';
            actionHeader.textContent = 'Acción';
            actionHeader.classList.add('action-column');
            tableHead.querySelector('tr').appendChild(actionHeader);
        }
    }

    response.forEach(item => {
        const dataInfo = JSON.stringify({user_id: item.id}).replace(/"/g, '&quot;');
        const status = item.active ? 'Activo' : 'Inactivo';
        const alertType = item.active ? 'success' : 'danger';

        let actionButtons = '';
        
        // Crear los botones de acuerdo a los permisos
        if (canUpdate) {
            actionButtons += createButton('btn-primary btn-update', 'Editar', dataInfo, 'updateModal', 'bi bi-pencil');
        }
        if (canDelete) {
            actionButtons += createButton('btn-danger btn-delete', 'Borrar', dataInfo, 'deleteModal', 'bi bi-trash-fill');
        }

        rows += `
            <tr>
                <td>${item.user_name}</td>
                <td>${item.username}</td>
                <td class="${item.employee_name ? '' : 'text-danger'}">${item.employee_name}</td>
                <td class="${item.role_name ? '' : 'text-danger'}">${item.role_name}</td>
                <td><span class="badge bg-${alertType}">${status}</span></td>
                ${hasActions ? `<td><div class="d-flex">${actionButtons}</div></td>` : ''}
            </tr>
        `;
    });
    
    tableBody.innerHTML = rows;
    
    assignSearchEvent('searchInput', 'tableBody', [0, 1, 2, 3, 4]);

    if (hasActions) {
        assignModalEvent('.btn-update', updateModal);
        assignModalEvent('.btn-delete', deleteModal);
    }

    assignFormSubmitEvent('insertForm', insertFormSubmit);
    assignFormSubmitEvent('updateForm', updateFormSubmit);
    assignFormSubmitEvent('deleteForm', deleteFormSubmit);

    resetModal('insertModal', 'insertForm');
}

const populateSelectRole = async (selectId, module) =>  {
    const select = document.getElementById(selectId);
    const newSelect = select.outerHTML;
    select.outerHTML = newSelect;

    const newSelectElement = document.getElementById(selectId);
    newSelectElement.innerHTML = '';

    try {
        const options = await apiService.fetchData(`${urlBase}/${module}/mostrar`, 'GET');
        options.forEach(item => {
            if (item.active === 1) {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.role;
                newSelectElement.appendChild(option);
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

const populateSelectEmployee = async (selectId, module) =>  {
    const select = document.getElementById(selectId);
    const newSelect = select.outerHTML;
    select.outerHTML = newSelect;

    const newSelectElement = document.getElementById(selectId);
    newSelectElement.innerHTML = '';
    
    try {
        const options = await apiService.fetchData(`${urlBase}/${module}/mostrar`, 'GET');
        newSelectElement.appendChild(new Option('Ninguno', ''));
        options.forEach(item => {
            if (item.active === 1) {
                const option = document.createElement('option');
                const name = item.id + ' - ' + item.employee_name;
                option.value = item.id;
                option.textContent = name;
                newSelectElement.appendChild(option);
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

const insertFormSubmit = async () => {
    const urlInsert = `${urlBase}/${currentModule}/agregar`;
    const urlSearch = `${urlBase}/${currentModule}/buscar`;
    const password = document.getElementById('insModPassword');
    const confirmPassword = document.getElementById('insModConfirmPassword');
    const username =  document.getElementById('insModUsername');
  
    username.addEventListener('input', function() {
        this.classList.remove('input-error');
    });

    password.addEventListener('input', function() {
        this.classList.remove('input-error');
    });

    confirmPassword.addEventListener('input', function() {
        this.classList.remove('input-error');
    });


    try {
        const response = await apiService.fetchData(urlSearch, 'POST', {username: username.value });
        if (response.username === username.value) {
            showAlert('El usuario ya se encuentra registrado.', 'danger');
            username.classList.add('input-error');
            return;
        }
    } catch (error) {
        console.error('Error:', error);
    }

    if (!isValidPassword(password.value)) {
        showAlert('La contraseña debe tener al menos 8 caracteres, incluyendo una letra y un número.', 'danger');
        password.classList.add('input-error');
        return;
    }

    if (!arePasswordsMatching(password.value, confirmPassword.value)) {
        showAlert('Las contraseñas no coinciden.', 'danger');
        password.classList.add('input-error');
        confirmPassword.classList.add('input-error');
        return;
    }
    
    const formData = () => ({
        first_name: document.getElementById('insModFirstName').value || '',
        last_name: document.getElementById('insModLastName').value || '',
        username: username.value || '',
        employee_id: Number(document.getElementById('insModEmployee').value) || null,
        role_id: Number(document.getElementById('insModRole').value) || null,
        active: Number(document.getElementById('insModStatus').value),
        password: password.value || null,
        user_id: currentData.user_id || null
    });
    
    try {
        await apiService.fetchData(urlInsert, 'POST', formData());
        showAlert("Operación exitosa.", 'success');
        closeModal('insertModal');
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }

    await initModule(currentData, currentModule);
};

const updateModal = async (data) => {
    const url = `${urlBase}/${currentModule}/filtrar`;
    const dataInfo = JSON.stringify(data);
    const id = data.user_id;

    await populateSelectRole('updModRole', 'rol');
    await populateSelectEmployee('updModEmployee', 'empleado');
    togglePassword('updModTogglePassword', 'updModPassword');

    try {
        const response = await apiService.fetchData(url, 'POST', { id });
        const roleSelect = document.getElementById('updModRole');
        const employeeSelect = document.getElementById('updModEmployee');
        const roleOption = Array.from(roleSelect.options).find(option => option.text === response.role_name);
        const employeeOption = Array.from(employeeSelect.options).find(option => option.text === response.employee_name);
        
        document.getElementById('updateForm').setAttribute('data-info', dataInfo);
        document.getElementById('updModFirstName').value = response.first_name || '';
        document.getElementById('updModLastName').value = response.last_name || '';
        document.getElementById('updModEmployee').value = employeeOption ? employeeOption.value : '';
        document.getElementById('updModRole').value = roleOption ? roleOption.value : '';
        document.getElementById('updModStatus').value = response.active.toString() || '';
    } catch (error) {
        console.error('Error:', error);
    }

    resetModal('updateModal', 'updateForm');
};

const updateFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/actualizar`;
    const dataInfo = JSON.parse(document.getElementById('updateForm').getAttribute('data-info'));
    const password = document.getElementById('updModPassword');

    password.addEventListener('input', function() {
        this.classList.remove('input-error');
    });

    if (!isValidPassword(password.value) && !isEmpty(password.value)) {
        showAlert('La contraseña debe tener al menos 8 caracteres, incluyendo una letra y un número.', 'danger');
        password.classList.add('input-error');
        return;
    }

    const formData = () => ({
        role_id: Number(document.getElementById('updModRole').value) || null,
        first_name: document.getElementById('updModFirstName').value || '',
        last_name: document.getElementById('updModLastName').value || '',
        employee_id: Number(document.getElementById('updModEmployee').value) || null,
        password: password.value || null,
        active: Number(document.getElementById('updModStatus').value),
        user_id: currentData.user_id || null,
        id: dataInfo.user_id || null
    });
    
    try {
        await apiService.fetchData(url, 'POST', formData());
        showAlert("Operación exitosa.", 'success');
        closeModal('updateModal');
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }

    await initModule(currentData, currentModule);
};

const deleteModal = async (data) => {
    const url = `${urlBase}/${currentModule}/filtrar`;
    const dataInfo = JSON.stringify(data);
    const id = data.user_id;
    
    try {
        const response = await apiService.fetchData(url, 'POST', { id });
        const status = response.active ? 'Activo' : 'Inactivo';
        const name = response.first_name + ' ' + response.last_name;

        document.getElementById('deleteForm').setAttribute('data-info', dataInfo);
        document.getElementById('delModName').innerText = name || '';
        document.getElementById('delModUsername').innerText = response.username || '';
        document.getElementById('delModEmployee').innerText = response.employee_name || '';
        document.getElementById('delModRole').innerText = response.role_name || '';
        document.getElementById('delModStatus').innerText = status || '';
    } catch (error) {
        console.error('Error:', error);
    }
};

const deleteFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/eliminar`;
    const dataInfo = JSON.parse(document.getElementById('deleteForm').getAttribute('data-info'));

    const formData = () => ({
        user_id: currentData.user_id || null,
        id: dataInfo.user_id || null
    });

    try {
        await apiService.fetchData(url, 'POST', formData());
        showAlert("Operación exitosa.", 'success');
        closeModal('deleteModal');
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }

    await initModule(currentData, currentModule);
};