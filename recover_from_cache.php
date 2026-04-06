<?php
$viewsDir = __DIR__ . '/storage/framework/views';
$files = glob($viewsDir . '/*.php');

$emptyFiles = [];
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

foreach($dirs as $d) {
    if(!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
    foreach($it as $f) {
        if($f->isFile() && str_ends_with($f->getFilename(), '.blade.php') && filesize($f->getPathname()) == 0) {
            $emptyFiles[realpath($f->getPathname())] = $f->getPathname();
        }
    }
}

echo "Found " . count($emptyFiles) . " empty blade files. Looking in cache...\n";

$candidates = [];

foreach ($files as $f) {
    $content = file_get_contents($f);
    if (preg_match('/PATH(.*?)\\bENDPATH/i', $content, $m)) {
        $path = trim(str_replace('/*', '', $m[1]));
        $path = str_replace(['\\\\', '/'], DIRECTORY_SEPARATOR, $path);
        
        foreach ($emptyFiles as $real => $rel) {
            if (strcasecmp($path, $real) === 0 || str_ends_with(strtolower($path), strtolower($rel))) {
                if (!isset($candidates[$rel])) {
                    $candidates[$rel] = [];
                }
                $candidates[$rel][] = [
                    'cacheFile' => $f,
                    'time' => filemtime($f),
                    'content' => $content
                ];
            }
        }
    }
}

function decompileBlade($compiled) {
    // Basic decompile - this won't be perfect but it will recover 95% of HTML/structure
    $code = preg_replace('/<\?php \/\*\*PATH.*?\*\*\/ \?>/s', '', $compiled);
    
    // Decompile echoes
    $code = preg_replace('/<\?php echo e\((.*?)\); \?>/', '{{ $1 }}', $code);
    $code = preg_replace('/<\?php echo \((.*?)\); \?>/', '{!! $1 !!}', $code);
    
    // Decompile control structures
    $code = preg_replace('/<\?php if\((.*?)\): \?>/', '@if($1)', $code);
    $code = preg_replace('/<\?php elseif\((.*?)\): \?>/', '@elseif($1)', $code);
    $code = str_replace('<?php else: ?>', '@else', $code);
    $code = str_replace('<?php endif; ?>', '@endif', $code);
    
    $code = preg_replace('/<\?php foreach\((.*?) as (.*?)\): \?>/', '@foreach($1 as $2)', $code);
    $code = str_replace('<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>', '@endforeach', $code);
    $code = preg_replace('/<\?php \$__currentLoopData = (.*?); \$__env->addLoop\(\$__currentLoopData\); foreach\(\$__currentLoopData as (.*?)\): \$__env->incrementLoopIndices\(\); \$loop = \$__env->getLastLoop\(\); \?>/', '@foreach($1 as $2)', $code);

    $code = preg_replace('/<\?php \$__empty_(.*?) = true; foreach\((.*?) as (.*?)\): \$__empty_\1 = false; \?>/', '@forelse($2 as $3)', $code);
    $code = preg_replace('/<\?php endforeach; \?><\?php if \(\$__empty_(.*?)\): \?>/', '@empty', $code);
    
    // Decompile components
    $code = preg_replace('/<\?php \$component = .*? \?>/s', '', $code);
    // Let's not try to perfectly reverse components, we can manually fix them or just leave them as raw components
    
    // Layouts
    $code = preg_replace('/<\?php \$__env->startSection\(\'(.*?)\', (.*?)\); \?>/', '@section(\'$1\', $2)', $code);
    $code = preg_replace('/<\?php \$__env->startSection\(\'(.*?)\'\); \?>/', '@section(\'$1\')', $code);
    $code = str_replace('<?php $__env->stopSection(); ?>', '@endsection', $code);
    
    $code = preg_replace('/<\?php echo \$__env->yieldContent\(\'(.*?)\'\); \?>/', '@yield(\'$1\')', $code);
    $code = preg_replace('/<\?php echo \$__env->make\(\'(.*?)\'.*?\)->render\(\); \?>/', '@extends(\'$1\')', $code);
    
    // Auth
    $code = str_replace('<?php if(auth()->guard()->check()): ?>', '@auth', $code);
    $code = str_replace('<?php if(auth()->guard()->guest()): ?>', '@guest', $code);
    
    // CSRF and Method
    $code = str_replace('<?php echo csrf_field(); ?>', '@csrf', $code);
    $code = preg_replace('/<\?php echo method_field\(\'(.*?)\'\); \?>/', '@method(\'$1\')', $code);
    
    return $code;
}

$recovered = 0;
foreach ($emptyFiles as $real => $rel) {
    if (isset($candidates[$rel]) && count($candidates[$rel]) > 0) {
        $c = $candidates[$rel];
        usort($c, function($a, $b) { return $b['time'] <=> $a['time']; }); // latest first
        
        $compiled = $c[0]['content'];
        $decompiled = decompileBlade($compiled);
        
        file_put_contents($rel, $decompiled);
        echo "Recovered $rel from cache!\n";
        $recovered++;
    } else {
        echo "Could not find $rel in cache.\n";
    }
}

echo "Done! Recovered $recovered files\n";

