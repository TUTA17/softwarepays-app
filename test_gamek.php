
require "vendor/autoload.php";
$rss = file_get_contents("https://gamek.vn/the-gioi-game.rss");
$xml = simplexml_load_string($rss, "SimpleXMLElement", LIBXML_NOCDATA);
$link = (string)$xml->channel->item[0]->link;
$link = str_replace("cl44.cnnd.vn", "gamek.vn", $link);
$html = file_get_contents($link);
if (preg_match("/<div class=\"detail-content\">(.*?)<div class=\"author-info/is", $html, $m)) {
    echo "Found detail-content, length: " . strlen($m[1]);
} elseif (preg_match("/<div class=\"right-detail\">(.*?)<div class=\"author-info/is", $html, $m)) {
    echo "Found right-detail, length: " . strlen($m[1]);
} else {
    echo "Could not find content container. First 500 chars: \n";
    echo substr(strip_tags($html), 0, 500);
}

