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
    <?php
    // LẤY ID
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // KẾT NỐI
    $link = mysql_connect("localhost", "usertmdt", "passtmdt");
    mysql_select_db("tmdt_db");
    mysql_query("SET NAMES UTF8");

    // LẤY DỮ LIỆU SẢN PHẨM
    $sql = "SELECT * FROM sanpham WHERE idsp = $id";
    $ketqua = mysql_query($sql, $link);
    $row = mysql_fetch_array($ketqua);

    $ten = $row['tensp'];
    $gia = $row['gia'];
    $giamgia = $row['giamgia'];
    $mota = $row['mota'];
    $idcty = $row['idcty'];
    $hinhcu = $row['hinh'];
    ?>
  <h2 align="center">QUẢN LÝ SẢN PHẨM</h2>

  <div style="text-align:center; margin-bottom:20px;">
    <a href="./admin.php">Danh sách</a> |
    <a href="./admin.php?page=them">Thêm</a> 
  </div>

  <hr>
  <form method="post" enctype="multipart/form-data">
      <table width="800" border="1" align="center" cellpadding="6" cellspacing="0">
            <tr align="center">
              <td colspan="2">SỬA SẢN PHẨM</td>
            </tr>
            <tr>
                <td>Nhà sản xuất</td>
                <td>
                    <?php
                    // combobox có selected
                    $link2 = mysql_connect("localhost", "usertmdt", "passtmdt");
                    mysql_select_db("tmdt_db");
                    $kqcty = mysql_query("SELECT * FROM congty", $link2);

                    echo '<select name="congty">';
                    while ($cty = mysql_fetch_array($kqcty)) {
                        if ($cty['idcty'] == $idcty)
                            echo '<option value="' . $cty['idcty'] . '" selected>' . $cty['tencty'] . '</option>';
                        else
                            echo '<option value="' . $cty['idcty'] . '">' . $cty['tencty'] . '</option>';
                    }
                    echo '</select>';
                    ?>
                </td>
            </tr>

            <tr>
                <td>Tên sản phẩm</td>
                <td><input name="txtten" type="text" value="<?php echo $ten; ?>" size="50"></td>
            </tr>

            <tr>
                <td>Giá</td>
                <td><input name="txtgia" type="text" value="<?php echo $gia; ?>" size="50"></td>
            </tr>

            <tr>
                <td>Giảm giá</td>
                <td><input name="txtgiamgia" type="text" value="<?php echo $giamgia; ?>" size="50"></td>
            </tr>

            <tr>
                <td>Mô tả</td>
                <td><textarea name="txtmota" cols="55" rows="10"><?php echo $mota; ?></textarea></td>
            </tr>

            <tr>
                <td>Hình hiện tại</td>
                <td><img src="../img/<?php echo $hinhcu; ?>" width="80"></td>
            </tr>

            <tr>
                <td>Đổi hình</td>
                <td><input type="file" name="myfile"></td>
            </tr>

            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="nut" value="Cập nhật">
                </td>
            </tr>
        </table>
    </form>

    <?php
    // XỬ LÝ UPDATE
    if (isset($_POST['nut'])) {

        $ten = $_POST['txtten'];
        $gia = $_POST['txtgia'];
        $giamgia = $_POST['txtgiamgia'];
        $mota = trim($_POST['txtmota']);
        $congty = $_POST['congty'];

        $name = $_FILES['myfile']['name'];
        $tmp_name = $_FILES['myfile']['tmp_name'];

        // nếu có upload hình mới
        if ($name != '') {
            move_uploaded_file($tmp_name, "../img/" . $name);

            $sql = "UPDATE sanpham SET 
                    tensp='$ten',
                    gia='$gia',
                    giamgia='$giamgia',
                    mota='$mota',
                    idcty='$congty',
                    hinh='$name'
                WHERE idsp=$id";
        } else {
            // không đổi hình
            $sql = "UPDATE sanpham SET 
                    tensp='$ten',
                    gia='$gia',
                    giamgia='$giamgia',
                    mota='$mota',
                    idcty='$congty'
                WHERE idsp=$id";
        }

        if ($p->themxoasua($sql) == 1) {
            echo "<script>alert('Cập nhật thành công'); window.location='?page=sua';</script>";
        } else {
            echo "<script>alert('Cập nhật thất bại');</script>";
        }
    }
    ?>
</body>

</html>