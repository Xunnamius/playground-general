<?php require_once 'assets/includes/~devkey.inc'; ?>
<?php require_once 'assets/includes/global.php'; ?>
<?php
	final class Xunnamius extends Controller
	{
		protected function run_AJAX()
		{
			header('Content-Type: text/html');
			$dbc = SQL::load_driver('MySQL');
			$dbc->new_connection('ajax');
			
			$req = $this->filter('req', TYPE_STRING, 'POST');
			
			if(isset($req))
			{
				if($req == 'planets')
				{
					header('Content-Type: application/json');
					$result = $dbc->query('SELECT planet_id id, name name, description `desc`, locked FROM planets');
					exit(json_encode($result->rows ? $result->rows : array()));
				}
				
				else exit('Illegal Call (2).');
			}
			
			exit('Illegal Call (1).');
		}
		
		protected function run()
		{
			// Connect to MySQL
			$data = Browser::detect();
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
        <meta name="Version" content="2.0" />
		<meta name="Robots" content="index, follow" />
        
		<?php Browser::patch(); ?>
        <style type="text/css">
        <?php
			if($data->browser == BROWSER_IE && $data->version < 9)
			{
				echo '.button { filter: alpha(opacity = 80); }';
				echo '.button:hover, .button:focus { filter: alpha(opacity = 90); }';
				echo '.bg { filter: alpha(opacity = 40); }';
				echo '.arrow:focus, .arrow:hover { filter: alpha(opacity = 70); }';
				echo '.arrow.disabled { filter: alpha(opacity = 10); }';
				echo '.movementDiv { top: -12.5px !important; }';
			}
		
			else if($data->browser == BROWSER_SAFARI || $data->browser == BROWSER_CHROME)
			{
				echo '.unselectable { -webkit-user-select: none; }';
			}
		
			elseif($data->browser == BROWSER_OPERA)
			{
				echo '.unselectable { -o-user-select: none; }';
			}
		
			else if($data->browser == BROWSER_FIREFOX && $data->version < 4)
			{
				echo '.unselectable { -moz-user-select: none; }';
				echo '.bg { -moz-border-radius: 15px; }';
				echo '.button { -moz-border-radius: 5px; }';
			}
			
			else
			{
				echo '.unselectable { user-select: none; }';
			}
			
			if($data->browser != BROWSER_FIREFOX || $data->version >= 4)
			{
				echo '.bg { border-radius: 15px; }';
				echo '.button { border-radius: 5px; }';
			}
			
			if($data->browser != BROWSER_IE)
			{
				echo 'body #wrapper #inner_wrapper div.movementDiv { top: -248px !important; right: -1000px; }';
			}
		?>
		</style>
        
		<link rel="stylesheet" type="text/css" href="assets/stylesheets/reset.css" />
		<link rel="stylesheet" type="text/css" href="assets/stylesheets/default.css" />
        
        <script type="text/javascript"> 
		/* <![CDATA[ */
			var DG_REAL_SCRIPT_NAME = "<?php echo DG_REAL_SCRIPT_NAME; ?>";
			var DG_BROWSER = <?php echo json_encode((array) $data); ?>;
			var DG_REAL_HOST = "<?php echo DG_REAL_HOST; ?>";
			var DG_DEBUG_MODE = <?php echo (integer) DG_DEBUG_MODE; ?>;
		/* ]]> */
		</script> 
        
		<script type="text/javascript" src="assets/scripts/default.js"></script>
	</head>
	
	<body>
    	<div id="valign"></div>
        <div id="wrapper" class="unselectable">
        	<div id="inner_wrapper">
                <p class="button hidden" id="back">Back</p>
                <p class="button" id="login">Login</p>
                <p class="button hidden" id="next">Next</p>
                <div id="title">
                    <h1>World Generation Software</h1>
                    <p id="copyright">&copy; Dark Gray</p>
                </div>
            	<div id="movement_wrapper">
                    <div id="bg" class="bg"></div>
                    <p id="left" class="arrow disabled"></p>
                    <p id="right" class="arrow disabled"></p>
                    <div id="content">
                        <div id="planet">
                            <h2 id="planet_name" class="hidden">Loading...</h2>
                            <p id="planet_image"><img src="assets/images/loading.gif" alt="Loading..." title="Loading..." /></p>
                            <p id="select" class="hidden">Click to Select</p>
                            <h2 id="planet_description">Loading Planet Data...</h2>
                        </div>
                    </div>
            	</div>
                <!--<div id="movement_wrapper3" class="movementDiv">
                    <div id="bg3" class="bg"></div>
                    <div id="content">
                    	<h3 id="title3" class="title">(Planet Name)'s Core Composition</h3>
                        <ul><li><label for="world_diameter">(Planet Name)'s Diameter (kilometers)</label><input id="world_diameter" name="world_diameter" type="text" maxlength="10" size="8" /></li></ul>
                        <div id="center">
                        	<h3>Percentage of Metals (adds up to 100%)</h3>
                            <ol class="floatleft">
                                <li><label for="world_iron">Iron</label><input id="world_iron" name="world_iron" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_nickel">Nickel</label><input id="world_nickel" name="world_nickel" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_sulfur">Sulfur</label><input id="world_sulfur" name="world_sulfur" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_gold">Gold</label><input id="world_gold" name="world_gold" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_silver">Silver</label><input id="world_silver" name="world_silver" type="text" maxlength="2" size="2" /></li>
                            </ol>
                            <ol class="floatright">
                                <li><label for="world_bronze">Bronze</label><input id="world_bronze" name="world_bronze" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_copper">Copper</label><input id="world_copper" name="world_copper" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_sodium">Sodium</label><input id="world_sodium" name="world_sodium" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_titanium">Titanium</label><input id="world_titanium" name="world_titanium" type="text" maxlength="2" size="2" /></li>
                                <li><label for="world_other">Other</label><input id="world_other" name="world_other" type="text" maxlength="2" size="2" /></li>
                            </ol>
                    	</div>
                 	</div>
              	</div> -->
            </div>
            <div id="footer"><a href="#/help" title="Help">Help</a> <a href="#/DG" title="Dark Gray">Dark Gray</a> <a href="#/dev" title="Freelance">Need Web Development?</a></div>
        </div>
	</body>
</html>
<?php
		}
	}
	
	$Xunn = new Xunnamius;
?>