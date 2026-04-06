<?php
$dirs = ['resources/views', 'app'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $newContent = str_replace('citalia', 'citalify', $content);
            $newContent = str_replace('Citalia', 'Citalify', $newContent);
            $newContent = str_replace('CITALIA', 'CITALIFY', $newContent);
            if ($content !== $newContent) {
                file_put_contents($path, $newContent);
                echo "Replaced in: $path\n";
            }
        }
    }
}
echo "Done.\n";
