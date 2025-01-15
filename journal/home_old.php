<div id="lijevi">
      <img src="images/knjigica-3d-12.jpg" />
      <h3>Dear Readers,</h3>

<p>Even though our Journal in 2015 and 2016 achieved the greatest success, and entered two strongest index databases (Web of Science and Scopus), we believe that the turning point, that is, the point from which our Journal would continue to grow faster and to develop more, was entering the year 2017 when we reformed the editorial board of the Journal, and introduced a significant number of young people who are, we are sure, the new driving force, who will lead our Journal to the goal that we set in the forthcoming five-year mandate period, to make it a solid leader in the region and recognized by all relevant authors in the field of sports science and medicine around the world.</p>

<p>The Montenegrin Journal of Sports Science and Medicine (MJSSM) continues facing the tremendous challenges in last months too. Even though our Journal in 2015 and 2016 achieved the greatest success, and entered two strongest index databases (Web of Science and Scopus), the rest of databases continue recognizing the development of our journal that is proved by reaching highest impact ever, such as Index Copernicus (ICV 2016: 98.15) and Scopus (CiteScoreTracker 2017: 0.50, updated 08 February, 2018). On the other hand, the acceptance rate was decreased on 15% for original research submitted in period 2016-2017 and expected to decrease it further more for the upcoming period, while the time from submission to first decision is also decreased on 38 days, and the time from submission to publication on 55 days (online & print).</p>

<p>We would also highlight that our journal will continue working on growing academic publication in the fields of sports science and medicine; all clinical aspects of exercise, health, and sport; exercise physiology and biophysical investigation of sports performance; sport biomechanics; sports nutrition; rehabilitation, physiotherapy; sports psychology; sport pedagogy, sport history, sport philosophy, sport sociology, sport management; and all aspects of scientific support of the sports coaches from the natural, social and humanistic side, in various formats: original papers, review papers, editorials, short reports, peer review - fair review, as well as invited papers and award papers, as well as promote all other academic activities of Montenegrin Sports Academy and Faculty for sport and Physical Education at University of Montenegro, such as publishing of academic books, conference proceedings, brochures etc.</p>

<p>Finally, we would like to thank our authors one more time, who have chosen precisely our Journal to publish their scientific papers, and we would like to invite them to continue our cooperation to our mutual satisfaction, since we intend to develop our journal as “open access” Journal, free of any claims against the authors, because we do believe that to be the best way we can achieve our basic idea for which we have established this Journal, and that is to promote science and scientific achievements and its availability to all interested users without any restrictions.</p>

<p>Thank you for reading us and we hope you will find this issue of MJSSM informative enough.</p>

<p>Editors-in-Chief<br />
Prof. Dusko Bjelica, PhD<br />
Assist. Prof. Stevo Popovic, PhD</p>





</div>
<div id="desni">
	  <h3>Current Issue</h3>

<?php
function fetch_autor($aut) {
	$aa_query = mysql_query("SELECT * FROM autori WHERE `id`='$aut' LIMIT 1") or die(mysql_error());
	$aa_result = mysql_fetch_array($aa_query);
	
	return $aa_result["autor_eng"];
}
function fetch_institucija($ins) {
	$ai_query = mysql_query("SELECT * FROM institucije WHERE `id`='$ins' LIMIT 1") or die(mysql_error());
	$ai_result = mysql_fetch_array($ai_query);
	
	return $ai_result["institucija_eng"];
}

function brisi_duplikate($array){
	$noviArray = array();
	foreach($array as $key=>$val){
		$noviArray[$val] = 1;
	}
	return array_keys($noviArray);
}

?>

<?php
	$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`='12' ORDER BY id ASC") or die(mysql_error());
	
	
 	$b_query = mysql_query("SELECT * FROM brojevi WHERE `broj`='12' LIMIT 1") or die(mysql_error());
	$b_result = mysql_fetch_array($b_query);
	
	
	
	while($al_result = mysql_fetch_array($al_query)){
?>

<div class="sizeclanka">
<p class="tip"><?php echo "&nbsp;&nbsp;" . $al_result["tip"]; ?></p>
<p><!-- sekcija=article umjesto sekcija=under-construction -->
<span class="artlink"><a href="?sekcija=article&artid=<?php echo $al_result["id"]; ?>"><?php echo $al_result["naslov_eng"]; ?> </a></span>
</p>

<?php
	$institucija_id = array();
	$broj_autora = 0;
	for($i=1;$i<=10;$i++){
		if($al_result["autor".$i]!=""){
			$autor_id[$i]=$al_result["autor".$i];
			$institucija_id[$i]=explode(",",$al_result["institucija".$i]);
			$broj_autora++;
		}
	}

	$ins_counter = 0;
	$institucije = array();

	foreach ($institucija_id as $h){
		for($k=1; $k<=sizeof($h); $k++){
			$institucije[] = $h[$k-1];
		}
	}

	$institucije = brisi_duplikate($institucije);

	$aut_string = "";
	$ins_string = "";

	for($i=1; $i<=$broj_autora; $i++){
		if($i>1) $aut_string = $aut_string . ", ";
		$aut_string .= "<em><a href=\"/?sekcija=articles&alc=autor&alv=".$autor_id[$i]."\">" . fetch_autor($autor_id[$i]) . "</a></em>";
	}
	
	echo "<p>".$aut_string."</p>";
	
/*	
	foreach ($institucije as $key => $h){
		$ins_string .= "<sup>".($key+1)."</sup><em>" . fetch_institucija($institucije[$key]) . "</em><br />";
	}

	
	echo "<p>".$ins_string."</p>";
*/
?>




<?php /*if(!isset($_SESSION["myusername"])){
$clink="<a href=\"#\" onclick=\"$('#login-login').click()\";>";
}else{ */
$clink="<a href=\"/clanci/" . $al_result["file"] . "\" onclick=\"brojac(" . $al_result["id"] . ")\" target=\"_blank\">";



/*}*/ ?>


<!-- MJSSM broj i DOI -->
<p><span class="artlink-manji">
Monten. J. Sports Sci. Med. <?php echo $b_result["godina"]; ?>, <?php echo $b_result["vol"]; ?>(<?php echo $b_result["no"]; ?>), <?php echo $al_result["str"]; ?>
</span></p>
<?php if($al_result["doi"]!=""){?>
<p><span class="artlink-manji">
 DOI: <a href="https://doi.org/<?php echo $al_result["doi"]; ?>" target="_blank"><?php echo $al_result["doi"]; ?></a>
</span></p>
<?php } ?>

<!-- ?sekcija=abstract-->
<p><span class="artlink-manji"><a href="/?sekcija=abstract&artid=<?php echo $al_result["id"]; ?>">Abstract </a> | <?php echo $clink; ?>Article (PDF – <?php echo round(filesize("clanci/".$al_result["file"])/1024) ."KB)"; ?></a>
<?php if($al_result["references"]!=""){?>
 | <a href="/?sekcija=abstract&artid=<?php echo $al_result["id"]; ?>#references">References</a>
 <?php } ?>
</span></p>

</div> <!--  sizeclanka -->
<?php
	}
?>

</div>
