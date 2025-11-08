<?php
require_once 'acid_conf.php';
require_once ADODB_PATH . 'adodb.inc.php';
require_once PHPLOT_PATH . 'phplot.php';

$db = ADONewConnection('mysqli');
$db->Connect(DBHOST, DBUSER, DBPASS, DBNAME);

// Get top 10 signatures (alert types) and their event count
$sql = "
    SELECT signature, COUNT(*) AS cnt
    FROM event
    GROUP BY signature
    ORDER BY cnt DESC
    LIMIT 10
";
$results = $db->Execute($sql);

// Build data array for PHPlot
$data = [];
while (!$results->EOF) {
    $data[] = [$results->fields['signature'], (int)$results->fields['cnt']];
    $results->MoveNext();
}


if (empty($data)) {
    $data = [
        ['No Data', 1]
    ];
}

$plot = new PHPlot(800, 400);
$plot->SetDataType('text-data-single');
$plot->SetDataValues($data);
$plot->SetTitle('Top 10 Alert Signatures');
$plot->SetXTitle('Signature');
$plot->SetYTitle('Count');
$plot->SetPlotType('bars');
$plot->SetImageBorderType('plain');
$plot->DrawGraph();
?>
