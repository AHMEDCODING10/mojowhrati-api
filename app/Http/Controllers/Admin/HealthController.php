<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function index()
    {
        // CPU load (average of 1 minute) as percentage
        $cpuLoad = sys_getloadavg()[0] * 100;
        $cpu = $cpuLoad > 100 ? 100 : $cpuLoad;

        // Memory usage in MB
        $memory = memory_get_usage(true) / 1024 / 1024;

        // Disk usage in MB
        $diskFree = disk_free_space(base_path()) / 1024 / 1024;
        $diskTotal = disk_total_space(base_path()) / 1024 / 1024;
        $diskUsed = $diskTotal - $diskFree;

        // DB connections count
        $dbStat = DB::selectOne('SHOW STATUS LIKE "Threads_connected"');
        $dbConnections = $dbStat->Value ?? 0;

        // Last request latency cached by middleware (ms)
        $latency = cache()->get('last_request_latency', 0);

        return view('admin.health', compact('cpu', 'memory', 'diskUsed', 'diskTotal', 'dbConnections', 'latency'));
    }
}
