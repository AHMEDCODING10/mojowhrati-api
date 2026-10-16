<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * عرض صفحة النسخ الاحتياطية
     */
    public function index()
    {
        $backups = $this->getStoredBackups();
        return view('backups.index', compact('backups'));
    }

    /**
     * إنشاء نسخة احتياطية من جميع البيانات
     * Returns the backup file as a download (browser file-manager dialog)
     */
    public function create(Request $request)
    {
        try {
            $appName = preg_replace('/[^a-zA-Z0-9_\-]/', '', config('app.name', 'System'));
            if (empty($appName)) $appName = 'Backup';
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename  = "backup_{$appName}_{$timestamp}.zip";

            $sqlDump = $this->generateSqlDump();

            // Save to local storage
            $storagePath = storage_path("app/backups/{$filename}");
            if (!is_dir(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0775, true);
            }

            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                $zip->open($storagePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                $zip->addFromString('database_dump.sql', $sqlDump);
                $zip->addFromString('backup_info.json', json_encode([
                    'created_at'  => now()->toIso8601String(),
                    'app_name'    => config('app.name'),
                    'db_name'     => config('database.connections.mysql.database'),
                    'version'     => '1.0',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $zip->close();
            } else {
                // Fallback: save as plain SQL if ZipArchive is unavailable
                file_put_contents($storagePath, $sqlDump);
                $filename = str_replace('.zip', '.sql', $filename);
                $storagePath = str_replace('.zip', '.sql', $storagePath);
            }

            // Serve the file as download (triggers browser save-dialog / file manager)
            return response()->download($storagePath, $filename, [
                'Content-Type'        => 'application/zip',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ])->deleteFileAfterSend(false);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء النسخة الاحتياطية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحقق من صلاحية ملف النسخة الاحتياطية المرفوع
     */
    public function validate(Request $request)
    {
        $request->validate(['backup_file' => 'required|file']);

        $file     = $request->file('backup_file');
        $ext      = strtolower($file->getClientOriginalExtension());
        $origName = $file->getClientOriginalName();

        if (!in_array($ext, ['zip', 'sql'])) {
            return response()->json([
                'valid'   => false,
                'message' => 'صيغة الملف غير مدعومة. يجب أن يكون .zip أو .sql',
            ]);
        }

        // ─── Save to a temp path using getRealPath() directly ──────
        // Avoid re-reading through Laravel Storage to prevent ZipArchive issues
        $backupsDir = storage_path('app/backups');
        if (!is_dir($backupsDir)) {
            mkdir($backupsDir, 0775, true);
        }

        // Generate a safe unique destination name
        $safeBase   = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $origName);
        $destName   = $safeBase;
        $destPath   = $backupsDir . DIRECTORY_SEPARATOR . $destName;

        // Move uploaded file directly from PHP temp to our destination
        if (!move_uploaded_file($file->getRealPath(), $destPath)) {
            // Fallback: use Laravel move
            $file->move($backupsDir, $destName);
        }

        if (!file_exists($destPath) || filesize($destPath) === 0) {
            @unlink($destPath);
            return response()->json([
                'valid'   => false,
                'message' => 'فشل رفع الملف أو الملف فارغ',
            ]);
        }

        if ($ext === 'zip') {
            if (!class_exists('ZipArchive')) {
                // Can't validate, assume ok
                session([
                    'pending_restore_file' => "backups/{$destName}",
                    'pending_restore_name' => $destName,
                ]);
                return response()->json([
                    'valid'    => true,
                    'message'  => 'تم قبول الملف (التحقق من ZIP غير متاح)',
                    'filename' => $origName,
                ]);
            }

            $zip = new ZipArchive();
            $openResult = $zip->open($destPath);

            if ($openResult !== true) {
                @unlink($destPath);
                return response()->json([
                    'valid'   => false,
                    'message' => 'ملف ZIP تالف أو غير قابل للقراءة (رمز الخطأ: ' . $openResult . ')',
                ]);
            }

            $hasSql = false;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                if (str_ends_with(strtolower($zip->getNameIndex($i)), '.sql')) {
                    $hasSql = true;
                    break;
                }
            }
            $zip->close();

            if (!$hasSql) {
                @unlink($destPath);
                return response()->json([
                    'valid'   => false,
                    'message' => 'الملف لا يحتوي على بيانات SQL صالحة',
                ]);
            }
        }

        // Store in session for later restore step
        session([
            'pending_restore_file' => "backups/{$destName}",
            'pending_restore_name' => $destName,
        ]);

        return response()->json([
            'valid'    => true,
            'message'  => 'الملف سليم وجاهز للاستعادة',
            'filename' => $origName,
        ]);
    }

    /**
     * استعادة النسخة الاحتياطية (بعد التحقق من كلمة المرور)
     */
    public function restore(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        // Verify admin password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['success' => false, 'message' => 'كلمة المرور غير صحيحة']);
        }

        $restoreFile = session('pending_restore_file');
        if (!$restoreFile || !Storage::disk('local')->exists($restoreFile)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد ملف استعادة أو انتهت الجلسة']);
        }

        $fullPath = storage_path("app/{$restoreFile}");
        $ext      = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        try {
            $sql = '';
            if ($ext === 'zip' && class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($fullPath) !== true) {
                    return response()->json(['success' => false, 'message' => 'تعذّر فتح ملف ZIP']);
                }
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (str_ends_with(strtolower($name), '.sql')) {
                        $sql = $zip->getFromIndex($i);
                        break;
                    }
                }
                $zip->close();
            } else {
                $sql = file_get_contents($fullPath);
            }

            if (empty(trim($sql))) {
                return response()->json(['success' => false, 'message' => 'ملف SQL فارغ']);
            }

            // Execute the SQL dump statement by statement
            DB::unprepared($sql);

            // Clear pending session
            session()->forget(['pending_restore_file', 'pending_restore_name']);

            return response()->json(['success' => true, 'message' => 'تمت استعادة النسخة الاحتياطية بنجاح']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'فشل الاستعادة: ' . $e->getMessage()]);
        }
    }

    /**
     * تحميل نسخة محفوظة
     */
    public function download(Request $request, $filename)
    {
        $safe = basename($filename);
        $path = storage_path("app/backups/{$safe}");
        if (!file_exists($path)) abort(404);
        return response()->download($path, $safe);
    }

    /**
     * حذف نسخة احتياطية
     */
    public function destroy(Request $request, $filename)
    {
        $safe = basename($filename);
        $path = storage_path("app/backups/{$safe}");
        if (file_exists($path)) @unlink($path);
        return back()->with('success', 'تم حذف النسخة الاحتياطية');
    }

    // ─── Helpers ──────────────────────────────────────────

    private function getStoredBackups(): array
    {
        $dir     = storage_path('app/backups');
        $backups = [];
        if (!is_dir($dir)) return $backups;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($fullPath)) {
                $backups[] = [
                    'name'     => $file,
                    'size'     => round(filesize($fullPath) / 1024 / 1024, 2),
                    'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                ];
            }
        }
        // Sort newest first
        usort($backups, fn($a, $b) => strcmp($b['modified'], $a['modified']));
        return $backups;
    }

    private function generateSqlDump(): string
    {
        $dbName = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$dbName}";

        $sql = "-- Backup generated by Mojawharati Admin\n";
        $sql .= "-- Date: " . now()->toIso8601String() . "\n";
        $sql .= "-- Database: {$dbName}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey ?? array_values((array)$tableObj)[0];
            
            // CREATE TABLE
            $create = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $create[0]->{'Create Table'} ?? '';
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createSql . ";\n\n";

            // INSERT rows
            $rows = DB::table($table)->get();
            if ($rows->isEmpty()) continue;
            foreach ($rows as $row) {
                $row  = (array)$row;
                $cols = '`' . implode('`, `', array_keys($row)) . '`';
                $vals = implode(', ', array_map(function ($v) {
                    if (is_null($v)) return 'NULL';
                    return "'" . addslashes((string)$v) . "'";
                }, array_values($row)));
                $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES ({$vals});\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }
}
