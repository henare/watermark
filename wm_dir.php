<?
$mmPage = $_GET['page'];
$mypfad = $_GET['pfad'];
$myFunk = $_GET['funk'];
$myAnza = $_GET['anza'];
$myBaku = $_GET['baku'];

$WorkAnzahl=0;

$wm_bak_options="wm_bak_options";

//get_currentuserinfo();
if (!current_user_can('manage_options')) {
	die ("Sorry, you must be logged in and at least a level 8 user to access admin setup options.");
}

for ($i=0; $i< $myAnza; $i++) {
	$TmpVar="myC".$i;
	${$TmpVar} = $_GET['C'.$i];
	if(${$TmpVar} != "") {
		$WorkAnzahl++;
	}
}

if ($mypfad != "") {
	$dir="$mypfad";
	$dir=rtrim($dir,'/');
}
else {
	$dir=(ABSPATH.'wp-content/plugins');
	$dir=rtrim($dir,'/');
}

$anzahl=0;
$fileliste=array();
if ($handle = @opendir($dir)) {
	while (false !== ($datei = readdir($handle))) {
		if (function_exists('exif_imagetype')) {
			if ((is_dir("$dir/$datei") || ((exif_imagetype("$dir/$datei") < 4) && (exif_imagetype("$dir/$datei") > 0))) && $datei != '.') {
				array_push($fileliste, $datei);
				$anzahl++;
			}
		}
		else {
			$dateiextension=MM_CheckFile_WM($datei);
			if ((is_dir("$dir/$datei") && ($datei != '.')) || ($dateiextension != "")) {
				array_push($fileliste, $datei);
				$anzahl++;
			}
		}
	}
	$opendirfehler=0;
}
else {
	$opendirfehler=1;
}

if ($WorkAnzahl == 0) {
	?>
	<html>
	<head>
	<SCRIPT LANGUAGE="JavaScript">
	<!-- Begin
	function checkAll() {
	for (var j = 0; j < <?php echo "$anzahl"; ?>; j++) {
	box = eval("document.checkboxform.C" + j);
	if (box.checked == false) box.checked = true;
	   }
	}

	function uncheckAll() {
	for (var j = 0; j < <?php echo "$anzahl"; ?>; j++) {
	box = eval("document.checkboxform.C" + j);
	if (box.checked == true) box.checked = false;
	   }
	}

	function switchAll() {
	for (var j = 0; j < <?php echo "$anzahl"; ?>; j++) {
	box = eval("document.checkboxform.C" + j);
	box.checked = !box.checked;
	   }
	}
	//  End -->
	</script>
	</head>
	<body>
	<div class="wrap">
	<?php
	echo '
		<h2>Watermark-Plugin '.WM_AKT_VERSION.' - Watermark - Directory</h2>';

	if ($myFunk == "execute") {
		print "<div id=\"message\" class=\"updated fade\"><p><strong>No files selected </strong></p></div>";
	}
	if ($myFunk == "writebak") {
		$wm_bak_options="wm_bak_options";
		update_option($wm_bak_options, $myBaku);
		print "<div id=\"message\" class=\"updated fade\"><p><strong>Option successfully saved!</strong></p></div>";
	}
	echo "<form action=\"".$_SERVER['PHP_SELF'];
	echo '" method="GET"> <p>Path: <input name="pfad" type="text" size="90" maxlength="2048" value="'.$dir.'">
			<input type=hidden name="page" value="'.$mmPage.'">
			<input type=submit value="search files"></p>
			</form>
			<br>';
	$getBakFromDB=get_option($wm_bak_options);

	echo "<form action=\"".$_SERVER['PHP_SELF'];
	echo '" method="GET">
		   Create bakup (.bak) from each file?  <input type="checkbox" name="baku"';
	if ($getBakFromDB=="on") {
		echo ' checked';
	}
	echo '>
			<input type=submit value="Update Option &raquo;">
			<input type=hidden name="page" value="'.$mmPage.'">
			<input type=hidden name="funk" value="writebak">
			</form><br>
			<hr>';
	if ($opendirfehler==0) {
		echo'<table border="1">
			<form name=checkboxform action="'.$_SERVER['PHP_SELF'].'">
			<tr><td align="center" width="20">
				<input type=button value="Check All" onClick="checkAll()"><br>
				<input type=button value="Uncheck All" onClick="uncheckAll()"><br>
				<input type=button value="Switch All" onClick="switchAll()"><br>
			</td><td width="20">&nbsp;</td></tr>
			';
		$zaehler=0;
		foreach ($fileliste as $datei) {
			print '<tr><td align="center">';
			$tmpdatei="$dir/$datei";
			if (is_dir($tmpdatei)) {
				print "&nbsp;";
			}
			else {
				print '<input type="checkbox" name=C'.$zaehler.' value="'.$datei.'">';
					$zaehler++;
			}
			print '</td><td>';
			if (is_dir($tmpdatei)) {
				if ($datei == "..") {
					$pfadteile = explode("/", $dir);
				$ab = array_pop($pfadteile);
						$ab = array_shift($pfadteile);
						foreach($pfadteile as $tmpteile) {
						$tmp2dir="$tmp2dir/$tmpteile";
					}
					$tmpdatei="$tmp2dir";
				}
				else {
					$tmpdatei="$dir/$datei";
				}
				print "<a href=\"".$_SERVER['PHP_SELF']."?page=".$mmPage."&pfad=".$tmpdatei."\">";
				if ($datei == "..") {
					print "&nbsp;&lt;parent&nbsp;directory&gt;&nbsp;</a>";
				}
				else {
					print "&nbsp;$datei</a>&nbsp;";
					//echo substr(sprintf('%o', fileperms($tmpdatei)), -4);
				}
		 	}
			else {
				print "$datei";
			}
			print "</td></tr>\n";
		}
		echo '</table>';
		echo '
			<input type=hidden name="page" value="'.$mmPage.'">
			<input type=hidden name="anza" value="'.$zaehler.'">
			<input type=hidden name="funk" value="execute">
			<input type=hidden name="pfad" value="'.$dir.'">
			<input type=submit value="Create Watermark &raquo;"><br>
			</form>';
		closedir($handle);
	}
	else {
		print "<div id=\"message\" class=\"updated fade\"><p><strong>You can't lists files in this directory!</strong></p></div>";
	}
}
else {
	echo '
	<html>
	<body>
	<div class="wrap">
	<h2>Watermark-Plugin '.WM_AKT_VERSION.' - Watermark - Directory</h2><p>
	<p><b>Watermark files:</b></p>';
	if(is_writable($mypfad)) {
		for ($i=0; $i< $myAnza; $i++) {
			$newVariable="myC".$i;
			if (${$newVariable} != ""){
				$getBakFromDB=get_option($wm_bak_options);
				if ($getBakFromDB == "on") {
					copy($mypfad.'/'.${$newVariable}, $mypfad.'/'.${$newVariable}.'.bak');
				}
				$file=$mypfad.'/'.${$newVariable};
				$outfile=$mypfad.'/'.${$newVariable};
				echo $outfile;
				MM_Execute_WM($file,"");
				if(is_writable($file)) {
					if (function_exists('exif_imagetype')) {
		   				switch(@exif_imagetype($file)) {
	    	    			case 2: @ImageJPEG($file,$outfile,100); break;
							case 1: if (function_exists("imagegif")) {
										@ImageGif($file,$outfile);}
									else {
	    								$outfile=str_replace('gif','jpg',$outfile);
										@ImageJPEG($file,$outfile,100);
		    							}
								break;
	    	    			case 3: @ImagePNG($file,$outfile);break;
	   					}
	   				}
		   			else {
   						switch(MM_CheckFile_WM($file)) {
   		 	    			case "jpg"	: @ImageJPEG($file,$outfile,100); break;
							case "gif"	: if (function_exists("imagegif")) {
											@ImageGif($file,$outfile);}
   										else {
   											$outfile=str_replace('gif','jpg',$outfile);
											@ImageJPEG($file,$outfile,100);
   										}
										break;
    	    				case "png": @ImagePNG($file,$outfile);break;
						}
					}
   						echo ' ... done<br>';
   				}
				else {
					echo ' ... error<br>';
					print "<div id=\"message\" class=\"updated fade\"><p><strong>You dont't have permission to overwrite:<br>".$outfile." !</strong></p></div>";
				}
			}
   		}
   	}
	else {
		print "<div id=\"message\" class=\"updated fade\"><p><strong>You dont't have permission to write in this directory!</strong></p></div>";
	}
	echo "<h3><a href=\"".$_SERVER['PHP_SELF']."?page=".$mmPage."\">Back to Watermark main page</a></h3>";
}
?>
</div>
</body>
</html>