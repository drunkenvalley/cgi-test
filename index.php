<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<!-- Meta, external content -->
<head>
<meta charset="UTF-8">
<title>Strømavlesning - Output</title>
<link rel="icon" type="image/png" href="favicon.png">
<link rel="stylesheet" type="text/css" href="styles.css">
<script src="api/jquery-3.3.1.min.js"></script>
<script src="api/meter.js"></script>
</head>
<!-- Content -->
<body>

<a href=".">Hjem</a> <a href="./?register">Les av måler</a>

<hr/>

<?php
if(isset($_GET['register'])) {
    include_once("pages/register.php");
}
else {
    include_once("pages/home.php");
}
?>

<hr/>

<div id="log"></div>
<?php
//I'm just hiding a clock here because JS requires me to pad zeroes.
//That seems like a bit of a ridiculous solution. This is also,
//but it's a tiny bit more sane to me.
$today = date("Y-m-j");
$weekago = date("Y-m-j",time() - 7*24*60*60);
echo "<input type='hidden' value='$today' id='hidden_clock' />";
echo "<input type='hidden' value='$weekago' id='hidden_past' />";

?>
</body>
</html>