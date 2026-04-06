<?php
$dirs = ['resources/views/banners','resources/views/bookings','resources/views/categories','resources/views/custom_designs','resources/views/gold-prices','resources/views/merchants','resources/views/notifications','resources/views/products','resources/views/reports','resources/views/settings','resources/views/users','resources/views/search'];
$empty = [];
foreach($dirs as $d) {
    if(!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
    foreach($it as $f) {
        if($f->isFile() && str_ends_with($f->getFilename(), '.blade.php') && filesize($f->getPathname()) == 0) {
            $empty[] = $f->getPathname();
        }
    }
}
echo implode("\n", $empty);
