<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Message;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\MidtransService;
use App\Services\AccountingService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CustomerController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $midtransService;
    protected $accountingService;

    public function __construct(MidtransService $midtransService, AccountingService $accountingService)
    {
        $this->midtransService = $midtransService;
        $this->accountingService = $accountingService;
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isCustomer()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = auth()->user();
        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'delivered_orders' => $user->orders()->where('status', 'delivered')->count(),
        ];

        $recent_orders = $user->orders()->with('orderItems.menu')->latest()->take(3)->get();
        
        return view('customer.dashboard', compact('stats', 'recent_orders'));
    }

    public function menuIndex()
    {
        $menus = Menu::available()->paginate(12);
        return view('customer.menu.index', compact('menus'));
    }

    public function menuShow(Menu $menu)
    {
        return view('customer.menu.show', compact('menu'));
    }

    public function orderIndex()
    {
        $menus = Menu::available()->paginate(12);
        return view('customer.order.index', compact('menus'));
    }

    public function orderCreate(Request $request)
    {
        // Validate cart data from session or request
        $cartItems = [];
        
        if ($request->has('cart_data')) {
            $cartItems = json_decode($request->cart_data, true);
        } elseif (session('cart_items')) {
            $cartItems = session('cart_items');
        }
        
        if (empty($cartItems)) {
            return redirect()->route('customer.order.index')
                ->with('error', 'Keranjang belanja kosong! Silakan pilih menu terlebih dahulu.');
        }

        // Validate all menu items exist and calculate total
        $menuIds = array_column($cartItems, 'id');
        $menus = Menu::whereIn('id', $menuIds)->where('is_available', true)->get();
        $menuData = $menus->keyBy('id');
        
        $validCartItems = [];
        $totalAmount = 0;
        
        foreach ($cartItems as $item) {
            if ($menuData->has($item['id'])) {
                $menu = $menuData->get($item['id']);
                $validItem = [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'image' => $menu->image ? Storage::url($menu->image) : null,
                    'subtotal' => $menu->price * $item['quantity']
                ];
                $validCartItems[] = $validItem;
                $totalAmount += $validItem['subtotal'];
            }
        }
        
        if (empty($validCartItems)) {
            return redirect()->route('customer.order.index')
                ->with('error', 'Menu yang dipilih tidak tersedia!');
        }

        // Get payment methods - FIX: Pastikan ini ada dan aktif
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        // DEBUG: Cek apakah ada payment methods
        if ($paymentMethods->isEmpty()) {
            return redirect()->route('customer.order.index')
                ->with('error', 'Tidak ada metode pembayaran yang tersedia. Hubungi administrator.');
        }
        
        // Store cart in session for checkout process
        session(['checkout_cart' => $validCartItems]);
        
        // FIX: Pastikan variable ini dikirim ke view
        return view('customer.order.create', compact('validCartItems', 'totalAmount', 'paymentMethods'));
    }

    public function orderStore(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string',
            'phone' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        // Get cart items from session
        $cartItems = session('checkout_cart');
        
        if (empty($cartItems)) {
            return redirect()->route('customer.order.index')
                ->with('error', 'Keranjang belanja kosong!');
        }

        try {
            $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
            \Log::info('Payment method selected', [
                'payment_method_id' => $paymentMethod->id,
                'payment_method_name' => $paymentMethod->name,
                'payment_method_type' => $paymentMethod->type
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment method not found', [
                'payment_method_id' => $validated['payment_method_id']
            ]);
            return back()->with('error', 'Metode pembayaran tidak valid!');
        }

        // Validate menu availability again
        $menuIds = array_column($cartItems, 'id');
        $menus = Menu::whereIn('id', $menuIds)->where('is_available', true)->get();
        $menuData = $menus->keyBy('id');
        
        foreach ($cartItems as $item) {
            if (!$menuData->has($item['id'])) {
                return back()->with('error', 'Menu "' . $item['name'] . '" tidak tersedia lagi!');
            }
        }

        try {
            $order = DB::transaction(function () use ($validated, $cartItems, $menuData, $paymentMethod) {
                // Generate order number
                $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
                
                // Calculate total amount from current prices
                $totalAmount = 0;
                foreach ($cartItems as $item) {
                    $menu = $menuData->get($item['id']);
                    $totalAmount += $menu->price * $item['quantity'];
                }
                
                // Create order
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => $orderNumber,
                    'total_amount' => $totalAmount,
                    'delivery_address' => $validated['delivery_address'],
                    'phone' => $validated['phone'],
                    'notes' => $validated['notes'],
                    'status' => 'pending'
                ]);

                // Create order items
                foreach ($cartItems as $item) {
                    $menu = $menuData->get($item['id']);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'quantity' => $item['quantity'],
                        'price' => $menu->price,
                        'notes' => null
                    ]);
                }

                // Create payment record dengan error handling
                try {
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_method_id' => $validated['payment_method_id'],
                        'amount' => $order->total_amount,
                        'status' => 'pending'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create payment record', [
                        'order_id' => $order->id,
                        'payment_method_id' => $validated['payment_method_id'],
                        'error' => $e->getMessage()
                    ]);
                    throw new \Exception('Gagal membuat record pembayaran: ' . $e->getMessage());
                }

                return $order;
            });

            // Clear checkout cart from session
            session()->forget('checkout_cart');

            \Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method_type' => $paymentMethod->type
            ]);

            // Redirect berdasarkan metode pembayaran
            if ($paymentMethod->type === 'midtrans') {
                return redirect()->route('customer.order.midtrans', $order);
            } else {
                return redirect()->route('customer.order.show', $order)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to create order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function orderMidtrans(Order $order)
    {
        try {
            $this->authorize('view', $order);
            
            \Log::info('=== MIDTRANS PAYMENT PAGE ACCESSED ===', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => auth()->id()
            ]);
            
            // Load relationships dengan error handling
            try {
                $order->load(['orderItems.menu', 'payment.paymentMethod', 'user']);
            } catch (\Exception $e) {
                \Log::error('Failed to load order relationships', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->route('customer.order.show', $order)
                    ->with('error', 'Gagal memuat data pesanan.');
            }
            
            // Pastikan ada payment record, jika tidak buat baru
            if (!$order->payment) {
                \Log::info('Creating missing payment record', [
                    'order_id' => $order->id
                ]);
                
                try {
                    // Cari payment method Midtrans
                    $midtransPaymentMethod = PaymentMethod::where('name', 'LIKE', '%midtrans%')
                                                          ->orWhere('type', 'midtrans')
                                                          ->first();
                    
                    if (!$midtransPaymentMethod) {
                        // Buat payment method Midtrans jika belum ada
                        $midtransPaymentMethod = PaymentMethod::create([
                            'name' => 'Midtrans Payment Gateway',
                            'type' => 'midtrans',
                            'is_active' => true,
                            'description' => 'Payment via Midtrans (Credit Card, Bank Transfer, E-Wallet)'
                        ]);
                        
                        \Log::info('Created Midtrans payment method', [
                            'payment_method_id' => $midtransPaymentMethod->id
                        ]);
                    }
                    
                    // Buat payment record
                    $payment = Payment::create([
                        'order_id' => $order->id,
                        'payment_method_id' => $midtransPaymentMethod->id,
                        'amount' => $order->total_amount + 5000, // Tambah ongkir
                        'status' => 'pending'
                    ]);
                    
                    // Reload order dengan payment baru
                    $order->load(['payment.paymentMethod']);
                    
                    \Log::info('Payment record created successfully', [
                        'payment_id' => $payment->id
                    ]);
                    
                } catch (\Exception $e) {
                    \Log::error('Failed to create payment record', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    return redirect()->route('customer.order.show', $order)
                        ->with('error', 'Gagal membuat record pembayaran: ' . $e->getMessage());
                }
            }
            
            // Validasi data order sebelum membuat transaksi
            if ($order->orderItems->isEmpty()) {
                return redirect()->route('customer.order.show', $order)
                    ->with('error', 'Pesanan tidak memiliki item yang valid.');
            }
            
            if (!$order->user || !$order->user->email) {
                return redirect()->route('customer.order.show', $order)
                    ->with('error', 'Data user tidak lengkap. Silakan update profil Anda.');
            }
            
            if (empty($order->delivery_address)) {
                return redirect()->route('customer.order.show', $order)
                    ->with('error', 'Alamat pengiriman tidak boleh kosong.');
            }
            
            if (empty($order->phone)) {
                return redirect()->route('customer.order.show', $order)
                    ->with('error', 'Nomor telefon tidak boleh kosong.');
            }
            
            // Cek apakah snap token sudah ada dan masih valid
            if ($order->payment && $order->payment->snap_token) {
                \Log::info('Using existing snap token', [
                    'order_id' => $order->id,
                    'existing_token' => substr($order->payment->snap_token, 0, 20) . '...'
                ]);
                
                return view('customer.order.midtrans', [
                    'order' => $order,
                    'snapToken' => $order->payment->snap_token
                ]);
            }
            
            // Generate snap token dengan error handling yang lebih baik
            try {
                \Log::info('Generating new snap token', [
                    'order_id' => $order->id
                ]);
                
                $snapToken = $this->midtransService->createTransaction($order);
                
                if (empty($snapToken)) {
                    throw new \Exception('Snap token kosong dari service');
                }
                
                \Log::info('Snap token generated successfully', [
                    'order_id' => $order->id,
                    'token_preview' => substr($snapToken, 0, 20) . '...'
                ]);
                
                return view('customer.order.midtrans', [
                    'order' => $order,
                    'snapToken' => $snapToken
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to create Midtrans transaction', [
                    'order_id' => $order->id,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString()
                ]);
                
                // Berikan pesan error yang lebih spesifik berdasarkan jenis error
                $errorMessage = 'Gagal membuat transaksi pembayaran: ';
                
                if (str_contains($e->getMessage(), 'credentials')) {
                    $errorMessage .= 'Konfigurasi Midtrans belum benar. Hubungi administrator.';
                } elseif (str_contains($e->getMessage(), 'Undefined array key')) {
                    $errorMessage .= 'Ada kesalahan dalam data pesanan. Silakan buat pesanan ulang.';
                } elseif (str_contains($e->getMessage(), 'environment')) {
                    $errorMessage .= 'Konfigurasi environment tidak konsisten. Hubungi administrator.';
                } else {
                    $errorMessage .= $e->getMessage();
                }
                
                return redirect()->route('customer.order.show', $order)
                    ->with('error', $errorMessage);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error in orderMidtrans method', [
                'order_id' => $order->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('customer.orders')
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function midtransCallback(Request $request)
    {
        try {
            \Log::info('=== MIDTRANS CALLBACK RECEIVED ===', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validate required fields
            $requiredFields = ['order_id', 'status_code', 'gross_amount', 'signature_key'];
            foreach ($requiredFields as $field) {
                if (!$request->has($field)) {
                    \Log::error('Missing required field in callback', ['field' => $field]);
                    return response()->json(['status' => 'error', 'message' => 'Missing required field: ' . $field], 400);
                }
            }

            // Validate signature
            $serverKey = config('services.midtrans.server_key');
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $signatureKey = $request->signature_key;
            
            $hash = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            
            if (!hash_equals($hash, $signatureKey)) {
                \Log::error('Invalid signature in callback', [
                    'expected' => $hash,
                    'received' => $signatureKey,
                    'order_id' => $orderId
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
            }

            // Find payment record
            $payment = Payment::where('midtrans_order_id', $orderId)->first();
            
            if (!$payment) {
                \Log::error('Payment not found for callback', ['order_id' => $orderId]);
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // Load order relationship
            $payment->load('order');
            $order = $payment->order;

            if (!$order) {
                \Log::error('Order not found for payment', ['payment_id' => $payment->id]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status ?? null;
            $paymentType = $request->payment_type ?? null;
            $transactionId = $request->transaction_id ?? null;

            \Log::info('Processing transaction status', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType
            ]);

            // Process different transaction statuses
            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        // Transaction is challenged by FDS
                        $this->updatePaymentStatus($payment, $order, 'pending', $request->all(), $transactionId, $paymentType);
                        \Log::info('Payment challenged by FDS');
                    } elseif ($fraudStatus == 'accept') {
                        // Transaction is accepted
                        $this->updatePaymentStatus($payment, $order, 'confirmed', $request->all(), $transactionId, $paymentType);
                        \Log::info('Payment accepted after challenge');
                    }
                    break;

                case 'settlement':
                    // Transaction is settled (successful)
                    $this->updatePaymentStatus($payment, $order, 'confirmed', $request->all(), $transactionId, $paymentType);
                    \Log::info('Payment settled successfully');
                    break;

                case 'pending':
                    // Transaction is pending
                    $this->updatePaymentStatus($payment, $order, 'pending', $request->all(), $transactionId, $paymentType);
                    \Log::info('Payment is pending');
                    break;

                case 'deny':
                    // Transaction is denied
                    $this->updatePaymentStatus($payment, $order, 'failed', $request->all(), $transactionId, $paymentType);
                    \Log::info('Payment denied');
                    break;

                case 'expire':
                    // Transaction is expired
                    $this->updatePaymentStatus($payment, $order, 'expired', $request->all(), $transactionId, $paymentType);
                    \Log::info('Payment expired');
                    break;

                case 'cancel':
                    // Transaction is cancelled
                    $this->updatePaymentStatus($payment, $order, 'cancelled', $request->all(), $transactionId, $paymentType);
                    \Log::info('Payment cancelled');
                    break;

                default:
                    \Log::warning('Unknown transaction status', ['status' => $transactionStatus]);
                    $this->updatePaymentStatus($payment, $order, 'unknown', $request->all(), $transactionId, $paymentType);
            }

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            \Log::error('Exception in Midtrans callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ✅ NEW: Helper method to update payment and order status
    private function updatePaymentStatus($payment, $order, $status, $callbackData, $transactionId = null, $paymentType = null)
    {
        try {
            $wasAlreadyConfirmed = $payment->status === 'confirmed';
            DB::transaction(function () use ($payment, $order, $status, $callbackData, $transactionId, $paymentType) {
                // Update payment record
                $paymentData = [
                    'status' => $status,
                    'midtrans_response' => $callbackData
                ];

                if ($transactionId) {
                    $paymentData['midtrans_transaction_id'] = $transactionId;
                }

                if ($paymentType) {
                    $paymentData['payment_type'] = $paymentType;
                }

                if ($status === 'confirmed') {
                    $paymentData['paid_at'] = now();
                    $paymentData['midtrans_paid_at'] = now();
                }

                $payment->update($paymentData);

                // Update order status based on payment status
                $orderStatus = $this->getOrderStatusFromPaymentStatus($status);
                if ($orderStatus && $order->status !== $orderStatus) {
                    $order->update(['status' => $orderStatus]);
                    
                    \Log::info('Order status updated', [
                        'order_id' => $order->id,
                        'old_status' => $order->getOriginal('status'),
                        'new_status' => $orderStatus,
                        'payment_status' => $status
                    ]);
                }
            });
            // ===== SAMBUNGAN AKUNTANSI =====
            // Jika status BARU adalah confirmed, DAN status LAMA BUKAN confirmed
            // (Agar tidak dobel jurnal)
            if ($status === 'confirmed' && !$wasAlreadyConfirmed) {
                \Log::info("Memanggil AccountingService untuk Order: {$order->order_number}");
                $this->accountingService->recordSale($order);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to update payment status', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ✅ NEW: Map payment status to order status
    private function getOrderStatusFromPaymentStatus($paymentStatus)
    {
        $statusMap = [
            'confirmed' => 'confirmed',    // Payment confirmed -> Order confirmed
            'pending' => 'pending',        // Payment pending -> Order pending  
            'failed' => 'cancelled',       // Payment failed -> Order cancelled
            'expired' => 'cancelled',      // Payment expired -> Order cancelled
            'cancelled' => 'cancelled',    // Payment cancelled -> Order cancelled
        ];

        return $statusMap[$paymentStatus] ?? null;
    }

    // ✅ IMPROVED: Enhanced finish callback (redirect after successful payment)
    public function midtransFinish(Request $request)
    {
        try {
            $orderId = $request->order_id;
            
            \Log::info('Midtrans finish callback', [
                'order_id' => $orderId,
                'request_data' => $request->all()
            ]);

            if (!$orderId) {
                return redirect()->route('customer.dashboard')
                    ->with('error', 'ID pesanan tidak ditemukan');
            }

            $payment = Payment::where('midtrans_order_id', $orderId)->first();
            
            if (!$payment) {
                \Log::warning('Payment not found in finish callback', ['order_id' => $orderId]);
                return redirect()->route('customer.dashboard')
                    ->with('error', 'Pesanan tidak ditemukan');
            }

            $order = $payment->order;

            // ✅ Try to get latest transaction status from Midtrans
            try {
                $transactionStatus = $this->midtransService->getTransactionStatus($orderId);
                
                if ($transactionStatus) {
                    \Log::info('Retrieved transaction status from Midtrans', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus->transaction_status ?? 'unknown'
                    ]);

                    // Update status based on latest info from Midtrans
                    if (isset($transactionStatus->transaction_status)) {
                        if (in_array($transactionStatus->transaction_status, ['capture', 'settlement'])) {
                            $this->updatePaymentStatus($payment, $order, 'confirmed', (array)$transactionStatus, 
                                                     $transactionStatus->transaction_id ?? null, 
                                                     $transactionStatus->payment_type ?? null);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get transaction status from Midtrans', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage()
                ]);
            }

            // Reload payment to get latest status
            $payment->refresh();
            $order->refresh();

            $message = 'Pembayaran berhasil! Pesanan Anda telah dikonfirmasi.';
            $messageType = 'success';

            // Customize message based on current status
            if ($payment->status === 'confirmed') {
                $message = 'Pembayaran berhasil! Pesanan Anda telah dikonfirmasi dan sedang diproses.';
            } elseif ($payment->status === 'pending') {
                $message = 'Pembayaran sedang diproses. Kami akan mengupdate status pesanan Anda segera.';
                $messageType = 'info';
            }

            return redirect()->route('customer.order.show', $order)
                ->with($messageType, $message);

        } catch (\Exception $e) {
            \Log::error('Error in midtrans finish callback', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->route('customer.dashboard')
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran');
        }
    }

    // ✅ IMPROVED: Enhanced unfinish callback
    public function midtransUnfinish(Request $request)
    {
        try {
            $orderId = $request->order_id;
            
            \Log::info('Midtrans unfinish callback', [
                'order_id' => $orderId,
                'request_data' => $request->all()
            ]);

            if (!$orderId) {
                return redirect()->route('customer.dashboard')
                    ->with('warning', 'Pembayaran belum selesai');
            }

            $payment = Payment::where('midtrans_order_id', $orderId)->first();
            
            if (!$payment) {
                return redirect()->route('customer.dashboard')
                    ->with('warning', 'Pesanan tidak ditemukan');
            }

            $order = $payment->order;

            return redirect()->route('customer.order.show', $order)
                ->with('warning', 'Pembayaran belum selesai. Silakan selesaikan pembayaran Anda atau coba lagi.');

        } catch (\Exception $e) {
            \Log::error('Error in midtrans unfinish callback', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->route('customer.dashboard')
                ->with('warning', 'Pembayaran belum selesai');
        }
    }

    // ✅ IMPROVED: Enhanced error callback
    public function midtransError(Request $request)
    {
        try {
            $orderId = $request->order_id;
            
            \Log::info('Midtrans error callback', [
                'order_id' => $orderId,
                'request_data' => $request->all()
            ]);

            if (!$orderId) {
                return redirect()->route('customer.order.index')
                    ->with('error', 'Pembayaran gagal');
            }

            $payment = Payment::where('midtrans_order_id', $orderId)->first();
            
            if (!$payment) {
                return redirect()->route('customer.order.index')
                    ->with('error', 'Pembayaran gagal. Pesanan tidak ditemukan.');
            }

            $order = $payment->order;

            // Update payment status to failed
            $this->updatePaymentStatus($payment, $order, 'failed', $request->all());

            return redirect()->route('customer.order.show', $order)
                ->with('error', 'Terjadi kesalahan dalam proses pembayaran. Silakan coba lagi atau hubungi customer service.');

        } catch (\Exception $e) {
            \Log::error('Error in midtrans error callback', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->route('customer.order.index')
                ->with('error', 'Pembayaran gagal');
        }
    }

    // ✅ NEW: Manual status check method (for admin or debugging)
    public function checkPaymentStatus(Order $order)
    {
        try {
            $this->authorize('view', $order);
            
            if (!$order->payment || !$order->payment->midtrans_order_id) {
                return redirect()->back()
                    ->with('error', 'Pesanan ini tidak memiliki data pembayaran Midtrans');
            }

            $midtransOrderId = $order->payment->midtrans_order_id;
            
            // Get status from Midtrans
            $transactionStatus = $this->midtransService->getTransactionStatus($midtransOrderId);
            
            if (!$transactionStatus) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat mengambil status dari Midtrans');
            }

            \Log::info('Manual status check result', [
                'order_id' => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'transaction_status' => $transactionStatus->transaction_status ?? 'unknown'
            ]);

            // Update local status based on Midtrans response
            if (isset($transactionStatus->transaction_status)) {
                $status = $transactionStatus->transaction_status;
                
                if (in_array($status, ['capture', 'settlement'])) {
                    $this->updatePaymentStatus($order->payment, $order, 'confirmed', (array)$transactionStatus,
                                             $transactionStatus->transaction_id ?? null,
                                             $transactionStatus->payment_type ?? null);
                    $message = 'Status pembayaran berhasil diperbarui: Terkonfirmasi';
                    $type = 'success';
                } elseif ($status === 'pending') {
                    $this->updatePaymentStatus($order->payment, $order, 'pending', (array)$transactionStatus);
                    $message = 'Status pembayaran: Pending';
                    $type = 'info';
                } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
                    $this->updatePaymentStatus($order->payment, $order, 'failed', (array)$transactionStatus);
                    $message = 'Status pembayaran: Gagal/Dibatalkan';
                    $type = 'error';
                } else {
                    $message = 'Status pembayaran: ' . $status;
                    $type = 'info';
                }
            } else {
                $message = 'Status tidak dapat ditentukan dari respons Midtrans';
                $type = 'warning';
            }

            return redirect()->back()->with($type, $message);

        } catch (\Exception $e) {
            \Log::error('Error checking payment status', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal memeriksa status pembayaran: ' . $e->getMessage());
        }
    }
    
    public function orders()
    {
        $orders = auth()->user()->orders()->with('orderItems.menu')->latest()->paginate(10);
        return view('customer.order.history', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['orderItems.menu', 'payment.paymentMethod']);
        return view('customer.order.show', compact('order'));
    }

    public function orderReorder(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load('orderItems.menu');
        
        // Convert order items back to cart format
        $cartItems = [];
        foreach ($order->orderItems as $item) {
            if ($item->menu->is_available) {
                $cartItems[] = [
                    'id' => $item->menu->id,
                    'name' => $item->menu->name,
                    'price' => $item->menu->price,
                    'quantity' => $item->quantity,
                    'image' => $item->menu->image ? Storage::url($item->menu->image) : null
                ];
            }
        }
        
        if (empty($cartItems)) {
            return redirect()->route('customer.order.index')
                ->with('warning', 'Beberapa menu dari pesanan sebelumnya tidak tersedia lagi.');
        }
        
        // Store in session and redirect to checkout
        session(['checkout_cart' => $cartItems]);
        
        return redirect()->route('customer.order.create')
            ->with('reorder_data', [
                'delivery_address' => $order->delivery_address,
                'phone' => $order->phone,
                'notes' => $order->notes
            ])
            ->with('success', 'Item berhasil ditambahkan ke keranjang!');
    }

    public function messageIndex()
    {
        // Ambil semua percakapan
        $messages = auth()->user()->messages()
                            ->orderBy('created_at', 'asc')
                            ->get();
        
        // Tandai semua sebagai sudah dibaca oleh customer
        auth()->user()->messages()->where('is_read', false)->update(['is_read' => true]);

        return view('customer.message.index', compact('messages'));
    }

    public function messageCreate()
    {
        return view('customer.message.create');
    }

    public function messageStore(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Simpan pesan
        $msg = auth()->user()->messages()->create([
            'subject' => 'Chat Pelanggan', // Hardcode karena tidak dipakai di UI baru
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        // GANTI INI: Jangan redirect, tapi kembalikan JSON sukses
        // return redirect()->route('customer.message.index'); <--- HAPUS INI
        
        return response()->json(['status' => 'success', 'data' => $msg]); // <--- GANTI JADI INI
    }

    // ... method lainnya ...

    public function clearChat()
    {
        // Hapus semua pesan milik user yang sedang login
        auth()->user()->messages()->delete();

        return response()->json(['status' => 'success', 'message' => 'Riwayat chat berhasil dihapus.']);
    }

    public function messageJson()
    {
        $messages = auth()->user()->messages()
                            ->orderBy('created_at', 'asc')
                            ->get();
        
        // Tandai sebagai sudah dibaca
        auth()->user()->messages()->where('is_read', false)->update(['is_read' => true]);
        
        return response()->json([
            'html' => view('customer.message.partials.chat-bubble', compact('messages'))->render()
        ]);
    }

    public function messageShow(Message $message)
    {
        $this->authorize('view', $message);
        return view('customer.message.show', compact('message'));
    }
}