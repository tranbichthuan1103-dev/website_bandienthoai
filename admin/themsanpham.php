<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['user']) && isset($_SESSION['pass']) && isset($_SESSION['phanquyen'])) {
    require_once("../classlogin/clslogin.php");
    $q = new login();
    $q->confirmlogin($_SESSION['id'], $_SESSION['user'], $_SESSION['pass'], $_SESSION['phanquyen']);
} else {
    header('location:../login/login.php');
    exit();
}
if (!isset($p)) {
    require_once("../class/classtmdt.php");
    $p = new csdltmdt();
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>ADMIN</title>
    <style type="text/css">
        #nut {
            text-align: center;
        }
    </style>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data" name="form1">
        <table width="800" border="1" align="center" cellpadding="3" cellspacing="0">
            <tr>
                <td colspan="2" align="center">THÊM SẢN PHẨM</td>
            </tr>
            <tr>
                <td width="249">Nhà sản xuất</td>
                <td width="533">
                    <?php
                    $p->combobox_congty("select idcty, tencty from congty order by tencty asc");
                    ?>
                </td>
            </tr>
            <tr>
                <td>Nhập tên sản phẩm</td>
                <td><label for="txtten"></label>
                    <input name="txtten" type="text" id="txtten" size="50">
                </td>
            </tr>
            <tr>
                <td>Nhập giá</td>
                <td><input name="txtgia" type="text" id="txtgia" size="50"></td>
            </tr>
            <tr>
                <td>Nhập giảm giá</td>
                <td><input name="txtgiamgia" type="text" id="txtgiamgia" size="50"></td>
            </tr>
            <tr>
                <td>Mô tả </td>
                <td><label for="txtmota"></label>
                    <textarea name="txtmota" cols="50" rows="5" id="txtmota"></textarea>
                </td>
            </tr>
            <tr>
                <td>Hình đại diện</td>
                <td><label for=""></label>
                    <input name="myfile" type="file" id="myfile">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="submit" name="nut" id="nut" value="Thêm sản phẩm"></td>
            </tr>
        </table>
        <?php
        switch ($_POST['nut']) {
            case 'Thêm sản phẩm': {
                $ten = $_REQUEST['txtten'];
                $gia = $_REQUEST['txtgia'];
                $giamgia = $_REQUEST['txtgiamgia'];
                $mota = trim($_REQUEST['txtmota']);
                $congty = $_REQUEST['congty'];
                $name = $_FILES['myfile']['name'];
                $tmp_name = $_FILES['myfile']['tmp_name'];
                if ($ten != '' && $gia != '' && $congty != 0) {
                    if ($p->uploadfile($name, $tmp_name, "../img") == 1) {
                        $sql = "INSERT INTO sanpham(tensp, gia, giamgia, mota, hinh, idcty) 
							VALUES ('$ten', '$gia', '$giamgia', '$mota', '$name', '$congty')";

                        if ($p->themxoasua($sql) == 1) {
                            echo '<script>alert("Thêm sản phẩm thành công!");</script>';
                        } else {
                            echo '<script>alert("Lỗi: Không thể lưu vào cơ sở dữ liệu!");</script>';
                        }
                    } else {
                        echo 'Upload hình không thành công';
                    }
                } else {
                    echo 'Vui lòng điền đầy đủ thông tin!';
                }
                break;
            }
        }
        ?>
    </form>
</body>

</html>