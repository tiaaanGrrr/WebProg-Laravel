/* Interaksi UI saja. Harga/stok di browser tidak boleh dipercaya saat checkout nyata. */
(() => {
    'use strict';

    const storageKey = 'cahaya-sembahyang-cart-v1';
    const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
    const toast = document.getElementById('site-toast');
    const dialog = document.getElementById('cart-dialog');
    const cartItems = document.getElementById('cart-items');
    const cartEmpty = document.getElementById('cart-empty');
    const subtotal = document.getElementById('cart-subtotal');
    let toastTimer;
    let previousFocus;
    let storageAvailable = true;

    const normalize = (value) => String(value ?? '').toLocaleLowerCase('id-ID').trim();
    const imageUrl = (value) => {
        try {
            const url = new URL(value, window.location.href);
            return ['http:', 'https:', 'file:'].includes(url.protocol) ? url.href : '';
        } catch { return ''; }
    };

    function notify(message) {
        if (!toast) return;
        clearTimeout(toastTimer);
        toast.textContent = message;
        toast.hidden = false;
        toastTimer = setTimeout(() => { toast.hidden = true; }, 4200);
    }

    function readCart() {
        try {
            const value = JSON.parse(localStorage.getItem(storageKey) ?? '[]');
            if (!Array.isArray(value)) return [];
            const seen = new Set();
            return value.slice(0, 100).filter((item) => {
                if (!item || typeof item.id !== 'string' || seen.has(item.id)) return false;
                if (!/^[a-zA-Z0-9_-]{1,100}$/.test(item.id)) return false;
                if (typeof item.name !== 'string' || !item.name.trim()) return false;
                if (!Number.isFinite(item.price) || item.price < 0 || item.price > 1e12) return false;
                if (!Number.isInteger(item.quantity) || item.quantity < 1 || item.quantity > 99) return false;
                seen.add(item.id);
                return true;
            }).map((item) => ({
                id: item.id,
                name: item.name.slice(0, 240),
                price: item.price,
                image: imageUrl(item.image),
                quantity: item.quantity,
                maxQuantity: Math.max(item.quantity, Math.min(99, Number(item.maxQuantity) || 99)),
            }));
        } catch {
            // Storage bisa tidak tersedia dalam mode privat atau preview file lokal.
            storageAvailable = false;
            return [];
        }
    }

    let cart = readCart();

    function saveCart() {
        try { localStorage.setItem(storageKey, JSON.stringify(cart)); }
        catch { storageAvailable = false; }
        renderCart();
    }

    function element(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function renderCart() {
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.querySelectorAll('[data-cart-count]').forEach((node) => { node.textContent = count; });
        document.querySelectorAll('[data-open-cart]').forEach((node) => { node.setAttribute('aria-label', `Buka keranjang, ${count} barang`); });
        if (!cartItems) return;
        cartItems.replaceChildren();
        cartEmpty.hidden = cart.length > 0;
        subtotal.textContent = rupiah.format(cart.reduce((sum, item) => sum + item.price * item.quantity, 0));

        cart.forEach((item) => {
            const row = element('article', 'cart-item');
            const picture = element('img', 'cart-item__image');
            picture.alt = item.name;
            picture.width = 64;
            picture.height = 64;
            if (item.image) picture.src = item.image;
            else picture.hidden = true;
            picture.addEventListener('error', () => { picture.hidden = true; }, { once: true });
            const body = element('div');
            body.append(element('h3', '', item.name), element('p', 'cart-item__price', rupiah.format(item.price)));
            const controls = element('div', 'cart-item__controls');
            for (const [action, text, label] of [['decrease', '−', 'Kurangi'], ['increase', '+', 'Tambah']]) {
                const button = element('button', 'quantity-button', text);
                button.type = 'button';
                button.dataset.cartAction = action;
                button.dataset.cartId = item.id;
                button.setAttribute('aria-label', `${label} jumlah ${item.name}`);
                button.disabled = action === 'decrease' ? item.quantity <= 1 : item.quantity >= item.maxQuantity;
                if (action === 'increase') controls.append(element('span', '', String(item.quantity)));
                controls.append(button);
            }
            const remove = element('button', 'cart-remove', 'Hapus');
            remove.type = 'button';
            remove.dataset.cartAction = 'remove';
            remove.dataset.cartId = item.id;
            remove.setAttribute('aria-label', `Hapus ${item.name} dari keranjang`);
            controls.append(remove);
            body.append(controls);
            row.append(picture, body);
            cartItems.append(row);
        });
    }

    function openCart() {
        if (!dialog || dialog.open) return;
        previousFocus = document.activeElement;
        renderCart();
        dialog.showModal();
        document.body.classList.add('modal-open');
    }

    function addToCart(button) {
        const card = button.closest('[data-product-card]');
        if (!card || button.disabled) return;
        const price = Number(card.dataset.productPrice);
        const stock = card.dataset.productStock === undefined ? 99 : Number(card.dataset.productStock);
        if (!Number.isFinite(price) || price < 0 || !Number.isFinite(stock) || stock < 1) {
            notify('Produk belum bisa ditambahkan ke keranjang.');
            return;
        }
        const maxQuantity = Math.min(99, Math.floor(stock));
        const existing = cart.find((item) => item.id === card.dataset.productId);
        if (existing) {
            existing.maxQuantity = maxQuantity;
            existing.price = price;
            if (existing.quantity >= maxQuantity) {
                notify('Jumlah sudah mencapai batas stok untuk demo ini.');
                return;
            }
            existing.quantity += 1;
        } else {
            cart.push({ id: card.dataset.productId, name: card.dataset.productName, price, image: imageUrl(card.dataset.productImage), quantity: 1, maxQuantity });
        }
        saveCart();
        notify(storageAvailable ? 'Produk ditambahkan ke keranjang demo.' : 'Produk ditambahkan. Browser ini tidak menyimpan keranjang setelah halaman ditutup.');
        if (button.hasAttribute('data-buy-now')) openCart();
    }

    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;
        if (button.hasAttribute('data-open-cart')) openCart();
        if (button.hasAttribute('data-add-to-cart')) addToCart(button);
        if (button.dataset.demoMessage) notify(button.dataset.demoMessage);
        if (button.dataset.cartAction) {
            const id = button.dataset.cartId;
            const action = button.dataset.cartAction;
            const item = cart.find((entry) => entry.id === id);
            if (!item) return;
            if (action === 'remove') cart = cart.filter((entry) => entry.id !== id);
            if (action === 'increase' && item.quantity < item.maxQuantity) item.quantity += 1;
            if (action === 'decrease' && item.quantity > 1) item.quantity -= 1;
            saveCart();
            const replacement = cartItems.querySelector(`[data-cart-id="${CSS.escape(id)}"][data-cart-action="${action}"]`);
            if (replacement && !replacement.disabled) replacement.focus();
            else dialog.querySelector('.cart-close').focus();
        }
    });

    dialog?.addEventListener('close', () => {
        document.body.classList.remove('modal-open');
        previousFocus?.focus();
    });
    dialog?.addEventListener('click', (event) => {
        const rect = dialog.getBoundingClientRect();
        const outside = event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
        if (event.target === dialog && outside) dialog.close();
    });

    /* Menu mobile bisa dipakai dengan mouse maupun keyboard. */
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const menu = document.getElementById('main-menu');
    function closeMenu() {
        menu?.classList.remove('is-open');
        menuToggle?.setAttribute('aria-expanded', 'false');
        menuToggle?.setAttribute('aria-label', 'Buka menu navigasi');
    }
    menuToggle?.addEventListener('click', () => {
        const open = menu.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(open));
        menuToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    });
    menu?.addEventListener('click', (event) => { if (event.target.closest('a')) closeMenu(); });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu?.classList.contains('is-open')) {
            closeMenu();
            menuToggle.focus();
        }
    });
    window.matchMedia('(min-width: 1200px)').addEventListener('change', (event) => { if (event.matches) closeMenu(); });

    /* Newsletter belum mengirim atau menyimpan alamat email. */
    document.querySelectorAll('[data-newsletter]').forEach((form) => {
        function previewNewsletter(event) {
            event.preventDefault();
            if (!form.reportValidity()) return;
            const status = form.querySelector('[data-newsletter-status]');
            status.textContent = 'Form ini masih demo. Alamat email belum disimpan atau dikirim.';
            status.hidden = false;
        }
        form.addEventListener('submit', previewNewsletter);
        form.querySelector('[data-newsletter-button]').addEventListener('click', previewNewsletter);
    });

    /* Filter berlaku pada produk yang sudah dimuat oleh ProductController. */
    const catalog = document.querySelector('[data-catalog]');
    if (catalog) {
        const cards = [...catalog.querySelectorAll('[data-product-card]')];
        const categorySelect = catalog.querySelector('[data-category-filter]');
        const search = document.getElementById('nav-search');
        const params = new URLSearchParams(window.location.search);
        let category = params.get('category') ?? '';
        let query = params.get('q') ?? '';
        if (search) search.value = query;
        const aliases = { hio: ['hio', 'incense', 'dupa'], lilin: ['lilin'], kertas: ['kertas', 'kim cua'], aksesoris: ['aksesoris', 'rupang', 'altar'], minyak: ['minyak'] };
        if (category && ![...categorySelect.options].some((option) => option.value === category)) {
            const option = new Option(`Kategori: ${category}`, category);
            categorySelect.add(option);
        }
        categorySelect.value = category;

        function filterProducts() {
            const keywords = aliases[normalize(category)] ?? [normalize(category)];
            let visible = 0;
            cards.forEach((card) => {
                const productCategory = normalize(card.dataset.productCategory);
                const text = normalize(`${card.dataset.productName} ${card.dataset.productCategory} ${card.querySelector('.product-card__description')?.textContent ?? ''}`);
                const categoryMatches = !category || keywords.some((keyword) => productCategory.includes(keyword));
                const queryMatches = !query || text.includes(normalize(query));
                card.hidden = !(categoryMatches && queryMatches);
                if (!card.hidden) visible += 1;
            });
            catalog.querySelector('[data-catalog-summary]').textContent = `${visible} dari ${cards.length} produk ditampilkan`;
            catalog.querySelector('[data-catalog-empty]').hidden = visible > 0;
            catalog.querySelector('[data-catalog-grid]').hidden = visible === 0;
            catalog.querySelector('[data-empty-message]').textContent = cards.length ? 'Tidak ada produk yang cocok. Coba kata kunci atau kategori lain.' : 'Koleksi sedang diperbarui. Silakan kunjungi kembali nanti.';
            const url = new URL(window.location.href);
            query ? url.searchParams.set('q', query) : url.searchParams.delete('q');
            category ? url.searchParams.set('category', category) : url.searchParams.delete('category');
            if (url.protocol !== 'file:') history.replaceState({}, '', url);
        }

        categorySelect.addEventListener('change', () => { category = categorySelect.value; filterProducts(); });
        search?.addEventListener('input', () => { query = search.value.trim(); filterProducts(); });
        document.querySelector('[data-search-form]')?.addEventListener('submit', (event) => {
            event.preventDefault();
            query = search.value.trim();
            filterProducts();
            catalog.scrollIntoView({ block: 'start', behavior: 'smooth' });
        });
        catalog.querySelector('[data-reset-filters]').addEventListener('click', () => {
            query = ''; category = ''; search.value = ''; categorySelect.value = '';
            filterProducts();
        });
        filterProducts();
    }

    document.querySelectorAll('[data-product-image-fallback]').forEach((image) => {
        function placeholder() {
            image.hidden = true;
            image.parentElement.querySelector('.product-image-placeholder')?.remove();
            image.parentElement.append(element('div', 'product-image-placeholder', 'Gambar belum tersedia'));
        }
        image.addEventListener('error', placeholder, { once: true });
        if (image.complete && !image.naturalWidth) placeholder();
    });

    window.addEventListener('storage', (event) => {
        if (event.key === storageKey || event.key === null) { cart = readCart(); renderCart(); }
    });
    renderCart();
})();
