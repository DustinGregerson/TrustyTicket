<?php

function ConvertToImgString($data){
    try{
    $base64String=base64_encode($data);
    return 'data:image/png;base64 ,'.$base64String;
    }
    catch(Exception $e){
        return "";
    }
}
function ConvertToBlob(){
    if(isset($_FILES["image"])){
       $file=$_FILES["image"];
       $tempPath=$file["tmp_name"];
       if(!empty($tempPath)){
       $binaryString=file_get_contents($tempPath);
       }
       else{
        $binaryString=0;
       }
       return $binaryString;
    }
    else{
        return "unable to load picture";
    }

}

?>