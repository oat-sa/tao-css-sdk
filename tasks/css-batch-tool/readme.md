# Batch apply a stylesheet to multiple items
You can use the script `tasks/css-batch-tool/css-batch-tool.php` to apply your stylesheet to multiple items. 

The command line is <pre>php css-batch-tool.php path/to/stylesheet.css</pre>. 

Alternatively you can place the stylesheet (only one!) in the directory `tasks/css-batch-tool/` and call the script without an argument. <pre>php css-batch-tool.php</pre>

It is assumed that the SDK is placed under `tao-root/vendors`. You can run the script from any other place, you will however need to change the line `require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/tao/includes/raw_start.php';` to reflect this change.