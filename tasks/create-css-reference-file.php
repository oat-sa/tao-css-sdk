
<?php
/**
 * Generate CSS reference file for CSS SDK
 *
 * 2015-02-09 Dieter Raber <dieter@taotesting.com>
 */

function createDir($directory, $perms = 0777) {
    if (!file_exists($directory) && !is_dir($directory)) {
        mkdir($directory, $perms, true);
    }
}

function lineToBlockComments($matches) {
    $matches = preg_split('~(\r|\n)+~', $matches[0]);
    $matches = array_filter($matches);
    foreach($matches as &$match) {
        $match = ltrim(trim($match), '//');
        $match = trim($match);
    }
    $matches = "\n/* " . implode("\n", $matches) . " */\n";
    return $matches;
}


// make sure you always start in project root
$originalPath = __DIR__;
$cssReference = $originalPath . '/css/reference.css';

chdir($originalPath . '/..');


$basePath = './scss';
$objects  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath), RecursiveIteratorIterator::SELF_FIRST);

foreach($objects as $scssFile => $object){
    if(strtolower(substr(strrchr($scssFile, '.'), 1)) !== 'scss') {
        continue;
    }
    $newPath = $originalPath . ltrim($scssFile, '.');
    createDir(dirname($newPath));

    // bootstrap and such
    // don't transform comments
    if(basename(dirname($scssFile)) === 'inc') {
        copy($scssFile, $newPath);
        continue;
    }

    $content = preg_replace_callback('~(\s*//([^\n])*\n)+~', 'lineToBlockComments', file_get_contents($scssFile));
    // adding a rule makes sure this code is used by SASS
    $content = str_replace('{', "{background: red;", $content );
    file_put_contents($newPath, $content);
}

chdir($originalPath . '/scss');

system('sass main.scss ../' . basename(dirname($cssReference)) . '/' . basename($cssReference) . ' --style expanded');

// remove dummy code, beautify
$css = str_replace(
    array("background: red;", '}', "{\n", "{\r\n", ", ", '/*# sourceMappingURL=reference.css.map */'), 
    array('', "}\n", '{', '{', ",\n", ''), 
    file_get_contents($cssReference));
createDir(dirname($originalPath) . '/css-reference-file');
file_put_contents(dirname($originalPath) . '/css-reference-file/css-reference-file.css', trim($css));
