<?php 
include ("../classlogin/clslogin.php");
$p = new login();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đăng nhập khách hàng</title>
    
</head>

<body>
    <div class="login-container">
        <h2>Đăng nhập khách hàng</h2>
        <form id="form1" name="form1" method="post">
            <p>
                <label for="txtemail">Email: </label>
                <input type="text" name="txtemail" id="txtemail" required>
            </p>
            <p>
                <label for="txtpass">Mật khẩu:</label>
                <input type="password" name="txtpass" id="txtpass" required>
            </p>
            <p>
                <input type="submit" name="nut" id="nut" value="Đăng nhập">
            </p>
            <?php 
            if (isset($_POST['nut'])) {
                $email = trim($_REQUEST['txtemail']);
                $pass = trim($_REQUEST['txtpass']);
                
                if($email != '' && $pass != '') {
                    if($p->loginkhachhang($email, $pass) == 0) {
                        echo '<div class="error">Sai email hoặc mật khẩu. Vui lòng thử lại!</div>';
                    }
                } else {
                    echo '<div class="error">Vui lòng nhập đầy đủ thông tin!</div>';
                }
            }
            ?>
            <div class="links">
                <p><a href="../index.php">Quay lại trang chủ</a></p>
                <p>Chưa có tài khoản? <a href="#">Đăng ký tại đây</a></p>
            </div>
        </form>
    </div>
</body>
</html>