<?php
if(isset($_REQUEST['stat']) && $_REQUEST['stat']=="changeme"){
	
	$event['29.Jan.2020'] = 'Created';
	$event['30.Jan.2020'] = 'Publised : Facebook - Ledergruppa';
	$event['06.Feb.2020'] = 'Audits: Performance:100%, Accessibility:100%, Best Practices:100%, SEO:100% ';
	$event['08.Feb.2020'] = 'Published: Hva skjer i Ski?(reach:13,229)';
	
	echo '<!doctype html><html class="no-js" lang="no"><head><meta charset="utf-8"><title>Stats</title>
		<style>
			html, body{
				margin: 0;
				padding:0;
				color: black;
				background-color: white;
				font: 300 15px/1.55em sans-serif;  
				height: 100%;
				width:100%;
			}
	
			table{
				margin:auto;
			}
			table .header td{
				text-align:center;
				font-weight:bold;
				background-color:#bfbfbf;;
			}
			table td{
				padding:3px;
				background-color:#e3e5e6;;
			}
			table .weeksum td{
				text-align:right;
				background-color:#bfbfbf;;
				font-weight:bold;

			}
			.graf{
				background-color:#00e135;
				height:100%;
			}
			.graf span{
				padding:5px;
				font-size:10px;
			}

			.uniqNr{
				width:50px;
				text-align:right;
				font-weight:bold;
			}
			.eventCol{
				font-size:10px;
			}
			.weekcl{
				text-align:center;
			}

		</style>
	</head><body>';

	$totalLines = 0;
	$lastIp = 0;
	$cnt = [];
	$handle = fopen("xstats.txt", "r");
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			$line = explode(";",$line);
			$date = strtotime($line[0]);
			$ip = trim($line[1]);
			$d = date("d.M.Y", $date);
			$cnt[$d]['date']=$date;
			// uniq
			if($lastIp!=$ip){
				if(isset($cnt[$d]['uniq'])){
					$cnt[$d]['uniq']++;
				}else{
					$cnt[$d]['uniq']=1;
				}
				$lastIp = $ip;
			}
			// total
			if(isset($cnt[$d]['total'])){
				$cnt[$d]['total']++;
			}else{
				$cnt[$d]['total']=1;
			}
		}
		fclose($handle);
	} else {
		// error opening the file.
	} 

	$maxUniq = 0;
	$maxTotal = 0;
	foreach($cnt as $date => $data){
		$maxUniq = $maxUniq<$data['uniq']?$data['uniq']:$maxUniq;
		$maxTotal = $maxTotal<$data['total']?$data['total']:$maxTotal;
	}

	$colums = 200;

	
	$lastWeek = 0;
	$weekSum = 0;
	$weekTotalSum = 0;
	echo "<table>";
	echo "<tr class=\"header\">";
	echo "<td>WEEK</td>";
	echo "<td>DATE</td>";
	echo "<td>UNIQ</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>TOTAL</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>EVENTS</td>";
	echo "</tr>";

	foreach($cnt as $date => $data){
		
		

		$weekNumber = date("W", $data['date']);
		$uniqProsent = round($data['uniq'] / $maxUniq * 100);
		$maxTotalProsent = round($data['total'] / $maxTotal * 100);

		if($lastWeek>0 && $lastWeek!=$weekNumber){
			echo "<tr class=\"weeksum\">";
			echo "<td colspan=\"2\" style=\"text-align:center\">Week:".$lastWeek."</td>";
			echo "<td>".$weekSum."</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>{$weekTotalSum}</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "</tr>";
			$weekSum =0;
			$lastWeek= 0;
		}

		$weekSum += $data['uniq'];
		$weekTotalSum += $data['total'];
		$lastWeek=$weekNumber;

		echo "<tr>";
		echo "<td class=\"weekcl\">".$weekNumber."</td>";
		echo "<td>".$date ."</td>";
		echo "<td class=\"uniqNr\"> ".$data['uniq']."</td>";
		echo "<td width=\"".$colums."px\">
				<div class=\"graf\" style=\"width:".($uniqProsent*$colums/100)."px\">
					<span>{$uniqProsent}%</span>
				</div>
			</td>";

		echo "<td style=\"width:50px;text-align:right;\">".$data['total']."</td>";
		echo "<td width=\"".$colums."px\">
				<div class=\"graf\" style=\"width:".($maxTotalProsent*$colums/100)."px\"> 
					<span>{$maxTotalProsent}%</span>
				</div>
			</td>";
		echo "<td class=\"eventCol\">".(isset($event[$date])?$event[$date]:'')."</td>";
		echo "</tr>";
	}

	echo "<tr class=\"weeksum\">";
	echo "<td colspan=\"2\" style=\"text-align:center\">Week:".$lastWeek."</td>";
	echo "<td>".$weekSum."</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>{$weekTotalSum}</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "</tr>";

	echo "</table>";
	echo "</body></html>";

}else{

	$file = file_get_contents('raw.html');

	echo $file;

	try{
		$file_name = 'xstats.txt';
		$myfile = fopen($file_name, 'a') or die('Cannot open file: '.$file_name); 
		fwrite($myfile, "".date("d.m.Y H:i:s", time())." ;  ".$_SERVER['REMOTE_ADDR']." ;  ".$_SERVER['HTTP_USER_AGENT'].PHP_EOL) ;
		fclose($myfile);
		
	} catch (Exception $e) {
		//echo 'Caught exception: ',  $e->getMessage(), "\n";
	}/**/

}


function deb($m){
	echo "<pre>".print_r($m, true)."</pre>";
}