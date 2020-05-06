<?php
	/* Standard "Coming Soon!" page, by XoDG */
	
	//Simple or Advanced Layout?
	$layoutMode 	= 0; // 0 = simple, 1 = advanced
	$currentStage	= 0; // Applies to advanced layout only
	
	// Load delay (artificial delay time, keep at 0 if not debugging)
	$loadArtifDelay = 0000;
	
	// Site name, appears in the title
	$siteName 				= 'Dark Gray : Web Design';
	$showSiteNameInTitle 	= false;
	
	// Name that'll appear in the "contact" link
	$contactName = 'Bernard Dickens';
	
	// Email address that pops up when "contact" is clicked
	$contactEmail = 'soon@darkgray.org';
	
	// Number of loading dots & delay
	
	$loaderDots 	= 4;
	$loaderDelay 	= 500;
	
	// Color Pallet
	$color_pallet = array(
						array('#000000', '#111111'), // Base state
						array('#FFFFFF', '#DDDDDD'),
						array('#500101', '#300101'),
						array('#013750', '#011730'),
						array('#015001', '#013001'),
						array('#661170', '#400D3C'),
						array('#333333', '#222222'),
					);

	// Color pallet selection (by index)
	$color_selection = 2;
	
	// Loading flicker
	$flicker_frequency 	= 3;
	$flicker_delay 		= 350;
	$flicker_phase 		= 100;
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html version="-//W3C//DTD XHTML 1.1//EN" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Coming Soon<?php echo !empty($siteName) && $showSiteNameInTitle ? (' - '.$siteName) : ''; ?></title>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="reset.css"  media="all" />
		<link rel="stylesheet" type="text/css" href="global.css" media="all" />
		
		<script type="text/javascript">
			/* <![CDATA[ */
			var LOADER_DOTS = <?php echo $loaderDots; ?>,
			LOADER_DOT_DELAY = <?php echo $loaderDelay; ?>,
			COLOR_PALLET = <?php echo json_encode($color_pallet); ?>,
			COLOR_SELECTION = <?php echo json_encode($color_pallet[$color_selection]); ?>,
			FX_FLICKER_FREQUENCY = <?php echo $flicker_frequency; ?>,
			FX_FLICKER_DELAY = <?php echo $flicker_delay; ?>,
			FX_FLICKER_PHASE = <?php echo $flicker_phase; ?>,
			LAYOUT_MODE = <?php echo $layoutMode; ?>,
			CURRENT_STAGE = <?php echo $currentStage; ?>,
			LOAD_ARTIF_DELAY = <?php echo $loadArtifDelay; ?>;
			/* ]]> */
		</script>
	</head>
	<body class="white layout_mode_<?php echo $layoutMode; ?>">
		<div id="outer_wrapper">
			<div id="scaling_wrapper">
				<h1 id="title" class="pseudo-hidden"><?php echo $siteName; ?></h1>
				<h2 class="pseudo-hidden">Sorry, this website is currently under development!</h2>
				<div id="inner_wrapper"><!-- TODO: this progress bar! -->
					<h3 id="loading">Loading.</h3>
					<div id="status">
						<div id="bar">
							<div class="node" title="Phase 1: Pending"></div>
							<div class="node" title="Phase 2: Pre-Production"></div>
							<div class="node" title="Phase 3: Layout"></div>
							<div class="node" title="Phase 4: Content Negotiation"></div>
							<div class="node" title="Phase 5: Finalized Design"></div>
							<div class="node" title="Phase 6: Beta Status"></div>
							<div class="node" title="Phase 7: Live-Ready!"></div>
						</div>
					<p id="contact" class="pseudo-hidden"><a href="mailto:<?php echo $contactEmail; ?>" title="Contact Us!">Contact | <?php echo !empty($contactName) ? $contactName : 'Us'; ?></a></p>
					</div>
				</div>
				<p id="footer" class="pseudo-hidden">Powered by <a href="http://darkgray.org" title="Standards-compliant web 2.0 (coming soon)">Dark Gray</a> and <a href="http://mootools.net" title="Best JSF in current existence">MooTools</a></p>
			</div>
		</div>
		<script type="text/javascript" src="mootools-core-1.4.4-full-nocompat-yc.js"></script> <!-- TODO: chop this down to size! -->
		<script type="text/javascript" src="mootools-more-1.4.0.1-nocompat-yc.js"></script> <!-- TODO: is this necessary? -->
		<script type="text/javascript" src="global.js"></script>
	</body>
</html>