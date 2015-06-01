<?php
require_once("config.inc.php");
require_once("gameq/src/GameQ/Autoloader.php");

if (isset($_POST['query-servers']) && $_POST['query-servers'] == true)
{

	// Call the class, and add your servers.
	$gq = \GameQ\GameQ::factory();
	$gq->addServers($servers);

	// You can optionally specify some settings
	$gq->setOption('timeout', 3); //in seconds

	// Send requests, and parse the data
	$results = $gq->process();


	echo "<div class='div-table'>\n";
	//iterate through each server
	foreach ($results as $key => $server)
	{
		//the server is online and running
		if ($server['gq_online'])
		{
			//use var_dump for testing
			//http://php.net/var_dump
			//var_dump($server);

			//row one
			echo "<div class='div-table-row'>\n";
				echo "<div class='div-table-col div-left'>\n";
					//figure out Arma version for logo
					if (isset($server['gq_mod']) && $server['gq_mod'] == "arma2arrowpc") {
						echo "<img src='images/arma2.jpg' alt='Arma2 Logo' title='Arma2 Logo' />\n";
					} else if (isset($server['gq_mod']) && $server['gq_mod'] == "dayz") {
						echo "<img src='images/dayz.jpg' alt='DayZ Logo' title='DayZ Logo' />\n";
					} else { //arma3pc
						echo "<img src='images/arma3.jpg' alt='Arma3 Logo' title='Arma3 Logo' />\n";
					}
					if (GEOIP == "true") {
						//Use GeoIP to determine country
						echo "<a href='http://www.hostip.info'>";
						echo "<img src='http://api.hostip.info/flag.php?ip=". gethostbyname($server['gq_address']) ."' alt='Country' title='Country' /></a>\n";
					}
					//server OS (Windows/Linux)
					if (isset($server['platform']) && $server['platform'] == "win") {
						echo "<img src='images/windows_logo.jpg' alt='Server Runs Windows' title='Server Runs Windows' />\n";
					} else {
						echo "<img src='images/linux_logo.jpg' alt='Server Runs Linux' title='Server Runs Linux' />\n";
					}
					//display join link
					if (isset($server['gq_joinlink'])) {
						echo "<a href='".$server['gq_joinlink']."'>Join Server</a>";
					}
				echo "</div>\n";

				//Server name
				echo "<div class='div-table-col div-right'>\n";
					//do regex on hostname to make a link for sixupdater
					$server['gq_hostname'] = preg_replace('@(sixupdater://([\w-.]+)+(:\d+)?(/([\w/_.-]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $server['gq_hostname']);
					echo $server['gq_hostname'];
				echo "</div>\n";
			echo "</div>\n";

			//row two
			echo "<div class='div-table-row'>\n";
				echo "<div class='div-table-col div-left'>\n";
					//IP address:port
					echo $server['gq_address'] . ":" . $server['port'];
				echo "</div>\n";

				echo "<div class='div-table-col div-right'>\n";
					//Map
					echo "Map: " . $server['gq_mapname'];
				echo "</div>\n";
			echo "</div>\n";

			//row three
			echo "<div class='div-table-row'>\n";
				echo "<div class='div-table-col div-left'>\n";
					//Mission
					echo "Mission: " . $server['game_descr'];
				echo "</div>\n";

				echo "<div class='div-table-col div-right'>\n";
					//GameType
					echo "Type: " . $server['gq_gametype'];
				echo "</div>\n";
			echo "</div>\n";

			//row four
			echo "<div class='div-table-row'>\n";
				echo "<div class='div-table-col div-left'>\n";
					//Players
					echo "Players: " . $server['gq_numplayers'] . "/" . $server['gq_maxplayers'];
				echo "</div>\n";

				echo "<div class='div-table-col div-right'>\n";
					//Password
					echo "Password: ";
					if ($server['gq_password'] == 0) {
						echo "No";
					} else {
						echo "Yes";
					}
				echo "</div>\n";
			echo "</div>\n";

			//row five (hidden in divs, activated by click)
			echo "<div class='div-table-row'>\n";
				echo "<div class='div-table-col div-left'>\n";
					if ($server['gq_numplayers'] > 0) //if there are players, show the option for displaying their names
					{
						echo "<div class='players' style='cursor: pointer' title='Show/Hide Player Listing'><div class='like-link'>Player Listing</div>\n";
							echo "<table class='hide-players'>";
							echo "<tr><td class='underline'>Player</td><td class='underline'>Score</td></tr>\n";
							//Iterate through all the players
							foreach ($server['players'] as $player)
							{
								echo "<tr>\n";
									echo "<td>".$player['gq_name']."</td>\n";
									echo "<td>".$player['gq_score']."</td>\n";
									//TODO: can we grab this other information?
									//echo "<td>".$player['gq_deaths']."</td>\n";
									//echo "<td>".$player['gq_ping']."</td>\n";
									//time?...
								echo "</tr>\n";
							}
							echo "</table>";
						echo "</div>\n";
					}
				echo "</div>\n";

				echo "<div class='div-table-col div-right'>\n";
					echo "<div class='details'>\n";
					//Server details
					echo "<p>Dedicated: ";
					if ($server['gq_dedicated'] == 'd') {
						echo "Yes</p>\n";
					} else {
						echo "No</p>\n";
					}
					echo "<p><div class='mods' style='cursor: pointer' title='Show/Hide Mod List'><div class='like-link'>Mods:</div>\n";
					//TODO: this really should be fixed by normalizing the variable name
					//issue upstream with GameQ, see here: https://github.com/Austinb/GameQ/issues/246
					if (isset($server['sigNames:0-1'])) {
						echo "<div class='hide-mods'>" . $server['sigNames:0-1'] . "</div>\n";
					} else if (isset($server['sigNames:0-2'])) {
						echo "<div class='hide-mods'>" . $server['sigNames:0-2'] . "</div>\n";
					} else {
						echo "<div class='hide-mods'>No Mods</div>\n";
					}
					echo "</div></div></p>\n";
				echo "</div>\n";
			echo "</div>\n";

		} else {
			echo "<p class='error'>The server " . $key . " is down.</p>";
		}
	}
	echo "</div>\n";

	//exit and return result
	exit();
}

?>


