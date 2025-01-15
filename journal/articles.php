<?php
function fetch_autor($aut)
{
	$aa_query = mysql_query("SELECT * FROM autori WHERE `id`='$aut' LIMIT 1") or die(mysql_error());
	$aa_result = mysql_fetch_array($aa_query);

	return $aa_result["autor_eng"];
}
function fetch_institucija($ins)
{
	$ai_query = mysql_query("SELECT * FROM institucije WHERE `id`='$ins' LIMIT 1") or die(mysql_error());
	$ai_result = mysql_fetch_array($ai_query);

	return $ai_result["institucija_eng"];
}

function brisi_duplikate($array)
{
	$noviArray = array();
	foreach ($array as $key => $val) {
		$noviArray[$val] = 1;
	}
	return array_keys($noviArray);
}

function stampaj_listu($al_query)
{
	while ($al_result = mysql_fetch_array($al_query)) {

		$broj = $al_result["broj"];
		$b_query = mysql_query("SELECT * FROM brojevi WHERE `broj`=$broj LIMIT 1") or die(mysql_error());
		$b_result = mysql_fetch_array($b_query);

		?>
		<div class="sizeclanka">
			<p class="tip">
				<?php echo "&nbsp;&nbsp;" . $al_result["tip"]; ?>
			</p>
			<p>
				<span class="artlink"><a href="?sekcija=article&artid=<?php echo $al_result["id"]; ?>">
						<?php echo $al_result["naslov_eng"]; ?>
					</a></span>
			</p>

			<?php
			$institucija_id = array();
			$broj_autora = 0;
			for ($i = 1; $i <= 13; $i++) {
				if ($al_result["autor" . $i] != "") {
					$autor_id[$i] = $al_result["autor" . $i];
					$institucija_id[$i] = explode(",", $al_result["institucija" . $i]);
					$broj_autora++;
				}
			}

			$ins_counter = 0;
			$institucije = array();

			foreach ($institucija_id as $h) {
				for ($k = 1; $k <= sizeof($h); $k++) {
					$institucije[] = $h[$k - 1];
				}
			}

			$institucije = brisi_duplikate($institucije);

			$aut_string = "";
			$ins_string = "";

			for ($i = 1; $i <= $broj_autora; $i++) {
				if ($i > 1)
					$aut_string = $aut_string . ", ";
				$aut_string .= "<em><a href=\"/?sekcija=articles&alc=autor&alv=" . $autor_id[$i] . "\">" . fetch_autor($autor_id[$i]) . "</a></em><sup>";
				$nadjen = false;
				foreach ($institucija_id[$i] as $h) {
					$pretraga = array_search($h, $institucije);
					if ($nadjen)
						$aut_string = $aut_string . ",";
					$aut_string .= ($pretraga + 1);
					$nadjen = true;
				}
				$aut_string .= "</sup>";
			}

			echo "<p>" . $aut_string . "</p>";


			foreach ($institucije as $key => $h) {
				$ins_string .= "<sup>" . ($key + 1) . "</sup><em>" . fetch_institucija($institucije[$key]) . "</em><br />";
			}


			echo "<p>" . $ins_string . "</p>";

			?>

			<?php /*if(!isset($_SESSION["myusername"])){
																		   $tekstic="Login required to view full text";
																		   $clink="<a href=\"#\" onclick=\"$('#login-login').click()\";>";
																		   }else{ */
			$tekstic = "";







			$clink = "<a href=\"/clanci/" . $al_result["file"] . "\" onclick=\"brojac(" . $al_result["id"] . ")\" target=\"_blank\">";





			/*}*/ ?>
			<?php
			/* Privremeno */
			// if($alc!="press"){
			?>

			<!-- MJSSM broj i DOI -->
			<p><span class="artlink-manji">
					Monten. J. Sports Sci. Med.
					<?php echo $b_result["godina"]; ?>,
					<?php echo $b_result["vol"]; ?>(
					<?php echo $b_result["no"]; ?>),
					<?php echo $al_result["str"]; ?>
				</span></p>
			<?php if ($al_result["doi"] != "") { ?>
				<p><span class="artlink-manji">
						DOI: <a href="https://doi.org/<?php echo $al_result["doi"]; ?>" target="_blank">
							<?php echo $al_result["doi"]; ?>
						</a>
					</span></p>
			<?php } ?>

			<p><span class="artlink"><a href="/?sekcija=abstract&artid=<?php echo $al_result["id"]; ?>">Abstract </a> |
					<?php echo $clink; ?>Article (PDF –
					<?php echo round(filesize("clanci/" . $al_result["file"]) / 1024) . "KB)"; ?></a>
					<?php echo $tekstic; ?>
					<?php if ($al_result["references"] != "") { ?>
						| <a href="/?sekcija=abstract&artid=<?php echo $al_result["id"]; ?>#references">References</a>
					<?php } ?>
				</span></p>
			<?php
			// } // if alc == press
			?>
		</div> <!--  sizeclanka -->
		<?php
	} // while

}




?>



<?php


if ($alc == "autor") {
	echo "<h3>Articles by author <b>" . fetch_autor($alv) . "</b></h3>";
	$al_query = mysql_query("SELECT * FROM clanci WHERE `autor1`='$alv' OR `autor2`='$alv' OR `autor3`='$alv' OR `autor4`='$alv' OR `autor5`='$alv' OR `autor6`='$alv' OR `autor7`='$alv' OR `autor8`='$alv' OR `autor9`='$alv' OR `autor10`='$alv'") or die(mysql_error());
} elseif ($alc == "press") { // Articles in Press
	echo "<h3>Ahead of Print</h3>"; // staviti ovo na kraju ako ima clanaka: (last updated 18/08/2019)
	$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`>'$alv' ORDER BY id ASC") or die(mysql_error());
	if (mysql_num_rows($al_query) == 0)
		echo "NO ACCEPTED MANUSCRIPTS";
} elseif ($alc == "current") { // Current Issue
	echo "<h3>Current Issue</h3>";
	$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`='$alv' ORDER BY id ASC") or die(mysql_error());
} elseif ($alc == "past") { // Past Issues
	echo "<h3>Past Issues</h3>";
	?>
	<div class="faq">
		<div class="question"><u><b>2012</b></u></div>
		<div class="answer">


			<?php

			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`='$alv' ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-1.jpg\" border=\"0\"><br /><b>September 2012, Vol.1, No.1 <a href='/clanci/full_issues/MJSSM_Sept_2012.pdf' target='_blank'>[print version]</a></b></p>";
} elseif ($alc == "search") { // search

	$alv = $_POST['searchstring'];

	echo "<h3>Search results for search string <em>\"" . $alv . "\"</em></h3>";
	$al_query = mysql_query("SELECT * FROM clanci WHERE `naslov_eng` like '%$alv%' OR `naslov_mne` like '%$alv%' OR `sazetak_eng` like '%$alv%' OR `sazetak_mne` like '%$alv%' OR `keywords_eng` like '%$alv%' OR `keywords_mne` like '%$alv%'") or die(mysql_error());
	//		$al_query = mysql_query("SELECT * FROM clanci c LEFT JOIN (autori as a1, autori as a2, autori as a3, autori as a4, autori as a5) ON (c.autor1 = a1.id OR c.autor2 = a2.id OR c.autor3 = a3.id OR c.autor4 = a4.id OR c.autor5 = a5.id) WHERE `naslov_eng` like '%$alv%' OR `naslov_mne` like '%$alv%' OR `sazetak_eng` like '%$alv%' OR `sazetak_mne` like '%$alv%' OR `keywords_eng` like '%$alv%' OR `keywords_mne` like '%$alv%' OR a1.autor_eng like '%$alv%'") or die(mysql_error());
} else {
	$al_query = mysql_query("SELECT * FROM clanci WHERE `$alc`='$alv' ORDER BY id ASC") or die(mysql_error());
}


stampaj_listu($al_query);


if ($alc == "past") {
	?>
		</div>
	</div>


	<div class="faq">
		<div class="question"><u><b>2013</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=2 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-2.jpg\" border=\"0\"><br /><b>March 2013, Vol.2, No.1 <a href='/clanci/full_issues/MJSSM_March_2013.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=3 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-3.jpg\" border=\"0\"><br /><b>September 2013, Vol.2, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2013.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>
	<div class="faq">
		<div class="question"><u><b>2014</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=4 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-4.jpg\" border=\"0\"><br /><b>March 2014, Vol.3, No.1 <a href='/clanci/full_issues/MJSSM_March_2014.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=5 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-5.jpg\" border=\"0\"><br /><b>September 2014, Vol.3, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2014.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>
	<div class="faq">
		<div class="question"><u><b>2015</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=6 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-6.jpg\" border=\"0\"><br /><b>March 2015, Vol.4, No.1 <a href='/clanci/full_issues/MJSSM_March_2015.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=7 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-7.jpg\" border=\"0\"><br /><b>September 2015, Vol.4, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2015.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>
	<div class="faq">
		<div class="question"><u><b>2016</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=8 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-8.jpg\" border=\"0\"><br /><b>March 2016, Vol.5, No.1 <a href='/clanci/full_issues/MJSSM_March_2016.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=9 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-9.jpg\" border=\"0\"><br /><b>September 2016, Vol.5, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2016.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>
	<div class="faq">
		<div class="question"><u><b>2017</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=10 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-10.jpg\" border=\"0\"><br /><b>March 2017, Vol.6, No.1 <a href='/clanci/full_issues/MJSSM_March_2017.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=11 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-11.jpg\" border=\"0\"><br /><b>September 2017, Vol.6, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2017.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>
	<div class="faq">
		<div class="question"><u><b>2018</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=12 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-12.jpg\" border=\"0\"><br /><b>March 2018, Vol.7, No.1 <a href='/clanci/full_issues/MJSSM_March_2018.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=13 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-13.jpg\" border=\"0\"><br /><b>September 2018, Vol.7, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2018.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>
	<div class="faq">
		<div class="question"><u><b>2019</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=14 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-14.jpg\" border=\"0\"><br /><b>March 2019, Vol.8, No.1 <a href='/clanci/full_issues/MJSSM_March_2019.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=15 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-15.jpg\" border=\"0\"><br /><b>September 2019, Vol.8, No.2 <a href='/clanci/full_issues/MJSSM_Sept_2019.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>

	<div class="faq">
		<div class="question"><u><b>2020</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=16 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-16.jpg\" border=\"0\"><br /><b>March 2020, Vol.9, No.1 <a href='/clanci/full_issues/MJSSM_March_2020.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>

			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=17 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/korica.jpg\" border=\"0\"><br /><b>April 2020, Vol.9, No.S1 <a href='/clanci/full_issues/MJSSM_April_2020.pdf' target='_blank'>[print version]</a></b></p>";
			//stampaj_listu($al_query);
			?>

			<div class="sizeclanka">
				<p class="tip">&nbsp;&nbsp;Meeting Abstracts</p>
				<p>
					<span class="artlink"><a href="?sekcija=article&amp;artid=500">Abstracts from the 17th Annual Scientific
							Conference of
							Montenegrin Sports Academy “Sport, Physical Activity and
							Health: Contemporary Perspectives”: Cavtat, Dubrovnik,
							Croatia. 2-5 April 2020</a></span>
				</p>

				<p><em><a href="/?sekcija=articles&amp;alc=autor&amp;alv=16">Dusko Bjelica</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=11">Stevo Popovic</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=5">Selçuk Akpınar</a></em><sup>2</sup></p>
				<p><sup>1</sup><em>University of Montenegro, Faculty for Sport and Physical Education, Niksic,
						Montenegro</em><br><sup>2</sup><em>Nevşehir Hacı Bektaş Veli University, Department
						of Physical Education and Sports, Nevşehir, Turkey</em><br></p>

				<!-- MJSSM broj i DOI -->
				<p><span class="artlink-manji">
						Monten. J. Sports Sci. Med. 2020, 9(S1), 5-37</span></p>
				<p><span class="artlink-manji">
						DOI: <a href="https://doi.org/10.26773/mjssm.200401" target="_blank">10.26773/mjssm.200401</a>
					</span></p>

				<p><span class="artlink"><a href="/?sekcija=abstract&amp;artid=500">Abstract </a> | <a
							href="/clanci/MJSSM_0April_2020_Bjelica_5-37.pdf" onclick="brojac(500)" target="_blank">Article
							(PDF – 358KB)</a> | <a href="/?sekcija=abstract&amp;artid=500#references">References</a>
					</span></p>
			</div>

			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=18 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-18.jpg\" border=\"0\"><br /><b>September 2020, Vol.9, No.2 <a href='/clanci/full_issues/MJSSM_September_2020.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>





		</div>
	</div>

	<div class="faq">
		<div class="question"><u><b>2021</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=19 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-19.jpg\" border=\"0\"><br /><b>March 2021, Vol.10, No.1 <a href='/clanci/full_issues/MJSSM_March_2021.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>

			<?php
			//Dodatni broj - nema stampaj listu jer ima samo jedan clanak
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=20 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/koricaApril2021.jpg\" border=\"0\"><br /><b>April 2021, Vol.10, No.S1 <a href='/clanci/full_issues/MJSSM_April_2021.pdf' target='_blank'>[print version]</a></b></p>";
			//stampaj_listu($al_query);
			?>

			<div class="sizeclanka">
				<p class="tip">&nbsp;&nbsp;Meeting Abstracts</p>
				<p>
					<span class="artlink"><a href="?sekcija=article&amp;artid=501">Abstracts from the 18th Annual Scientific
							Conference of
							Montenegrin Sports Academy and 16th FIEP European Congress
							“Sport, Physical Education, Physical Activity and Health:
							Contemporary perspectives”: Dubrovnik, Croatia. 8-11 April 2021</a></span>
				</p>

				<p><em><a href="/?sekcija=articles&amp;alc=autor&amp;alv=16">Dusko Bjelica</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=11">Stevo Popovic</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=5">Selçuk Akpınar</a></em><sup>2</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=73">Dario Novak</a></em><sup>3</sup> </p>
				<p><sup>1</sup><em>University of Montenegro, Faculty for Sport and Physical Education, Niksic,
						Montenegro</em><br><sup>2</sup><em>Nevşehir Hacı Bektaş Veli University, Department
						of Physical Education and Sports, Nevşehir, Turkey</em><br> <sup>3</sup><em>University of Zagreb,
						Faculty of Kinesiology, Zagreb, Croatia</em><br></p>

				<!-- MJSSM broj i DOI -->
				<p><span class="artlink-manji">
						Monten. J. Sports Sci. Med. 2020, 10(S1), 5-19</span></p>
				<p><span class="artlink-manji">
						DOI: <a href="https://doi.org/10.26773/mjssm.210401" target="_blank">10.26773/mjssm.210401</a>
					</span></p>

				<p><span class="artlink"><a href="/?sekcija=abstract&amp;artid=501">Abstract </a> | <a
							href="/clanci/MJSSM_April_2021_Bjelica_5-19.pdf" onclick="brojac(501)" target="_blank">Article
							(PDF – 291KB)</a> | <a href="/?sekcija=abstract&amp;artid=501#references">References</a>
					</span></p>
			</div>

			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=21 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-20.jpg\" border=\"0\"><br /><b>September 2021, Vol.10, No.2 <a href='/clanci/full_issues/MJSSM_Septebmber_2021.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>




		</div>
	</div>

	<div class="faq">
		<div class="question"><u><b>2022</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=22 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/rsz_2knjigica-3d-22.jpg\" border=\"0\"><br /><b>March 2022, Vol.11, No.1 <a href='/clanci/full_issues/MJSSM_March_2022.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
			<?php
			//Dodatni broj - nema stampaj listu jer ima samo jedan clanak
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=20 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-23.jpg\" border=\"0\"><br /><b>April 2022, Vol.11, No.S1 <a href='/clanci/full_issues/MSA_Meeting_Abstracts_2022.pdf' target='_blank'>[print version]</a></b></p>";
			//stampaj_listu($al_query);
			?>

			<div class="sizeclanka">
				<p class="tip">&nbsp;&nbsp;Meeting Abstracts</p>
				<p>
					<span class="artlink"><a href="?sekcija=article&amp;artid=502">Abstracts from the 19th Annual Scientific
							Conference of Montenegrin Sports
							Academy “Sport, Physical Activity and Health: Contemporary perspectives”:
							Dubrovnik, Croatia. 7-10 April 2022</a></span>
				</p>

				<p><em><a href="/?sekcija=articles&amp;alc=autor&amp;alv=16">Dusko Bjelica</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=11">Stevo Popovic</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=5">Selçuk Akpınar</a></em><sup>2</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=73">Damir Sekulic</a></em><sup>3</sup> </p>
				<p><sup>1</sup><em>University of Montenegro, Faculty for Sport and Physical Education, Niksic,
						Montenegro</em><br><sup>2</sup><em>Nevşehir Hacı Bektaş Veli University, Department
						of Physical Education and Sports, Nevşehir, Turkey</em><br> <sup>3</sup><em>University of Split,
						Faculty of Kinesiology, Split, Croatia</em><br></p>

				<!-- MJSSM broj i DOI -->
				<p><span class="artlink-manji">
						Monten. J. Sports Sci. Med. 2022, 11(S1), 3-42</span></p>
				<p><span class="artlink-manji">
						DOI: <a href="https://doi.org/10.26773/mjssm.220401" target="_blank">10.26773/mjssm.220401</a>
					</span></p>

				<p><span class="artlink"><a href="/?sekcija=abstract&amp;artid=502">Abstract </a> | <a
							href="/clanci/full_issues/MSA_Meeting_Abstracts_2022.pdf" onclick="brojac(502)"
							target="_blank">Article (PDF – 7642KB)</a> | <a
							href="/?sekcija=abstract&amp;artid=502#references">References</a>
					</span></p>
			</div>

			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=24 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-24.jpg\" border=\"0\"><br /><b>September 2022, Vol.11, No. 2 <a href='/clanci/full_issues/MJSSM_September_2022.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>

	<div class="faq">
		<div class="question"><u><b>2023</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=25 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-25.jpg\" border=\"0\"><br /><b>March 2023, Vol.12, No.1 <a href='/clanci/full_issues/MJSSM_March_2023.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>

			<?php
			//Dodatni broj - nema stampaj listu jer ima samo jedan clanak
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=26 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-26-resize.jpg\" border=\"0\"><br /><b>April 2023, Vol.12, No.S1 <a href='/clanci/full_issues/MSA_Meeting_Abstracts_2023.pdf' target='_blank'>[print version]</a></b></p>";
			//stampaj_listu($al_query);
			?>

			<div class="sizeclanka">
				<p class="tip">&nbsp;&nbsp;Meeting Abstracts</p>
				<p>
					<span class="artlink"><a href="?sekcija=article&amp;artid=503">Abstracts from the 20th Annual Scientific
							Conference of Montenegrin
							Sports Academy and “Sport, Physical Activity and Health:
							Contemporary perspectives”: Dubrovnik, Croatia. 20-23 April 2023</a></span>
				</p>

				<p><em><a href="/?sekcija=articles&amp;alc=autor&amp;alv=16">Dusko Bjelica</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=480">Damir Sekulic</a></em><sup>2</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=565">Maja Pajek</a></em><sup>3</sup>, </p>
				<p><sup>1</sup><em>University of Montenegro, Faculty for Sport and Physical Education, Niksic,
						Montenegro</em><br><sup>2</sup><em>University of Split, Faculty of Kinesiology, Split,
						Croatia</em><br> <sup>3</sup><em>University of Ljubljana, Faculty of Sport, Ljubljana,
						Slovenia</em><br></p>

				<!-- MJSSM broj i DOI -->
				<p><span class="artlink-manji">
						Monten. J. Sports Sci. Med. 2023, 11(S1), 3-36</span></p>
				<p><span class="artlink-manji">
						DOI: <a href="https://doi.org/10.26773/mjssm.230401" target="_blank">10.26773/mjssm.230401</a>
					</span></p>

				<p><span class="artlink"><a href="/?sekcija=abstract&amp;artid=503">Abstract </a> | <a
							href="/clanci/full_issues/MSA_Meeting_Abstracts_2023.pdf" onclick="brojac(503)"
							target="_blank">Article (PDF – 7642KB)</a> | <a
							href="/?sekcija=abstract&amp;artid=502#references">References</a>
					</span></p>
			</div>

			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=27 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-26.jpg\" border=\"0\"><br /><b>September 2023, Vol.12, No.2 <a href='/clanci/full_issues/MJSSM_September_2023.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>

	<div class="faq">
		<div class="question"><u><b>2024</b></u></div>
		<div class="answer">
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=28 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-27.jpg\" border=\"0\"><br /><b>March 2024, Vol.13, No.1 <a href='/clanci/full_issues/MJSSM_March_2024.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>

			<?php
			//Dodatni broj - nema stampaj listu jer ima samo jedan clanak
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=29 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/rsz_knjigica_april.jpg\" border=\"0\"><br /><b>April 2024, Vol.13, No.S1 <a href='/clanci/full_issues/MSA_Meeting_Abstracts_2024.pdf' target='_blank'>[print version]</a></b></p>";
			//stampaj_listu($al_query);
			?>

			<div class="sizeclanka">
				<p class="tip">&nbsp;&nbsp;Meeting Abstracts</p>
				<p>
					<span class="artlink"><a href="?sekcija=article&amp;artid=504">Abstracts from the 21th Annual Scientific
							Conference of Montenegrin
							Sports Academy and “Sport, Physical Activity and Health:
							Contemporary perspectives”: Dubrovnik, Croatia. 18-21 April 2024</a></span>
				</p>

				<p><em><a href="/?sekcija=articles&amp;alc=autor&amp;alv=16">Dusko Bjelica</a></em><sup>1</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=480">Damir Sekulic</a></em><sup>2</sup>, <em><a
							href="/?sekcija=articles&amp;alc=autor&amp;alv=565">Maja Pajek</a></em><sup>3</sup>, </p>
				<p><sup>1</sup><em>University of Montenegro, Faculty for Sport and Physical Education, Niksic,
						Montenegro</em><br><sup>2</sup><em>University of Split, Faculty of Kinesiology, Split,
						Croatia</em><br> <sup>3</sup><em>University of Ljubljana, Faculty of Sport, Ljubljana,
						Slovenia</em><br></p>

				<!-- MJSSM broj i DOI -->
				<p><span class="artlink-manji">
						Monten. J. Sports Sci. Med. 2024, 11(S1), 3-36</span></p>
				<p><span class="artlink-manji">
						DOI: <a href="https://doi.org/10.26773/mjssm.230401" target="_blank">10.26773/mjssm.240401</a>
					</span></p>

				<p><span class="artlink"><a href="/?sekcija=abstract&amp;artid=504">Abstract </a> | <a
							href="/clanci/full_issues/MSA_Meeting_Abstracts_2024.pdf" onclick="brojac(503)"
							target="_blank">Article (PDF – 7642KB)</a> | <a
							href="/?sekcija=abstract&amp;artid=504#references">References</a>
					</span></p>
			</div>
			<?php
			$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`=29 ORDER BY id ASC") or die(mysql_error());
			echo "<p><img src=\"images/knjigica-3d-29.jpg\" border=\"0\"><br /><b>September 2024, Vol.13, No.2 <a href='/clanci/full_issues/MJSSM_September_2024.pdf' target='_blank'>[print version]</a></b></p>";
			stampaj_listu($al_query);
			?>
		</div>
	</div>

			



	<?php
} // zadnji if
?>