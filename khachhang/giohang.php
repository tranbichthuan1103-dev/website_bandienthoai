<?php
session_start();
include("../class/classtmdt.php");
include("../classlogin/clslogin.php");

// Bắt buộc đăng nhập mới xem được giỏ hàng
if (!isset($_SESSION['idkh'])) {
    header('location:../login/loginkhachhang.php');
    exit();
}

$p = new csdltmdt();
$idkh = $_SESSION['idkh'];

// Xử lý xóa 1 sản phẩm
if (isset($_GET['xoa'])) {
    $p->xoaSanPhamKhoiGiohang(intval($_GET['xoa']));
    header('location:giohang.php');
    exit();
}

// Xử lý cập nhật số lượng
if (isset($_POST['capnhat'])) {
    foreach ($_POST['soluong'] as $idchitiet => $soluong) {
        $p->capNhatSoluongGiohang(intval($idchitiet), intval($soluong));
    }
    header('location:giohang.php');
    exit();
}

$danhsach = $p->xemChiTietGiohang($idkh);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" type="text/css" href="../css/css.css">
    <style>
        table { border-collapse: collapse; width: 95%; margin: 10px auto; }
        td, th { border: 1px solid #ccc; padding: 6px; text-align: left; }
        img { max-width: 80px; }
        input[type=text] { width: 45px; }
    </style>
</head>
<body>
<div id="container">
    <div id="banner">BANNER</div>
    <div id="main">
        <div id="left">
            <ul style="list-style:none; padding:0; margin:0;">
                <li><a href="../index.php">Trang Chủ</a></li>
                <li><strong>Giỏ Hàng</strong></li>
                <li><a href="lichsumua.php">Lịch sử mua hàng</a></li>
                <li><a href="../login/logout.php">Đăng xuất</a></li>
            </ul>
        </div>
        <div id="right">
            <h3 align="center">GIỎ HÀNG CỦA BẠN</h3>
            <?php if (empty($danhsach)): ?>
                <p align="center">Giỏ hàng trống. <a href="../index.php">Tiếp tục mua sắm</a></p>
            <?php else: ?>
            <form method="post" action="">
            <table>
                <tr>
                    <th>Hình</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
                <?php
                $tongtien = 0;
                foreach ($danhsach as $item):
                    $thanhtien = $item['gia'] * $item['soluong'];
                    $tongtien += $thanhtien;
                ?>
                <tr>
                    <td><img src="../img/<?php echo $item['hinh']; ?>" /></td>
                    <td><?php echo $item['tensp']; ?></td>
                    <td><?php echo $item['gia']; ?> USD</td>
                    <td>
                        <input type="text" name="soluong[<?php echo $item['idchitiet']; ?>]"
                               value="<?php echo $item['soluong']; ?>" />
                    </td>
                    <td><?php echo $thanhtien; ?> USD</td>
                    <td><a href="giohang.php?xoa=<?php echo $item['idchitiet']; ?>"
                           onclick="return confirm('Xóa sản phẩm này?')">Xóa</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><b>Tổng cộng: <?php echo $tongtien; ?> USD</b></p>
            <div align="center" style="margin:10px;">
                <input type="submit" name="capnhat" value="Cập nhật giỏ hàng" />
                &nbsp;&nbsp;
                <a href="../index.php">Tiếp tục mua sắm</a>
                &nbsp;&nbsp;
                <a href="xacnhandathang.php">Tiến hành đặt hàng &raquo;</a>
            </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div id="footer">FOOTER</div>
</div>
</body>
</html>
