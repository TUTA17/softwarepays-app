<?php
$urls = [
    "https://gamehub.vn/rss",
    "https://gamehub.vn/feed",
    "https://gamehub.vn/rss.xml",
    "https://gamehub.vn/tin-tuc/rss",
    "https://gamehub.vn/tin-tuc/index.rss"
];
foreach($urls as $url) {
    echo "Checking: $url\n";
    $headers = @get_headers($url, 1);
    if($headers) {
        echo "Status: " . $headers[0] . "\n\n";
    }
}

