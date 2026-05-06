<?php
if (session_id() == '')
    session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['user'])) {
    header('location:../login/login.php');
    exit();
}
if (!isset($p)) {
    include_once("../class/classtmdt.php");
    $p = new csdltmdt();
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Xóa sản phẩm</title>
    <style>
        table {
            border-collapse: collapse;
            width: 95%;
            margin: 10px auto
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left
        }

        img {
            max-width: 80px
        }
    </style>
</head>

<body>
    <?php
    // XỬ LÝ XÓA
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "DELETE FROM sanpham WHERE idsp = $id";
        if ($id > 0) {
            if ($p->themxoasua($sql) == 1) {
                echo "<script>alert('Xóa thành công'); window.location='?page=xoa';</script>";
            } else {
                echo "<script>alert('Xóa thất bại');</script>";
            }
        }
    }
    ?>

    <h3 align="center">DANH SÁCH SẢN PHẨM</h3>
    <div align="center" style="margin-bottom:20px;">
        <form method="GET">
            <select name="idcty" id="congty" onchange="this.form.submit();">
                <option value="0">-- Tất cả công ty --</option>
                <?php
                $link = mysql_connect("localhost", "usertmdt", "passtmdt");
                mysql_select_db("tmdt_db");
                mysql_query("SET NAMES UTF8");
                
                $result = mysql_query("SELECT idcty, tencty FROM congty ORDER BY tencty ASC", $link);
                while ($row = mysql_fetch_array($result)) {
                    $selected = (isset($_GET['idcty']) && $_GET['idcty'] == $row['idcty']) ? 'selected' : '';
                    echo '<option value="' . $row['idcty'] . '" ' . $selected . '>' . $row['tencty'] . '</option>';
                }
                ?>
            </select>
        </form>
    </div>


    <table border="1" width="800" align="center">
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Nhà sản xuất</th>
            <th>Hình</th>
            <th>Hành động</th>
        </tr>

        <?php
        // LẤY DANH SÁCH
        $link = mysql_connect("localhost", "usertmdt", "passtmdt");
        mysql_select_db("tmdt_db");
        mysql_query("SET NAMES UTF8");

        // Kiểm tra nếu có idcty được chọn
        if (isset($_GET['idcty']) && $_GET['idcty'] != '0') {
            $idcty = intval($_GET['idcty']);
            $sql = "SELECT sanpham.*, congty.tencty 
            FROM sanpham 
            INNER JOIN congty ON sanpham.idcty = congty.idcty
            WHERE sanpham.idcty = $idcty
            ORDER BY sanpham.tensp";
        } else {
            $sql = "SELECT sanpham.*
            FROM sanpham ";
        }

        $ketqua = mysql_query($sql, $link);

        while ($row = mysql_fetch_array($ketqua)) {
            $id = $row['idsp'];
            $ten = $row['tensp'];
            $gia = $row['gia'];
            $hinh = $row['hinh'];
            $congty = $row['tencty'];

            echo "
    <tr>
        <td>$id</td>
        <td>$ten</td>
        <td>$gia</td>
        <td>$congty</td>
        <td><img src='../img/$hinh' width='60'></td>
        <td>
            <a href='suasanpham.php?id=$id' class='btn-sua'>Sửa</a>
            <a href='?page=xoa&id=$id' 
               onclick=\"return confirm('Bạn có chắc muốn xóa?')\">
               Xóa
            </a>
        </td>
    </tr>
    ";
        }
        ?>
    </table>
</body>

</html>