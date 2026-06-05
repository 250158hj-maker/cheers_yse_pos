/**
 * public/js/register.js
 * レジ画面のフロントエンド制御（税率即時反映版）
 */
document.addEventListener('DOMContentLoaded', function() {
    // 要素の取得
    const categoryTabs = document.querySelectorAll('#category-tabs .nav-link');
    const productItems = document.querySelectorAll('.product-item');
    const productButtons = document.querySelectorAll('#product-grid button');
    const orderListBody = document.querySelector('#order-list-body');
    const totalAmountDisplay = document.querySelector('#total-amount');
    const receivedAmountDisplay = document.querySelector('#received-amount');
    const changeAmountDisplay = document.querySelector('#change-amount');
    const calcDisplay = document.querySelector('#calc-display');
    
    const numButtons = document.querySelectorAll('.num-btn');
    const calcButtons = document.querySelectorAll('.calc-btn');
    const checkoutBtn = document.querySelector('#checkout-btn');
    const checkoutForm = document.querySelector('#checkout-form');
    const orderDataInput = document.querySelector('#order-data-input');

    // 状態管理
    let currentOrder = [];
    let calcInput = '0';
    // PHPから渡された定数を使用、なければデフォルト値を設定
    const TAX_NORMAL = window.TAX_CONFIG ? (1 + window.TAX_CONFIG.NORMAL) : 1.10;
    const TAX_REDUCED = window.TAX_CONFIG ? (1 + window.TAX_CONFIG.REDUCED) : 1.08;
    
    let lastAppliedTaxRate = TAX_NORMAL; // 最後に適用した税率（新規追加用）

    // --- 1. カテゴリ切り替え ---
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const categoryId = this.getAttribute('data-category-id');
            productItems.forEach(item => {
                if (categoryId === 'all' || item.getAttribute('data-category-id') === categoryId) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });
    });

    // --- 2. 商品ボタン（即時追加） ---
    productButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-product-id');
            const name = this.getAttribute('data-product-name');
            const basePrice = parseInt(this.getAttribute('data-product-price'));

            // 最後に適用された税率で追加
            addToOrder(id, name, basePrice, 1);
        });
    });

    // --- 3. テンキー（預り金入力） ---
    numButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const val = this.getAttribute('data-value');
            let nextInput = (calcInput === '0') ? val : calcInput + val;
             
            // 【追加】上限バリデーション
             const total = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
             const nextReceived = parseInt(nextInput);
            
            // 条件: 10,000円以上 かつ 合計の3倍超え
            if (nextReceived >= 10000 && nextReceived > total * 3) {
                alert('預り金が過大です（10,000円以上かつ合計の3倍を超えています）');
                return; // 入力を反映させない
            }
   
            calcInput = nextInput;
            updateCalcDisplay();
            updateChange();
        });
    });

    // --- 4. 操作ボタン ---
    calcButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const op = this.getAttribute('data-op');
            const currentVal = parseInt(calcInput);

            switch(op) {
                case 'ac': // 全リセット
                    currentOrder = [];
                    calcInput = '0';
                    lastAppliedTaxRate = TAX_NORMAL;
                    renderOrder();
                    updateCalcDisplay();
                    updateChange();
                    break;
                case 'clear':
                    calcInput = '0';
                    updateCalcDisplay();
                    updateChange();
                    break;
                case 'tax8':
                    applyTaxToOrder(TAX_REDUCED, currentVal);
                    break;
                case 'tax10':
                    applyTaxToOrder(TAX_NORMAL, currentVal);
                    break;
            }
        });
    });

    /**
     * 明細に税率を反映させる
     */
    function applyTaxToOrder(rate, manualValue) {
        lastAppliedTaxRate = rate;

        // 1. 手入力がある場合はそれを追加（従来通り）
        if (manualValue > 0) {
            const priceWithTax = Math.floor(manualValue * rate);
            addToOrder(null, `手入力(${Math.round((rate-1)*100)}%)`, manualValue, 1, true);
            calcInput = '0';
            updateCalcDisplay();
        }

        // 2. 現在の全明細の価格を再計算して「反映」させる
        currentOrder.forEach(item => {
            item.price = Math.floor(item.basePrice * rate);
        });

        renderOrder();
    }

    /**
     * 注文リストに追加
     */
    function addToOrder(id, name, basePrice, quantity = 1, isManual = false) {
        const price = Math.floor(basePrice * lastAppliedTaxRate);
        
        const existingItem = (!isManual && id) ? currentOrder.find(item => item.id === id) : null;
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            currentOrder.push({ id, name, basePrice, price, quantity });
        }
        renderOrder();
    }

    function updateCalcDisplay() {
        calcDisplay.textContent = parseInt(calcInput).toLocaleString();
    }

    /**
     * 明細描画と合計更新
     */
    function renderOrder() {
        orderListBody.innerHTML = '';
        let total = 0;

        currentOrder.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            const taxPercent = Math.round((item.price / item.basePrice - 1) * 100);
            const taxBadgeClass = taxPercent === 8 ? 'bg-warning text-dark' : 'bg-secondary';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    ${item.name}
                    <span class="badge ${taxBadgeClass}" style="font-size: 0.6rem;">${taxPercent}%</span>
                </td>
                <td class="text-end">¥${item.price.toLocaleString()}</td>
                <td>
                    <div class="d-flex align-items-center justify-content-center">
                        <button class="btn btn-sm btn-outline-secondary me-1 py-0 px-1 update-qty" data-index="${index}" data-delta="-1">-</button>
                        <span>${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-1 py-0 px-1 update-qty" data-index="${index}" data-delta="1">+</button>
                    </div>
                </td>
                <td class="text-end">¥${subtotal.toLocaleString()}</td>
            `;
            orderListBody.appendChild(tr);
        });

        totalAmountDisplay.textContent = `¥ ${total.toLocaleString()}`;
        updateChange();

        document.querySelectorAll('.update-qty').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                const delta = parseInt(this.getAttribute('data-delta'));
                updateQuantity(index, delta);
            });
        });
    }

    function updateQuantity(index, delta) {
        currentOrder[index].quantity += delta;
        if (currentOrder[index].quantity <= 0) {
            currentOrder.splice(index, 1);
        }
        renderOrder();
    }

    function updateChange() {
        const total = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const received = parseInt(calcInput);
        const change = received - total;
        
        receivedAmountDisplay.textContent = `¥ ${received.toLocaleString()}`;
        changeAmountDisplay.textContent = `¥ ${(change > 0 ? change : 0).toLocaleString()}`;
    }

    // --- 5. 計上処理 ---
    checkoutBtn.addEventListener('click', function() {
        if (currentOrder.length === 0) {
            alert('注文がありません');
            return;
        }
        const total = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const received = parseInt(calcInput);
        if (received < total) {
            alert('預り金が不足しています');
            return;
        }
        if (received >= 10000 && received > total * 3) {
            alert('預り金が過大です。入力し直してください。');
            return;
        }
        if (!confirm('計上しますか？')) return;

        const payload = {
            items: currentOrder,
            total_amount: total,
            received_amount: received,
            change_amount: received - total
        };
        orderDataInput.value = JSON.stringify(payload);
        checkoutForm.submit();
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'F10') {
            e.preventDefault();
            checkoutBtn.click();
        }
    });
});
