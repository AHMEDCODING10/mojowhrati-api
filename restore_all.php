<?php
$emptyFiles = [
    'resources/views/banners/create.blade.php',
    'resources/views/banners/edit.blade.php',
    'resources/views/banners/index.blade.php',
    'resources/views/bookings/create.blade.php',
    'resources/views/bookings/index.blade.php',
    'resources/views/bookings/show.blade.php',
    'resources/views/categories/create.blade.php',
    'resources/views/categories/edit.blade.php',
    'resources/views/categories/index.blade.php',
    'resources/views/custom_designs/index.blade.php',
    'resources/views/custom_designs/show.blade.php',
    'resources/views/gold-prices/index.blade.php',
    'resources/views/merchants/index.blade.php',
    'resources/views/merchants/show.blade.php',
    'resources/views/merchants/verify.blade.php',
    'resources/views/notifications/index.blade.php',
    'resources/views/products/create.blade.php',
    'resources/views/products/index.blade.php',
    'resources/views/products/show.blade.php',
    'resources/views/reports/index.blade.php',
    'resources/views/settings/index.blade.php',
    'resources/views/users/create.blade.php',
    'resources/views/users/edit.blade.php',
    'resources/views/users/index.blade.php',
    'resources/views/users/show.blade.php',
    'resources/views/search/index.blade.php'
];

$baseDir = realpath(__DIR__);
$historyPaths = [
    getenv('APPDATA') . '\Cursor\User\History',
    getenv('APPDATA') . '\Code\User\History'
];

foreach ($emptyFiles as $relPath) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath);
    echo "--- Searching for $relPath ---\n";
    $found = false;

    foreach ($historyPaths as $hPath) {
        if (!is_dir($hPath)) continue;

        $it = new DirectoryIterator($hPath);
        foreach ($it as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $json = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . 'entries.json';
                if (file_exists($json)) {
                    $data = json_decode(file_get_contents($json), true);
                    if (isset($data['resource'])) {
                        $resUri = urldecode($data['resource']);
                        // Cursor uses file:///c:/...
                        if (stripos($resUri, str_replace('\\', '/', $relPath)) !== false) {
                            echo "Match in $hPath: " . basename($fileinfo->getPathname()) . "\n";
                            if (isset($data['entries']) && count($data['entries']) > 0) {
                                usort($data['entries'], function($a, $b) { return $b['timestamp'] <=> $a['timestamp']; });
                                foreach($data['entries'] as $entry) {
                                    $backup = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . $entry['id'];
                                    if (file_exists($backup) && filesize($backup) > 500) { // arbitrary size gate to skip empty ones
                                        copy($backup, $fullPath);
                                        echo "RESTORED from " . date('Y-m-d H:i:s', $entry['timestamp']/1000) . "\n";
                                        $found = true;
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if (!$found) echo "NOT FOUND in history.\n";
}
