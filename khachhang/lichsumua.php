<?php
session_start();
include("../class/classtmdt.php");

if (!isset($_SESSION['idkh'])) {
    header('location:../login/loginkhachhang.php');
    exit();
}

$p      = new csdltmdt();
$idkh   = $_SESSION['idkh'];
$TY_GIA = 25000;

$danhsachDH = $p->xemLichSuMuaHang($idkh);

// Trạng thái đơn hàng
$trangThaiLabel = array(
    0 => 'Chờ xử lý',
    1 => 'Đang giao',
    2 => 'Đã giao'
);
$trangThaiColor = array(
    0 => '#e6a817',
    1 => '#2196F3',
    2 => '#4caf50'
);

// Xem chi tiết 1 đơn hàng
$chiTiet  = array();
$iddhXem  = 0;
if (isset($_GET['xemdon'])) {
    $iddhXem = intval($_GET['xemdon']);
    $chiTiet = $p->xemChiTietDonHang($iddhXem);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lịch Sử Mua Hàng</title>
    <link rel="stylesheet" type="text/css" href="../css/css.css">
    <style>
        table { border-collapse: collapse; width: 95%; margin: 10px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f0f0f0; }
        img { max-width: 60px; }
        .tong-tket { font-weight: bold; font-size: 16px; color: #c00;
                     text-align: right; width: 95%; margin: 6px auto; }
        .trangthai { font-weight: bold; padding: 3px 8px; border-radius: 4px; color: #fff; }
        .box-tongtke { background: #fff8e1; border: 1px solid #f0c040; border-radius: 6px;
                       padding: 14px 24px; width: 95%; margin: 12px auto; box-sizing: border-box; }
        .box-tongtke h4 { margin: 0 0 8px; }
        .box-tongtke .so { font-size: 22px; font-weight: bold; color: #c00; }
    </style>
</head>
<body>
<div id="container">
    <div id="banner">BANNER</div>
    <div id="main">
        <div id="left">
            <ul style="list-style:none; padding:0; margin:0;">
                <li><a href="../index.php">Trang Chủ</a></li>
                <li><a href="giohang.php">Giỏ Hàng</a></li>
                <li><strong>Lịch sử mua hàng</strong></li>
                <li><a href="../login/logout.php">Đăng xuất</a></li>
            </ul>
        </div>
        <div id="right">
            <h3 align="center">LỊCH SỬ MUA HÀNG</h3>

            <?php if (isset($_GET['success'])): ?>
                <p align="center" style="color:green; font-weight:bold;">
                    Đặt hàng thành công! Cảm ơn bạn đã mua hàng.
                </p>
            <?php endif; ?>

            <?php
            // ---- THỐNG KÊ TỔNG ----
            $tongTatCa   = 0;
            $soDonHang   = count($danhsachDH);
            foreach ($danhsachDH as $dh) {
                $tongTatCa += $dh['tongtien'];
            }
            ?>
            <div class="box-tongtke">
                <h4>THỐNG KÊ TỔNG TIỀN ĐÃ MUA</h4>
                <table width="100%" style="border:none; width:auto; margin:0;">
                    <tr>
                        <td style="border:none; padding:4px 20px 4px 0;">Tổng số đơn hàng:</td>
                        <td style="border:none;" class="so"><?php echo $soDonHang; ?> đơn</td>
                    </tr>
                    <tr>
                        <td style="border:none; padding:4px 20px 4px 0;">Tổng tiền đã chi:</td>
                        <td style="border:none;" class="so">
                            <?php echo number_format($tongTatCa * $TY_GIA, 0, ',', '.'); ?> VNĐ
                        </td>
                    </tr>
                </table>
            </div>

            <?php if (empty($danhsachDH)): ?>
                <p align="center">Bạn chưa có đơn hàng nào. <a href="../index.php">Mua sắm ngay</a></p>
            <?php else: ?>

            <table>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền (VNĐ)</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
                <?php foreach ($danhsachDH as $dh):
                    $tt    = intval($dh['trangthai']);
                    $label = isset($trangThaiLabel[$tt]) ? $trangThaiLabel[$tt] : 'Không rõ';
                    $color = isset($trangThaiColor[$tt]) ? $trangThaiColor[$tt] : '#999';
                ?>
                <tr>
                    <td>#<?php echo $dh['iddh']; ?></td>
                    <td><?php echo $dh['ngaydathang']; ?></td>
                    <td><?php echo number_format($dh['tongtien'] * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
                    <td>
                        <span class="trangthai" style="background:<?php echo $color; ?>">
                            <?php echo $label; ?>
                        </span>
                    </td>
                    <td><a href="lichsumua.php?xemdon=<?php echo $dh['iddh']; ?>">Xem</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php endif; ?>

            <?php if ($iddhXem > 0): ?>
            <hr>
            <h4 align="center">Chi tiết đơn hàng #<?php echo $iddhXem; ?></h4>
            <?php if (empty($chiTiet)): ?>
                <p align="center">Không có dữ liệu.</p>
            <?php else: ?>
            <table>
                <tr>
                    <th>Hình</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá (VNĐ)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (VNĐ)</th>
                </tr>
                <?php
                $tongDon = 0;
                foreach ($chiTiet as $sp):
                    $tt_sp = $sp['dongia'] * $sp['soluong'];
                    $tongDon += $tt_sp;
                ?>
                <tr>
                    <td><img src="../img/<?php echo $sp['hinh']; ?>" /></td>
                    <td><?php echo $sp['tensp']; ?></td>
                    <td><?php echo number_format($sp['dongia'] * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
                    <td><?php echo $sp['soluong']; ?></td>
                    <td><?php echo number_format($tt_sp * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div class="tong-tket">
                Tổng đơn: <?php echo number_format($tongDon * $TY_GIA, 0, ',', '.'); ?> VNĐ
            </div>
            <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
    <div id="footer">FOOTER</div>
</div>
</body>
</html>
