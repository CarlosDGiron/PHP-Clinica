import { apiService } from '../services/apiService.js';
import { showAlert } from '../utils/showArlert.js';

let currentData;
let currentModule;

export async function initModule(data, module) {
    currentData = data;
    currentModule = module;
    await populateSelect('addMedicine', 'medicina');
    await populateSelect('addBranch', 'sucursal');

    document.getElementById('addBranch').addEventListener('change', async function () {
        document.getElementById('addMedicine').value = '';
        document.getElementById('addSalePrice').value = '';
        document.getElementById('addStock').value = '';
    });

    document.getElementById('addMedicine').addEventListener('change', async function () {
        const selectedBranch = document.getElementById('addBranch').value

        if (!selectedBranch) {
            document.getElementById('addMedicine').value = '';
            showAlert("Seleccione una sucursal.", 'warning');
            return;
        }

        const selectedMedicine = this.value;
        await populateInput(selectedMedicine, selectedBranch);
        const totalRegisteredQuantity = await getTotalQuantityFromTable(selectedMedicine, selectedBranch);
        const currentStock = Number(document.getElementById('addStock').value) || 0;
        const availableStock = currentStock - totalRegisteredQuantity;
        const stock = document.getElementById('addStock');
        const quantity = document.getElementById('addQuantity');
        stock.value = availableStock >= 0 ? availableStock : 0;
        quantity.max = stock.value;
    });

    document.getElementById('resetButton').addEventListener('click', async function () {
        await resetForm();
        document.getElementById('finalizeButton').style.display = 'none';
        document.getElementById('totalRow').style.display = 'none';
    });

    document.getElementById('addForm').addEventListener('submit', async function (event) {
        event.preventDefault();
        await addProduct();
        document.getElementById('addDate').disabled = true;
        document.getElementById('addCustomer').disabled = true;
        document.getElementById('addBranch').disabled = true;
        document.getElementById('addMedicine').value = '';
        document.getElementById('addSalePrice').value = '';
        document.getElementById('addStock').value = '';
        document.getElementById('addQuantity').value = '';
    });

    document.getElementById('finalizeButton').addEventListener('click', async function () {
        await insertFormSubmit();
        await resetForm();
        document.getElementById('finalizeButton').style.display = 'none';
        document.getElementById('totalRow').style.display = 'none';
    });
    
};

const updateTotal = async () => {
    const tableBody = document.getElementById('tableBody');
    let totalSum = 0;

    // Verificar si hay filas en la tabla antes de iterar
    if (tableBody.rows.length > 0) {
        Array.from(tableBody.rows).forEach(row => {
            const rowTotal = parseFloat(row.cells[3]?.innerText.replace('Q', '')) || 0;
            totalSum += rowTotal;
        });
    }

    // Controlar la visibilidad de la fila de "Total"
    let totalRow = document.getElementById('totalRow');
    if (tableBody.rows.length > 0) {
        totalRow.style.display = '';
        document.getElementById('totalAmount').innerText = `Q${totalSum.toFixed(2)}`;
    } else {
        totalRow.style.display = 'none';
        await resetForm();
    }

    // Llamar a `showFinalizeButton` para actualizar el estado del botón "Finalizar Venta"
    await showFinalizeButton();
};

const showFinalizeButton = async () => {
    const finalizeButton = document.getElementById('finalizeButton');
    const tableBody = document.getElementById('tableBody');
    
    // Mostrar el botón solo si hay filas en la tabla
    finalizeButton.style.display = tableBody.rows.length > 0 ? 'inline-block' : 'none';
};

const getTotalQuantityFromTable = async (medicineId, branchId) => {
    const tableBody = document.getElementById('tableBody');
    let totalQuantity = 0;

    // Recorrer cada fila de la tabla para acumular la cantidad del medicamento y sucursal especificados
    Array.from(tableBody.rows).forEach(row => {
        const rowMedicineId = row.dataset.medicineId; // Obtiene medicine_id de data attribute
        const rowBranchId = row.dataset.branchId;     // Obtiene branch_id de data attribute
        const rowQuantity = Number(row.cells[1].innerText) || 0; // Cantidad de la columna correspondiente

        if (rowMedicineId === medicineId && rowBranchId === branchId) {
            totalQuantity += rowQuantity;
        }
    });

    return totalQuantity;
};

const resetForm = async () => {
    document.getElementById('addForm').reset();
    document.getElementById('tableBody').innerHTML = '';
    document.getElementById('addDate').disabled = false;
    document.getElementById('addCustomer').disabled = false;
    document.getElementById('addBranch').disabled = false;
};

const addProduct = async () => {
    const url = `${urlBase}/${currentModule}/filtrar`;
    const tableBody = document.getElementById('tableBody');
    const quantity=  Number(document.getElementById('addQuantity').value) || null;

    const inputData = () => ({
        medicine_id: document.getElementById('addMedicine').value || '',
        branch_id: document.getElementById('addBranch').value || ''
    });

    const response = await apiService.fetchData(url, 'POST', inputData());
    const sellingPrice = parseFloat(response.selling_price) || 0;
    const total = (sellingPrice * quantity).toFixed(2);

    const row = document.createElement('tr');
    row.dataset.medicineId = response.medicine_id;
    row.dataset.branchId = response.branch_id;

    row.innerHTML = `
        <td>${response.medicine_name}</td>
        <td>${quantity}</td>
        <td>Q${sellingPrice.toFixed(2)}</td>
        <td>Q${total}</td>
        <td><button class="btn btn-danger btn-sm delete-button">
                <i class="bi bi bi-trash-fill"></i>
            </button>
        </td>
    `;

    row.querySelector('.delete-button').addEventListener('click', () => {
        row.remove(); // Elimina la fila de la tabla
        updateTotal();
        document.getElementById('addMedicine').value = '';
        document.getElementById('addSalePrice').value = '';
        document.getElementById('addStock').value = '';
    });

    tableBody.appendChild(row);

    updateTotal();
};


const populateSelect = async (selectId, module) =>  {
    const select = document.getElementById(selectId);
    const newSelect = select.outerHTML;
    select.outerHTML = newSelect;

    const newSelectElement = document.getElementById(selectId);
    newSelectElement.innerHTML = '';

    try {
        const options = await apiService.fetchData(`${urlBase}/${module}/mostrar`, 'GET');
        const defaultOption = new Option('Seleccionar', '');
        defaultOption.hidden = true;
        defaultOption.selected = true;
        newSelectElement.appendChild(defaultOption);

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

const populateInput = async (medicine_id, branch_id) => {
    const url = `${urlBase}/${currentModule}/filtrar`;

    try {
        const response = await apiService.fetchData(url, 'POST', { medicine_id,  branch_id});
        document.getElementById('addSalePrice').value = `Q${response.selling_price}` || '';
        document.getElementById('addStock').value = response.quantity || '';
    } catch (error) {
        console.error('Error:', error);
    }
};

const insertFormSubmit = async () => {
    const urlInsSale = `${urlBase}/${currentModule}/agregar`;
    const urlSaleDetail = `${urlBase}/ventadetalle/agregar`;
    const urlUpdSale = `${urlBase}/${currentModule}/actualizar`;

    const inputData = () => ({
        branch_id: document.getElementById('addBranch').value || null,
        customer: document.getElementById('addCustomer').value || '',
        sale_date: document.getElementById('addDate').value || '',
        created_by: currentData.user_id || null,
        updated_by: currentData.user_id || null
    });

    try {
        const response = await apiService.fetchData(urlInsSale, 'POST', inputData());
        
        const tableBody = document.getElementById('tableBody');

        const promises = Array.from(tableBody.rows).map(async row => {
            const tableData = () => ({
                sale_id: response.sale_id || null,
                medicine_id: row.dataset.medicineId,
                selling_price: parseFloat(row.cells[2]?.innerText.replace('Q', '')) || null,
                quantity: Number(row.cells[1].innerText) || null,
                created_by: currentData.user_id || null,
                updated_by: currentData.user_id || null
            });
    
            try {
                return await apiService.fetchData(urlSaleDetail, 'POST', tableData());
            } catch (error) {
                showAlert("Error de conexión.", 'danger');
                console.error('Error:', error);
            }
        });
        
        await Promise.all(promises);

        const saleData = () => ({
            sale_id: response.sale_id || null,
            updated_by: currentData.user_id || null
        });

        try {
            await apiService.fetchData(urlUpdSale, 'POST', saleData());
        } catch (error) {
            showAlert("Error de conexión.", 'danger');
            console.error('Error:', error);
        }

        showAlert("Operación exitosa.", 'success');
    } catch (error) {
        showAlert("Error de conexión.", 'danger');
        console.error('Error:', error);
    }
};