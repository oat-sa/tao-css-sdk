# Batch apply a stylesheet to multiple items
You can use the script `tasks/css-batch-tool/css-batch-tool.php` to apply your stylesheet to multiple items. Make sure you have the right permissions on the item directory that on a regular installation resides under `/data/taoItems/itemData`. You should act under the same user name your server does. Under the assumption that this is `www-data` and you are running a `*nix` system, type on the command line <pre>sudo -u www-data // along with one of the following commands</pre>

The command line is <pre>sudo -u www-data php css-batch-tool.php path/to/stylesheet.css</pre>. 

Alternatively you can place the stylesheet (only one!) in the directory `tasks/css-batch-tool/` and call the script without an argument. <pre>sudo -u www-data php css-batch-tool.php</pre>

It is assumed that the SDK is placed under `tao-root/vendors`. You can run the script from any other place, you will however need to change the line `require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/tao/includes/raw_start.php';` to reflect this change.