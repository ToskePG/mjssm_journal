<?php



$doi = $a_result["doi"]; //"10.1002/advs.201500305"; // 10.1002/advs.201500305
$ut = $a_result["wos_id"]; //"000362092800006"; // 000362092800006
$wos = "";

if($ut != "")
{
	$wos = "ut";
}
elseif($doi != "")
{
	$wos = "doi";
}
else
{
	return;
}

$query = array();
$query[] = sprintf('<map name="%s"><val name="%s">%s</val></map>', htmlspecialchars($$wos), htmlspecialchars($wos), htmlspecialchars($$wos));
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
  return; // exit((string) current($errors));
$messages = $xml->xpath("t:fn[@name='LinksAMR.retrieve']/t:map/t:map/t:map[@name='WOS']/t:val[@name='message']");
if (!empty($messages))
  return; // exit((string) current($messages));
$results = array();

foreach ($xml->xpath("t:fn[@name='LinksAMR.retrieve']/t:map/t:map/t:map[@name='WOS']") as $item){
  $result = array();
  foreach ($item->val as $value)
    $result[(string) $value['name']] = (string) $value;  
  $results[$result[$wos]] = $result;
}

if(isset($results[$$wos]['timesCited']) || array_key_exists('timesCited', $results[$$wos])){
	$timesCited = $results[$$wos]['timesCited'];
}else{
	$timesCited = 0;
}

if($timesCited != "")
	$citingArticlesURL = $results[$$wos]['citingArticlesURL'];

	$sourceURL = $results[$$wos]['sourceURL'];

?>

<div style="color: #333; background-color: #F8E8D1; margin: auto; width: 180px; border: 1px solid; border-radius: 2px 15px; padding: 5px 0px;">
This article has been<br />cited in WoS <b><span style="color: #ec6807; font-size: 1.75em;"><?php echo $timesCited; ?></span></b> times<br />
—————————————<br />
<a href="<?php echo $sourceURL; ?>" target="_blank"><span style="color: #ec6807; font-weight: bold;">Article on Web of Science</span></a><br />
<?php
if($timesCited!=""){
?>
—————————————<br />
<a href="<?php echo $citingArticlesURL; ?>" target="_blank"><span style="color: #ec6807; font-weight: bold;">List of citing articles</span></a>
<?php
}
?>

</div>

<?php
/*$possibleValues = [
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
echo "</pre>";*/
?>
