import { loadModuleContent } from './moduleContent.js';
import { apiService } from '../services/apiService.js';
import { showAlert } from '../utils/showArlert.js';

export const createMenu = (menuItems, activeModule, data) => {
    const navbarNav = document.querySelector('.navbar-modules');
    
    // Limpiar el menú existente
    navbarNav.innerHTML = ''; 

    // Generar nuevo menú
    menuItems.map(item => {
        const link = createMenuItem(item, activeModule);
        navbarNav.appendChild(link);
    });
};

export const createMenuItem = (item, activeModule) => {
    const link = document.createElement('a');
    link.href = `${urlBase}/${item.link}`;
    
    if (activeModule === item.link) {
        link.className = 'nav-item nav-link active';
        moduleTitle.textContent = item.module;
        moduleIcon.className = `${item.icon} me-2 fa-2x text-dark`;
    } else {
        link.className = 'nav-item nav-link';
    }

    const icon = document.createElement('i');
    icon.className = `${item.icon} me-2`;
    link.appendChild(icon);
    link.appendChild(document.createTextNode(item.module));

    // Manejo de eventos
    link.addEventListener('click', async (e) => {
        e.preventDefault();
        $('.sidebar, .content').removeClass("open");
        
        const url = `${urlBase}/login/token`;
        try {
            const data = await apiService.fetchData(url, 'GET');
            history.pushState(null, '', `${urlBase}/${item.link}`);
            await loadModuleContent(item, data);
            updateActiveMenuItem(item.link);

        } catch (error) {
            if (error.message.includes('401')) {
                localStorage.setItem('tokenExpired', 'true');
                window.location.href = urlBase;
            } else {
                showAlert('Error de conexión.', 'danger');
            }
        }
    });

    return link;
};

export const updateActiveMenuItem = (activeLink) => {
    const navbarNav = document.querySelector('.navbar-modules');
    const links = navbarNav.querySelectorAll('a');

    links.forEach(link => {
        if (link.href.includes(activeLink)) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
};
