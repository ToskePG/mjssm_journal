<div id="lijevi">
	<img src="images/knjigica-3d-29.jpg" />
	<h3>Dear Contributors and Authors,</h3>

	<p>
		We are delighted to announce the release of the latest issue of Montenegrin Journal of Sports Science and
		Medicine. This publication would not have been possible without the invaluable contributions and dedication
		of each one of you. Your research, insights, and scholarly work have enriched our journal and advanced the
		discourse in our respective fields.
	</p>
	<p>
		We extend our heartfelt gratitude to all contributors and authors for their hard work and commitment to
		excellence. Your efforts have significantly contributed to the academic community and have helped us maintain
		the high standards of our publication.
	</p>
	<p>
		In addition to celebrating the release of our new issue, we are excited to take this opportunity to introduce
		our upcoming 21st Annual Scientific Conference of Montenegrin Sports Academy scheduled for 18-21 April,
		which will be held in Cavtat/Dubrovnik, Croatia. The conference will provide a platform for researchers,
		scholars, and professionals to come together, exchange ideas, and discuss the latest advancements in our fields
		of study
	</p>
	<p>
		Details regarding the conference, including the theme, keynote speakers, are available at conference website
		<a href="https://csakademija.me/conference/index.html">conference website</a>. We encourage all of you to
		consider participating in this
		enriching event and to share your valuable insights with our academic community
	</p>
	<p>
		Once again, we extend our sincere appreciation to each one of you for your contributions to our journal.
		Your dedication to advancing knowledge and scholarship is truly commendable, and we look forward to our
		continued collaboration and success.
	</p>




	<p>Warm regards,<br />
		Prof. Dusko Bjelica, PhD<br />
		Prof. Damir Sekulic, PhD</p>





</div>
<div id="desni">
	<h3>Current Issue</h3>

	<?php
	function fetch_autor($aut)
	{
		$aa_query = mysql_query("SELECT * FROM autori WHERE `id`='$aut' LIMIT 1") or die (mysql_error());
		$aa_result = mysql_fetch_array($aa_query);

		return $aa_result["autor_eng"];
	}
	function fetch_institucija($ins)
	{
		$ai_query = mysql_query("SELECT * FROM institucije WHERE `id`='$ins' LIMIT 1") or die (mysql_error());
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

	?>

	<?php
	$al_query = mysql_query("SELECT * FROM clanci WHERE `broj`='29' ORDER BY id ASC") or die (mysql_error());
	$b_query = mysql_query("SELECT * FROM brojevi WHERE `broj`='29' LIMIT 1") or die (mysql_error());
	$b_result = mysql_fetch_array($b_query);
	while ($al_result = mysql_fetch_array($al_query)) {
		?>

		<div class="sizeclanka">
			<p class="tip">
				<?php echo "&nbsp;&nbsp;" . $al_result["tip"]; ?>
			</p>
			<p><!-- sekcija=article umjesto sekcija=under-construction -->
				<span class="artlink"><a href="?sekcija=article&artid=<?php echo $al_result["id"]; ?>">
						<?php echo $al_result["naslov_eng"]; ?>
					</a></span>
			</p>

			<?php
			$institucija_id = array();
			$broj_autora = 0;
			for ($i = 1; $i <= 12; $i++) {
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
				$aut_string .= "<em><a href=\"/?sekcija=articles&alc=autor&alv=" . $autor_id[$i] . "\">" . fetch_autor($autor_id[$i]) . "</a></em>";
			}

			echo "<p>" . $aut_string . "</p>";

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
			$clink = "<a href=\"/clanci/" . $al_result["file"] . "\" onclick=\"brojac(" . $al_result["id"] . ")\" target=\"_blank\">";



			/*}*/ ?>


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

			<!-- ?sekcija=abstract-->
			<p><span class="artlink-manji"><a href="/?sekcija=abstract&artid=<?php echo $al_result["id"]; ?>">Abstract </a>
					|
					<?php echo $clink; ?>Article (PDF –
					<?php echo round(filesize("clanci/" . $al_result["file"]) / 1024) . "KB)"; ?></a>
					<?php if ($al_result["references"] != "") { ?>
						| <a href="/?sekcija=abstract&artid=<?php echo $al_result["id"]; ?>#references">References</a>
					<?php } ?>
				</span></p>

		</div> <!--  sizeclanka -->
		<?php
	}
	?>
</div>