<?php 
include("includes/header.php");

$profile_id = $user['username'];
$imgSrc = "";
$result_path = "";
$msg = "";

/***********************************************************
    0 - Remove The Temp image if it exists
***********************************************************/
$temppath = 'assets/images/profile_pics/' . $profile_id . '_temp.jpeg';
if (!isset($_POST['x']) && !file_exists($temppath)){
    // Delete users temp image safely
    unlink($temppath);
} 

if (isset($_FILES['image']['name'])) {
/***********************************************************
    1 - Upload Original Image To Server
***********************************************************/ 
    // Get Name, Size, Temp Location       
    $imageName = $_FILES['image']['name'];
    $imageSize = $_FILES['image']['size'];
    $imageTempName = $_FILES['image']['tmp_name'];
    // Get File Extension
    $imageType = explode('/', $_FILES['image']['type']);
    $type = $imageType[1]; // file type

    // Set Upload directory and sanitize file name
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/studentsasylum/assets/images/profile_pics/';
    $fileTempName = $profile_id . '_original.' . md5(time()) . 'n' . preg_replace("/[^a-zA-Z0-9.]/", "", $type); // the temp file name, sanitizing input

    $fullPath = $uploadDir . $fileTempName; // the temp file path
    $fileName = $profile_id . '_temp.jpeg'; // for the final resized image
    $fullPath2 = $uploadDir . $fileName; // for the final resized image

    // Move the file to correct location
    if (!move_uploaded_file($imageTempName, $fullPath)) {
        die('File did not upload');
    } else {
        chmod($fullPath, 0777);
        $imgSrc = "assets/images/profile_pics/" . $fileName; // the image to display in crop area
        $msg = "Upload Complete!";  // message to page
        $src = $fileName; // the file name to post from cropping form to the resize
    }

/***********************************************************
    2 - Resize The Image To Fit In Cropping Area
***********************************************************/ 
    $originalSize = getimagesize($fullPath);
    $originalWidth = $originalSize[0];
    $originalHeight = $originalSize[1];
    // Specify The new size
    $mainWidth = 500; // set the width of the image
    $mainHeight = $originalHeight / ($originalWidth / $mainWidth); // this sets the height in ratio

    // Create new image using correct PHP function
    switch ($_FILES['image']['type']) {
        case 'image/gif':
            $src2 = imagecreatefromgif($fullPath);
            break;
        case 'image/jpeg':
        case 'image/pjpeg':
            $src2 = imagecreatefromjpeg($fullPath);
            break;
        case 'image/png':
            $src2 = imagecreatefrompng($fullPath);
            break;
        default:
            $msg .= "There was an error uploading the file. Please upload a .jpg, .gif or .png file. <br />";
            break;
    }

    if ($src2 !== false) {
        $main = imagecreatetruecolor($mainWidth, $mainHeight);
        imagecopyresampled($main, $src2, 0, 0, 0, 0, $mainWidth, $mainHeight, $originalWidth, $originalHeight);
        imagejpeg($main, $fullPath2, 90);
        chmod($fullPath2, 0777);
        imagedestroy($src2);
        imagedestroy($main);
        unlink($fullPath); // delete the original upload
    }
}// ADD Image

/***********************************************************
    3- Cropping & Converting The Image To Jpg
***********************************************************/
if (isset($_POST['x'])) {
    // the file type posted
    $type = $_POST['type']; 
    // the image src
    $src = 'assets/images/profile_pics/' . preg_replace("/[^a-zA-Z0-9.]/", "", $_POST['src']); // Sanitizing input
    $finalName = $profile_id . md5(time()); 

    // the target dimensions 150x150
    $targW = $targH = 150;
    // quality of the output
    $jpegQuality = 90;

    // Depending on image type, create a cropped copy of the image
    if (in_array(strtolower($type), ['jpg', 'jpeg', 'png', 'gif'])) {
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                $imgR = imagecreatefromjpeg($src);
                break;
            case 'png':
                $imgR = imagecreatefrompng($src);
                break;
            case 'gif':
                $imgR = imagecreatefromgif($src);
                break;
        }

        if ($imgR) {
            $dstR = imagecreatetruecolor($targW, $targH);
            imagecopyresampled($dstR, $imgR, 0, 0, $_POST['x'], $_POST['y'], $targW, $targH, $_POST['w'], $_POST['h']);
            imagejpeg($dstR, "assets/images/profile_pics/" . $finalName . "n.jpeg", $jpegQuality);
            imagedestroy($imgR);
            imagedestroy($dstR);
            unlink($src); // delete the original upload

            $resultPath = "assets/images/profile_pics/" . $finalName . "n.jpeg";
            $insertPicQuery = mysqli_query($con, "UPDATE users SET profile_pic='$resultPath' WHERE username='$userLoggedIn'");
            header("Location: " . $userLoggedIn);
        }
    }
}
?>
<div id="Overlay" style="width:100%; height:100%; border:0px #990000 solid; position:absolute; top:0px; left:0px; z-index:2000; display:none;"></div>
<div class="main_column column">
    <div id="formExample">
        <p><b> <?= $msg ?> </b></p>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            Upload something<br /><br />
            <input type="file" id="image" name="image" style="width:200px; height:30px; " /><br /><br />
            <input type="submit" value="Submit" style="width:85px; height:25px;" />
        </form><br /><br />
    </div> <!-- Form-->  

    <?php if ($imgSrc) { //if an image has been uploaded display cropping area ?>
        <script>
            $('#Overlay').show();
            $('#formExample').hide();
        </script>
        <div id="CroppingContainer" style="width:800px; max-height:600px; background-color:#FFF; margin-left: -200px; position:relative; overflow:hidden; border:2px #666 solid; z-index:2001; padding-bottom:0px;">  
            <div id="CroppingArea" style="width:500px; max-height:400px; position:relative; overflow:hidden; margin:40px 0px 40px 40px; border:2px #666 solid; float:left;">    
                <img src="<?= $imgSrc ?>" border="0" id="jcrop_target" style="border:0px #990000 solid; position:relative; margin:0px 0px 0px 0px; padding:0px; " />
            </div>  

            <div id="InfoArea" style="width:180px; height:150px; position:relative; overflow:hidden; margin:40px 0px 0px 40px; border:0px #666 solid; float:left;">   
                <p style="margin:0px; padding:0px; color:#444; font-size:18px;">          
                    <b>Crop Profile Image</b><br /><br />
                    <span style="font-size:14px;">
                        Crop / resize your uploaded profile image. <br />
                        Once you are happy with your profile image then please click save.
                    </span>
                </p>
            </div>  

            <br />

            <div id="CropImageForm" style="width:100px; height:30px; float:left; margin:10px 0px 0px 40px;" >  
                <form action="upload.php" method="post" onsubmit="return checkCoords();">
                    <input type="hidden" id="x" name="x" />
                    <input type="hidden" id="y" name="y" />
                    <input type="hidden" id="w" name="w" />
                    <input type="hidden" id="h" name="h" />
                    <input type="hidden" value="jpeg" name="type" /> <?php // $type ?> 
                    <input type="hidden" value="<?= $src ?>" name="src" />
                    <input type="submit" value="Save" style="width:100px; height:30px;"   />
                </form>
            </div>

            <div id="CropImageForm2" style="width:100px; height:30px; float:left; margin:10px 0px 0px 40px;" >  
                <form action="upload.php" method="post" onsubmit="return cancelCrop();">
                    <input type="submit" value="Cancel Crop" style="width:100px; height:30px;"   />
                </form>
            </div>            
                
        </div><!-- CroppingContainer -->
    <?php } ?>
</div>
 
 
<?php if ($resultPath) { ?>
    <img src="<?= $resultPath ?>" style="position:relative; margin:10px auto; width:150px; height:150px;" />
<?php } ?>
<br /><br />
