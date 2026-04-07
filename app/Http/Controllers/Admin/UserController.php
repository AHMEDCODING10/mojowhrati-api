<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,merchant,admin,moderator,support,super_admin',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $userData = $request->only(['name', 'email', 'phone', 'role']);
        $userData['password'] = bcrypt($request->password);
        $userData['password_plain'] = $request->password;
        $userData['status'] = 'active';

        if ($request->hasFile('profile_image')) {
            $userData['profile_image'] = app(\App\Services\ImgbbService::class)->upload($request->file('profile_image'));
        }

        $user = User::create($userData);

        // Handle Permissions
        if (in_array($request->role, ['admin', 'moderator', 'support'])) {
            $permissions = $request->input('permissions', []);
            foreach ($permissions as $screen => $actions) {
                $user->permissions()->create([
                    'screen' => $screen,
                    'can_view' => isset($actions['view']),
                    'can_create' => isset($actions['create']),
                    'can_edit' => isset($actions['edit']),
                    'can_delete' => isset($actions['delete']),
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function export()
    {
        return back()->with('success', 'تم التحويل لعملية التصدير، سيتم تحميل ملف البيانات قريباً');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('permissions')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with('permissions')->findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'role' => 'required|in:customer,merchant,admin,moderator,support,super_admin',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $userData = $request->only(['name', 'email', 'phone', 'role']);
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
            $userData['password_plain'] = $request->password;
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && !str_starts_with($user->profile_image, 'http')) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $userData['profile_image'] = app(\App\Services\ImgbbService::class)->upload($request->file('profile_image'));
        }

        $user->update($userData);

        // Sync Permissions
        if (in_array($request->role, ['admin', 'moderator', 'support'])) {
            $user->permissions()->delete();
            $permissions = $request->input('permissions', []);
            foreach ($permissions as $screen => $actions) {
                $user->permissions()->create([
                    'screen' => $screen,
                    'can_view' => isset($actions['view']),
                    'can_create' => isset($actions['create']),
                    'can_edit' => isset($actions['edit']),
                    'can_delete' => isset($actions['delete']),
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'active' ? 'blocked' : 'active';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'تم تفعيل حساب المستخدم' : 'تم تعطيل حساب المستخدم';
        return back()->with('success', $message);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي');
        }

        try {
            // التحقق من وجود بيانات مرتبطة (للحفاظ على سلامة البيانات المالية)
            if ($user->role === User::ROLE_MERCHANT && $user->merchant) {
                if ($user->merchant->bookings()->exists()) {
                    return back()->with('error', 'لا يمكن حذف هذا التاجر لوجود عمليات حجز مرتبطة به. يفضل حظر الحساب بدلاً من حذفه للحفاظ على السجلات.');
                }
                if ($user->merchant->products()->exists()) {
                    return back()->with('error', 'لا يمكن حذف هذا التاجر لوجود منتجات مسجلة باسمه. يرجى حذف المنتجات أولاً.');
                }
            }

            if ($user->role === User::ROLE_CUSTOMER) {
                // استخدام الـ custom query لتجنب أي مشاكل في العلاقات غير المحملة
                $hasBookings = \DB::table('bookings')->where('customer_id', $user->id)->exists();
                if ($hasBookings) {
                    return back()->with('error', 'لا يمكن حذف هذا العميل لوجود طلبات سابقة مرتبطة بحسابه.');
                }
            }

            $user->delete();
            return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
            
        } catch (\Exception $e) {
            \Log::error('User deletion error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الحذف: يوجد بيانات مرتبطة بهذا المستخدم تمنع حذفه نهائياً.');
        }
    }
}
