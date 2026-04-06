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
    'resources/views/users',
    'resources/views/search'
];

$fixes = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $newContent = $content;

            // Fix 1: confirm('{{ __(\'...\') }}')
            $newContent = preg_replace_callback("/\{\{ __\(\\\\'(.*?)\\\\'\) \}\}/u", function($matches) {
                return "{{ __('" . str_replace("\\'", "'", $matches[1]) . "') }}";
            }, $newContent);

            // Fix 2: {{ $var->{{ __('prop }} text') }}
            // We want to unwrap nested things like: {{ $category->{{ __('products_count }} قطعة') }}
            // => {{ $category->products_count }} {{ __('قطعة') }}
            $newContent = preg_replace_callback("/\{\{ \\$([a-zA-Z0-9_]+)->\{\{ __\('([a-zA-Z0-9_]+) \}\} (.*?)'\) \}\}/u", function($m) {
                return "{{ \$" . $m[1] . "->" . $m[2] . " }} {{ __('" . $m[3] . "') }}";
            }, $newContent);

            // Fix 3: {{ $user->{{ __('role === \'merchant\' ? \'🏪 تاجر مجوهرات\' : \'👤 عميل منصة\') }} }}
            // Just revert to standard text.
            $newContent = preg_replace_callback("/\{\{ \\$([a-zA-Z0-9_]+)->\{\{ __\('([^']+)'\) \}\} \}\}/u", function($m) {
                return "{{ \$" . $m[1] . "->" . str_replace("\\'", "'", $m[2]) . " }}";
            }, $newContent);

            // Fix 4: DiffForHumans
            $newContent = preg_replace_callback("/\{\{ __('diffForHumans\(\) : \\\\'([^']+)\\\\' \}\}') \}\}/u", function($m) {
                return "diffForHumans() ?? __('" . $m[1] . "') }}";
            }, $newContent);

            // Fix 5: final_price
            $newContent = preg_replace_callback("/\{\{ __('final_price\) \}\} (.*?)'\) \}\}/u", function($m) {
                return "final_price) }} {{ __('" . $m[1] . "') }}";
            }, $newContent);

            // Fix 6: {{ $product->{{ __('manufacturer ?? \'غير محدد\' }}') }}
            $newContent = str_replace("{{ __('manufacturer ?? \\'غير محدد\\' }}')", "manufacturer ?? __('غير محدد') }}", $newContent);

            // Fix 7: {{ __('status === \'active\' ? \'🚫 تعطيل الحساب\' : \'✅ تفعيل الحساب\' }}') }}
            $newContent = preg_replace_callback("/\{\{ __('status === \\\\'([^\']+)\\\\' \? \\\\'([^\']+)\\\\' : \\\\'([^\']+)\\\\' \}\}') \}\}/u", function($m) {
                return "status === '" . $m[1] . "' ? __('" . $m[2] . "') : __('" . $m[3] . "') }}";
            }, $newContent);
            
            // Fix 8: {{ __('status === \'blocked\' ? \'🚫 محظور مؤقتاً\' : \'⏳ قيد المراجعة\' }}') }}
            $newContent = preg_replace_callback("/\{\{ __('status === \\\\'([^\']+)\\\\' \? \\\\'([^\']+)\\\\' : \\\\'([^\']+)\\\\'\)') \}\}/u", function($m) {
                return "status === '" . $m[1] . "' ? __('" . $m[2] . "') : __('" . $m[3] . "')";
            }, $newContent);

            // General Fix 9: Any arbitrary {{ __('... }}') }}
            $newContent = preg_replace_callback("/\{\{ __('(.*?) \}\}') \}\}/u", function($m) {
                return $m[1] . " }}";
            }, $newContent);

            // General Fix 10: Any {{ $var->{{ __('xxx') }}
            // Just unwrap the inner {{ __('xxx') }} back to xxx
            $newContent = preg_replace_callback("/\{\{ __\('([^']+)'\) \}\}/u", function($m) {
                // If it's a valid string with no php code, keep it, otherwise unwrap
                if(strpos($m[1], '===') !== false || strpos($m[1], '??') !== false) {
                    return str_replace("\\'", "'", $m[1]);
                }
                return $m[0];
            }, $newContent);

            if ($content !== $newContent) {
                file_put_contents($path, $newContent);
                $fixes++;
            }
        }
    }
}
echo "Applied fixes to $fixes files.\n";
