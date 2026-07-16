<?php
$rss = file_get_contents("https://gamehub.vn/portal/index.rss");
$xml = simplexml_load_string($rss, "SimpleXMLElement", LIBXML_NOCDATA);
$item = $xml->channel->item[0];
echo "Description: " . $item->description . "\n";
echo "Enclosure: " . print_r($item->enclosure, true) . "\n";

