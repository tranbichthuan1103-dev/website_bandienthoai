<?php
if (session_id() == '') session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['user'])) {
    header('location:../login/login.php');
    exit();
}
// Nếu truy cập trực tiếp (không qua admin.php include) thì redirect
if (!defined('IN_ADMIN')) {
    header('location:admin.php?page=dathang' . (isset($_GET['xemdon']) ? '&xemdon=' . intval($_GET['xemdon']) : ''));
    exit();
}
header('Content-Type: text/html; charset=utf-8');
if (!isset($p)) {
    include_once("../class/classtmdt.php");
    $p = new csdltmdt();
}

$TY_GIA = 24000;

// Cập nhật trạng thái đơn hàng
if (isset($_GET['capnhat']) && isset($_GET['iddh']) && isset($_GET['tt'])) {
    $p->capNhatTrangThaiDonHang(intval($_GET['iddh']), intval($_GET['tt']));
    header('location:admin.php?page=dathang');
    exit();
}

// Xem chi tiết đơn hàng
$chiTiet = array();
$iddhXem = 0;
if (isset($_GET['xemdon'])) {
    $iddhXem = intval($_GET['xemdon']);
    $chiTiet = $p->xemChiTietDonHang($iddhXem);
}

$danhsach = $p->xemDSDatHangAdmin();

$trangThaiLabel = array(0 => 'Chờ xử lý', 1 => 'Đang giao', 2 => 'Đã giao');
$trangThaiColor = array(0 => '#e6a817', 1 => '#2196F3', 2 => '#4caf50');
?>
<style>
    table { border-collapse: collapse; width: 95%; margin: 10px auto; }
    td, th { border: 1px solid #ccc; padding: 6px; text-align: left; }
    img { max-width: 80px; }
</style>

<h3 align="center">DANH SÁCH ĐẶT HÀNG</h3>

<?php
$tongTatCa = 0;
foreach ($danhsach as $dh) $tongTatCa += $dh['tongtien'];
?>
<p><b>Tổng số đơn:</b> <?php echo count($danhsach); ?> &nbsp;|&nbsp;
   <b>Tổng doanh thu:</b> <?php echo number_format($tongTatCa * $TY_GIA, 0, ',', '.'); ?> VNĐ</p>

<?php if (empty($danhsach)): ?>
    <p align="center">Chưa có đơn hàng nào.</p>
<?php else: ?>
<table>
    <tr>
        <th>Mã ĐH</th>
        <th>Khách hàng</th>
        <th>Email</th>
        <th>Điện thoại</th>
        <th>Địa chỉ nhận</th>
        <th>Ngày đặt</th>
        <th>Tổng tiền (VNĐ)</th>
        <th>Trạng thái</th>
        <th>Chi tiết</th>
    </tr>
    <?php foreach ($danhsach as $dh):
        $tt    = intval($dh['trangthai']);
        $label = isset($trangThaiLabel[$tt]) ? $trangThaiLabel[$tt] : 'Không rõ';
        $color = isset($trangThaiColor[$tt]) ? $trangThaiColor[$tt] : '#999';
    ?>
    <tr>
        <td>#<?php echo $dh['iddh']; ?></td>
        <td><?php echo $dh['hodem'] . ' ' . $dh['ten']; ?></td>
        <td><?php echo $dh['email']; ?></td>
        <td><?php echo $dh['dienthoai']; ?></td>
        <td><?php echo $dh['diachinhanhang']; ?></td>
        <td><?php echo $dh['ngaydathang']; ?></td>
        <td><?php echo number_format($dh['tongtien'] * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
        <td>
            <?php echo $label; ?><br>
            <select onchange="window.location='danhsachdathang.php?capnhat=1&iddh=<?php echo $dh['iddh']; ?>&tt='+this.value">
                <option value="">-- Đổi --</option>
                <?php foreach ($trangThaiLabel as $val => $lbl): ?>
                    <option value="<?php echo $val; ?>" <?php if ($tt == $val) echo 'selected'; ?>>
                        <?php echo $lbl; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><a href="admin.php?page=dathang&xemdon=<?php echo $dh['iddh']; ?>">Xem</a></td>
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
    <tr>
        <td colspan="4" align="right"><b>Tổng đơn:</b></td>
        <td><b><?php echo number_format($tongDon * $TY_GIA, 0, ',', '.'); ?> VNĐ</b></td>
    </tr>
</table>
<?php endif; ?>
<?php endif; ?>
