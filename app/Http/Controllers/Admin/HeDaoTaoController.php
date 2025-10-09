<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeDaoTaoController extends Controller
{
    protected string $configKey = 'education_systems';
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = config_path($this->configKey . '.php');
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $all = config($this->configKey) ?? [];
        $systems = array_filter($all, function ($item) use ($user) {
            return isset($item['university_id']) && $item['university_id'] == $user->university_id;
        });

        return view('admin.hedaotao.index', compact('systems'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập'], 401);

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $all = config($this->configKey) ?? [];
        $now = now()->format('d/m/Y');

        $all[$request->code] = [
            'code'          => $request->code,
            'name'          => $request->name,
            'created_at'    => $now,
            'updated_at'    => $now,
            'university_id' => $user->university_id,
        ];

        $this->saveConfig($all);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $code)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập'], 401);

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $all = config($this->configKey) ?? [];

        if (isset($all[$code]) && $all[$code]['university_id'] == $user->university_id) {
            $old = $all[$code];

            $all[$request->code] = [
                'code'          => $request->code,
                'name'          => $request->name,
                'created_at'    => $old['created_at'],
                'updated_at'    => now()->format('d/m/Y'),
                'university_id' => $user->university_id,
            ];

            if ($code !== $request->code) {
                unset($all[$code]);
            }

            $this->saveConfig($all);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($code)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập'], 401);

        $all = config($this->configKey) ?? [];

        if (isset($all[$code]) && $all[$code]['university_id'] == $user->university_id) {
            unset($all[$code]);
            $this->saveConfig($all);
        }

        return response()->json(['success' => true]);
    }

    protected function saveConfig($data)
    {
        $export = var_export($data, true);
        $content = "<?php\n\nreturn $export;\n";
        file_put_contents($this->configPath, $content);
    }
}
