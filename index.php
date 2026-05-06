<?php
session_start();
include("class/classtmdt.php");
include("classlogin/clslogin.php");

$p = new csdltmdt();

// Check login khách hàng
if (isset($_SESSION['idkh']) && isset($_SESSION['email']) && isset($_SESSION['pass'])) {
    $q = new login();
    $q->confirmloginkhachhang($_SESSION['idkh'], $_SESSION['email'], $_SESSION['pass']);
}
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
                        <a href="khachhang/giohang.php">
                            Giỏ Hàng 
                            <?php 
                            if (isset($_SESSION['idkh'])) {
                                $sosp = $p->demSanPhamTrongGiohang($_SESSION['idkh']);
                                if ($sosp > 0) echo '(' . $sosp . ')';
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        if (isset($_SESSION['idkh'])) {
                            $hodem = isset($_SESSION['hodem']) ? $_SESSION['hodem'] : '';
                            $ten = isset($_SESSION['ten']) ? $_SESSION['ten'] : '';
                            echo '<a href="index.php"> Xin chào ' . $hodem . ' ' . $ten . '</a>';
                            echo '<br>';
                            echo '<a href="login/logout.php">Đăng xuất</a>';
                        } else {
                            echo '<a href="./login/loginkhachhang.php">Đăng nhập khách hàng</a>';
                        }
                        ?>
                    </li>
					<li>
						<b>Danh sách công ty</b>
						<br>
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