<?php
require_once(dirname(__FILE__).'/../inc/init.php');
require_once(BASE.'inc/HTTPClient.php');
require_once(BASE.'inc/Amazon.php');
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

        $num = $item->OfferSummary->TotalNew +
               $item->OfferSummary->TotalUsed +
               $item->OfferSummary->TotalCollectible +
               $item->OfferSummary->TotalRefurbished;
        if(!$num) continue; // skip non available products

        if($item->OfferSummary->LowestNewPrice->FormattedPrice){
            $price = $item->OfferSummary->LowestNewPrice->FormattedPrice.' (new)';
        }else{
            $price = $item->OfferSummary->LowestUsedPrice->FormattedPrice.' (used)';
        }

        $stmt_ins->execute(array(
            $row['sid'],
            $item->ASIN,
            $item->ItemAttributes->Title,
            $price,
            $item->DetailPageURL,
            $item->MediumImage->URL,
            $item->EditorialReviews->EditorialReview->Content,
        ));
    }

}

