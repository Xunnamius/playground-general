<?php
	ini_set('error_reporting', -1); // 0
	session_start();
	
	if(isset($_GET['ajax']))
	{
		$sql = new MySQLi('mysql1407.ixwebhosting.com', 'C359644_wwwwrite', 'fdsaFDSA4321', 'C359644_playground');
		
		if($sql->connect_errno)
			echo json_encode(array('error' => 'sqlConnectFail'));
		
		else
		{
			if(isset($_GET['sender'], $_GET['msg']))
			{
				$sender = $sql->escape_string($_GET['sender']);
				$message = $sql->escape_string($_GET['msg']);
				if($sql->query("INSERT INTO chat_data (chat_sender, chat_message) VALUES ('$sender', '$message')"))
					echo json_encode(array('result' => 'success'));
				else
					echo json_encode(array('result' => 'failure'));
			}
			
			else if(isset($_GET['action']) && $_GET['action'] == 'poll')
			{
				$sleeptimes = 2; // COMET XHR long-polling 10-second sleep
				
				$time = isset($_SESSION['lastAccessTime']) ? $_SESSION['lastAccessTime'] : time();
				$query = "SELECT * FROM chat_data WHERE chat_send_timestamp >  FROM_UNIXTIME($time)";
				
				$result = $sql->query($query);
				
				if($sql->errno)
				{
					echo json_encode(array('error' => 'badSQL'));
					exit;
				}
				
				while($sleeptimes-- && !$result->num_rows)
				{
					sleep(1);
					$result = $sql->query($query);
				}
				
				$_SESSION['lastAccessTime'] = time();
				
				$returnData = array();
				while($row = $result->fetch_assoc())
					$returnData[] = array('id' => $row['chat_id'], 'sender' => $row['chat_sender'], 'message' => $row['chat_message'], 'timestamp' => $row['chat_send_timestamp']);
				
				echo json_encode($returnData);
			}
			
			else echo json_encode(array('error' => 'badRequest'));
		}
		
		$sql->close();
		exit;
	}
	
	header('Location: '.htmlentities(dirname($_SERVER['PHP_SELF'])));
?>