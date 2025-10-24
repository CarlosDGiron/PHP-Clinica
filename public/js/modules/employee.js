import { apiService } from '../services/apiService.js';
import { showAlert } from '../utils/showArlert.js';
import { 
    createButton, 
    assignModalEvent, 
    assignFormSubmitEvent, 
    assignSearchEvent,
    closeModal,
    resetModal
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
            populateSelect('insModPosition', 'posicion');
            populateSelect('insModBranch', 'sucursal');
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
        const dataInfo = JSON.stringify({employee_id: item.id}).replace(/"/g, '&quot;');
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
                <td>${item.id}</td>
                <td>${item.employee_name}</td>
                <td class="${item.position ? '' : 'text-danger'}">${item.position}</td>
                <td class="${item.branch ? '' : 'text-danger'}">${item.branch}</td>
                <td>${item.email}</td>
                <td>${item.phone}</td>
                <td><span class="badge bg-${alertType}">${status}</span></td>
                ${hasActions ? `<td><div class="d-flex">${actionButtons}</div></td>` : ''}
            </tr>
        `;
    });
    
    tableBody.innerHTML = rows;
    
    assignSearchEvent('searchInput', 'tableBody', [0, 1, 2, 3, 4, 5, 6]);

    if (hasActions) {
        assignModalEvent('.btn-update', updateModal);
        assignModalEvent('.btn-delete', deleteModal);
    }

    assignFormSubmitEvent('insertForm', insertFormSubmit);
    assignFormSubmitEvent('updateForm', updateFormSubmit);
    assignFormSubmitEvent('deleteForm', deleteFormSubmit);

    resetModal('insertModal', 'insertForm');
}

const populateSelect = async (selectId, module) =>  {
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
                option.textContent = item.name;
                newSelectElement.appendChild(option);
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

const insertFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/agregar`;

    const formData = () => ({
        first_name: document.getElementById('insModFirstName').value || '',
        last_name: document.getElementById('insModLastName').value || '',
        position_id: Number(document.getElementById('insModPosition').value) || null,
        branch_id: Number(document.getElementById('insModBranch').value) || null,
        email: document.getElementById('insModEmail').value || '',
        phone: document.getElementById('insModPhone').value || '',
        active: Number(document.getElementById('insModStatus').value),
        created_by: currentData.user_id || null,
        updated_by: currentData.user_id || null
    });
    
    try {
        await apiService.fetchData(url, 'POST', formData());
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
    const id = data.employee_id;

    await populateSelect('updModPosition', 'posicion');
    await populateSelect('updModBranch', 'sucursal');

    try {
        const response = await apiService.fetchData(url, 'POST', { id });
        const positionSelect = document.getElementById('updModPosition');
        const branchSelect = document.getElementById('updModBranch');
        const positionOption = Array.from(positionSelect.options).find(option => option.text === response.position);
        const branchOption = Array.from(branchSelect.options).find(option => option.text === response.branch);
        
        document.getElementById('updateForm').setAttribute('data-info', dataInfo);
        document.getElementById('updModEmployeeCode').value = response.id || '';
        document.getElementById('updModFirstName').value = response.first_name || '';
        document.getElementById('updModLastName').value = response.last_name || '';
        document.getElementById('updModPosition').value = positionOption ? positionOption.value : '';
        document.getElementById('updModBranch').value = branchOption ? branchOption.value : '';
        document.getElementById('updModEmail').value = response.email || '';
        document.getElementById('updModPhone').value = response.phone || '';
        document.getElementById('updModStatus').value = response.active.toString() || '';
    } catch (error) {
        console.error('Error:', error);
    }

    resetModal('updateModal', 'updateForm');
};


const updateFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/actualizar`;
    const dataInfo = JSON.parse(document.getElementById('updateForm').getAttribute('data-info'));
    
    const formData = () => ({
        first_name: document.getElementById('updModFirstName').value || '',
        last_name: document.getElementById('updModLastName').value || '',
        position_id: Number(document.getElementById('updModPosition').value) || null,
        branch_id: Number(document.getElementById('updModBranch').value) || null,
        email: document.getElementById('updModEmail').value || '',
        phone: document.getElementById('updModPhone').value || '',
        active: Number(document.getElementById('updModStatus').value),
        updated_by: currentData.user_id || null,
        id: dataInfo.employee_id || null,
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
    const id = data.employee_id;
    
    try {
        const response = await apiService.fetchData(url, 'POST', { id });
        const status = response.active ? 'Activo' : 'Inactivo';
        const name = response.first_name + ' ' + response.last_name;

        document.getElementById('deleteForm').setAttribute('data-info', dataInfo);
        document.getElementById('delModEmployeeCode').innerText = response.id || '';
        document.getElementById('delModName').innerText = name || '';
        document.getElementById('delModPosition').innerText = response.position || '';
        document.getElementById('delModBranch').innerText = response.branch || '';
        document.getElementById('delModEmail').innerText = response.email || '';
        document.getElementById('delModPhone').innerText = response.phone || '';
        document.getElementById('delModStatus').innerText = status || '';
    } catch (error) {
        console.error('Error:', error);
    }
};

const deleteFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/eliminar`;
    const dataInfo = JSON.parse(document.getElementById('deleteForm').getAttribute('data-info'));

    const formData = () => ({
        deleted_by: currentData.user_id || null,
        id: dataInfo.employee_id || null
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