<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NhomHocPhanController extends Controller
{
    protected $configPath;

    public function __construct()
    {
        $this->configPath = config_path('education_nhomhocphan.php');
    }

    public function index()
    {
        $courseGroups = config('education_nhomhocphan');
        return view('admin.nhomhocphan.index', compact('courseGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $data = config('education_nhomhocphan');
        $now = now()->format('d/m/Y');

        $data[$request->code] = [
            'code'       => $request->code,
            'name'       => $request->name,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $this->saveConfig($data);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $code)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $data = config('education_nhomhocphan');

        if (isset($data[$code])) {
            $data[$request->code] = [
                'code'       => $request->code,
                'name'       => $request->name,
                'created_at' => $data[$code]['created_at'],
                'updated_at' => now()->format('d/m/Y'),
            ];

            if ($code !== $request->code) {
                unset($data[$code]);
            }

            $this->saveConfig($data);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($code)
    {
        $data = config('education_nhomhocphan');

        if (isset($data[$code])) {
            unset($data[$code]);
            $this->saveConfig($data);
        }

        return response()->json(['success' => true]);
    }

    protected function saveConfig($data)
    {
        $export  = var_export($data, true);
        $content = "<?php\n\nreturn $export ;\n";
        file_put_contents($this->configPath, $content);
    }
}
