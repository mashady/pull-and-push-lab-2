<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

function generateStockData() {
    $symbols = ["AAPL", "GOOG", "AMZN", "MSFT", "TSLA"];
    $symbol = $symbols[array_rand($symbols)];
    $price = rand(100, 1500) + rand(0, 99) / 100;
    return json_encode(['symbol' => $symbol, 'price' => $price, 'timestamp' => date("Y-m-d H:i:s")]);
}

while (true) {
    echo "data: " . generateStockData() . "\n\n";
    flush();
    sleep(5); 
}
?>
