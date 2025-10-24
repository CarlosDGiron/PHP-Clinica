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

    const canShow = moduleData.show_operation === 1;
    const canCreate = moduleData.create_operation === 1;
    const canUpdate = moduleData.update_operation === 1;
    const canDelete = moduleData.delete_operation === 1;
    const hasActions = canShow || canUpdate || canDelete;
    let rows = '';

    if (canCreate) {
        addButton.innerHTML = `
            <div class="rounded ps-4">
                <button type="button" class="btn btn-primary fw-bold btn-insert" data-bs-toggle="modal" data-bs-target="#insertModal">Agregar</button>
            </div>
        `;
    } else {
        addButton.innerHTML = ''; // Si no hay permisos, limpiar el contenedor
    }

    if (hasActions) {
        // Verificar si ya existe la columna "Acción" para evitar duplicados
        if (!document.querySelector('th.action-column')) {
            const actionHeader = document.createElement('th');
            actionHeader.scope = 'col';
            actionHeader.textContent = 'Acción';
            actionHeader.classList.add('action-column');
            tableHead.querySelector('tr').appendChild(actionHeader);
        }
    }

    response.forEach(item => {
        const dataInfo = JSON.stringify({medicine_id: item.id, medicine: item.name}).replace(/"/g, '&quot;');
        const status = item.active ? 'Activo' : 'Inactivo';
        const alertType = item.active ? 'success' : 'danger';
        
        let actionButtons = '';
        
        // Crear los botones de acuerdo a los permisos
        if (canShow) {
            actionButtons += createButton('btn-info btn-show', 'Inventario', dataInfo, 'showModal', 'bi bi-clipboard-data');
        }
        if (canUpdate) {
            actionButtons += createButton('btn-primary btn-update', 'Editar', dataInfo, 'updateModal', 'bi bi-pencil');
        }
        if (canDelete) {
            actionButtons += createButton('btn-danger btn-delete', 'Borrar', dataInfo, 'deleteModal', 'bi bi-trash-fill');
        }

        // Si existen acciones permitidas, agregamos el <td> de acciones
        rows += `
            <tr>
                <td>${item.name}</td>
                <td>${item.description}</td>
                <td>Q${item.selling_price}</td>
                <td>${item.quantity}u.</td>
                <td>${item.brand}</td>
                <td class="text-center"><img src="${item.image_path}" alt="${item.name}" width="30" height="30"></td>
                <td><span class="badge bg-${alertType}">${status}</span></td>
                ${hasActions ? `<td><div class="d-flex">${actionButtons}</div></td>` : ''}
            </tr>
        `;
    });
    
    tableBody.innerHTML = rows;
    
    assignSearchEvent('searchInput', 'tableBody', [0, 1, 2, 3, 4, 5]);

    if (hasActions) {
        assignModalEvent('.btn-show', showModal, 'inventario');
        assignModalEvent('.btn-update', updateModal, currentModule);
        assignModalEvent('.btn-delete', deleteModal, currentModule);
    }

    assignFormSubmitEvent('insertForm', insertFormSubmit, currentModule);
    assignFormSubmitEvent('updateForm', updateFormSubmit, currentModule);
    assignFormSubmitEvent('deleteForm', deleteFormSubmit, currentModule);

    resetModal('insertModal', 'insertForm');
}

const showModal = async (data) => {
    const url = `${urlBase}/inventario/filtrar`;
    const { medicine_id, medicine } = data;

    try {
        const response = await apiService.fetchData(url, 'POST', { id: medicine_id });
        const showTableBody = document.getElementById('showTableBody');
        const showModalTitle = document.getElementById('showModalTitle');
        let rows = '';
        showModalTitle.textContent = `Inventario de  ${medicine}`;

        if (response) {
            response.forEach(item => {
                rows += `
                <tr>
                <td>${item.batch_id}</td>
                <td>${item.branch_name}</td>
                <td>Q${item.purchase_price}</td>
                <td>${item.original_quantity}u.</td>
                <td>${item.current_quantity}u.</td>
                <td>${item.created_at}</td>
                <td>${item.updated_at}</td>
                <td>${item.expiration_date}</td>
                </tr>
                `;
            });
            showTableBody.innerHTML = rows;
        } else {
            showTableBody.innerHTML = '';
        }
    } catch (error) {
        console.error('Error:', error);
    }
};

const insertFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/agregar`;
    const formData = new FormData();
    
    formData.append('name', document.getElementById('insModName').value || '');
    formData.append('description', document.getElementById('insModDescription').value || '');
    formData.append('selling_price', document.getElementById('insModSellingPrice').value || '');
    formData.append('brand', document.getElementById('insModBrand').value || '');
    formData.append('image', document.getElementById('insModImage').files[0]); // Añadir imagen
    formData.append('active', Number(document.getElementById('insModStatus').value));
    formData.append('created_by', currentData.user_id || null);
    formData.append('updated_by', currentData.user_id || null);

    try {
        await apiService.fetchData(url, 'POST', formData);
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
    const id = data.medicine_id;
    
    try {
        const response = await apiService.fetchData(url, 'POST', { id });
        document.getElementById('updateForm').setAttribute('data-info', dataInfo);
        document.getElementById('updModName').value = response.name || '';
        document.getElementById('updModDescription').innerText = response.description || '';
        document.getElementById('updModSellingPrice').value = response.selling_price || '';
        document.getElementById('updModBrand').value = response.brand || '';
        document.getElementById('updModStatus').value = response.active.toString() || '';
    } catch (error) {
        console.error('Error:', error);
    }
};

const updateFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/actualizar`;
    const dataInfo = JSON.parse(document.getElementById('updateForm').getAttribute('data-info'));
    const formData = new FormData();

    formData.append('name', document.getElementById('updModName').value || '');
    formData.append('description', document.getElementById('updModDescription').value || '');
    formData.append('selling_price', document.getElementById('updModSellingPrice').value || '');
    formData.append('brand', document.getElementById('updModBrand').value || '');
    formData.append('image', document.getElementById('updModImage').files[0]); // Añadir imagen
    formData.append('active', Number(document.getElementById('updModStatus').value));
    formData.append('updated_by', currentData.user_id || null);
    formData.append('id', dataInfo.medicine_id || null);

    try {
        const response = await apiService.fetchData(url, 'POST', formData);
        
        if (response.success) {
            showAlert("Operación exitosa.", 'success');
            closeModal('updateModal');
        } else {
            showAlert(response.message, 'danger');
            closeModal('updateModal');
        }
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }

    await initModule(currentData, currentModule);
};

const deleteModal = async (data) => {
    const url = `${urlBase}/${currentModule}/filtrar`;
    const dataInfo = JSON.stringify(data);
    const id = data.medicine_id;
    
    try {
        const response = await apiService.fetchData(url, 'POST', { id });
        const status = response.active ? 'Activo' : 'Inactivo';
        document.getElementById('deleteForm').setAttribute('data-info', dataInfo);
        document.getElementById('delModName').innerText = response.name || '';
        document.getElementById('delModDescription').innerText = response.description || '';
        document.getElementById('delModSellingPrice').innerText = response.selling_price || '';
        document.getElementById('delModBrand').innerText = response.brand || '';
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
        id: dataInfo.medicine_id || null
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