<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

while (true) {
    $message = "Random message at " . date("H:i:s") . "\n";
    
    echo "data: {$message}\n\n";
    
    ob_flush();
    flush();
    
    sleep(3);
}
?>
