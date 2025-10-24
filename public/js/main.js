import { loadModule } from './moduleLoader.js';
import { apiService } from './services/apiService.js';
import { loadModuleContent } from './utils/moduleContent.js';
import { showAlert } from './utils/showArlert.js';

document.addEventListener('DOMContentLoaded', async () => {
    const module = window.location.pathname.split('/').pop();
    await loadModule(module);

    document.getElementById('logoutLink').addEventListener('click', async (e) => {
        e.preventDefault();
        const url = `${urlBase}/login/salir`;
        const token = localStorage.getItem('token');
        
        try {
            const response = await apiService.fetchData(url, 'POST', { token });
            if (response.success) {
                localStorage.removeItem('token');
                window.location.href = urlBase;
            } else {
                window.location.href = urlBase;
            }
        } catch (error) {
            window.location.href = urlBase;
        }
    });

    // Manejo del evento popstate para navegación
    window.addEventListener('popstate', async (event) => {
        const module = window.location.pathname.split('/').pop();

        const url = `${urlBase}/login/token`;
        try {
            const data = await apiService.fetchData(url, 'GET');
            const MenuItem = data.modules.find(item => item.link.includes(module));
            await loadModuleContent(MenuItem, data);
            const navbarNav = document.querySelector('.navbar-modules');
            const links = navbarNav.querySelectorAll('a');
        
            links.forEach(link => {
                if (link.href.includes(module)) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
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