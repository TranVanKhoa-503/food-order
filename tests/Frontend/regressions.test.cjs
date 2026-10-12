const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const {File} = require('node:buffer');

const root = path.resolve(__dirname, '../..');
const read = name => fs.readFileSync(path.join(root, name), 'utf8');
const script = name => read(name).match(/<script>([\s\S]*?)<\/script>/)[1];
const deferred = () => {
    let resolve;
    const promise = new Promise(done => { resolve = done; });
    return {promise, resolve};
};

function foodForm(fetch) {
    const elements = {
        foodId: {value: ''}, foodCategory: {value: '1'}, foodName: {value: 'Món test'},
        foodPrice: {value: '50000'}, foodDesc: {value: 'Mô tả'}, foodAvailable: {value: 'true'},
        foodImageUpload: {files: []}, foodImage: {value: ''}
    };
    const button = {disabled: false};
    const state = {alerts: [], reloads: 0};
    const context = {
        FormData,
        document: {
            getElementById: id => elements[id],
            querySelector: selector => selector.startsWith('#foodForm') ? button : {getAttribute: () => 'test-token'}
        },
        fetch,
        alert: message => state.alerts.push(message),
        window: {location: {reload: () => state.reloads++}, addEventListener() {}}
    };
    vm.createContext(context);
    vm.runInContext(script('resources/views/admin/foods/index.blade.php'), context);
    return {elements, button, state, submit: () => context.handleFoodSubmit({preventDefault() {}})};
}

test('food form sends multipart fields, numeric boolean, image and PUT override', async () => {
    const requests = [];
    const form = foodForm(async (url, options) => {
        requests.push({url, options});
        return {ok: false, json: async () => ({message: 'Validation test'})};
    });
    form.elements.foodImageUpload.files = [new File(['image'], 'food.png', {type: 'image/png'})];
    await form.submit();
    const create = requests[0].options;
    assert.ok(create.body instanceof FormData);
    assert.equal(create.body.get('name'), 'Món test');
    assert.equal(create.body.get('is_available'), '1');
    assert.equal(create.body.get('image_upload').name, 'food.png');
    assert.equal(create.headers['Content-Type'], undefined);

    form.elements.foodId.value = '4';
    form.elements.foodAvailable.value = 'false';
    await form.submit();
    assert.equal(requests[1].url, '/api/v1/admin/foods/4');
    assert.equal(requests[1].options.method, 'POST');
    assert.equal(requests[1].options.body.get('_method'), 'PUT');
    assert.equal(requests[1].options.body.get('is_available'), '0');
});

test('food form sends once while pending and can retry after a failure', async () => {
    const pending = deferred();
    let requests = 0;
    const form = foodForm(() => { requests++; return pending.promise; });
    const first = form.submit();
    await form.submit();
    assert.equal(requests, 1);
    assert.equal(form.button.disabled, true);
    pending.resolve({ok: false, json: async () => ({message: 'Try again'})});
    await first;
    assert.equal(form.button.disabled, false);
    await form.submit();
    assert.equal(requests, 2);
});

function discount(voucher, subtotal) {
    const source = read('resources/views/layouts/app.blade.php');
    const fn = source.slice(source.indexOf('function calculateDiscount('), source.indexOf('function selectedShippingFee('));
    const context = vm.createContext({});
    vm.runInContext(`let appliedVoucher = ${JSON.stringify(voucher)};\n${fn}`, context);
    return context.calculateDiscount(subtotal);
}

test('voucher preview obeys the cap, minimum subtotal, zero cap and no-cap case', () => {
    const voucher = {code: 'CAP20', discount_type: 'percent', discount_value: 20, min_order_value: 100000, max_discount_amount: 10000};
    assert.equal(discount(voucher, 200000), 10000);
    assert.equal(discount(voucher, 50000), 0);
    assert.equal(discount({...voucher, max_discount_amount: 0}, 200000), 0);
    assert.equal(discount({...voucher, max_discount_amount: null}, 200000), 40000);
});

test('voucher editor preserves store-local date and time regardless of browser timezone', () => {
    const source = script('resources/views/admin/vouchers/index.blade.php')
        .replace("@json(config('app.timezone'))", JSON.stringify('Asia/Ho_Chi_Minh'));
    const context = vm.createContext({});
    vm.runInContext(source, context);
    assert.equal(context.toLocalDate('2026-10-20T06:00:00.000000Z'), '2026-10-20T13:00');
    assert.equal(context.toLocalDate('2026-10-19T18:00:00.000000Z'), '2026-10-20T01:00');
    assert.equal(context.toLocalDate(null), '');
});

function cancellation(fetch) {
    const state = {reloads: 0, alerts: []};
    const button = {disabled: false};
    const context = {
        confirm: () => true, fetch, alert: message => state.alerts.push(message),
        document: {querySelector: () => ({getAttribute: () => 'test-token'})},
        window: {location: {reload: () => state.reloads++}, addEventListener() {}}
    };
    vm.createContext(context);
    vm.runInContext(script('resources/views/partials/cancel-order-script.blade.php'), context);
    return {button, state, cancel: () => context.cancelOrder(7, button)};
}

test('customer cancellation submits once while pending and keeps the button locked until reload', async () => {
    const pending = deferred();
    let requests = 0;
    const form = cancellation(() => { requests++; return pending.promise; });
    const first = form.cancel();
    await form.cancel();
    assert.equal(requests, 1);
    pending.resolve({ok: true});
    await first;
    await form.cancel();
    assert.equal(requests, 1);
    assert.equal(form.button.disabled, true);
    assert.equal(form.state.reloads, 1);
});

test('stale customer cancellation refreshes the order instead of leaving an error on the old page', async () => {
    const form = cancellation(async () => ({ok: false, status: 409}));
    await form.cancel();
    assert.equal(form.state.reloads, 1);
    assert.equal(form.state.alerts.length, 1);
});

test('customer cancellation can retry after a network failure', async () => {
    let requests = 0;
    const form = cancellation(async () => { requests++; throw new Error('Offline'); });
    await form.cancel();
    assert.equal(form.button.disabled, false);
    await form.cancel();
    assert.equal(requests, 2);
    assert.equal(form.state.reloads, 0);
});

test('native expense and delivery forms prevent duplicate submissions and recover after Back', () => {
    const events = {};
    const button = {disabled: false};
    const form = {
        dataset: {}, matches: () => true,
        querySelectorAll: () => [button], setAttribute() {}, removeAttribute() {}
    };
    const context = vm.createContext({
        document: {addEventListener: (name, fn) => { events[name] = fn; }, querySelectorAll: () => [form]},
        window: {addEventListener: (name, fn) => { events[name] = fn; }, confirm: () => true}
    });
    vm.runInContext(script('resources/views/partials/submit-guard.blade.php'), context);
    const event = () => ({target: form, defaultPrevented: false, preventDefault() { this.defaultPrevented = true; }});
    const first = event();
    events.submit(first);
    const second = event();
    events.submit(second);
    assert.equal(first.defaultPrevented, false);
    assert.equal(second.defaultPrevented, true);
    assert.equal(button.disabled, true);
    events.pageshow();
    assert.equal(button.disabled, false);
    assert.equal(form.dataset.submitting, undefined);
    assert.match(read('resources/views/admin/expenses/index.blade.php'), /data-prevent-double-submit/);
});

test('public and admin layouts load the submit guard and protect their logout forms', () => {
    for (const name of ['resources/views/layouts/app.blade.php', 'resources/views/admin/layout.blade.php']) {
        const source = read(name);
        assert.ok(source.includes("@include('partials.submit-guard')"));
        assert.match(source, /<form[^>]*route\('logout'\)[^>]*data-prevent-double-submit/);
    }
});

test('food detail quantity is added to the cart and never exceeds 99', () => {
    const source = read('resources/views/layouts/app.blade.php');
    const fn = source.slice(source.indexOf('function addToCart('), source.indexOf('function changeQty('));
    const state = {saved: 0, messages: []};
    const context = vm.createContext({
        saveCart: () => state.saved++, showToast: message => state.messages.push(message)
    });
    vm.runInContext(`let cart = [];\n${fn}`, context);
    context.addToCart(1, 'Cơm gà', 50000, null, 3);
    assert.equal(vm.runInContext('cart[0].quantity', context), 3);
    context.addToCart(1, 'Cơm gà', 50000, null, 2);
    assert.equal(vm.runInContext('cart[0].quantity', context), 5);
    context.addToCart(1, 'Cơm gà', 50000, null, 99);
    assert.equal(vm.runInContext('cart[0].quantity', context), 99);
    const saved = state.saved;
    context.addToCart(1, 'Cơm gà', 50000, null, 1);
    assert.equal(vm.runInContext('cart[0].quantity', context), 99);
    assert.equal(state.saved, saved);
    context.addToCart(2, 'Món khác', 20000, null);
    assert.equal(vm.runInContext('cart[1].quantity', context), 1);
});
