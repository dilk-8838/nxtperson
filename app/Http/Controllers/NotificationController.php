<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Resolve the active authenticated user, with fallback to demo user perspective.
     */
    protected function getActiveUser(): User
    {
        $user = Auth::user();
        if ($user) {
            return $user;
        }

        $demoRole = session('demo_role', 'farmer');

        return match ($demoRole) {
            'admin' => User::firstOrCreate(
                ['email' => 'admin@demo.nxtperson.farm'],
                [
                    'name' => 'Agronomic Systems Administrator (Demo Admin)',
                    'phone' => '9876543230',
                    'role' => 'admin',
                    'district' => 'Gandhinagar',
                    'state' => 'Gujarat',
                    'pincode' => '382010',
                    'password' => bcrypt('demo1234'),
                ]
            ),
            'buyer' => User::firstOrCreate(
                ['email' => 'buyer.procurement@demo.nxtperson.farm'],
                [
                    'name' => 'Adani Agri Fresh / Saurashtra Agro (Demo Buyer)',
                    'phone' => '9876543240',
                    'role' => 'buyer',
                    'district' => 'Ahmedabad',
                    'state' => 'Gujarat',
                    'pincode' => '380001',
                    'password' => bcrypt('demo1234'),
                ]
            ),
            'supplier', 'distributor' => User::firstOrCreate(
                ['email' => 'supplier.agritech@demo.nxtperson.farm'],
                [
                    'name' => 'AgriTech Inputs Gujarat (Demo Supplier)',
                    'phone' => '9876543220',
                    'role' => 'supplier',
                    'district' => 'Anand',
                    'state' => 'Gujarat',
                    'pincode' => '388001',
                    'password' => bcrypt('demo1234'),
                ]
            ),
            'fpo', 'cooperative' => User::firstOrCreate(
                ['email' => 'fpo.saurashtra@demo.nxtperson.farm'],
                [
                    'name' => 'Saurashtra Kisan Producer Co. (Demo FPO)',
                    'phone' => '9876543250',
                    'role' => 'fpo',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360001',
                    'password' => bcrypt('demo1234'),
                ]
            ),
            default => User::firstOrCreate(
                ['phone' => '9876543210'],
                [
                    'name' => 'Suresh Patel (Demo Farmer)',
                    'email' => 'suresh.patel@demo.nxtperson.farm',
                    'role' => 'farmer',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360001',
                    'password' => bcrypt('demo1234'),
                ]
            ),
        };
    }

    /**
     * Notification History Center (For Farmer, Buyer, Admin, Distributor).
     */
    public function index(Request $request)
    {
        $user = $this->getActiveUser();
        $filter = $request->input('filter', 'all');

        $query = $user->notifications()->orderBy('created_at', 'desc');

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('pages.notifications-index', compact('user', 'notifications', 'unreadCount', 'totalCount', 'filter'));
    }

    /**
     * Live Polling Endpoint for Real-time Notification Popups and Header Bell Dropdown.
     * Returns newly arrived notifications since client's last poll timestamp,
     * plus latest 5 recent notifications for instant dropdown sync.
     */
    public function poll(Request $request)
    {
        $user = $this->getActiveUser();
        $since = $request->input('since');

        $query = $user->unreadNotifications()->orderBy('created_at', 'desc');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $newNotifications = $query->take(5)->get();
        $totalUnread = $user->unreadNotifications()->count();

        // Latest 5 for dropdown panel preview
        $recentNotifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();

        $items = $newNotifications->map(function ($notif) {
            $data = $notif->data;

            return [
                'id' => $notif->id,
                'type' => $data['type'] ?? 'notification',
                'title' => $data['title'] ?? 'New Notification',
                'message' => $data['message'] ?? '',
                'crop_name' => $data['crop_name'] ?? '',
                'variety' => $data['variety'] ?? '',
                'grade' => $data['grade'] ?? 'Good',
                'available_quantity_kg' => $data['available_quantity_kg'] ?? 0,
                'district' => $data['district'] ?? '',
                'price_per_kg' => $data['buyer_sale_price_per_kg'] ?? ($data['price_per_kg'] ?? 0),
                'image_url' => $data['image_url'] ?? '',
                'view_url' => $data['view_url'] ?? route('notifications.index'),
                'action_label' => $data['action_label'] ?? 'View Details',
                'is_read' => $notif->read_at !== null,
                'created_at' => $notif->created_at->toIso8601String(),
                'time_ago' => $notif->created_at->diffForHumans(),
            ];
        });

        $recentItems = $recentNotifications->map(function ($notif) {
            $data = $notif->data;

            return [
                'id' => $notif->id,
                'type' => $data['type'] ?? 'notification',
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'view_url' => $data['view_url'] ?? route('notifications.index'),
                'action_label' => $data['action_label'] ?? 'View',
                'is_read' => $notif->read_at !== null,
                'created_at' => $notif->created_at->toIso8601String(),
                'time_ago' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'role' => $user->role,
            'unread_count' => $totalUnread,
            'new_count' => $items->count(),
            'has_new' => $items->isNotEmpty(),
            'notifications' => $items,
            'recent' => $recentItems,
            'polled_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Mark single notification as read with strict user authorization check.
     */
    public function markAsRead(string $id)
    {
        $user = $this->getActiveUser();

        // Strict authorization: Only the notification owner can mark it read
        $notification = $user->notifications()->where('id', $id)->first();
        if (! $notification) {
            abort(403, 'Unauthorized. You do not have permission to modify this notification.');
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications of the active user as read.
     */
    public function markAllAsRead()
    {
        $user = $this->getActiveUser();
        $user->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
