let newsTimer = null;

export function initModule(data, module) {
    const greetingTitle = document.getElementById("greetingTitle");
    if (greetingTitle) greetingTitle.textContent = `¡Hola ${data.first_name}!`;
    renderHealthNews();
    if (newsTimer) clearInterval(newsTimer);
    newsTimer = setInterval(renderHealthNews, 180000);
}

async function renderHealthNews() {
    const grid = document.getElementById('newsGrid');
    if (!grid) return;

    const endpoint = `${urlBase}/inicio/noticias`;
    grid.setAttribute('aria-busy', 'true');
    try {
        const res = await fetch(endpoint, { cache: 'no-store' });
        const payload = await res.json();
        const items = payload.items || {};

        // Dedupe por URL canónica (sin query) o por título
        const seen = new Set();
        const unique = [];
        Object.keys(items).forEach(key => {
            const it = items[key];
            if (!it || !it.url) return;
            let canonical = '';
            try {
                const u = new URL(String(it.url));
                canonical = (u.origin + u.pathname).toLowerCase();
            } catch {
                canonical = String(it.url || '').trim().toLowerCase();
            }
            if (!canonical) {
                canonical = String(it.title || '').trim().toLowerCase();
            }
            if (canonical && !seen.has(canonical)) {
                seen.add(canonical);
                unique.push([key, it]);
            }
        });

        const cards = unique.map(([key, it]) => createNewsCard(it, key));

        if (cards.length === 0) {
            grid.innerHTML = '';
            grid.appendChild(createNewsCard({
                title: 'Últimas noticias de salud',
                source: 'CNN Español',
                url: 'https://cnnespanol.cnn.com/category/salud/',
                image: ''
            }, 'fallback'));
            setupNeonInteractions(grid);
            return;
        }

        grid.innerHTML = '';
        cards.forEach(card => grid.appendChild(card));
        setupNeonInteractions(grid);

        grid.setAttribute('aria-busy', 'false');
    } catch (e) {
        grid.innerHTML = '';
        grid.appendChild(createNewsCard({
            title: 'Noticias de salud (fuente externa)',
            source: 'El País Salud',
            url: 'https://elpais.com/salud-y-bienestar/',
            image: ''
        }, 'fallback'));
        setupNeonInteractions(grid);
        grid.setAttribute('aria-busy', 'false');
    }

    // Sin explosión: removido manejo de confetti
}

// ==========================
// Neon tilt + hotspot runtime
// ==========================
function setupNeonInteractions(rootEl) {
    const root = rootEl || document;
    const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const cards = Array.from(root.querySelectorAll('.neon-card'));
    if (cards.length === 0) return;

    if (!reduceMotion) cards.forEach(el => el.classList.add('tiltable'));

    cards.forEach(card => {
        if (card.dataset.neonInit === '1') return;
        if (card.classList.contains('no-neon') || card.getAttribute('data-no-neon') === 'true') return;
        card.dataset.neonInit = '1';

        // Garantiza sub-elementos decorativos si el HTML no los incluye
        if (!card.querySelector('.sheen')) {
            const sheen = document.createElement('div');
            sheen.className = 'sheen';
            sheen.setAttribute('aria-hidden', 'true');
            card.appendChild(sheen);
        }
        if (!card.querySelector('.cursor-glow')) {
            const hotspot = document.createElement('div');
            hotspot.className = 'cursor-glow';
            hotspot.setAttribute('aria-hidden', 'true');
            card.appendChild(hotspot);
        }
        if (!card.querySelector('.runner')) {
            const runner = document.createElement('span');
            runner.className = 'runner';
            runner.setAttribute('aria-hidden', 'true');
            card.appendChild(runner);
        }

        // Interacciones tilt + hotspot
        let rect = null;
        let raf = 0;
        let rx = 0, ry = 0, dz = 0;
        let tx = 0, ty = 0, tz = 0;
        const maxTilt = 10;  // grados
        const pushZ = 12;    // px
        const ease = 0.12;   // suavizado

        function update() {
            rx += (tx - rx) * ease;
            ry += (ty - ry) * ease;
            dz += (tz - dz) * ease;
            card.style.setProperty('--rx', rx.toFixed(2) + 'deg');
            card.style.setProperty('--ry', ry.toFixed(2) + 'deg');
            card.style.setProperty('--dz', dz.toFixed(2) + 'px');
            raf = requestAnimationFrame(update);
        }
        function onEnter() {
            rect = card.getBoundingClientRect();
            if (!raf && !reduceMotion) raf = requestAnimationFrame(update);
        }
        function onMove(e) {
            if (!rect) rect = card.getBoundingClientRect();
            const clientX = (e.touches && e.touches[0] ? e.touches[0].clientX : e.clientX);
            const clientY = (e.touches && e.touches[0] ? e.touches[0].clientY : e.clientY);
            const x = clientX - rect.left;
            const y = clientY - rect.top;
            const px = Math.max(0, Math.min(1, x / rect.width));
            const py = Math.max(0, Math.min(1, y / rect.height));
            card.style.setProperty('--mx', (px * 100) + '%');
            card.style.setProperty('--my', (py * 100) + '%');
            if (reduceMotion) return;
            ty = (px - 0.5) * (maxTilt * 2);
            tx = (0.5 - py) * (maxTilt * 2);
            tz = pushZ;
        }
        function onLeave() { if (!reduceMotion) { tx = 0; ty = 0; tz = 0; } }

        card.addEventListener('mouseenter', onEnter);
        card.addEventListener('mousemove', onMove);
        card.addEventListener('mouseleave', onLeave);
        card.addEventListener('touchstart', onEnter, {passive: true});
        card.addEventListener('touchmove', onMove, {passive: true});
        card.addEventListener('touchend', onLeave);
    });
}

// Inicialización defensiva (por si existen tarjetas estáticas en el DOM)
document.addEventListener('DOMContentLoaded', () => setupNeonInteractions());

function createNewsCard(item, key) {
    const col = document.createElement('div');
    col.className = 'col-12 col-md-6 col-lg-4';

    const a = document.createElement('a');
    a.href = item.url;
    a.target = '_blank';
    a.rel = 'noopener noreferrer';
    a.className = 'news-card text-decoration-none neon-card tiltable';
    a.setAttribute('aria-label', `${item.title ? escapeHtml(item.title) : 'Noticia externa'} — ${item.source ? escapeHtml(item.source) : key}`);

    const mediaUrl = (item.image && item.image.trim()) ? item.image.trim() : '';
    const published = item.published ? formatDate(item.published) : '';

    const thumb = document.createElement('div');
    thumb.className = 'news-thumb';
    if (mediaUrl) {
        if (/\.(mp4|webm|ogg|ogv|mov|m4v|mpg|mpeg|m3u8)(\?|#|$)/i.test(mediaUrl)) {
            const vid = document.createElement('video');
            vid.src = mediaUrl;
            vid.playsInline = true;
            vid.muted = true;
            vid.loop = true;
            try {
                const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (!reduce) vid.autoplay = true;
            } catch {}
            thumb.appendChild(vid);
        } else {
            const img = document.createElement('img');
            img.src = mediaUrl;
            img.alt = item.title ? `Imagen: ${item.title}` : 'Imagen de la noticia';
            img.loading = 'lazy';
            thumb.appendChild(img);
        }
    }

    const content = document.createElement('div');
    content.className = 'news-content';
    const source = document.createElement('div');
    source.className = 'news-source small text-muted';
    source.textContent = item.source || key;
    const title = document.createElement('div');
    title.className = 'news-title';
    title.textContent = item.title || '';
    content.appendChild(source);
    content.appendChild(title);
    if (published) {
        const meta = document.createElement('div');
        meta.className = 'news-meta';
        meta.textContent = published;
        content.appendChild(meta);
    }

    a.appendChild(thumb);
    a.appendChild(content);

    // Elementos decorativos para efectos neon
    if (!a.querySelector('.sheen')) {
        const sheen = document.createElement('div');
        sheen.className = 'sheen';
        sheen.setAttribute('aria-hidden', 'true');
        a.appendChild(sheen);
    }
    if (!a.querySelector('.cursor-glow')) {
        const hotspot = document.createElement('div');
        hotspot.className = 'cursor-glow';
        hotspot.setAttribute('aria-hidden', 'true');
        a.appendChild(hotspot);
    }
    if (!a.querySelector('.runner')) {
        const runner = document.createElement('span');
        runner.className = 'runner';
        runner.setAttribute('aria-hidden', 'true');
        a.appendChild(runner);
    }

    col.appendChild(a);
    return col;
}

// Confetti removido

// Removed observer (no longer needed)

function formatDate(ts) {
    const d = typeof ts === 'number' ? new Date(ts*1000) : new Date(ts);
    if (Number.isNaN(d.getTime())) return '';
    try {
        return new Intl.DateTimeFormat('es-ES', { dateStyle: 'medium', timeStyle: undefined }).format(d);
    } catch { return d.toLocaleDateString(); }
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"]+/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
}
