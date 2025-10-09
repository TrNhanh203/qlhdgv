<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $notifications = SystemNotification::with('creator')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('superadmin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('superadmin.notifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:300',
            'message' => 'nullable|string',
            'is_global' => 'boolean',
            'university_id' => 'nullable|exists:universities,id',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $data['created_by'] = Auth::id();

        SystemNotification::create($data);

        return redirect()->route('superadmin.notifications.index')
            ->with('success', 'Thông báo đã được tạo.');
    }
}
