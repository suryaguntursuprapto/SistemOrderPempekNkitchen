@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="py-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pembayaran Pesanan</h1>
            <p class="text-sm text-gray-600">Order #{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pesanan</h3>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Order Items -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Item Pesanan</h4>
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg mb-3 transition-all hover:bg-gray-100">
                            @if($item->menu->image)
                                <img src="{{ Storage::url($item->menu->image) }}" 
                                     alt="{{ $item->menu->name }}" 
                                     class="w-16 h-16 rounded-lg object-cover shadow-sm">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-200 to-red-200 rounded-lg flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900">{{ $item->menu->name }}</h5>
                                <p class="text-sm text-gray-600">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                <p class="font-semibold text-orange-600">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pricing Breakdown -->
                <div class="border-t pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ongkos Kirim:</span>
                            <span class="font-medium">Rp {{ number_format(5000, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total:</span>
                                <span class="text-xl font-bold text-gray-900">
                                    Rp {{ number_format($order->total_amount + 5000, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pembayaran
                </h3>
            </div>
            
            <div class="p-6">
                <!-- Payment Methods Info -->
                <div class="text-center mb-6">
                    <div class="w-20 h-20 custom-gradient rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Pembayaran Aman & Mudah</h4>
                    <p class="text-gray-600 mb-4">Berbagai metode pembayaran tersedia untuk kemudahan Anda</p>
                    
                    <!-- Payment Method Icons -->
                    <div class="flex justify-center space-x-4 mb-6">
                        <div class="bg-blue-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <path fill="white" d="M4 6v12h16V6H4z"/>
                            </svg>
                        </div>
                        <div class="bg-green-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                <path fill="white" d="M12 4c4.41 0 8 3.59 8 8s-3.59 8-8 8-8-3.59-8-8 3.59-8 8-8z"/>
                            </svg>
                        </div>
                        <div class="bg-purple-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Indicator -->
                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                    <div class="flex items-center justify-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></div>
                        <span class="text-sm text-blue-800 font-medium" id="payment-status">Memuat sistem pembayaran...</span>
                    </div>
                </div>

                <!-- Payment Button -->
                <div class="text-center">
                    @if(isset($snapToken))
                        <button id="pay-button" 
                                class="w-full custom-gradient hover:shadow-2xl text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg transform hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 group"
                                disabled>
                            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="font-bold">Memuat...</span>
                        </button>
                        
                        <!-- Alternative Payment Button (Hidden initially) -->
                        <button id="alternative-pay-button" 
                                class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg transform hover:-translate-y-1 active:translate-y-0 items-center justify-center gap-3 mt-3 hidden group"
                                onclick="openAlternativePayment()">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span class="font-bold">Buka di Tab Baru</span>
                        </button>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Sistem Pembayaran Tidak Tersedia</h3>
                            <p class="text-gray-600 mb-4">Silakan hubungi customer service untuk bantuan</p>
                            <a href="{{ route('customer.order.show', $order) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Detail Pesanan
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Payment Security Info -->
                @if(isset($snapToken))
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600 font-medium">Transaksi Aman</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600 font-medium">SSL Encrypted</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600 font-medium">Multiple Payment</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Back Button -->
                <div class="mt-6">
                    <a href="{{ route('customer.order.show', $order) }}" 
                       class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200 text-center block border border-gray-300 hover:border-gray-400 flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Detail Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Payment Script -->
<script type="text/javascript">
class MidtransPaymentHandler {
    constructor() {
        this.snapToken = '{{ $snapToken ?? '' }}';
        this.clientKey = '{{ config("services.midtrans.client_key") }}';
        this.isProduction = {{ config('services.midtrans.is_production') ? 'true' : 'false' }};
        this.snapJsUrl = this.isProduction 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
        
        this.snapJsLoaded = false;
        this.snapJsLoadAttempts = 0;
        this.maxLoadAttempts = 3;
        this.isProcessing = false;
        
        this.init();
    }
    
    init() {
        if (this.snapToken) {
            this.updatePaymentStatus('Memuat sistem pembayaran...');
            this.loadSnapJs();
        } else {
            this.updatePaymentStatus('Token pembayaran tidak tersedia');
        }
        
        // Timeout checker
        setTimeout(() => {
            if (!this.snapJsLoaded) {
                this.updatePaymentStatus('Sistem pembayaran lambat dimuat. Coba refresh halaman.');
                this.showAlternativeButton();
            }
        }, 10000);
    }
    
    updatePaymentStatus(message) {
        const element = document.getElementById('payment-status');
        if (element) {
            element.textContent = message;
        }
    }
    
    showAlternativeButton() {
        const altButton = document.getElementById('alternative-pay-button');
        if (altButton) {
            altButton.classList.remove('hidden');
            altButton.classList.add('flex');
        }
    }
    
    hideAlternativeButton() {
        const altButton = document.getElementById('alternative-pay-button');
        if (altButton) {
            altButton.classList.add('hidden');
            altButton.classList.remove('flex');
        }
    }
    
    resetButton(button) {
        if (button) {
            button.disabled = false;
            button.innerHTML = `
                <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="font-bold">Bayar Sekarang</span>
            `;
            this.isProcessing = false;
        }
    }
    
    loadSnapJs() {
        if (this.snapJsLoadAttempts >= this.maxLoadAttempts) {
            this.updatePaymentStatus('Gagal memuat sistem pembayaran. Silakan refresh halaman.');
            this.showAlternativeButton();
            return;
        }

        this.snapJsLoadAttempts++;
        
        // Remove existing script
        const existingScript = document.querySelector('script[src*="snap.js"]');
        if (existingScript) {
            existingScript.remove();
        }
        
        const snapScript = document.createElement('script');
        snapScript.src = this.snapJsUrl;
        snapScript.setAttribute('data-client-key', this.clientKey);
        snapScript.async = true;
        
        snapScript.onload = () => {
            this.snapJsLoaded = true;
            this.updatePaymentStatus('Sistem pembayaran siap');
            setTimeout(() => this.initializePaymentButton(), 500);
        };
        
        snapScript.onerror = () => {
            if (this.snapJsLoadAttempts < this.maxLoadAttempts) {
                this.updatePaymentStatus(`Mencoba lagi... (${this.snapJsLoadAttempts}/${this.maxLoadAttempts})`);
                setTimeout(() => this.loadSnapJs(), 2000);
            } else {
                this.updatePaymentStatus('Gagal memuat sistem pembayaran');
                this.showAlternativeButton();
            }
        };
        
        document.head.appendChild(snapScript);
    }
    
    initializePaymentButton() {
        const payButton = document.getElementById('pay-button');
        
        if (payButton) {
            // Enable button
            payButton.disabled = false;
            payButton.innerHTML = `
                <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="font-bold">Bayar Sekarang</span>
            `;
            
            // Add click handler
            payButton.addEventListener('click', (e) => this.handlePaymentClick(e));
        }
    }
    
    handlePaymentClick(e) {
        e.preventDefault();
        
        if (this.isProcessing) return;
        
        this.isProcessing = true;
        const payButton = document.getElementById('pay-button');
        
        // Show loading state
        payButton.disabled = true;
        payButton.innerHTML = `
            <svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-bold">Memproses...</span>
        `;
        
        this.updatePaymentStatus('Membuka halaman pembayaran...');
        
        // Quick timeout for popup detection
        const popupTimeout = setTimeout(() => {
            this.checkPopupAndFallback();
        }, 3000);
        
        try {
            window.snap.pay(this.snapToken, {
                onSuccess: (result) => {
                    clearTimeout(popupTimeout);
                    this.updatePaymentStatus('Pembayaran berhasil!');
                    window.location.href = '{{ route("customer.midtrans.finish") }}?order_id=' + result.order_id;
                },
                onPending: (result) => {
                    clearTimeout(popupTimeout);
                    this.updatePaymentStatus('Pembayaran pending...');
                    window.location.href = '{{ route("customer.midtrans.unfinish") }}?order_id=' + result.order_id;
                },
                onError: (result) => {
                    clearTimeout(popupTimeout);
                    this.updatePaymentStatus('Pembayaran gagal. Silakan coba lagi.');
                    
                    let errorMessage = 'Pembayaran gagal';
                    if (result && result.status_message) {
                        errorMessage += ': ' + result.status_message;
                    }
                    
                    // Show alternative immediately on error
                    this.showAlternativeButton();
                    window.createToast('error', 'Pembayaran Gagal', errorMessage);
                    this.resetButton(payButton);
                },
                onClose: () => {
                    clearTimeout(popupTimeout);
                    this.updatePaymentStatus('Sistem pembayaran siap');
                    this.resetButton(payButton);
                }
            });
            
        } catch (error) {
            clearTimeout(popupTimeout);
            this.updatePaymentStatus('Terjadi kesalahan. Coba metode alternatif.');
            this.showAlternativeButton();
            window.createToast('error', 'Error', 'Terjadi kesalahan: ' + error.message);
            this.resetButton(payButton);
        }
    }
    
    checkPopupAndFallback() {
        // Check for popup elements
        const snapElements = [
            document.querySelector('.snap-overlay'),
            document.querySelector('[id*="snap"]'), 
            document.querySelector('[class*="snap"]'),
            document.querySelector('iframe[src*="midtrans"]'),
            document.querySelector('iframe[src*="snap"]')
        ].filter(el => el !== null);
        
        if (snapElements.length > 0) {
            // Check if any iframe is visible
            let visibleIframe = false;
            snapElements.forEach((el) => {
                if (el.tagName === 'IFRAME') {
                    const styles = window.getComputedStyle(el);
                    if (styles.display !== 'none' && 
                        styles.visibility !== 'hidden' && 
                        parseFloat(styles.opacity) > 0.1) {
                        visibleIframe = true;
                    }
                }
            });
            
            if (!visibleIframe) {
                // Popup exists but not visible - show alternative
                this.updatePaymentStatus('Popup terdeteksi tapi tidak terlihat');
                this.showAlternativeButton();
                
                // Try to force show with aggressive styling
                this.forceShowPopup(snapElements);
            }
        } else {
            // No popup found - show alternative immediately
            this.updatePaymentStatus('Popup tidak dapat dibuka');
            this.showAlternativeButton();
        }
    }
    
    forceShowPopup(elements) {
        let fixed = false;
        elements.forEach((el, index) => {
            if (el.tagName === 'IFRAME' && el.src && el.src.includes('midtrans')) {
                // More aggressive CSS override
                el.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    z-index: 999999 !important;
                    position: fixed !important;
                    top: 50% !important;
                    left: 50% !important;
                    transform: translate(-50%, -50%) !important;
                    width: 90% !important;
                    max-width: 600px !important;
                    height: 80vh !important;
                    border: 2px solid #007bff !important;
                    border-radius: 8px !important;
                    box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
                    background: white !important;
                `;
                
                // Fix parent containers
                let parent = el.parentElement;
                let level = 0;
                while (parent && parent !== document.body && level < 3) {
                    parent.style.cssText = `
                        display: block !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                        z-index: 999998 !important;
                    `;
                    parent = parent.parentElement;
                    level++;
                }
                
                fixed = true;
            }
        });
        
        if (fixed) {
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.style.cssText = `
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: rgba(0,0,0,0.7) !important;
                z-index: 999997 !important;
            `;
            document.body.appendChild(backdrop);
            
            this.updatePaymentStatus('Popup dipaksa tampil');
            
            // Auto-remove backdrop if payment completes
            setTimeout(() => {
                if (document.body.contains(backdrop)) {
                    backdrop.remove();
                }
            }, 30000);
        }
    }
    
    openAlternativePayment() {
        const redirectUrl = `${this.isProduction ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com'}/snap/v2/vtweb/${this.snapToken}`;
        window.open(redirectUrl, '_blank');
        this.updatePaymentStatus('Pembayaran dibuka di tab baru');
        
        // Reset main button
        const payButton = document.getElementById('pay-button');
        setTimeout(() => {
            this.resetButton(payButton);
            this.updatePaymentStatus('Menunggu pembayaran di tab lain...');
        }, 1000);
    }
}

// Global function for alternative payment
function openAlternativePayment() {
    if (window.midtransHandler) {
        window.midtransHandler.openAlternativePayment();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.midtransHandler = new MidtransPaymentHandler();
});
</script>

<style>
/* Enhanced Animations */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-spin { animation: spin 1s linear infinite; }
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
.animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }

/* Custom Gradient */
.custom-gradient {
    background: linear-gradient(135deg, #f97316, #dc2626);
}

/* Enhanced Button Styles */
button:disabled {
    cursor: not-allowed !important;
    transform: none !important;
}

/* Responsive Improvements */
@media (max-width: 640px) {
    .grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.5rem;
    }
    
    .text-xs {
        font-size: 0.65rem;
    }
}
</style>
@endsection