<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once('Amazon.php');
require_once('config.php');

$PDO = new PDO('sqlite:database.sqlite');
$AMZ = new Amazon($CONF['public_key'],$CONF['private_key']);


$stmt_srch = $PDO->prepare(
    "SELECT *
       FROM search
      WHERE added > datetime('NOW','-90 days')
        AND lastget > datetime('NOW','-5 days')"
);
$stmt_ins  = $PDO->prepare(
    'INSERT OR IGNORE INTO search_results
            (sid, asin, title, price, url, image, more)
     VALUES (?,?,?,?,?,?,?)'
);

$stmt_srch->execute();
while($row = $stmt_srch->fetch(PDO::FETCH_ASSOC)){
    printf("%5s %s\n",$row['region'],$row['query']);

    $AMZ->setRegion($row['region']);
    $items = $AMZ->search($row['query']);

    foreach($items as $item){
        $stmt_ins->execute(array(
            $row['sid'],
            $item->ASIN,
            $item->ItemAttributes->Title,
            $item->OfferSummary->LowestNewPrice->FormattedPrice,
            $item->DetailPageURL,
            $item->MediumImage->URL,
            $item->EditorialReviews->EditorialReview->Content,
        ));
    }

}

