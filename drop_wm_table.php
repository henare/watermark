<html>
<body>
<center>
<?
require_once('../../../wp-config.php');

$mmDBtable=$table_prefix.'watermark';



$query = "DROP TABLE `$mmDBtable`;";
mysql_query($query);
switch(mysql_errno()){
	case "0": 
		print "<p>Drop table... done</p>";
	break;
	case "1051": 
		print "<p>Drop table... table not found! Evarything is fine!</p>";;
	break;
	default:
		print "<font color=\"#ff0000\">Can not drop the `$mmDBtable`-table</font>";
		print "<p>Please read the documentation!</p>";
		die();
	break;
} 
?>
<font color="#00ff00"><h2>Watermark table successfull dropped</h2></font>
<p>&nbsp;</p>
<p>Please delete now the file <?echo $_SERVER['PHP_SELF'] ?></p>
</center>
</body>
</html>