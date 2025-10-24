import { apiService } from './services/apiService.js';
import { isEmpty } from './utils/validation.js';
import { showAlert } from './utils/showArlert.js';

// Inicializar la página
document.addEventListener("DOMContentLoaded", async function() {
    const url = `${urlBase}/login/token`;
    const token = localStorage.getItem('token');

    if (token) {
        const data = await fetchWithHandling(url, 'GET');
        if (data) {
            window.location.href = `${urlBase}/${data}`;
        }
    } else {
        $('#spinner').removeClass('show');
    }

    // Parallax visual only (no lógica de login)
    try {
        const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const root = document.querySelector('.login-page');
        if (!reduce && root) {
            let tx = 0, ty = 0, rx = 0, ry = 0; // target and rendered
            const ease = 0.08; // suavizado
            const max = 20;    // px de desplazamiento máximo (más visible)

            const rafStep = () => {
                rx += (tx - rx) * ease;
                ry += (ty - ry) * ease;
                root.style.setProperty('--plx-x', `${rx.toFixed(2)}px`);
                root.style.setProperty('--plx-y', `${ry.toFixed(2)}px`);
                frame = requestAnimationFrame(rafStep);
            };

            const onMove = (e) => {
                const vw = Math.max(1, window.innerWidth);
                const vh = Math.max(1, window.innerHeight);
                const cx = (e.clientX ?? (e.touches && e.touches[0]?.clientX) ?? vw/2);
                const cy = (e.clientY ?? (e.touches && e.touches[0]?.clientY) ?? vh/2);
                const px = (cx / vw) - 0.5; // -0.5..0.5
                const py = (cy / vh) - 0.5;
                tx = Math.max(-1, Math.min(1, px)) * max;
                ty = Math.max(-1, Math.min(1, py)) * max;
                if (!frame) frame = requestAnimationFrame(rafStep);
            };

            const onLeave = () => { tx = 0; ty = 0; };

            let frame = 0;
            window.addEventListener('mousemove', onMove, { passive: true });
            window.addEventListener('mouseleave', onLeave, { passive: true });
            window.addEventListener('touchmove', onMove, { passive: true });
            window.addEventListener('touchend', onLeave, { passive: true });
        }
    } catch {}

});

// Alternar visibilidad de contraseña
document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordField = document.querySelector("#loginPassword");
    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);
    this.querySelector('i').classList.toggle("bi-eye");
    this.querySelector('i').classList.toggle("bi-eye-slash");
});

// Enviar formulario de inicio de sesión
document.getElementById('loginForm').onsubmit = async function (e) {
    e.preventDefault();

    const usernameInput = document.getElementById('loginUsername');
    const passwordInput = document.getElementById('loginPassword');
    const username = usernameInput.value;
    const password = passwordInput.value;
    const url = `${urlBase}/login/ingresar`;

    if (isEmpty(username)) {
        showAlert('El campo de usuario es obligatorio', 'danger');
        usernameInput.classList.add('input-error');
        passwordInput.classList.remove('input-error');
        return;
    } else {
        usernameInput.classList.remove('input-error');
    }

    if (isEmpty(password)) {
        showAlert('El campo de contraseña es obligatorio', 'danger');
        passwordInput.classList.add('input-error');
        usernameInput.classList.remove('input-error');
        return;
    } else {
        passwordInput.classList.remove('input-error');
    }

    try {
        const data = await apiService.fetchData(url, 'POST', { username, password });

        if (data.success) {
            localStorage.setItem('token', data.token);

            // Usar el Credential Management API para almacenar las credenciales
            if ('credentials' in navigator) {
                const credentials = new PasswordCredential({
                    id: username,
                    password: password,
                    name: username
                });

                // Almacenar las credenciales en el navegador
                await navigator.credentials.store(credentials);
            }
            const url = `${urlBase}/login/token`;

            if (data.token) {
                const data = await fetchWithHandling(url, 'GET');
                if (data) {
                    window.location.href = `${urlBase}/${data}`;
                }
            } else {
                $('#spinner').removeClass('show');
            }
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        showAlert('Error de conexión.', 'danger');
    }
};

async function fetchWithHandling(url, method, body = {}) {
    try {
        const data = await apiService.fetchData(url, method, body);

        if (data && data.modules && data.modules[0]) {
            return data.modules[0].link;
        } else {
            if (!data.modules || data.modules.length === 0) {
                showAlert('El usuario no tiene módulos asignados.', 'warning');
                localStorage.removeItem('token');
                $('#spinner').removeClass('show');
            }
            return null;
        }
    } catch (error) {
        const tokenExpired = localStorage.getItem('tokenExpired');
        if (error.message.includes('401') && tokenExpired === 'true') {
            showAlert('Tu sesión ha expirado. Inicia sesión de nuevo.', 'danger');
            localStorage.removeItem('token');
            localStorage.removeItem('tokenExpired');
            $('#spinner').removeClass('show');
        } else if (error.message.includes('401')) {
            localStorage.removeItem('token');
            $('#spinner').removeClass('show');
        } else {
            showAlert('Error de conexión, por favor intenta de nuevo.', 'danger');
            $('#spinner').removeClass('show');
        }
        return null;
    }
}
