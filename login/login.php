<?php 
include ("../classlogin/clslogin.php");
$p = new login();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Đăng nhập</title>
</head>

<body>
<form id="form1" name="form1" method="post">
  <p>
    <label for="txtuser">Nhập username: </label>
    <input type="text" name="txtuser" id="txtuser">
  </p>
  <p>
    <label for="txtpass">Nhập password:</label>
    <input type="password" name="txtpass" id="txtpass">
  </p>
  <p>
    <input type="submit" name="nut" id="nut" value="Đăng nhập">
  </p>
  <?php 
  if (isset($_POST['nut'])) {
    switch($_POST['nut'])
    {
      case 'Đăng nhập':
      {
        $user = trim($_REQUEST['txtuser']);
        $pass = trim($_REQUEST['txtpass']);
        if ($user != '' && $pass != '')
        {
          if ($p->mylogin($user, $pass) == 0)
          {
            echo 'Sai username hoặc password. Vui lòng nhập lại thông tin!';
          }
        }
        else
        {
          echo 'Vui lòng nhập đầy đủ thông tin!';
        }
        break;
      }  
    }
  }
  ?>
</form>
</body>
</html>