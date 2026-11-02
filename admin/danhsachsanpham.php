<?php
if (session_id() == '') {
    session_start();
}

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['id']) || !isset($_SESSION['user']) || !isset($_SESSION['phanquyen']) || $_SESSION['phanquyen'] != 1) {
    header('location:../login/login.php');
    exit();
}

// Kết nối cơ sở dữ liệu MySQL
$link = mysql_connect("localhost", "usertmdt", "passtmdt");
if (!$link) {
    die("Không thể kết nối cơ sở dữ liệu: " . mysql_error());
}
mysql_select_db("tmdt_db", $link);
mysql_query("SET NAMES UTF8", $link);

// Hàm xóa file ảnh an toàn (chỉ xóa khỏi ổ đĩa khi KHÔNG còn sản phẩm nào khác dùng chung)
function xoaFileHinhAnToan($tenHinh, $excludeIdsp, $link) {
    if (empty($tenHinh)) return;
    $tenHinh_esc = mysql_real_escape_string($tenHinh, $link);
    $check = mysql_query("SELECT COUNT(*) AS total FROM sanpham WHERE hinh = '$tenHinh_esc' AND idsp != $excludeIdsp", $link);
    if ($check) {
        $row = mysql_fetch_assoc($check);
        if ($row && intval($row['total']) == 0) {
            $duongDan = "../img/" . $tenHinh;
            if (file_exists($duongDan) && is_file($duongDan)) {
                @unlink($duongDan);
            }
        }
    }
}

$thongbao = '';
$txtidsp = '';
$txtten = '';
$txtgia = '';
$txtgiamgia = '0';
$txtmota = '';
$congty_id = 0;
$hinh_hientai = '';

// 2. Xử lý khi nhấn 1 trong 3 nút chức năng bằng phương thức POST
if (isset($_POST['btnAction'])) {
    $action = $_POST['btnAction'];
    $txtidsp = isset($_POST['txtidsp']) ? intval($_POST['txtidsp']) : 0;
    $txtten = isset($_POST['txtten']) ? trim($_POST['txtten']) : '';
    $txtgia = isset($_POST['txtgia']) ? trim($_POST['txtgia']) : '';
    $txtgiamgia = isset($_POST['txtgiamgia']) ? trim($_POST['txtgiamgia']) : '0';
    $txtmota = isset($_POST['txtmota']) ? trim($_POST['txtmota']) : '';
    $congty_id = isset($_POST['congty']) ? intval($_POST['congty']) : 0;
    $hinh_hientai = isset($_POST['hinhcu']) ? trim($_POST['hinhcu']) : '';

    if ($action == 'Thêm sản phẩm') {
        if ($txtten == '') {
            $thongbao = 'Vui lòng nhập tên sản phẩm!';
        } else if ($txtgia == '' || !is_numeric($txtgia) || floatval($txtgia) < 0) {
            $thongbao = 'Vui lòng nhập giá sản phẩm hợp lệ!';
        } else if ($congty_id <= 0) {
            $thongbao = 'Vui lòng chọn công ty!';
        } else {
            $ten_esc = mysql_real_escape_string($txtten, $link);
            $gia_num = floatval($txtgia);
            $giamgia_num = (is_numeric($txtgiamgia) && floatval($txtgiamgia) >= 0) ? floatval($txtgiamgia) : 0;
            $mota_esc = mysql_real_escape_string($txtmota, $link);

            $hinh_name = '';
            if (isset($_FILES['myfile']) && $_FILES['myfile']['name'] != '') {
                // 1. Tải ảnh mới lên: tạo tên file độc nhất để không bao giờ ghi đè lên ảnh của sản phẩm khác
                $ext = pathinfo($_FILES['myfile']['name'], PATHINFO_EXTENSION);
                $base = pathinfo($_FILES['myfile']['name'], PATHINFO_FILENAME);
                $clean_base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
                $hinh_name = time() . "_" . uniqid() . "_" . $clean_base . ($ext ? "." . $ext : "");
                $tmp_name = $_FILES['myfile']['tmp_name'];
                move_uploaded_file($tmp_name, "../img/" . $hinh_name);
            } else if ($hinh_hientai != '' && file_exists("../img/" . $hinh_hientai)) {
                // 2. Thêm mới từ sản phẩm cũ nhưng không chọn ảnh mới:
                // Sao chép vật lý thành một file ảnh riêng biệt cho sản phẩm mới
                $ext = pathinfo($hinh_hientai, PATHINFO_EXTENSION);
                $base = pathinfo($hinh_hientai, PATHINFO_FILENAME);
                $clean_base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
                $hinh_name = time() . "_" . uniqid() . "_" . $clean_base . ($ext ? "." . $ext : "");
                copy("../img/" . $hinh_hientai, "../img/" . $hinh_name);
            }
            $hinh_esc = mysql_real_escape_string($hinh_name, $link);

            $sql_insert = "INSERT INTO sanpham (tensp, gia, giamgia, mota, hinh, idcty) 
                           VALUES ('$ten_esc', $gia_num, $giamgia_num, '$mota_esc', '$hinh_esc', $congty_id)";
            if (mysql_query($sql_insert, $link)) {
                $thongbao = 'Thêm sản phẩm thành công!';
            } else {
                $thongbao = 'Lỗi khi thêm sản phẩm: ' . mysql_error($link);
            }
        }
    } else if ($action == 'Sửa sản phẩm') {
        if ($txtidsp <= 0) {
            $thongbao = 'Vui lòng chọn sản phẩm cần sửa.';
        } else {
            $check = mysql_query("SELECT idsp, hinh FROM sanpham WHERE idsp = $txtidsp LIMIT 1", $link);
            if (mysql_num_rows($check) == 0) {
                $thongbao = 'Sản phẩm không tồn tại trong hệ thống!';
            } else {
                $row_check = mysql_fetch_assoc($check);
                if ($txtten == '') {
                    $thongbao = 'Vui lòng nhập tên sản phẩm!';
                } else if ($txtgia == '' || !is_numeric($txtgia) || floatval($txtgia) < 0) {
                    $thongbao = 'Vui lòng nhập giá sản phẩm hợp lệ!';
                } else if ($congty_id <= 0) {
                    $thongbao = 'Vui lòng chọn công ty!';
                } else {
                    $ten_esc = mysql_real_escape_string($txtten, $link);
                    $gia_num = floatval($txtgia);
                    $giamgia_num = (is_numeric($txtgiamgia) && floatval($txtgiamgia) >= 0) ? floatval($txtgiamgia) : 0;
                    $mota_esc = mysql_real_escape_string($txtmota, $link);

                    $hinh_sql = "";
                    if (isset($_FILES['myfile']) && $_FILES['myfile']['name'] != '') {
                        // Tải ảnh mới lên: tạo tên file độc nhất
                        $ext = pathinfo($_FILES['myfile']['name'], PATHINFO_EXTENSION);
                        $base = pathinfo($_FILES['myfile']['name'], PATHINFO_FILENAME);
                        $clean_base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
                        $new_hinh = time() . "_" . uniqid() . "_" . $clean_base . ($ext ? "." . $ext : "");
                        $tmp_name = $_FILES['myfile']['tmp_name'];
                        if (move_uploaded_file($tmp_name, "../img/" . $new_hinh)) {
                            // Xóa an toàn ảnh cũ (chỉ xóa nếu không có sản phẩm khác dùng chung)
                            xoaFileHinhAnToan($row_check['hinh'], $txtidsp, $link);
                            $new_hinh_esc = mysql_real_escape_string($new_hinh, $link);
                            $hinh_sql = ", hinh = '$new_hinh_esc'";
                        }
                    }

                    $sql_update = "UPDATE sanpham SET 
                                   tensp = '$ten_esc', 
                                   gia = $gia_num, 
                                   giamgia = $giamgia_num, 
                                   mota = '$mota_esc', 
                                   idcty = $congty_id 
                                   $hinh_sql 
                                   WHERE idsp = $txtidsp";
                    if (mysql_query($sql_update, $link)) {
                        $thongbao = 'Cập nhật sản phẩm thành công!';
                    } else {
                        $thongbao = 'Lỗi khi cập nhật sản phẩm: ' . mysql_error($link);
                    }
                }
            }
        }
    } else if ($action == 'Xóa sản phẩm') {
        if ($txtidsp <= 0) {
            $thongbao = 'Vui lòng chọn sản phẩm cần xóa.';
        } else {
            $check = mysql_query("SELECT idsp, hinh FROM sanpham WHERE idsp = $txtidsp LIMIT 1", $link);
            if (mysql_num_rows($check) == 0) {
                $thongbao = 'Sản phẩm không tồn tại trong hệ thống!';
            } else {
                $row_check = mysql_fetch_assoc($check);
                // Kiểm tra ràng buộc dữ liệu trong bảng dathang_chitiet
                $check_dh = mysql_query("SELECT COUNT(*) AS total FROM dathang_chitiet WHERE idsp = $txtidsp", $link);
                $row_dh = mysql_fetch_assoc($check_dh);
                if ($row_dh && $row_dh['total'] > 0) {
                    $thongbao = 'Không thể xóa sản phẩm này vì đã tồn tại trong ' . $row_dh['total'] . ' chi tiết đơn hàng!';
                } else {
                    $hinh_can_xoa = $row_check['hinh'];
                    $sql_delete = "DELETE FROM sanpham WHERE idsp = $txtidsp";
                    if (mysql_query($sql_delete, $link)) {
                        // Xóa an toàn ảnh của sản phẩm bị xóa (chỉ xóa nếu không có sản phẩm nào khác dùng chung)
                        xoaFileHinhAnToan($hinh_can_xoa, $txtidsp, $link);
                        $thongbao = 'Xóa sản phẩm thành công!';
                    } else {
                        $thongbao = 'Lỗi khi xóa sản phẩm: ' . mysql_error($link);
                    }
                }
            }
        }
    }
}

// Nếu có thông báo kết quả: hiển thị alert và reset lại trang
if ($thongbao != '') {
    $current_file = basename($_SERVER['PHP_SELF']);
    $reset_url = ($current_file == 'admin.php') ? 'admin.php?page=sanpham' : 'danhsachsanpham.php';
    echo "<script>alert('" . addslashes($thongbao) . "'); window.location.href = '" . $reset_url . "';</script>";
    exit();
}

// 3. Khi người dùng click chọn một dòng từ danh sách (truyền ID qua GET)
if (!isset($_POST['btnAction']) && isset($_GET['id']) && intval($_GET['id']) > 0) {
    $sel_id = intval($_GET['id']);
    $res_sel = mysql_query("SELECT * FROM sanpham WHERE idsp = $sel_id LIMIT 1", $link);
    if ($res_sel && mysql_num_rows($res_sel) > 0) {
        $row_sel = mysql_fetch_assoc($res_sel);
        $txtidsp = $row_sel['idsp'];
        $txtten = $row_sel['tensp'];
        $txtgia = $row_sel['gia'];
        $txtgiamgia = $row_sel['giamgia'];
        $txtmota = $row_sel['mota'];
        $congty_id = $row_sel['idcty'];
        $hinh_hientai = $row_sel['hinh'];
    }
}

$page_query = isset($_GET['page']) ? 'page=' . urlencode($_GET['page']) . '&' : '';
?>

<!-- PHẦN A & B: FORM QUẢN LÝ SẢN PHẨM & 3 NÚT CHỨC NĂNG -->
<h3 align="center">QUẢN LÝ SẢN PHẨM</h3>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="txtidsp" value="<?php echo htmlspecialchars($txtidsp); ?>">
    <table border="1" align="center" cellpadding="5" cellspacing="0" width="800">
        <tr>
            <td width="180">Công ty:</td>
            <td>
                <select name="congty">
                    <option value="0">-- Chọn công ty --</option>
                    <?php
                    $res_cty = mysql_query("SELECT idcty, tencty FROM congty ORDER BY tencty ASC", $link);
                    while ($cty = mysql_fetch_assoc($res_cty)) {
                        $sel = ($cty['idcty'] == $congty_id) ? 'selected' : '';
                        echo '<option value="' . $cty['idcty'] . '" ' . $sel . '>' . htmlspecialchars($cty['tencty']) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Tên sản phẩm:</td>
            <td><input type="text" name="txtten" value="<?php echo htmlspecialchars($txtten); ?>" size="50"></td>
        </tr>
        <tr>
            <td>Giá (USD):</td>
            <td><input type="text" name="txtgia" value="<?php echo htmlspecialchars($txtgia); ?>" size="50"></td>
        </tr>
        <tr>
            <td>Giảm giá (USD):</td>
            <td><input type="text" name="txtgiamgia" value="<?php echo htmlspecialchars($txtgiamgia); ?>" size="50"></td>
        </tr>
        <tr>
            <td>Mô tả:</td>
            <td><textarea name="txtmota" cols="50" rows="5"><?php echo htmlspecialchars($txtmota); ?></textarea></td>
        </tr>
        <tr>
            <td>Hình đại diện:</td>
            <td>
                <input type="file" name="myfile">
                <input type="hidden" name="hinhcu" value="<?php echo htmlspecialchars($hinh_hientai); ?>">
                <?php if ($hinh_hientai != '') { echo '<br>Hình hiện tại: ' . htmlspecialchars($hinh_hientai); } ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" name="btnAction" value="Thêm sản phẩm">
                <input type="submit" name="btnAction" value="Sửa sản phẩm">
                <input type="submit" name="btnAction" value="Xóa sản phẩm">
            </td>
        </tr>
    </table>
</form>

<div style="margin: 25px 0;"></div>

<!-- PHẦN C: DANH SÁCH SẢN PHẨM -->
<h3 align="center">DANH SÁCH SẢN PHẨM</h3>

<?php
// Phân trang sản phẩm: 5 sản phẩm mỗi trang
$limit_sp = 5;
$res_total_sp = mysql_query("SELECT COUNT(*) AS total FROM sanpham", $link);
$total_rows_sp = ($res_total_sp && mysql_num_rows($res_total_sp) > 0) ? intval(mysql_result($res_total_sp, 0, 'total')) : 0;
$total_pages_sp = ceil($total_rows_sp / $limit_sp);

$current_page_sp = isset($_GET['trang_sp']) ? intval($_GET['trang_sp']) : 1;
if ($current_page_sp < 1) $current_page_sp = 1;
if ($total_pages_sp > 0 && $current_page_sp > $total_pages_sp) $current_page_sp = $total_pages_sp;

$offset_sp = ($current_page_sp - 1) * $limit_sp;

$sql_sp = "SELECT sp.idsp, sp.tensp, sp.mota, sp.gia, ct.tencty 
           FROM sanpham sp 
           LEFT JOIN congty ct ON sp.idcty = ct.idcty 
           ORDER BY sp.idsp ASC 
           LIMIT $offset_sp, $limit_sp";
$res_sp = mysql_query($sql_sp, $link);
$stt = $offset_sp + 1;
?>

<table border="1" align="center" cellpadding="5" cellspacing="0" width="800">
    <tr>
        <th width="40">STT</th>
        <th width="160">Tên sản phẩm</th>
        <th width="390">Mô tả</th>
        <th width="110">Giá (USD)</th>
        <th width="100">Công ty</th>
    </tr>
    <?php
    if ($res_sp && mysql_num_rows($res_sp) > 0) {
        while ($row = mysql_fetch_assoc($res_sp)) {
            $id = $row['idsp'];
            $link_chon = '?' . $page_query . 'trang_sp=' . $current_page_sp . '&id=' . $id;
            echo '<tr>';
            echo '<td align="center"><a href="' . $link_chon . '">' . $stt++ . '</a></td>';
            echo '<td><a href="' . $link_chon . '">' . htmlspecialchars($row['tensp']) . '</a></td>';
            echo '<td><a href="' . $link_chon . '">' . htmlspecialchars($row['mota']) . '</a></td>';
            echo '<td align="center" style="white-space: nowrap;"><a href="' . $link_chon . '">' . htmlspecialchars($row['gia']) . ' USD</a></td>';
            echo '<td align="center"><a href="' . $link_chon . '">' . htmlspecialchars($row['tencty']) . '</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5" align="center">Không có sản phẩm nào.</td></tr>';
    }
    ?>
</table>

<?php if ($total_pages_sp > 1): ?>
<div align="center" style="margin-top: 10px;">
    <b>Trang: </b>
    <?php for ($i = 1; $i <= $total_pages_sp; $i++): ?>
        <?php if ($i == $current_page_sp): ?>
            <b>[<?php echo $i; ?>]</b>
        <?php else: ?>
            <a href="?<?php echo $page_query; ?>trang_sp=<?php echo $i; ?>">[<?php echo $i; ?>]</a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>
