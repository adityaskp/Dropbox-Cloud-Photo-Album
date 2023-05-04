<!-- Student Name:Aditya Shivaji Patil UTA id-1001995431   -->
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<img id="image_show" src="images/tmp.jpg" width ="300" height="400"></img>
 <form  enctype="multipart/form-data" action="" method="post">
   Select an Image to upload:
   <input type="file" name="FILE" id="FILE">
   <input type="submit" value="Upload Image" name="submit">
 </form>


<?php
// put your generated access token here (should have No Expiration)
$auth_token = 'Enter your token here';

// set it to true to display debugging info
$debug = true;
function directoryList ( $path ) {
  $path='';
   global $auth_token, $debug;
   $args = array("path" => $path);
   //echo $args;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
   		    'Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/list_folder');
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   $array = json_decode(trim($result), TRUE);

   curl_close($ch);
   return $array;
}
function download ( $path, $target_path ) {
   global $auth_token, $debug;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
      		    'Content-Type:', 'Dropbox-API-Arg: {"path":"/'.$path.'"}'));
   curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/download');
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   file_put_contents($target_path,$result);
   curl_close($ch);
 }

 function upload ( $path ) {

    global $auth_token, $debug;
    $args = array("path" => $path, "mode" => "add");
    $fp = fopen($path, 'rb');
    $size = filesize($path);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
    		     'Content-Type: application/octet-stream',
 		     'Dropbox-API-Arg: {"path":"/'.$path.'", "mode":"add"}'));
    curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/upload');
    curl_setopt($ch, CURLOPT_INFILE, $fp);
    curl_setopt($ch, CURLOPT_INFILESIZE, $size);
    try {
      $result = curl_exec($ch);
    } catch (Exception $e) {
      echo 'Error: ', $e->getMessage(), "\n";
    }
    if ($debug)
  //     print_r($result);
    curl_close($ch);
    fclose($fp);
 }

function delete ( $path )
{

global $auth_token, $debug;
$args = array("path" => "/".$path);
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
        'Content-Type: application/json'));
curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/delete_v2');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
try {
 $result = curl_exec($ch);
} catch (Exception $e) {
 echo 'Error: ', $e->getMessage(), "\n";
}

curl_close($ch);

 }


if(isset($_POST['submit']))
{

  $pat=$_FILES["FILE"]["name"];
  $dir = "";
  $path = $dir .  basename($_FILES["FILE"]["name"]);
  move_uploaded_file($_FILES["FILE"]["tmp_name"], $path);
  upload($pat);

  echo "<br> **LIST OF FILES - To preview and download the image click the link**";
  $result = directoryList("");
  echo "<ol>";
  foreach ($result['entries'] as $x) {
     echo "<li>";
    $f1=$x['name'];
     echo "<a  href='album.php?preview=true&file=$f1'>$f1</a>";
     echo "<form action='album.php?delete=".$x['name']."' method='post'><input type='submit' value='delete' name='delete'></form> ";
     echo "</li>";


  }
  echo "</ol><br>";

}
elseif (isset($_POST['delete'])&& $_POST['delete'] == 'delete')
{
  
  $path = isset($_GET['delete'])?$_GET['delete']:'';
  delete($path);
  echo "<br> **LIST OF FILES - To preview and download the image click the link**";
  $result = directoryList("");
  echo "<ol>";
  foreach ($result['entries'] as $x)
  {
     echo "<li>";
    $f1=$x['name'];
     echo "<a  href='album.php?preview=true&file=$f1'>$f1</a>";
     echo "<form action='album.php?delete=".$x['name']."' method='post'><input type='submit' value='delete' name='delete'></form> ";
     echo "<br></li>";

  }
  echo "</ol><br>";
}
else
{
  echo "<br> **LIST OF FILES - To preview and download the image click the link**";
  $result = directoryList("");
  echo "<ol>";
  foreach ($result['entries'] as $x)
 {
     echo "<li>";
     $f1=$x['name'];
     echo "<a href='album.php?preview=true&file=$f1' >$f1</a>";
     echo "<form action='album.php?delete=".$x['name']."' method='post'><input type='submit' value='delete' name='delete'></form> ";
     echo "<br></li>";

  }
  echo "</ol><br>";
}

if(isset($_GET['preview'])){
  $file=$_GET['file'];
  download($file,"images/tmp.jpg");

}

?>

<script type="text/javascript">
function Preview(){
document.getElementById("image_show").width="300";
document.getElementById("image_show").height="400";

document.getElementById("image_show").src= "images/tmp.jpg";
}
</script>
</body>
</html>
