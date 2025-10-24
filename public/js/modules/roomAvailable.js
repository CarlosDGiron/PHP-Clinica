import { apiService } from '../services/apiService.js';

let currentModule;

export async function initModule(data, module) {
    currentModule = module;
    const url = `${urlBase}/${currentModule}/mostrar`;

    // Función para obtener y actualizar los datos de las habitaciones
    async function actualizarHabitaciones() {
        // Llama a la API para obtener los datos de las habitaciones
        const response = await apiService.fetchData(url, 'GET');

        // Asegúrate de que el contenedor esté vacío antes de agregar nuevas tarjetas
        $('#roomsContainer').empty();

        // Itera sobre los datos de respuesta y genera las tarjetas
        response.forEach(room => {
            const roomName = room.room_name;
            const branchName = room.branch_name;
            const occupiedQuantity = room.occupied_quantity;
            const availableQuantity = room.available_quantity;
            const maxCapacity = occupiedQuantity + availableQuantity;

            // Determina el estilo del indicador de disponibilidad
            const availabilityClass = availableQuantity > 0 ? 'bg-primary text-white' : 'bg-danger text-white';
            const availabilityText = availableQuantity > 0 ? 'Disponible' : 'Ocupado';

            // Crea la estructura de la tarjeta de habitación
            const roomCard = `
                <div class="col-md-4 mb-4">
                    <div class="card text-center" style="background-color: #f3f6f9;">
                        <div class="card-body">
                            <h5 class="card-title">Habitación ${roomName}</h5>
                            <p class="card-text">Sucursal: ${branchName}</p>
                            <p class="card-text">Pacientes: ${occupiedQuantity} / ${maxCapacity}</p>
                            <span class="badge ${availabilityClass} p-2 rounded-pill">${availabilityText}</span>
                        </div>
                    </div>
                </div>
            `;

            // Agrega la tarjeta al contenedor
            $('#roomsContainer').append(roomCard);
        });
    }

    // Ejecuta actualizarHabitaciones cada segundo para actualizar en tiempo real
    setInterval(actualizarHabitaciones, 500);

    // Llama a actualizarHabitaciones inmediatamente la primera vez
    actualizarHabitaciones();
}