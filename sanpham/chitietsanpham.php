<?php
session_start();
include("../class/classtmdt.php");
include("../class/clskhachhang.php");

$q = new csdltmdt();
$p = new khachhang();

// Xử lý thêm vào giỏ hàng
if (isset($_POST['addtocart'])) {
    header('Content-Type: text/html; charset=utf-8');
    if (!isset($_SESSION['idkh'])) {
        echo '<script>alert("Vui lòng đăng nhập để thêm vào giỏ hàng!"); window.location="../login/login.php";</script>';
        exit();
    }
    $idsp    = intval($_POST['idsp']);
    $soluong = intval($_POST['soluong']);
    $gia     = floatval($_POST['gia']);
    if ($idsp > 0 && $soluong > 0) {
        $q->themSanPhamVaoGiohang($_SESSION['idkh'], $idsp, $soluong, $gia);
        echo '<script>alert("Thêm vào giỏ hàng thành công!"); window.location="../khachhang/giohang.php";</script>';
        exit();
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" type="text/css" href="../css/css.css">
</head>
<style>
    #right {
        width: 80%; 
        margin: 0 auto;
    }

    /* Đảm bảo class này khớp với bảng bên dưới */
    .product-detail-table {
        width: 100%;
        max-width: 900px;
        margin: 20px auto;
        background: #fff;
        border-collapse: collapse;
    }

    .product-detail-table img {
        max-width: 100%;
        height: auto;
    }
</style>
<body>
    <div id="container">
        <div id="banner">BANNER</div>
        <div id="main">
            <div id="right">
                <?php
                $idsp = intval($_REQUEST['id']);
                $chitietsanpham = $p->chitietsanpham($idsp);
                if ($chitietsanpham != 0) {
                    echo '<form method="post" action="">
                    <input type="hidden" name="idsp" value="' . $chitietsanpham['idsp'] . '" />
                    <input type="hidden" name="gia" value="' . $chitietsanpham['gia'] . '" />
                    
                    <!-- THÊM class="product-detail-table" VÀ TĂNG CHIỀU RỘNG HOẶC ĐỂ 100% -->
                    <table class="product-detail-table" width="100%" border="1" align="center" cellpadding="10" cellspacing="0">
    
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                            <strong>XEM CHI TIẾT SẢN PHẨM</strong>
                        </td>
                    </tr>

                    <tr>
                        <!-- Mở rộng cột chứa hình ảnh -->
                        <td width="300" rowspan="5" align="center" valign="middle">
                            <img src="../img/' . $chitietsanpham['hinh'] . '" width="250" height="248" />
                        </td>
                        <td width="150"><strong>Tên sản phẩm</strong></td>
                        <td width="450">' . $chitietsanpham['tensp'] . '</td>
                    </tr>

                    <tr>
                        <td><strong>Mô tả</strong></td>
                        <td>' . $chitietsanpham['mota'] . '</td>
                    </tr>

                    <tr>
                        <td><strong>Giá (USD)</strong></td>
                        <td>' . $chitietsanpham['gia'] . ' USD</td>
                    </tr>

                    <tr>
                        <td><strong>Số lượng</strong></td>
                        <td>
                            <input name="soluong" type="text" value="1" size="4" />
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" align="center" valign="middle">
                            <input type="submit" name="addtocart" value="Thêm Vào Giỏ Hàng" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                            <a href="../index.php">Quay lại danh sách</a>
                        </td>
                    </tr>

                    </table>
                    </form>
                    ';

                } else {
                    echo 'Không tìm thấy sản phẩm.';
                }
                ?>
            </div>
        </div>

        <div id="footer">FOOTER</div>
    </div>
</body>

</html>