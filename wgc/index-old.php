<?php require_once 'assets/includes/~devkey.inc'; ?>
<?php require_once 'assets/includes/global.php'; ?>
<?php
	final class Xunnamius extends Controller
	{
		protected function run_AJAX()
		{
			// Coming soon
		}
		
		protected function run()
		{
			// Connect to MySQL
			$dbc = SQL::load_driver('MySQL');
			$dbc->new_connection('main');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--
xHTML 1.1 Strict Document

Programming on the internet the way it was meant to be done.

By Xunnamius of Dark Gray.

Do not remove this header.
-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>&bull; The World Generation Calculation Machine &bull;</title>
		
		<!--[if !IE]>--><meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" /><!--<![endif]-->
		<!--[if IE]><meta http-equiv="content-type" content="text/html; charset=utf-8" /><![endif]-->
		
		<meta name="Description" content="The World Generator Calculator is a tool that will allow the user to create his or her very own planet!" />
		<meta name="Keywords" content="World, Generator, Calculator, Machine, You, will, create, using, JS, a, form, that, will, allow, the, user, to, create, a, world, one, planet" />
		<meta name="Author" content="Xunnamius of Dark Gray" />
		<meta name="Robots" content="index, follow" />
        
		<?php Browser::patch(); ?>
        
		<link rel="stylesheet" type="text/css" href="assets/stylesheets/reset.css" /><!-- Total Browser Style Reset -->
		<link rel="stylesheet" type="text/css" href="assets/stylesheets/default.css" />
		<script type="text/javascript" src="assets/scripts/default.js"></script>
	</head>
	
	<body>
		<div id="wrapper">
			<div id="header">
				<h1>World Generation Software</h1>
				<h2>&copy; Dark Gray</h2>
			</div><!-- Close header -->
			<div id="picGallery">
				<div id="center">
					<h3>Planet Type Selector</h3>
					<p id="intro">Choose your planet from the selection listed below.</p>
                    <?php
						$results = $dbc->query('SELECT planet_id i, name n, description d, locked l FROM PLANETS');
						foreach($results->rows as $row)
						{
					?>
							<div class="planet<?php echo $row['l'] == 'T' ? ' locked' : ''; ?>">
								<div class="photo"><img src="assets/images/planets/planet<?php echo $row['i'] ?>.jpg" alt="Planet <?php echo $row['i'] ?>" width="50" height="50" title="Planet <?php echo $row['i'] ?>: <?php echo $row['d'] ?>" /></div>
								<p><strong><?php echo $row['n'] ?></strong></p>
							</div>
                    <?php
						}
					?>
				</div><!-- Close center -->
                <div id="directions">
                </div><!-- Close directions -->
			</div><!-- Close picGallery -->
			<div id="optionsGallery">
				<ol>
					<li><label for="opt_GodTitle">Name Thy God</label><input id="opt_GodTitle" name="opt_GodTitle" type="text" maxlength="10" /></li>
					<li><label for="opt_WorldTitle">Name Thy Planet</label><input id="opt_WorldTitle" name="opt_WorldTitle" type="text" maxlength="10" /></li>
					<li><label for="opt_WorldSize">Planet Diameter (miles)</label><input id="opt_WorldSize" name="opt_WorldSize" type="text" maxlength="10" size="8" /></li>
					
					<li>
						<h3>Planet Core Composition</h3>
						<ol>
							<li>
								<label for="opt_Ccomp_Iron">Iron</label><input id="opt_Ccomp_Iron" name="opt_Ccomp_Iron" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Ccomp_Nickel" class="break">Nickel</label><input id="opt_Ccomp_Nickel" name="opt_Ccomp_Nickel" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Ccomp_Sulfur">Sulfur</label><input id="opt_Ccomp_Sulfur" name="opt_Ccomp_Sulfur" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Ccomp_Other" class="break">Other</label><input id="opt_Ccomp_Other" name="opt_Ccomp_Other" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" /></li>
						</ol>
					</li>
					
					<li>
						<h3>Atmospheric Composition</h3>
						<ol>
							<li>
								<label for="opt_Acomp_Nitrogen">Nitrogen</label><input id="opt_Acomp_Nitrogen" name="opt_Acomp_Nitrogen" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Oxygen" class="break">Oxygen</label><input id="opt_Acomp_Oxygen" name="opt_Acomp_Oxygen" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Argon">Argon</label><input id="opt_Acomp_Argon" name="opt_Acomp_Argon" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_CarbonD" class="break">Carbon Dioxide</label><input id="opt_Acomp_CarbonD" name="opt_Acomp_CarbonD" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Neon">Neon</label><input id="opt_Acomp_Neon" name="opt_Acomp_Neon" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Helium" class="break">Helium</label><input id="opt_Acomp_Helium" name="opt_Acomp_Helium" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Methane">Methane</label><input id="opt_Acomp_Methane" name="opt_Acomp_Methane" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Ozone" class="break">OZone</label><input id="opt_Acomp_Ozone" name="opt_Acomp_Ozone" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" />
								<label for="opt_Acomp_Other">Other</label><input id="opt_Acomp_Other" name="opt_Acomp_Other" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" /></li>
						</ol>
					</li>
					
					<li><label for="opt_Abundance">Resource Abundance</label><input id="opt_Abundance" name="opt_Abundance" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" /></li>
					
					<li><label for="opt_Alignment" id="widthPlus">Planet Alignment (+Good/-Evil)</label><input id="opt_Alignment" name="opt_Alignment" type="text" disabled="disabled" maxlength="1" size="1" /><img src="assets/images/udb.png" width="20.68" height="17" alt="+-" /></li>
					<li><label for="opt_InitInhabitants">Initial Population</label><input id="opt_InitInhabitants" name="opt_InitInhabitants" type="text" maxlength="7" size="5" /></li>
					<li><label for="opt_DevSpeed">Society's Development Speed</label>
						<select id="opt_DevSpeed" name="opt_DevSpeed">
							<option value="0">No Development (!)</option>
							<option value="1">Little</option>
							<option value="2">Slow</option>
							<option value="3" selected="selected">Normal</option>
							<option value="4">Abnormally Fast</option>
							<option value="5">Higher Beings</option>
						</select>
					</li>
					<li>
						<h3 class="leftMove">Obtains Sentience</h3>
						<label for="opt_InhabSentient">Yes</label><input id="opt_InhabSentient" name="opt_InhabSentient" type="radio" value="true" checked="checked" />
						<label for="opt_InhabSentient">No</label><input id="opt_InhabSentient" name="opt_InhabSentient" type="radio" value="false" /></li>
				</ol>
			</div><!-- Close optionsGallery -->
			<div id="popUnderResults" class="init">
				<div id="results">
					<div id="results_Header">
						<h4>Results</h4>
						<span id="results_CloseButton"><img src="assets/close.png" width="30" height="23.5" alt="Close" /></span>
					</div><!-- Close results_Header -->
					<div id="results_Body">
						<div id="results_LeftContent">
							<p><img src="" alt="Your Planet" /></p>
							<p>Your Planet's Caption</p>
							<div id="results_LeftContent_Statistics">
								<p>Your Statistics</p>
							</div>
						</div><!-- Close results_LeftContent -->
						<div id="results_RightContent">
							<p>Your Result Senario</p>
						</div><!-- Close results_RightContent -->
					</div><!-- Close results_Body -->
				</div><!-- Close results -->
			</div><!-- Close popUnderResults -->
		</div><!-- Close wrapper -->
	</body>
</html>
<?php
		}
	}
	
	$Xunn = new Xunnamius;
?>