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
// login logic
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

<?php 
  // logout logic

  if(isset($_GET['action']) and $_GET['action'] == 'logout'){
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['logged in']);
    
  }     

?>

<?php
// directory creation logic
if(isset($_GET["create dir"])){
  if($_GET["create dir"] != ""){
    $dir_to_create = './' . $_GET["path"] . $_GET["create_dir"];
    if(!is_dir($dir_to_create)) mkdir($dir_to_create, 0777, true);
  }
  $url = preg_replace("/(&?|\??)create_dir=(.+)?/", "", $_SERVER["REQUEST_URI"]);
  header('Location: ' . urldecode($url));
}
?>

<?php
    // directory deletion logic
    if(isset($_POST['delete'])){
      $objToDelete = './' . $_GET["path"] . $_POST['delete']; 
      $objToDeleteEscaped = str_replace("&nbsp;", " ", htmlentities($objToDelete, null, 'utf-8'));
      if(is_file($objToDeleteEscaped)){
          if (file_exists($objToDeleteEscaped)) {
              unlink($objToDeleteEscaped);
          }
      }
  }
?>

<?php
    // file download logic
    if(isset($_POST['download'])){
      print('Path to download: ' . './' . $_GET["path"] . $_POST['download']);
      $file='./' . $_GET["path"] . $_POST['download'];
      $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, null, 'utf-8'));

      header('Content-Description: File Transfer');
      header('Content-Type: application/pdf');
      header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-Length: ' . filesize($fileToDownloadEscaped));

      // flush();
      readfile($fileToDownloadEscaped);
      exit;
  }
  ?>

  <?php
      // file upload logic
      if(isset($_FILES['fileToUpload'])){
        $errors= array();
        $file_name = $_FILES['fileToUpload']['name'];
        $file_size = $_FILES['fileToUpload']['size'];
        $file_tmp = $_FILES['fileToUpload']['tmp_name'];
        $file_type = $_FILES['fileToUpload']['type'];
        $file_ext = strtolower(end(explode('.', $_FILES['fileToUpload']['name'])));
        
        $extensions= array("jpeg","jpg","png","pdf");
        
        if(in_array($file_ext , $extensions) === false){
           $errors[] = "extension not allowed, please choose a JPEG, PNG or PDF file.";
        }
        
        if($file_size > 2097152) {
           $errors[] = 'File size must be below 2 MB';
        }
        
        if(empty($errors)==true) {
           move_uploaded_file($file_tmp, './' . $_GET["path"] . $file_name);
           echo "Success";
        }else{
            print_r($_FILES);
            print('<br>');
            print_r($errors);
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
            
            print('<table><th>Type</th><th>Name</th><th>Actions</th>');
            foreach ($files_and_dirs as $fnd){
                if ($fnd != ".." and $fnd != ".") {
                    print('<tr>');
                    // ./.git/logs
                    print('<td>' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
                    print('<td>' . (is_dir($path . $fnd) 
                                ? '<a href="' . (isset($_GET['path']) 
                                        ? $_SERVER['REQUEST_URI'] . $fnd . '/' 
                                        : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                                : $fnd)
                        . '</td>');
                    print('<td>'
                        . (is_dir($path . $fnd) 
                            ? ''
                            : '<form style="display: inline-block" action="" method="post">
                                <input type="hidden" name="delete" value=' . str_replace(' ', '&nbsp;', $fnd) . '>
                                <input type="submit" value="Delete">
                               </form>
                               <form style="display: inline-block" action="" method="post">
                                <input type="hidden" name="download" value=' . str_replace(' ', '&nbsp;', $fnd) . '>
                                <input type="submit" value="Download">
                               </form>'
                        ) 
                        . "</form></td>");
                    print('</tr>');
                }
            }
            print("</table>");
        ?>
  <br><br>


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
        <form action="/php-file-browser" method="POST">
                <input type="hidden" name="path" value="<?php print($_GET['path']) ?>" /> 
                <input placeholder="Name of new folder" type="text" id="create_dir" name="create_dir">
                <button type="submit">Submit</button>
            </form>
        <div><br>
        <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="fileToUpload" id="img" style="display:none;"/>
                <button style="display: block; width: 20%" type="button">
                    <label for="img">Choose file</label>
                </button>
                <button style="display: block; width: 20%" type="submit">Upload</button>
            </form>
            <br>
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