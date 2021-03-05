<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Browser</title>
</head>
<style>
        * {
            font-family: sans-serif;
        }
    </style>
<body>
<?php
    $msg = '';
    if (isset($_POST['login']) 
        && !empty($_POST['username']) 
        && !empty($_POST['password'])
    ) { 
        if ($_POST['username'] == 'admin' && 
            $_POST['password'] == 'admin'
        ) {
          $_SESSION['logged_in'] = true;
          $_SESSION['timeout'] = time();
          $_SESSION['username'] = 'admin';
        } else {
            $msg = 'Wrong username or password';
        }
      }   
  ?>     

  <?php if($_SESSION['logged_in'] != true): ?>   
  <div class="container">
    <div class="row align-items-center">
      <div>
        <h1>Please enter your credentials</h1>
        </br>
        <?php echo $msg ?>
        <form action="./index.php" method="POST">
              <div>
            <input type="text" name="username" class="form-control" placeholder="admin" required>
              </div>
              </br>
              <div>
            <input type="password" name="password" class="form-control" placeholder="admin" required>
              </div>
              </br>
              <button type="submit" name="login">Submit</button>
        </form>
      </div>
      
    </div>
  </div>
  <?php endif; ?>
  <div>
        <?php if($_SESSION['logged_in'] == true): ?>
    

</body>
</html>