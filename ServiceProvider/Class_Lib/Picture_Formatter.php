<?php

function ConvertToImgString($data){
    $base64String=base64_encode($data);
    return 'data:image/png;base64 ,'.$base64String;
}
function ConvertToBlob(){
    if(isset($_FILES["image"])){
       $file=$_FILES["image"];
       $tempPath=$file["tmp_name"];
       $binaryString=file_get_contents($tempPath);
       return $binaryString;
    }

}

?>