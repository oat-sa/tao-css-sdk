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
 * Script to batch apply a stylesheet to a number of items
 */

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/tao/includes/raw_start.php';


use Jig\Utils\FsUtils;

try {
    // stylesheet can be either an argument...
    if(empty($argv[1])) {
        $localCss = glob(__DIR__ . '/*.css');
        if(!count($localCss)) {
            throw new Exception('No CSS file given');
        }
        $cssFile = array_shift($localCss);
    }
    // ...or a _single_ CSS file in the same directory
    else {
        if(!is_file($argv[1])) {
            throw new Exception('CSS file ' . $argv[1] . ' not found');
        }
        $cssFile = $argv[1];
    }

    $cssName = basename($cssFile);

    $itemPath = FsUtils::normalizePath(FILES_PATH . '/taoItems/itemData');

    // no permissions
    if(!is_writable($itemPath)){
        throw new Exception($itemPath . ' is not writable');
    }

    // invalid path
    if(!is_dir($itemPath)) {
        throw new Exception($itemPath . ' does not exist');
    }

    // no items found in path
    $itemList = glob($itemPath . '/*', GLOB_ONLYDIR);
    if(!count($itemList)) {
        throw new Exception('No items found in ' . FILES_PATH);
    } 

    $qtiItemList = array();

    foreach($itemList as $item) {

        foreach(glob($item . '/itemContent/*', GLOB_ONLYDIR) as $l10n) {
            if(!preg_match('~[a-z]-[A-Z]~', $l10n)) {
                continue;
            }

            $qtiXml = $l10n . '/qti.xml';

            // exclude non qti items
            if(!is_file($qtiXml)) {
                continue;
            }

            // copy CSS file to item
            copy($cssFile, $l10n . '/' . $cssName);

            $xml = simplexml_load_file($qtiXml);

            // Don't modify an item that already contains the custom
            $addStyleNode = true;
            foreach($xml[0]->stylesheet as $stylesheet) {
                if((string)$stylesheet->attributes() -> href === $cssName) {
                    $addStyleNode = false;
                    break;
                }
            }

            if($addStyleNode) {
                $css = $xml->addChild('stylesheet', '');
                $css -> addAttribute('href', basename($cssFile));
                $css -> addAttribute('type','text/css');
                $css -> addAttribute('media','all');
                $css -> addAttribute('title','');
                file_put_contents($qtiXml, $xml->asXML());               
                $qtiItemList['insert'][] = (string)$xml[0]->attributes()->title;
            }
            else {                
                $qtiItemList['update'][] = (string)$xml[0]->attributes()->title;
            }

        }
    }

    // there are items but none of them is QTI
    if(empty($qtiItemList['insert']) && empty($qtiItemList['update'])) {
        throw new Exception('No QTI items found in ' . FILES_PATH);
    }

    if(!empty($qtiItemList['insert'])) {
        printf ("\nAdded stylesheet %s to %d item(s):\n\n", $cssName, count($qtiItemList['insert']));
        print  '  * ' . implode("\n  * ", $qtiItemList['insert']) . "\n\n";
    }

    if(!empty($qtiItemList['update'])) {
        printf ("\nUpdated stylesheet %s in %d item(s):\n\n", $cssName, count($qtiItemList['update']));
        print  '  * ' . implode("\n  * ", $qtiItemList['update']) . "\n\n";
    }

}
catch(Exception $e) {
    print $e -> getMessage();
}



