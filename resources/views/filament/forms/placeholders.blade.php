<style>
.placeholder-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    cursor: pointer;
    transition: all 0.15s ease;
}
.placeholder-badge:hover {
    background-color: #dbeafe;
    border-color: #93c5fd;
    transform: translateY(-1px);
}
.placeholder-badge:active {
    transform: translateY(0);
}
.dark .placeholder-badge {
    background-color: rgba(59, 130, 246, 0.15);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.3);
}
.dark .placeholder-badge:hover {
    background-color: rgba(59, 130, 246, 0.25);
    border-color: rgba(59, 130, 246, 0.5);
}
.placeholder-toast {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    background-color: #22c55e;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 50;
    font-size: 0.875rem;
    font-weight: 500;
}
.placeholder-toast.show {
    transform: translateY(0);
    opacity: 1;
}
</style>

<div class="mb-3">
    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2 block">Placeholders <span class="text-gray-400 font-normal">(click to copy)</span></label>
    <div class="flex flex-wrap gap-2" id="placeholder-badges">
        <button type="button" onclick="copyPlaceholder('customer_name')" class="placeholder-badge">Customer Name</button>
        <button type="button" onclick="copyPlaceholder('email')" class="placeholder-badge">Email</button>
        <button type="button" onclick="copyPlaceholder('order_id')" class="placeholder-badge">Order ID</button>
        <button type="button" onclick="copyPlaceholder('order_total')" class="placeholder-badge">Order Total</button>
        <button type="button" onclick="copyPlaceholder('payment_url')" class="placeholder-badge">Payment URL</button>
        <button type="button" onclick="copyPlaceholder('payment_method')" class="placeholder-badge">Payment Method</button>
        <button type="button" onclick="copyPlaceholder('transaction_date')" class="placeholder-badge">Transaction Date</button>
        <button type="button" onclick="copyPlaceholder('expiry_time')" class="placeholder-badge">Expiry Time</button>
        <button type="button" onclick="copyPlaceholder('reset_url')" class="placeholder-badge">Reset URL</button>
        <button type="button" onclick="copyPlaceholder('expiry_minutes')" class="placeholder-badge">Expiry Minutes</button>
        <button type="button" onclick="copyPlaceholder('old_status')" class="placeholder-badge">Old Status</button>
        <button type="button" onclick="copyPlaceholder('new_status')" class="placeholder-badge">New Status</button>
        <button type="button" onclick="copyPlaceholder('tracking_number')" class="placeholder-badge">Tracking Number</button>
        <button type="button" onclick="copyPlaceholder('courier_name')" class="placeholder-badge">Courier Name</button>
        <button type="button" onclick="copyPlaceholder('reseller_name')" class="placeholder-badge">Reseller Name</button>
        <button type="button" onclick="copyPlaceholder('product_list')" class="placeholder-badge">Product List</button>
        <button type="button" onclick="copyPlaceholder('shipping_address')" class="placeholder-badge">Shipping Address</button>
        <button type="button" onclick="copyPlaceholder('order_date')" class="placeholder-badge">Order Date</button>
        <button type="button" onclick="copyPlaceholder('website_name')" class="placeholder-badge">Website Name</button>
    </div>
    <div id="placeholder-toast" class="placeholder-toast">
        <span id="toast-message">Copied!</span>
    </div>
</div>

@push('scripts')
<script>
function copyPlaceholder(key) {
    var leftBrace = '{';
    var rightBrace = '}';
    var placeholder = leftBrace + leftBrace + key + rightBrace + rightBrace;
    
    navigator.clipboard.writeText(placeholder).then(function() {
        showToast('Copied: ' + placeholder);
    }).catch(function(err) {
        fallbackCopy(placeholder);
        showToast('Copied: ' + placeholder);
    });
}

function fallbackCopy(text) {
    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}

function showToast(message) {
    var toast = document.getElementById('placeholder-toast');
    var toastMessage = document.getElementById('toast-message');
    
    toastMessage.textContent = message;
    toast.classList.add('show');
    
    setTimeout(function() {
        toast.classList.remove('show');
    }, 2000);
}
</script>
@endpush