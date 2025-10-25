@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-gray-900">Menu Pempek</h1>
        <p class="mt-1 text-sm text-gray-600">Pilih menu favorit Anda dan masukkan ke keranjang</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Menu List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Menu</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($menus as $menu)
                        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-200">
                            @if($menu->image)
                            <img src="{{ Storage::url($menu->image) }}"
                                 alt="{{ $menu->name }}"
                                 class="w-full h-48 object-cover">
                            @else
                            <div class="w-full h-48 bg-gradient-to-br from-orange-200 to-red-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif
                            
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $menu->name }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $menu->description }}</p>
                                <p class="text-lg font-bold text-orange-600 mb-3">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                
                                @if($menu->is_available)
                                <div class="flex items-center space-x-2">
                                    <select class="quantity-select w-20 px-2 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                            data-menu-id="{{ $menu->id }}"
                                            data-menu-name="{{ $menu->name }}"
                                            data-menu-price="{{ $menu->price }}"
                                            data-menu-image="{{ $menu->image ? Storage::url($menu->image) : '' }}">
                                        <option value="0">0</option>
                                        @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <button class="add-to-cart-btn flex-1 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                            data-menu-id="{{ $menu->id }}"
                                            disabled>
                                        Tambah ke Keranjang
                                    </button>
                                </div>
                                @else
                                <button class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed" disabled>
                                    Tidak Tersedia
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($menus->hasPages())
                    <div class="mt-6">
                        {{ $menus->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Shopping Cart -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Keranjang Belanja</h3>
                    <p class="text-sm text-gray-600">
                        <span id="cart-count">0</span> item dipilih
                    </p>
                </div>
                <div class="p-6">
                    <div id="cart-items" class="space-y-4 mb-6">
                        <!-- Cart items will be populated here -->
                    </div>

                    <!-- Empty Cart Message -->
                    <div id="empty-cart" class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0L17 21"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Keranjang masih kosong</p>
                    </div>

                    <!-- Cart Summary -->
                    <div id="cart-summary" class="hidden">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium" id="cart-subtotal">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Ongkos Kirim:</span>
                                <span class="font-medium">Rp 5.000</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total:</span>
                                <span class="text-lg font-bold text-orange-600" id="cart-total">Rp 5.000</span>
                            </div>
                        </div>

                        <form id="checkout-form" action="{{ route('customer.order.create') }}" method="GET">
                            <input type="hidden" name="cart_data" id="cart-data">
                            <button type="submit" id="checkout-btn" 
                                    class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                                Checkout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global cart object
let cart = {};
const deliveryFee = 5000;

// Initialize cart functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart initialized');
    
    // Quantity select change handler
    document.querySelectorAll('.quantity-select').forEach(select => {
        select.addEventListener('change', function() {
            const btn = this.parentElement.querySelector('.add-to-cart-btn');
            btn.disabled = this.value === '0';
            console.log('Quantity changed:', this.value);
        });
    });

    // Add to cart button handler
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const menuId = this.dataset.menuId;
            const select = this.parentElement.querySelector('.quantity-select');
            const quantity = parseInt(select.value);
            
            console.log('Adding to cart:', {menuId, quantity, data: select.dataset});
            
            if (quantity > 0) {
                addToCart(menuId, quantity, select.dataset);
                select.value = '0';
                this.disabled = true;
                
                // Show success feedback
                const originalText = this.textContent;
                this.textContent = 'Ditambahkan!';
                this.classList.remove('bg-orange-600');
                this.classList.add('bg-green-600');
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.classList.remove('bg-green-600');
                    this.classList.add('bg-orange-600');
                }, 1000);
            }
        });
    });

    // Checkout form submission
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            alert('Keranjang masih kosong!');
            return;
        }
        
        // Set cart data to hidden input
        document.getElementById('cart-data').value = JSON.stringify(Object.values(cart));
        console.log('Checkout data:', Object.values(cart));
    });
});

// Add item to cart
function addToCart(menuId, quantity, menuData) {
    console.log('addToCart called:', {menuId, quantity, menuData});
    
    if (cart[menuId]) {
        cart[menuId].quantity += quantity;
        console.log('Updated existing item:', cart[menuId]);
    } else {
        cart[menuId] = {
            id: parseInt(menuId),
            name: menuData.menuName,
            price: parseInt(menuData.menuPrice),
            quantity: quantity,
            image: menuData.menuImage
        };
        console.log('Added new item:', cart[menuId]);
    }
    
    console.log('Current cart:', cart);
    updateCartDisplay();
}

// Remove item from cart
function removeFromCart(menuId) {
    console.log('Removing from cart:', menuId);
    delete cart[menuId];
    updateCartDisplay();
}

// Update item quantity in cart
function updateCartQuantity(menuId, quantity) {
    console.log('Updating quantity:', {menuId, quantity});
    
    if (quantity <= 0) {
        removeFromCart(menuId);
    } else {
        if (cart[menuId]) {
            cart[menuId].quantity = quantity;
            updateCartDisplay();
        }
    }
}

// Update cart display
function updateCartDisplay() {
    console.log('Updating cart display...');
    
    const cartItems = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const cartSummary = document.getElementById('cart-summary');
    const emptyCart = document.getElementById('empty-cart');
    
    const totalItems = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
    const subtotal = Object.values(cart).reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const total = subtotal + deliveryFee;
    
    console.log('Cart totals:', {totalItems, subtotal, total});
    console.log('Cart contents:', cart);
    
    // Update item count
    cartCount.textContent = totalItems;
    
    if (totalItems === 0) {
        // Show empty cart
        emptyCart.classList.remove('hidden');
        cartSummary.classList.add('hidden');
        cartItems.innerHTML = '';
        console.log('Showing empty cart');
    } else {
        // Hide empty cart, show items and summary
        emptyCart.classList.add('hidden');
        cartSummary.classList.remove('hidden');
        
        // Generate cart items HTML
        const cartItemsHTML = Object.values(cart).map(item => {
            console.log('Rendering item:', item);
            return `
                <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                    ${item.image ? 
                        `<img src="${item.image}" alt="${item.name}" class="w-12 h-12 rounded-lg object-cover">` :
                        `<div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>`
                    }
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">${item.name}</h4>
                        <p class="text-sm text-gray-600">Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="decrease-btn w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-300" 
                                data-menu-id="${item.id}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="w-8 text-center text-sm font-medium">${item.quantity}</span>
                        <button class="increase-btn w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-300"
                                data-menu-id="${item.id}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        
        cartItems.innerHTML = cartItemsHTML;
        
        // Add event listeners to quantity buttons
        cartItems.querySelectorAll('.decrease-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const menuId = this.dataset.menuId;
                const currentQuantity = cart[menuId].quantity;
                updateCartQuantity(menuId, currentQuantity - 1);
            });
        });
        
        cartItems.querySelectorAll('.increase-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const menuId = this.dataset.menuId;
                const currentQuantity = cart[menuId].quantity;
                updateCartQuantity(menuId, currentQuantity + 1);
            });
        });
        
        // Update pricing
        document.getElementById('cart-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('cart-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        
        console.log('Cart display updated successfully');
    }
}
</script>

@endsection