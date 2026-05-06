<?php
class khachhang
{
    private function connect()
    {
        $con = mysql_connect("localhost", "usertmdt", "passtmdt");
        if (!$con) {
            echo 'Không kết nối được csdl';
            exit();
        } else {
            mysql_select_db('tmdt_db');
            mysql_query("SET NAMES utf8");
            mysql_query("SET CHARACTER SET utf8");
            return $con;
        }
    }

    public function xemdskhachhang($sql)
    {
        $link = $this->connect();
        $ketqua = mysql_query($sql, $link);
        $i = mysql_num_rows($ketqua);
        if ($i > 0) {
            echo '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
            echo '<tr>';
            echo '<th>ID Khách Hàng</th>';
            echo '<th>Tên Khách Hàng</th>';
            echo '<th>Email</th>';
            echo '<th>Điện Thoại</th>';
            echo '<th>Địa Chỉ</th>';
            echo '<th>Hành Động</th>';
            echo '</tr>';

            while ($row = mysql_fetch_array($ketqua)) {
                $idkh = $row['idkh'];
                $tenkh = $row['tenkh'];
                $email = $row['email'];
                $dienthoai = $row['dienthoai'];
                $diachi = $row['diachi'];

                echo '<tr>';
                echo '<td>' . $idkh . '</td>';
                echo '<td>' . $tenkh . '</td>';
                echo '<td>' . $email . '</td>';
                echo '<td>' . $dienthoai . '</td>';
                echo '<td>' . $diachi . '</td>';
                echo '<td>
					<a href="suakhachhang.php?id=' . $idkh . '">Sửa</a> | 
					<a href="xoakhachhang.php?id=' . $idkh . '">Xóa</a>
				</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'Không có dữ liệu khách hàng.';
        }
    }

    public function themkhachhang($sql)
    {
        $link = $this->connect();
        if (mysql_query($sql, $link)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function xoakhachhang($sql)
    {
        $link = $this->connect();
        if (mysql_query($sql, $link)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function suakhachhang($sql)
    {
        $link = $this->connect();
        if (mysql_query($sql, $link)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function timkhachhang($sql)
    {
        $link = $this->connect();
        $ketqua = mysql_query($sql, $link);
        $i = mysql_num_rows($ketqua);
        if ($i > 0) {
            return mysql_fetch_array($ketqua);
        } else {
            return 0;
        }
    }

    public function laythuoctinh($sql)
    {
        $link = $this->connect();
        $ketqua = mysql_query($sql, $link);
        $i = mysql_num_rows($ketqua);
        if ($i > 0) {
            while ($row = mysql_fetch_array($ketqua)) {
                return $row;
            }
        } else {
            return 0;
        }
    }
    public function chitietsanpham($idsp)
    {
        $link = $this->connect();
        $ketqua = mysql_query("SELECT * FROM sanpham WHERE idsp='$idsp' limit 1", $link);
        if ($ketqua && mysql_num_rows($ketqua) > 0) {
            return mysql_fetch_array($ketqua);
        } else {
            return 0;
        }
    }
}
?>