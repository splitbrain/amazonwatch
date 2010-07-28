<?php
require_once(dirname(__FILE__).'/inc/init.php');

$stmt_srch = $PDO->prepare(
    'SELECT *, strftime(\'%s\',added) as added
       FROM search
      WHERE sid = ?'
);
$stmt_srch->execute(array((int) $_REQUEST['sid']));
$info = $stmt_srch->fetch(PDO::FETCH_ASSOC);
if(!$info){
    header("HTTP/1.0 404 Not Found");
    echo 'No such monitoring';
    exit;
}

header('Content-Type:  text/xml; charset=utf-8');
echo '<?xml version="1.0"?>';
echo '<rss version="2.0">';
echo '   <channel>';
echo '      <title>'.htmlspecialchars($info['query']).' at Amazon.'.htmlspecialchars($info['region']).'</title>';
echo '      <link>http://fixme/</link>';
echo '      <description>Monitoring Amazon Product Availability</description>';

$stmt_srch = $PDO->prepare(
    'SELECT *, strftime(\'%s\',dt) as dt
       FROM search_results
      WHERE sid = ?
   ORDER BY dt DESC'
);
$stmt_srch->execute(array((int) $_REQUEST['sid']));

while($row = $stmt_srch->fetch(PDO::FETCH_ASSOC)){
    echo '<item>';
    echo '<title>'.htmlspecialchars($row['price'].' - '.$row['title']).'</title>';
    echo '<link>'.$row['url'].'</link>';
    echo '<description>'.htmlspecialchars('<img src="'.$row['image'].'" align="left" /> '.$row['more']).'</description>';
    echo '<pubDate>'.date('r',$row['dt']).'</pubDate>';
    echo '<guid>'.$row['url'].'</guid>';
    echo '</item>';
}

echo '</channel>';
echo '</rss>';

// set date
$stmt_upd = $PDO->prepare('UPDATE search SET lastget = NOW() WHERE sid = ?');
$stmt_upd->execute(array((int) $_REQUEST['sid']));

