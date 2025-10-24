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
            populateSelect('insModPatient', 'paciente');
            populateSelectDoctor('insModDoctor', 'empleado');
            populateSelect('insModBranch', 'sucursal');
            populateSelectExams('insModExams', 'examen');
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
        const dataInfo = JSON.stringify({diagnosis_id: item.id}).replace(/"/g, '&quot;');
        let actionButtons = '';
        
        // Crear los botones de acuerdo a los permisos
        if (canUpdate) {
            actionButtons += createButton('btn-primary btn-update', 'Editar', dataInfo, 'updateModal', 'bi bi-pencil');
        }
        if (canDelete) {
            actionButtons += createButton('btn-danger btn-delete', 'Borrar', dataInfo, 'deleteModal', 'bi bi-trash-fill');
        }

        const formattedExams = item.exams ? item.exams.split(',').map(exam => exam.trim()).join(', ') : 'Ninguno';

        rows += `
            <tr>
                <td class="${item.patient_name ? '' : 'text-danger'}">${item.patient_name}</td>
                <td class="${item.employee_name ? '' : 'text-danger'}">${item.employee_name}</td>
                <td class="${item.branch_name ? '' : 'text-danger'}">${item.branch_name}</td>
                <td>${item.visit_date}</td>
                <td>${item.diagnosis}</td>
                <td>${formattedExams}</td>
                <td>${item.treatment_plan || 'Ninguno'}</td>
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

const populateSelectDoctor = async (selectId, module) =>  {
    const select = document.getElementById(selectId);
    const newSelect = select.outerHTML;
    select.outerHTML = newSelect;

    const newSelectElement = document.getElementById(selectId);
    newSelectElement.innerHTML = '';
    
    try {
        const options = await apiService.fetchData(`${urlBase}/${module}/mostrar`, 'GET');
        options.forEach(item => {
            // Mostrar solo empleados activos (se elimina dependencia de patient_care)
            if (item.active === 1) {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.employee_name;
                newSelectElement.appendChild(option);
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

const populateSelectExams = async (dropdownId, module) => {
    const dropdownMenu = document.querySelector(`#${dropdownId} + .dropdown-menu`);
    dropdownMenu.innerHTML = '';

    try {
        const options = await apiService.fetchData(`${urlBase}/${module}/mostrar`, 'GET');
        options.forEach(item => {
            if (item.active === 1) {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    <div class="form-check ms-3">
                        <input class="form-check-input" type="checkbox" value="${item.name}" id="option${item.id}">
                        <label class="form-check-label" for="option${item.id}">${item.name}</label>
                    </div>
                `;
                dropdownMenu.appendChild(listItem);
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
};

const insertFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/agregar`;
    
    const formData = () => ({
        patient_id: Number(document.getElementById('insModPatient').value) || null,
        employee_id: Number(document.getElementById('insModDoctor').value) || '',
        branch_id: Number(document.getElementById('insModBranch').value) || '',
        visit_date: document.getElementById('insModVisitDate').value || '',
        diagnosis: document.getElementById('insModDiagnosis').value || '',
        created_by: currentData.user_id || null,
        updated_by: currentData.user_id || null
    });
    
    
    try {
        const response = await apiService.fetchData(url, 'POST', formData());
        await examsFormSubmit(response.diagnosis_id, 'insModExams');
        showAlert("Operación exitosa.", 'success');
        closeModal('insertModal');
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }
    
    await initModule(currentData, currentModule);
};

const examsFormSubmit = async (id, selectId) => {
    const urlInsert = `${urlBase}/pacienteexamen/agregar`;
    const urlUpdate = `${urlBase}/pacienteexamen/actualizar`;
    const rows = document.querySelectorAll(`#${selectId} + .dropdown-menu input`);

    const promises = Array.from(rows).map(async checkbox => {
        const exam_id = Number(checkbox.id.replace('option', ''));
        const assigned = checkbox.checked ? 1 : 0; // 1 si está seleccionado, 0 si no

        // Verifica si el examen ya existe para el paciente
        const existingExam = await apiService.fetchData(
            `${urlBase}/pacienteexamen/filtrar`,
            'POST', 
            { exam_id: exam_id, patient_diagnoses_id: id }
        );

        if (existingExam) {
            // Si existe, actualizar el estado
            const formData = () => ({
                exam_id: exam_id,
                patient_diagnoses_id: id,
                assigned: assigned,
                updated_by: currentData.user_id || null
            });
            await apiService.fetchData(urlUpdate, 'POST', formData());
        } else if (checkbox.checked) {
            // Si no existe y está seleccionado, insertar
            const formData = () => ({
                exam_id: exam_id,
                patient_diagnoses_id: id,
                assigned: assigned,
                created_by: currentData.user_id || null,
                updated_by: currentData.user_id || null
            });
            await apiService.fetchData(urlInsert, 'POST', formData());
        }
    });

    await Promise.all(promises);
};


const updateModal = async (data) => {
    const url = `${urlBase}/${currentModule}/filtrar`;
    const dataInfo = JSON.stringify(data);
    const id = data.diagnosis_id;

    await populateSelect('updModPatient', 'paciente');
    await populateSelectDoctor('updModDoctor', 'empleado');
    await populateSelect('updModBranch', 'sucursal');
    await populateSelectExams('updModExams', 'examen');

    try {
        const response = await apiService.fetchData(url, 'POST', { id });

        const patientSelect = document.getElementById('updModPatient');
        const employeeSelect = document.getElementById('updModDoctor');
        const branchSelect = document.getElementById('updModBranch');

        const patientOption = Array.from(patientSelect.options).find(option => option.text === response.patient_name);
        const employeeOption = Array.from(employeeSelect.options).find(option => option.text === response.employee_name);
        const branchOption = Array.from(branchSelect.options).find(option => option.text === response.branch_name);
        const selectedExamIds = response.exams_id ? response.exams_id.split(',') : [];

        document.getElementById('updateForm').setAttribute('data-info', dataInfo);
        document.getElementById('updModPatient').value = patientOption ? patientOption.value : '';
        document.getElementById('updModDoctor').value = employeeOption ? employeeOption.value : '';
        document.getElementById('updModBranch').value = branchOption ? branchOption.value : '';
        document.getElementById('updModVisitDate').value = response.visit_date || '';
        document.getElementById('updModDiagnosis').value = response.diagnosis || '';


        selectedExamIds.forEach(examId => {
            const checkbox = document.getElementById(`option${examId.trim()}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    } catch (error) {
        console.error('Error:', error);
    }
};


const updateFormSubmit = async () => {
    const url = `${urlBase}/${currentModule}/actualizar`;
    const dataInfo = JSON.parse(document.getElementById('updateForm').getAttribute('data-info'));

    const formData = () => ({ 
        patient_id: Number(document.getElementById('updModPatient').value) || null,
        employee_id: Number(document.getElementById('updModDoctor').value) || '',
        branch_id: Number(document.getElementById('updModBranch').value) || '',
        visit_date: document.getElementById('updModVisitDate').value || '',
        diagnosis: document.getElementById('updModDiagnosis').value || '',
        updated_by: currentData.user_id || null,
        id: dataInfo.diagnosis_id || null
    });
    
    try {
        await apiService.fetchData(url, 'POST', formData());
        await examsFormSubmit(dataInfo.diagnosis_id, 'updModExams');
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
    const id = data.diagnosis_id;
    
    try {
        const response = await apiService.fetchData(url, 'POST', { id });

        document.getElementById('deleteForm').setAttribute('data-info', dataInfo);
        document.getElementById('delModPatient').innerText = response.patient_name || '';
        document.getElementById('delModDoctor').innerText = response.employee_name || '';
        document.getElementById('delModBranch').innerText = response.branch_name || '';
        document.getElementById('delModVisitDate').innerText = response.visit_date || '';
        document.getElementById('delModDiagnosis').innerText = response.diagnosis || '';
        document.getElementById('delModExams').innerText = response.exams || '';
        document.getElementById('delModTreatmentPlan').innerText = response.treatment_plan || '';

    } catch (error) {
        console.error('Error:', error);
    }
};

const deleteFormSubmit = async () => {
    const urlDiagnosis = `${urlBase}/${currentModule}/eliminar`;
    const urlExams = `${urlBase}/pacienteexamen/eliminar`;
    const dataInfo = JSON.parse(document.getElementById('deleteForm').getAttribute('data-info'));

    const formDataDiagnosis = () => ({
        deleted_by: currentData.user_id || null,
        id: dataInfo.diagnosis_id || null
    });

    const formDataExams = () => ({
        deleted_by: currentData.user_id || null,
        patient_diagnoses_id: dataInfo.diagnosis_id || null
    });

    try {
        await apiService.fetchData(urlDiagnosis, 'POST', formDataDiagnosis());
        await apiService.fetchData(urlExams, 'POST', formDataExams());
        showAlert("Operación exitosa.", 'success');
        closeModal('deleteModal');
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }

    await initModule(currentData, currentModule);
};
