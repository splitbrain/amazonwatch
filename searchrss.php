<?php
error_reporting(E_ALL ^ E_NOTICE);
$PDO = new PDO('sqlite:database.sqlite');
$stmt_srch = $PDO->prepare('SELECT * FROM search_results WHERE sid = ?');
$stmt_srch->execute(array((int) $_REQUEST['sid']));


header('Content-Type:  text/xml; charset=utf-8');

echo '<?xml version="1.0"?>';
echo '<rss version="2.0">';
echo '   <channel>';
echo '      <title>Amazon Watch</title>';
echo '      <link>http://fixme/</link>';
echo '      <description>Monitor Amazon Product Availability</description>';
echo '      <pubDate>Tue, 10 Jun 2003 04:00:00 GMT</pubDate>';
echo '      <lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>';

while($row = $stmt_srch->fetch(PDO::FETCH_ASSOC)){
    echo '<item>';
    echo '<title>'.htmlspecialchars($row['price'].' - '.$row['title']).'</title>';
    echo '<link>'.$row['url'].'</link>';
    echo '<description>'.htmlspecialchars('<img src="'.$row['image'].'" align="left" /> '.$row['more']).'</description>';
    echo '<pubDate>Tue, 03 Jun 2003 09:39:21 GMT</pubDate>';
    echo '<guid>'.$row['url'].'</guid>';
    echo '</item>';
}

echo '</channel>';
echo '</rss>';
