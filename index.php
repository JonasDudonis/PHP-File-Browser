<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Browser</title>
    <link rel="stylesheet" href="./custom.css">
</head>
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



  <?php if($_SESSION['logged_in'] == true): ?>

    <?php 
            $path = './' . $_GET["path"];
            $files_and_dirs = scandir($path);
            
            print('<h4>You are currently here: ' . str_replace('?path=','',$_SERVER['REQUEST_URI']) . '</h4>');
            print('<table><th>Type</th><th>Name</th><th>Actions</th>');
            foreach ($files_and_dirs as $fnd){
                if ($fnd != ".." and $fnd != ".") {
                    print('<tr>');
                    print('<td>' . (is_dir($path . $fnd) ? "Folder" : "File") . '</td>');
                    print('<td>' . (is_dir($path . $fnd) 
                                ? '<a href="' . (isset($_GET['path']) 
                                        ? $_SERVER['REQUEST_URI'] . $fnd . '/' 
                                        : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                                : $fnd) 
                        . '</td>');
                    print('<td></td>');
                    print('</tr>');
                }
            }
            print("</table>");
        ?>


  <div class="container-fluid">
    <div class="row">
        <div><br>
            <button type="button" class="btn btn-outline-primary btn-md" onclick="goBack()">Back</button>
            <script>
            function goBack() {
              window.history.back();
            }
            </script>
        </div><br>    
        <div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">    
            <input type="name" name="name" class="form-control" id="name" placeholder="Name of new folder">
        </div>
        <div>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
        <div><br>
        </form>
        <div>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <div><input class="form-control" type="file" id="formFile" name="image"></div>
            <div><button type="submit" name="submit" class="btn btn-primary">Upload</button></div>
          </form>
        </div>
        <br>
    </div>
  </div>

<div class="container-fluid">
  <div class="row">
    <div">Click here to <a href = "index.php?action=logout">Logout</a></div>
  </div>
</div>
<?php endif; ?>
</body>
</html>