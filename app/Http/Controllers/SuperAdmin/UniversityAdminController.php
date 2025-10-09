<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\University;
use Illuminate\Support\Facades\Log;

class UniversityAdminController extends Controller
{
    public function index()
    {
        try {
            $admins = User::where('user_type', 'admin')->with('university')->paginate(10);
            $universities = University::all();
            return view('superadmin.university_admins.index', compact('admins', 'universities'));
        } catch (\Throwable $e) {
            Log::error('Error loading University Admins index: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            abort(500, 'Lỗi khi tải danh sách admin. Xem log để biết chi tiết.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'id' => 'nullable|exists:users,id',
                'email' => 'required|email|max:100',
                'password' => 'nullable|string|min:6',
                'university_id' => 'required|exists:universities,id',
                'status_id' => 'required|integer|in:1,2,3,4,5,6,7',
            ]);

            $status = (int) $request->status_id;
            Log::info('Received data for store/update:', [
                'id' => $request->id,
                'email' => $request->email,
                'university_id' => $request->university_id,
                'status' => $status,
            ]);

            if ($request->id) {
                $admin = User::findOrFail($request->id);
                $admin->update([
                    'email' => $request->email,
                    'university_id' => $request->university_id,
                    'status_id' => $status,
                ]);

                if ($request->password) {
                    $admin->update(['password_hash' => bcrypt($request->password)]);
                }

                Log::info("Updated admin ID {$admin->id}", ['current' => $admin->toArray()]);

            } else {
                $admin = User::create([
                    'email' => $request->email,
                    'password_hash' => bcrypt($request->password ?? '123456'),
                    'university_id' => $request->university_id,
                    'status_id' => $status,
                    'user_type' => 'admin',
                ]);
                Log::info("Created new admin ID {$admin->id}", ['current' => $admin->toArray()]);
            }

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            Log::error('Error saving University Admin: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, xem log để biết chi tiết.']);
        }
    }

    public function destroy($id)
    {
        try {
            User::findOrFail($id)->delete();
            Log::info("Deleted admin ID {$id}");
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error deleting University Admin: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, xem log để biết chi tiết.']);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if ($ids) {
                User::whereIn('id', $ids)->delete();
                Log::info("Deleted multiple admins: ".implode(',', $ids));
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error deleting multiple University Admins: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'ids' => $request->input('ids')
            ]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, xem log để biết chi tiết.']);
        }
    }
}
