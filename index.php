<?php
require_once(dirname(__FILE__).'/inc/init.php');
header('Content-Type: text/html; charset=utf-8');
?>
<html>
<head>
    <title>Amazon Watch</title>
</head>
<body>

<form action="" method="post">
    <fieldset>
        <legend>Monitor Search Results</legend>
        <label for="query">Search for</label>
        <input type="text" name="query" id="query" />

        <label for="region">at</label>
        <select name="region" id="region">
            <option value="com">Amazon.com</option>
            <option value="de">Amazon.de</option>
            <option value="co.uk">Amazon.co.uk</option>
            <option value="fr">Amazon.fr</option>
        </select>.<br />

        <input type="submit" value="Create Monitor Feed" />
    </fieldset>
</form>

<?php
if($_REQUEST['query']){
    $ins = $PDO->prepare('INSERT INTO search (query,region) VALUES (?,?)');
    $ins->execute(array($_REQUEST['query'],$_REQUEST['region']));
    $sid = $PDO->lastInsertId();

    echo '<p>Your search term will now be monitored for three months.
             All found products will be added to the following RSS feed:</p>';

    echo '<a href="searchrss.php?sid='.$sid.'" target="_blank">Amazon Watch Feed</a>';

    echo '<p>Please subscribe to the feed to be informed when Amazon adds new
             products matching your query.</p>';
}
?>

</body>
