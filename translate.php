<?php

$directories = [
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
    'resources/views/users'
];

function containsArabic($string) {
    return preg_match('/[\x{0600}-\x{06FF}]/u', $string);
}

function processContent($content) {
    // 1. Text between HTML >Text<
    $content = preg_replace_callback('/>([^<>{]*?[\x{0600}-\x{06FF}][^<>{]*?)</u', function($matches) {
        $text = trim($matches[1]);
        if(empty($text) || strpos($text, '{') !== false || strpos($text, '@') !== false || strpos($text, '__(') !== false) {
            return $matches[0];
        }
        $wrapped = str_replace($text, "{{ __('" . addslashes($text) . "') }}", $matches[0]);
        return $wrapped;
    }, $content);

    // 2. placeholder="..." or title="..."
    $content = preg_replace_callback('/(placeholder|title)="([^"]*?[\x{0600}-\x{06FF}][^"]*?)"/u', function($matches) {
        $attr = $matches[1];
        $text = $matches[2];
        if(strpos($text, '{') !== false || strpos($text, '__(') !== false) return $matches[0];
        return $attr . '="{{ __(\'' . addslashes($text) . '\') }}"';
    }, $content);
    
    // 3. confirm('Arabic')
    $content = preg_replace_callback('/confirm\(\'([^\']*?[\x{0600}-\x{06FF}][^\']*?)\'\)/u', function($matches) {
        $text = $matches[1];
        if(strpos($text, '{') !== false || strpos($text, '__(') !== false) return $matches[0];
        return "confirm('{{ __(\'" . addslashes($text) . "\') }}')";
    }, $content);

    return $content;
}

$collectedStrings = [];

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $newContent = processContent($content);
            if ($content !== $newContent) {
                file_put_contents($path, $newContent);
            }
            
            // Extract translations
            preg_match_all('/__\(\'([^\']+)\'\)/u', $newContent, $m1);
            preg_match_all('/__\("([^"]+)"\)/u', $newContent, $m2);
            foreach($m1[1] as $match) $collectedStrings[] = stripslashes($match);
            foreach($m2[1] as $match) $collectedStrings[] = stripslashes($match);
        }
    }
}

$collectedStrings = array_unique($collectedStrings);

$enJsonPath = 'lang/en.json';
$existing = [];
if(file_exists($enJsonPath)) {
    $existing = json_decode(file_get_contents($enJsonPath), true) ?: [];
}

$added = 0;
foreach($collectedStrings as $str) {
    if(containsArabic($str) && !isset($existing[$str])) {
        // Simple heuristic translation guesses for basic words:
        $t = "Translated: " . $str;
        if(strpos($str, 'إضافة') !== false) $t = 'Add';
        elseif(strpos($str, 'حذف') !== false) $t = 'Delete';
        elseif(strpos($str, 'تعديل') !== false) $t = 'Edit';
        elseif(strpos($str, 'حفظ') !== false) $t = 'Save';
        elseif(strpos($str, 'إدارة') !== false) $t = 'Manage';
        elseif(strpos($str, 'بحث') !== false) $t = 'Search';
        elseif(strpos($str, 'تأكيد') !== false) $t = 'Confirm';
        elseif(strpos($str, 'هل أنت متأكد') !== false) $t = 'Are you sure?';

        $existing[$str] = $t;
        $added++;
    }
}

file_put_contents($enJsonPath, json_encode($existing, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Processed files and added $added new strings to en.json\n";
