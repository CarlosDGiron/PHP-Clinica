export const apiService = {
    async fetchData(url, method, body = {}) {
        try {
            const token = localStorage.getItem('token');
            // Configuración inicial para el objeto de opciones
            const headers = {};
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const options = {
                method,
                headers
            };

            // Determina si el cuerpo es FormData
            if (body instanceof FormData) {
                options.body = body; // Si es FormData, lo asignamos directamente al body y omitimos Content-Type
            } else {
                options.headers['Content-Type'] = 'application/json';
                if (method !== 'GET') {
                    options.body = JSON.stringify(body); // Convierte el objeto a JSON
                }
            }

            const response = await fetch(url, options);

            if (response.ok) {
                const contentType = response.headers.get('Content-Type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                } else {
                    return await response.text(); // O simplemente response si no necesitas texto
                }
            } else {
                throw new Error(`Error: ${response.status} - ${response.statusText}`);
            }
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
};
