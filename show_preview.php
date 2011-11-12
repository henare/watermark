<?php
require("../../../wp-config.php");
require_once("wm_functions.php");
$PicType=$_GET['PicType'];
MM_Execute_WM("",$PicType);
?>