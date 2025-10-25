<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
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

    // Message Management
    public function messageIndex()
    {
        $messages = Message::with('user')->latest()->paginate(10);
        return view('admin.message.index', compact('messages'));
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
}