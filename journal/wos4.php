<!DOCTYPE html>
<html>
<head>
	<title>WoS</title>
	<style>	body {background-color: #111; color:#AAA; font-family: Arial, Helvetica, sans-serif;} </style>
</head>
<body>

<?php
$dois = array('10.26773/mjssm.190301','10.26773/mjssm.190302','10.26773/mjssm.190303','10.26773/mjssm.190304','10.26773/mjssm.190305','10.26773/mjssm.190306','10.26773/mjssm.190307','10.26773/mjssm.190308','10.26773/mjssm.190309','10.26773/mjssm.190310');
$query = array();
foreach ($dois as $doi)
  $query[] = sprintf('<map name="%s"><val name="doi">%s</val></map>', htmlspecialchars($doi), htmlspecialchars($doi));
$request = sprintf('<?xml version="1.0" encoding="UTF-8" ?>
<request xmlns="http://www.isinet.com/xrpc42" src="app.id=API Demo">
  <fn name="LinksAMR.retrieve">
    <list>
      <!-- WHOS REQUESTING -->
      <map>   
        <val name="username">Montenegro_HG</val>
        <val name="password">M0nT3n3g#0</val>
      </map>
      <!-- WHATS REQUESTED -->
      <map>
        <list name="WOS">
          <val>timesCited</val>
          <val>ut</val>
          <val>doi</val>
          <val>pmid</val>
          <val>sourceURL</val>
          <val>citingArticlesURL</val>
          <val>relatedRecordsURL</val>
        </list>
      </map>
<!-- LOOKUP DATA -->
      <map>%s</map>
    </list>
  </fn>
</request>
', implode("\n", $query));
$context = array('http' => array ('method'=> 'POST', 'content' => $request, 'header' => 'Content-Type: text/xml'));
$response = file_get_contents('https://ws.isiknowledge.com/cps/xrpc', FALSE, stream_context_create($context));
$xml = simplexml_load_string($response);
$xml->registerXPathNamespace('t', 'http://www.isinet.com/xrpc42');
$errors = $xml->xpath("t:fn/t:error");
if (!empty($errors))
  exit((string) current($errors));
$results = array();

foreach ($xml->xpath("t:fn[@name='LinksAMR.retrieve']/t:map/t:map/t:map[@name='WOS']") as $item){
  $result = array();
  foreach ($item->val as $value)
    $result[(string) $value['name']] = (string) $value;  
  $results[$result['doi']] = $result;
}

$possibleValues = [
	'doi',
	'ut',
	'pmid',
	'timesCited',
	'sourceURL',
	'citingArticlesURL',
	'relatedRecordsURL',
	'title',
	'issn',
	'isbn',
	'issue',
	'volume',
	'year',
	'pages'
];

echo "<table>";
foreach($results as $res)
{
	foreach($possibleValues as $poss)
	{
	echo "<tr>";
		if(isset($res[$poss]) || array_key_exists($poss, $res))
			echo "<td><b>$poss:</b> </td>
				  <td>".$res[$poss]."</td>";
	echo "</tr>";
	}
	echo "<td>&nbsp;</td>";
}
echo "<table>";

echo "<br /><br /><b>RAW response:</b><br />";
print_r("<pre>".htmlspecialchars($response)."</pre>");

echo "<br /><br /><b>Array response:</b><br />";
echo "<pre>";
print_r($results);
echo "</pre>";
?>

</body>
</html>
