<?php
$dirs = [
    'resources/views/banners',
    'resources/views/bookings',
    'resources/views/categories',
    'resources/views/custom_designs',
    'resources/views/gold-prices',
    'resources/views/merchants',
    'resources/views/notifications',
    'resources/views/products',
    'resources/views/reports',
    'resources/views/settings',
    'resources/views/users',
    'resources/views/search'
];

$emptyFiles = [];
foreach($dirs as $d) {
    if(!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
    foreach($it as $f) {
        if($f->isFile() && str_ends_with($f->getFilename(), '.blade.php') && filesize($f->getPathname()) == 0) {
            $emptyFiles[] = str_replace('/', '\\', realpath($f->getPathname()));
        }
    }
}

$historyPath = getenv('APPDATA') . '\Code\User\History';
if (!is_dir($historyPath)) {
    die("No history path found: $historyPath");
}

echo "Found " . count($emptyFiles) . " empty files. Searching history...\n";

// Load all entries
$it = new DirectoryIterator($historyPath);
$fileHistories = [];

foreach ($it as $fileinfo) {
    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        $jsonPath = $fileinfo->getPathname() . '\entries.json';
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            if (isset($data['resource'])) {
                $resourcePath = str_replace('/', '\\', parse_url($data['resource'], PHP_URL_PATH));
                // Remove leading backslash from file:///c%3A/...
                $resourcePath = urldecode($resourcePath);
                if (preg_match('/^[a-zA-Z]:/', substr($resourcePath, 1))) {
                    $resourcePath = substr($resourcePath, 1);
                }
                
                foreach ($emptyFiles as $ef) {
                    // Compare exactly or case-insensitively
                    if (strcasecmp($resourcePath, $ef) === 0) {
                        if (!isset($fileHistories[$ef])) {
                            $fileHistories[$ef] = [];
                        }
                        if (isset($data['entries'])) {
                            foreach($data['entries'] as $entry) {
                                $backupPath = $fileinfo->getPathname() . '\\' . $entry['id'];
                                if (file_exists($backupPath)) {
                                    $fileHistories[$ef][] = [
                                        'timestamp' => $entry['timestamp'],
                                        'path' => $backupPath,
                                        'size' => filesize($backupPath)
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

foreach ($emptyFiles as $ef) {
    if (isset($fileHistories[$ef]) && count($fileHistories[$ef]) > 0) {
        $entries = $fileHistories[$ef];
        // Sort descending by timestamp
        usort($entries, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        
        $restored = false;
        foreach($entries as $entry) {
            // Find latest NON-EMPTY backup
            if ($entry['size'] > 0) {
                file_put_contents($ef, file_get_contents($entry['path']));
                echo "Restored $ef from VS Code history (" . date('Y-m-d H:i:s', $entry['timestamp']/1000) . ")\n";
                $restored = true;
                break;
            }
        }
        if (!$restored) {
            echo "Failed to find non-empty backup for $ef\n";
        }
    } else {
        echo "No backup found in history for $ef\n";
    }
}
