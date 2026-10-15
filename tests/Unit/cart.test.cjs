const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../../public/js/cahaya.js'), 'utf8');
const storageKey = 'cahaya-sembahyang-cart-v1';

function storefront({ quantity = '1', stock = '5', detail = true } = {}) {
    const storage = new Map();
    const handlers = new Map();
    const input = { value: quantity, reportValidity() {} };
    const card = {
        dataset: { productId: 'database-1', productName: 'Hio Cendana', productPrice: '125000', productStock: stock, productImage: '/hio.jpg' },
        querySelector: () => detail ? input : null,
    };
    const button = {
        disabled: false,
        dataset: {},
        closest: (selector) => selector === 'button' ? button : card,
        hasAttribute: (name) => name === 'data-add-to-cart',
    };
    const document = {
        getElementById: () => null,
        querySelector: () => null,
        querySelectorAll: () => [],
        addEventListener: (name, callback) => handlers.set(name, callback),
    };
    vm.runInNewContext(source, {
        document, URL, Intl, setTimeout, clearTimeout,
        localStorage: { getItem: (key) => storage.get(key) ?? null, setItem: (key, value) => storage.set(key, value) },
        window: { location: { href: 'http://localhost/' }, addEventListener() {}, matchMedia: () => ({ addEventListener() {} }) },
    });
    return {
        input,
        click: () => handlers.get('click')({ target: button }),
        cart: () => JSON.parse(storage.get(storageKey) ?? '[]'),
    };
}

test('detail adds the selected quantity and accumulates within stock', () => {
    const page = storefront({ quantity: '3' });
    page.click();
    assert.equal(page.cart()[0].quantity, 3);
    page.input.value = '2';
    page.click();
    assert.equal(page.cart()[0].quantity, 5);
});

test('adding beyond stock keeps the existing cart unchanged', () => {
    const page = storefront({ quantity: '3' });
    page.click();
    page.click();
    assert.equal(page.cart()[0].quantity, 3);
});

test('invalid quantities and unavailable products do not enter the cart', () => {
    for (const quantity of ['0', '-1', '1.5', '', 'abc', '6']) {
        const page = storefront({ quantity });
        page.click();
        assert.deepEqual(page.cart(), [], `quantity: ${quantity}`);
    }
    const soldOut = storefront({ stock: '0' });
    soldOut.click();
    assert.deepEqual(soldOut.cart(), []);
});

test('the cart limit remains 99 even when stock is larger', () => {
    const page = storefront({ quantity: '99', stock: '150' });
    page.click();
    page.input.value = '1';
    page.click();
    assert.equal(page.cart()[0].quantity, 99);
});

test('catalog cards still add one unit without a quantity input', () => {
    const page = storefront({ detail: false });
    page.click();
    assert.equal(page.cart()[0].quantity, 1);
});
