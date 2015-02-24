
<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
 
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
$root         = dirname(dirname($originalPath));
$cssRefPath   = $root . '/css-reference';

chdir($root);

$objects  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./scss'), RecursiveIteratorIterator::SELF_FIRST);

foreach($objects as $scssFile => $object){
    if(strtolower(substr(strrchr($scssFile, '.'), 1)) !== 'scss') {
        continue;
    }
    $newPath = './tasks/create-css-reference-file' . ltrim($scssFile, '.');
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

system('sass main.scss ' . $cssRefPath . '/css-reference-file.css --style expanded');

// remove dummy code, beautify
$css = str_replace(
    array("background: red;", '}', "{\n", "{\r\n", ", ", '/*# sourceMappingURL=reference.css.map */'), 
    array('', "}\n", '{', '{', ",\n", ''), 
    file_get_contents($cssRefPath . '/css-reference-file.css'));

file_put_contents($cssRefPath . '/css-reference-file.css', trim($css));