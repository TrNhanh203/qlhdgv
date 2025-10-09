<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Room;

class PhongHocController extends Controller
{
    /**
     * Hiển thị danh sách phòng học
     */
    public function index()
    {
        $user = Auth::user();
         if (!$user) return redirect()->route('login');
       // $rooms = Room::where('university_id', $user->university_id)
         //   ->orderBy('created_at', 'desc')
           // ->get();
        $rooms = Room::where('university_id', $user->university_id)
            ->orderBy('created_at', 'asc')
            ->get();
        return view('admin.phonghoc.index', compact('rooms'));
    }

    public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'capacity' => 'required|integer|min:1',
        'location' => 'required|string|max:255',
        'status_id' => 'required|in:0,1',
    ]);
    $name = strtoupper($request->name);

    $exists = Room::where('university_id', $user->university_id)
        ->where('name', $name)
        ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'errors' => ['name' => ['Tên phòng đã tồn tại']]
        ]);
    }

    $room = Room::updateOrCreate(
    ['id' => $request->id],
    [
        'name' => $name,
        'type' => $request->type,
        'category' => 'hoc',
        'capacity' => $request->capacity,
        'location' => $request->location,
        'status_id' => $request->status_id,
        'university_id' => $user->university_id,
    ]
);

return response()->json([
    'success' => true,
    'room' => [
        'id' => $room->id,
        'name' => $room->name,
        'type' => $room->type,
        'category' => $room->category,
        'capacity' => $room->capacity,
        'location' => $room->location,
        'status_id' => $room->status_id,
        'created_at' => $room->created_at->format('d-m-Y'),
        'updated_at' => $room->updated_at->format('d-m-Y'),
    ]
]);

}



    public function destroy($id)
    {
        Room::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function destroyMultiple(Request $request)
    {
        Room::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
}
