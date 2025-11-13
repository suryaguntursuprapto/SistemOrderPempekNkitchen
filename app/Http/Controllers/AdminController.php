<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Message;
use App\Models\User;
use App\Models\Journal;
use App\Models\ChartOfAccount;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use App\Models\Expense; // <-- Dari langkah sebelumnya
use App\Exports\FinancialReportExport; // <-- Dari langkah sebelumnya
use Maatwebsite\Excel\Facades\Excel; // <-- Dari langkah sebelumnya
use Carbon\Carbon; // <-- TAMBAHKAN BARIS INI
use App\Services\AccountingService;

class AdminController extends Controller
{
    protected $accountingService;
    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'unread_messages' => Message::unread()->count(),
            'total_revenue' => Order::whereIn('status', ['delivered'])->sum('total_amount'),
        ];

        $recent_orders = Order::with('user')->latest()->take(5)->get();
        $recent_messages = Message::with('user')->unread()->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'recent_messages'));
    }

    // Menu Management
    public function menuIndex()
    {
        $menus = Menu::latest()->paginate(10);
        return view('admin.menu.index', compact('menus'));
    }

    public function menuCreate()
    {
        return view('admin.menu.create');
    }

    public function menuStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-images', 'public');
        }

        Menu::create($validated);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function menuShow(Menu $menu)
    {
        return view('admin.menu.show', compact('menu'));
    }

    public function menuEdit(Menu $menu)
    {
        return view('admin.menu.edit', compact('menu'));
    }

    public function menuUpdate(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menu-images', 'public');
        }

        $menu->update($validated);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function menuDestroy(Menu $menu)
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        
        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus!');
    }

    // Order Management
    public function orderIndex()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.order.index', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        $order->load(['user', 'orderItems.menu']);
        return view('admin.order.show', compact('order'));
    }

    public function orderUpdate(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.order.index')->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function orderDestroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.order.index')->with('success', 'Pesanan berhasil dihapus!');
    }

  public function messageIndex()
    {
        // Ambil SEMUA user dengan role customer, bukan cuma yang punya pesan
        $customers = User::where('role', 'customer')
            ->with(['messages' => function($q) {
                $q->latest();
            }])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('is_read', false);
            }])
            ->get()
            // Urutkan: Yang punya pesan terbaru di atas, sisanya berdasarkan tanggal daftar
            ->sortByDesc(function($user) {
                // Jika ada pesan, pakai tanggal pesan terakhir.
                // Jika TIDAK ADA pesan, pakai tanggal user mendaftar.
                // Kita gunakan helper optional() agar tidak error jika null.
                return optional($user->messages->first())->created_at ?? $user->created_at;
            });

        return view('admin.message.index', compact('customers'));
    }

    public function getCustomerChat(User $user)
    {
        // Tandai semua pesan user ini sebagai terbaca
        $user->messages()->where('is_read', false)->update(['is_read' => true]);

        $messages = $user->messages()->orderBy('created_at', 'asc')->get();
        
        return response()->json([
            'html' => view('admin.message.partials.chat-bubble', compact('messages'))->render(),
            'user' => $user
        ]);
    }

    public function sendChatReply(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'message' => 'required',
        ]);

        // Cek apakah ada pesan terakhir dari user yang belum dibalas
        $lastMessage = Message::where('user_id', $request->user_id)
                              ->whereNull('admin_reply')
                              ->latest()
                              ->first();

        if ($lastMessage) {
            // Skenario 1: Membalas pesan user yang ada
            $lastMessage->update([
                'admin_reply' => $request->message,
                'replied_at' => now(),
                'is_read' => true
            ]);
        } else {
            // Skenario 2: Admin chat duluan / Chat baru
            // Kita buat "Dummy Message" agar struktur DB tetap valid
            Message::create([
                'user_id' => $request->user_id,
                'subject' => 'Chat Admin',
                'message' => '[SYSTEM_INIT]', // Kode khusus untuk disembunyikan di View
                'admin_reply' => $request->message,
                'replied_at' => now(),
                'is_read' => true
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    // ... method lainnya ...

    public function clearChat(User $user)
    {
        // Hapus semua pesan milik user ini
        $user->messages()->delete();

        return response()->json(['status' => 'success', 'message' => 'Riwayat chat berhasil dihapus.']);
    }

    public function messageShow(Message $message)
    {
        $message->markAsRead();
        return view('admin.message.show', compact('message'));
    }

    public function messageReply(Request $request, Message $message)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $message->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => now(),
            'is_read' => true,
        ]);

        return redirect()->route('admin.message.index')->with('success', 'Pesan berhasil dibalas!');
    }

    public function expenseIndex()
    {
        // Data 'with' sekarang harus mengambil relasi 'chartOfAccount'
        $expenses = Expense::with('chartOfAccount')->latest()->paginate(10);
        return view('admin.expense.index', compact('expenses'));
    }

    public function expenseCreate()
    {
        // Ambil semua akun COA yang tipenya 'Expense'
        $expenseAccounts = ChartOfAccount::where('type', 'Expense')
                                         ->orderBy('name')
                                         ->get();
                                         
        return view('admin.expense.create', compact('expenseAccounts'));
    }

    public function expenseStore(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            
            // Validasi baru, menggantikan 'category'
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id', 
        ]);

        $validated['user_id'] = auth()->id();

        $expense = Expense::create($validated);

        // ===== SAMBUNGAN AKUNTANSI (Sudah benar) =====
        $this->accountingService->recordExpense($expense);
        // ===========================================

        return redirect()->route('admin.expense.index')->with('success', 'Biaya berhasil dicatat.');
    }

    public function expenseDestroy(Expense $expense)
    {
        // TODO: Buat Jurnal Balik jika diperlukan
        $expense->journal()->delete(); // Hapus jurnal terkait
        $expense->delete();
        
        return redirect()->route('admin.expense.index')->with('success', 'Biaya berhasil dihapus.');
    }

    public function reportIndex(Request $request)
    {
        // Tetapkan tanggal default (bulan ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // 1. Data Penjualan (dari Order)
        $salesData = Order::whereIn('status', ['delivered', 'confirmed', 'ready'])
                            ->whereBetween('created_at', [$startDateTime, $endDateTime])
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        // 2. Data Biaya/Pembelian (INI BAGIAN YANG DIPERBARUI)
        
        // Ambil data Biaya (Expense)
        $expenseData = Expense::whereBetween('date', [$startDate, $endDate])->get();

        // Ambil data Pembelian (Purchase)
        $purchaseData = Purchase::whereBetween('purchase_date', [$startDate, $endDate])->get();

        // 3. Kalkulasi Laba/Rugi
        $totalSales = $salesData->sum('total_amount');
        $totalExpenses = $expenseData->sum('amount');
        $totalPurchases = $purchaseData->sum('total_amount'); // <-- BARU
        
        $profit = $totalSales - ($totalExpenses + $totalPurchases); // <-- DIPERBARUI

        $summary = [
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses + $totalPurchases, // <-- DIPERBARUI
            'profit' => $profit,
        ];

        // 4. GABUNGKAN DATA BIAYA & PEMBELIAN UNTUK TABEL
        
        // Ubah format Biaya
        $expenses = $expenseData->map(function ($item) {
            return (object) [
                'date' => $item->date,
                'description' => $item->description,
                'category' => $item->category ?? 'Biaya Operasional',
                'amount' => $item->amount,
                'type' => 'expense' // Tandai sebagai 'expense'
            ];
        });

        // Ubah format Pembelian
        $purchases = $purchaseData->map(function ($item) {
            return (object) [
                'date' => $item->purchase_date,
                'description' => 'Pembelian: ' . ($item->supplier_name ?? $item->invoice_number ?? 'ID ' . $item->id),
                'category' => 'Pembelian Bahan Baku',
                'amount' => $item->total_amount,
                'type' => 'purchase' // Tandai sebagai 'purchase'
            ];
        });

        // Gabungkan dan urutkan berdasarkan tanggal
        $combinedCosts = collect($expenses)->merge($purchases)->sortBy('date');

        return view('admin.report.index', compact(
            'summary', 
            'salesData', 
            'combinedCosts', // <-- KIRIM DATA GABUNGAN (BUKAN expenseData)
            'startDate', 
            'endDate'
        ));
    }

    public function reportExport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $fileName = 'laporan_keuangan_' . $startDate . '_sd_' . $endDate . '.xlsx';

        return Excel::download(new FinancialReportExport($startDate, $endDate), $fileName);
    }

    /**
     * 📖 Menampilkan Jurnal Umum (Semua Transaksi)
     */
    public function journalIndex(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $journals = Journal::with('transactions.chartOfAccount', 'referenceable')
                            ->whereBetween('date', [$startDate, $endDate])
                            ->latest()
                            ->paginate(20);

        return view('admin.report.journal', compact('journals', 'startDate', 'endDate'));
    }

    /**
     * 📚 Menampilkan Buku Besar (Transaksi per Akun)
     */
    public function ledgerIndex(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Ambil semua akun yang punya transaksi di rentang tanggal ini
        $accounts = ChartOfAccount::whereHas('journalTransactions', function ($query) use ($startDate, $endDate) {
            $query->whereHas('journal', function ($subQuery) use ($startDate, $endDate) {
                $subQuery->whereBetween('date', [$startDate, $endDate]);
            });
        })
        ->with(['journalTransactions' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('journal', function ($subQuery) use ($startDate, $endDate) {
                $subQuery->whereBetween('date', [$startDate, $endDate]);
            })->with('journal');
        }])
        ->get();

        return view('admin.report.ledger', compact('accounts', 'startDate', 'endDate'));
    }
}