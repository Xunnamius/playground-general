<?php
	ini_set('error_reporting', 0); // 0
	
	function generateReturnData($type, &$returnData, $sql)
	{
		$type = 'project_'.$type;
		$result = mysqli_query($sql, 'SELECT * FROM construction_projects');
		$processed = array();
		
		if(mysqli_error($sql))
		{
			echo json_encode(array('error' => 'badSQL'));
			return;
		}
		
		while($row = mysqli_fetch_array($result))
		{
			$name = $row[$type];
			$nameKey = str_replace(' ', '_', strtolower($name));
			
			if(!in_array($name, $processed))
			{
				$returnData['list'][] = array('value' => $nameKey, 'text' => $name);
				$processed[] = $name;
				$returnData['listData'][$nameKey] = '';
			}
			
			$html = <<<EOL
<div class="projectResult"><h3>{$row['project_name']}</h3><ul><li>Country: {$row['project_country']}</li><li>Year: {$row['project_year']}</li><li>Organization: {$row['project_org']}</li></ul><h4>Parts</h4><ul>
EOL;
			
			$parts = mysqli_query($sql, 'SELECT * FROM construction_parts cp NATURAL JOIN construction_projects_parts cpp WHERE cpp.project_id = '.$row['project_id']);
			
			if(mysqli_error($sql))
			{
				echo json_encode(array('error' => 'badSQL'));
				return;
			}
			
			while($part = mysqli_fetch_array($parts))
				$html .= '<li>'.$part['part_name'].' ($'.$part['part_price_usd'].' per unit)</li>';
			
			$html .= '</ul></div>';
			$returnData['listData'][$nameKey] .= $html;
		}
	}
	
	if(isset($_GET['ajax'], $_GET['action'], $_GET['data']))
	{
		$action = $_GET['action'];
		$data = $_GET['data'];
		
		if($action == 'grabSubList')
		{
			$sql = mysqli_connect('mysql1407.ixwebhosting.com', 'C359644_wwwread', 'asdfASDF1234', 'C359644_playground');
			
			if(!$sql || mysqli_connect_errno($sql))
				echo json_encode(array('error' => 'sqlConnectError'));
			
			else
			{
				$returnData = array(
					'list' => array(/* array('value'=>'data','text'=>'data'), ... */),
					'listData' => array(/* 'key' => 'htmldata', ... */)
				);
				
				if(in_array($data, array('name', 'country', 'year', 'org')))
				{
					generateReturnData($data, $returnData, $sql);
					echo json_encode($returnData);
				}
				
				else echo json_encode(array('error' => 'badData'));
			}
			
			mysqli_close($sql);
		}
		
		else echo json_encode(array('error' => 'badAction'));
		exit;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Federal Construction</title>
		
		<style type="text/css">
			html, body { height: 100%; margin: 0; }
			
			h1, h3 { margin-top: 0; }
			
			.center { text-align: center; }
			.clear { clear: both; }
			.hide { display: none; }
			
			#wrapper {
				width: 600px;
				height: 350px;
				margin: 0 auto;
			}
			
			#paneLeft {
				width: 150px;
				height: 100%;
				
				float: left;
				
				border-right: solid black 3px;
				margin-right: 7px;
			}
			
			#paneLeft select {
				width: 90%;
			}
			
			#paneRight {
				width: 400px;
				height: 100%;
				overflow-y: scroll;
				float: right;
			}
			
			.projectResult {
				margin-top: 25px;
				border-bottom: solid black 3px;
			}
			
			.projectResult:first-child { margin-top: 0; }
			.projectResult:last-child { border-bottom: none; }
		</style>
	</head>

	<body>
		<h1 class="center">Federal Construction App</h1>
		
		<div id="wrapper">
			<div id="paneLeft">
				<form id="constForm" action="#">
					<div class="center">
						<label for="sortBy">Sort By:</label>
						<select id="sortBy">
							<option>Select one...</option>
							<option value="name">Name</option>
							<option value="country">Country</option>
							<option value="year">Year</option>
							<option value="org">Organization</option>
						</select>
						<br />
						<select id="sortBySubSort" class="hide"></select>
					</div>
				</form>
			</div>
			<div id="paneRight">
				<!--<div class="projectResult">
					<h3>Project Name</h3>
					<ul>
						<li>Country: x</li>
						<li>Year: x</li>
						<li>Organization: x</li>
					</ul>
					<h4>Parts</h4>
					<ul>
						<li>Part Name ($x per unit)</li>
						<li>Part Name ($x per unit)</li>
						<li>Part Name ($x per unit)</li>
						<li>Part Name ($x per unit)</li>
					</ul>
				</div>-->
			</div>
			<br class="clear" />
		</div>
		
		<script type="text/javascript">
		window.onload = function()
		{
			var xhr = null,
				subList = document.getElementById('sortBySubSort'),
				rightPane = document.getElementById('paneRight'),
				xhrdata = null,
				virgin = true,
				virgin2 = true,
				
			sendXHR = function(action, data, handler)
			{
				if(xhr) xhr.abort();
				xhr = new XMLHttpRequest();
				
				xhr.onreadystatechange = function()
				{
					if(xhr.readyState == 4)
					{
						if(xhr.status != 200)
						{
							rightPane.setText('Error: XHR malfunction!');
							console.error(xhr);
						}
						
						else
						{
							handler(JSON.parse(xhr.responseText));
							xhr = null;
						}
					}
				};
				
				xhr.open('GET', 'federal-construction.php?ajax=1&action='+escape(action)+'&data='+escape(data), true);
				xhr.send(null);
			};
			
			rightPane.setText = function(text){ this.innerHTML = '<p class="center">'+text+'</p>'; };
			
			document.getElementById('sortBy').onchange = function()
			{
				if(virgin)
				{
					virgin = false;
					var remove = this.getElementsByTagName('option')[0];
					remove.parentNode.removeChild(remove);
				}
				
				rightPane.setText('Loading...');
				sendXHR('grabSubList', this.value, function(dataObj)
				{
					subList.innerHTML = '<option>Select one...</option>';
					
					for(var i=0, j=dataObj.list.length; i<j; ++i)
						subList.innerHTML += '<option value="'+dataObj.list[i].value+'">'+dataObj.list[i].text+'</option>';
					
					xhrdata = dataObj.listData;
					subList.className = '';
					rightPane.setText('&lt;-- Please select a sub category to sort by');
				});
			};
			
			subList.onchange = function()
			{
				if(virgin2)
				{
					virgin2 = false;
					var remove = this.getElementsByTagName('option')[0];
					remove.parentNode.removeChild(remove);
				}
				
				var data = xhrdata[this.value];
				
				if(!data || !data.length) rightPane.setText('Error: sub category not found?!');
				else rightPane.innerHTML = data;
			};
			
			rightPane.setText('&lt;-- Please select a category on the left to begin');
		}
		</script>
	</body>
</html>