<?php
include("class/classtmdt.php");

$p = new csdltmdt();

?>
<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="css/css.css">
</head>

<body>
	<div id="container">
		<div id="banner">BANNER</div>
		<div id="main">
			<div id="left">

				<ul style="list-style:none; padding:0; margin:0;">
					<li><a href="index.php">Trang Chủ</a></li>
					<li>
						<p>Danh sách công ty</p>

						<?php
						$p->xemdscongty("select * from congty order by tencty asc");
						?>
					</li>
				</ul>

			</div>

			<div id="right">
				<?php
				if (isset($_REQUEST['id'])) {
					$layid = $_REQUEST['id'];
					$p->xemdssanpham("select * from sanpham where idcty='$layid' order by gia asc");
				} else {
					$p->xemdssanpham("select * from sanpham order by gia asc");
				}

				?>
			</div>
		</div>

		<div id="footer">FOOTER</div>
	</div>
</body>

</html>