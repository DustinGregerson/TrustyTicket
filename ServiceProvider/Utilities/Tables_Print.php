<?php
//Author Dustin Gregerson
include_once("ServiceProvider/Class_Lib/DB_Access.php");
interface printHTML{
    //...$remove
    //removed fields must be in stored as array([0]=>array ([0]=>fieldName [1]=>fieldName...))
    //put simply create a array and push the field names into it you want to remove and pass it to the function.
    //format:
    //$remove[0]="id";
    //$remove[1]="name";
    //and so on
    //then pass

    //Returns a simple form with the action set to the url.
    public function startForm($multiPartForm,$url,$getOrPost,$apiCall);

    public function endForm($buttonLabel);

    public function getTableInsertInputs($tableName,...$remove);

    public function getTableUpdateInputs($tableName,$id,...$remove);
}
class Tables_Print implements printHTML{

    
    private $db;
    private $conn;

    private $phpUpdateHeader=[];
    private $form;
    private $formType;
    private $rename=[];

    private $hidden=[];
    function __construct() {
        $this->db=new DB_Access();
        $this->db->setConnection();
        $this->conn=$this->db->getConnection();
    }

    function startForm($multiPartForm,$url,$getOrPost,$apiCall){
        $this->form="";
        $this->formType=$apiCall;
        if($multiPartForm){
            $this->form=$this->form.'<form id="target" method='.$getOrPost.' action="'.$url.'" enctype="multipart/form-data">';
            $this->form=$this->form.'<input type="hidden" name="api_function_call" value='.$apiCall.'>';
        }
        else{
            $this->form=$this->form.'<form id="target" method='.$getOrPost.' action="'.$url.'">';
            $this->form=$this->form.'<input type="hidden" name="api_function_call" value='.$apiCall.'>';
        }
    }
    
     function getTableInsertInputs($tableName, ...$remove){
        if(!empty($remove)){
            $remove=$remove[0];
        }
        
        $sampleRecord=$this->db->getColumns($tableName);
        foreach($sampleRecord as $record){
            if(!empty($remove)&&in_array($record['Field'],$remove)){continue;}
            
            if($this->ishiddenInput($record)){

            }
            else{
                $this->GenerateInput($record);
            }
        }

    }
    public function getTableUpdateInputs($tableName, $id,...$remove){
        
        if(!empty($remove)){
            $remove=$remove[0];
        }

        

        $sampleRecord=$this->db->getColumns($tableName);

        foreach($sampleRecord as $record){
            if(in_array($record,$remove)){
                continue;
            }
            array_push($this->phpUpdateHeader,"$".$record['Field'].'=$record["'.$record['Field'].'"];');
        }
        
        foreach($sampleRecord as $record){
            if(!empty($remove)&&in_array($record['Field'],$remove)){continue;}
            
            if($this->ishiddenInput($record)){

            }
            else{
                $this->GenerateInput($record);
            }
            
        }
    }
    private function GenerateInput($record){
        if(str_contains($record['Type'],"varchar")){
            $this->inputTypeVarchar($record);
        }
        elseif(str_contains($record['Type'],"datetime")){
            $this->inputTypeDateTime($record);
        }
        elseif(str_contains($record['Type'],"blob")){
            $this->inputTypeblob($record);
        }
        elseif(str_contains($record['Type'],"decimal")){
            $this->inputTypeDecimal($record);
        }
        elseif(str_contains($record['Type'],"tinyint")){
            $this->inputTypeTinyInt($record);
        }
        elseif(str_contains($record['Type'],"int")){
            $this->inputTypeInt($record);
        }
        else{
            $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
            $this->form=$this->form.'<input type="text" min=1 step="any" name='.$record["Field"].'>';
        }
    }
    private function inputTypeInt($record){
        $pattern="/(\d+)/";
        preg_match($pattern,$record["Type"],$match);
        if($this->formType=="insert"){
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="number" min=1 step="any" max="'.$match[0].'" name="'.$record["Field"].'">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="number" min=1 step="any" max="'.$match[0].'" name="'.$record["Field"].'">';
            }
        }
        else{
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="number" min=1 step="any" max="'.$match[0].'" name="'.$record["Field"].'" value="<?php echo($'.$record["Field"].');?>">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="number" min=1 step="any" max="'.$match[0].'" name="'.$record["Field"].'" value="<?php echo($'.$record["Field"].');?>">';
            }
        }
    }
    private function inputTypeTinyInt($record){
        if($this->formType=="insert"){
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<div>'.$this->rename[$record["Field"]];
            }
            else{
                $this->form=$this->form.'<div>'.$record["Field"];
            }
                
                $this->form=$this->form.'<label for="'.$record["Field"].'"> yes </label>
                ';
                $this->form=$this->form.'<input id="'.$record["Field"].'" type="radio" name="'.$record["Field"].'" value="1">';
                $this->form=$this->form.'<label for="'.$record["Field"].'"> no </label>';
                $this->form=$this->form.'<input id="'.$record["Field"].'" checked type="radio" name="'.$record["Field"].'" value="0">';
                $this->form=$this->form.'</div>';
            }
        else{
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<div>'.$this->rename[$record["Field"]];
            }
            else{
                $this->form=$this->form.'<div>'.$record["Field"];
            }
                $this->form=$this->form.'<?php if($'.$record['Field'].'):?>';
                $this->form=$this->form.'<label for="'.$record["Field"].'"> yes </label>';
                $this->form=$this->form.'<input id="'.$record["Field"].'" checked type="radio" name="'.$record["Field"].'" value="1">';
                $this->form=$this->form.'<label for="'.$record["Field"].'"> no </label>';
                $this->form=$this->form.'<input id="'.$record["Field"].'"  type="radio" name="'.$record["Field"].'" value="0">';
                $this->form=$this->form.'</div>';
                $this->form=$this->form.'<?php else:?>';
                $this->form=$this->form.'<label for="'.$record["Field"].'"> yes </label>';
                $this->form=$this->form.'<input id="'.$record["Field"].'" type="radio" name="'.$record["Field"].'" value="1">';
                $this->form=$this->form.'<label for="'.$record["Field"].'"> no </label>';
                $this->form=$this->form.'<input id="'.$record["Field"].'" checked type="radio" name="'.$record["Field"].'" value="0">';
                $this->form=$this->form.'</div>';
                $this->form=$this->form.'<?php endif;?>';
            }
    }
    private function inputTypeDecimal($record){
        $pattern="/(\d+),(\d+)/";
        preg_match($pattern,$record["Type"],$match);
        $values=explode(",",$match[0]);
        $high=$values[0];
        $low=$values[0];
        $highNumber=9;
        $lowNumber=1;
        $place=1;
        for($i=1;$i<$high;$i++){
            $place=$place*10;
            $highNumber=$highNumber+$place*9;
        }
        for($i=2;$i<$low;$i++){
            $lowNumber=$lowNumber/10;
        }
        
        if($this->formType=="insert"){
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="number" max="'.$highNumber.'" min="'.$lowNumber.'"step="'.$lowNumber.'"name="'.$record["Field"].'">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="number" max="'.$highNumber.'" min="'.$lowNumber.'"step="'.$lowNumber.'"name="'.$record["Field"].'">';
            }
        }
        else{
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="number" max="'.$highNumber.'" min="'.$lowNumber.'"step="'.$lowNumber.'"name="'.$record["Field"].'"value="<?php echo($'.$record["Field"].')?>">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="number" max="'.$highNumber.'" min="'.$lowNumber.'"step="'.$lowNumber.'"name="'.$record["Field"].'"value="<?php echo($'.$record["Field"].')?>">';
            }
        }

    }
    private function inputTypeblob($record){
        if($this->formType=="insert"){
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="file" accept="image/*" name="'.$record["Field"].'">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="file" accept="image/*" name="'.$record["Field"].'">';
            }
        }
    }
    private function inputTypeDateTime($record){
        if($this->formType=="insert"){
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="datetime-local" name="'.$record["Field"].'">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="datetime-local" name="'.$record["Field"].'">';
            }
        }
        else{
            if(key_exists($record["Field"],$this->rename)){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="datetime-local" name="'.$record["Field"].'" value="<?php echo($'.$record["Field"].')?>">';
            }
            else{
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="datetime-local" name="'.$record["Field"].'" value="<?php echo($'.$record["Field"].')?>">';
            }
        }

    }
    private function inputTypeVarchar($record){
        $pattern="/(\d+)/";
        preg_match($pattern,$record["Type"],$match);
        if($this->formType=="insert"){
            if(key_exists($record["Field"],$this->rename)){
                if($match[0]<=100){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="text" name="'.$record["Field"].'">';
                }
                else{
                    $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                    $this->form=$this->form.'<textarea name="'.$record["Field"].'"></textarea>';
                }
            }
            else{
                if($match[0]<=100){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="text" name="'.$record["Field"].'">';
                }
                else{
                    $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                    $this->form=$this->form.'<textarea name="'.$record["Field"].'"></textarea>';
                }
            }
        }
        else{
            if(key_exists($record["Field"],$this->rename)){
                if($match[0]<=100){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                $this->form=$this->form.'<input type="text" name="'.$record["Field"].'" value="<?php echo($'.$record["Field"].')?>">';
                }
                else{
                    $this->form=$this->form.'<label for="'.$record["Field"].'">'.$this->rename[$record["Field"]].'</label>';
                    $this->form=$this->form.'<textarea name="'.$record["Field"].'"><?php echo($'.$record["Field"].')?></textarea>';
                }
            }
            else{
                if($match[0]<=100){
                $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                $this->form=$this->form.'<input type="text" name="'.$record["Field"].'" value="<?php echo($'.$record["Field"].')?>">';
                }
                else{
                    $this->form=$this->form.'<label for="'.$record["Field"].'">'.$record["Field"].'</label>';
                    $this->form=$this->form.'<textarea name="'.$record["Field"].'"><?php echo($'.$record["Field"].')?></textarea>';
                }
            }
        }
    }
    function hiddenField($field,$toValue){
        $this->hidden[$field]=$toValue;
    }
    function hiddenFieldUpdate($field){
        $this->hidden[$field]='<?php echo($'.$field.')?>';
    }
    private function ishiddenInput($record){
        if(key_exists($record["Field"],$this->hidden)){
            $this->form=$this->form.'<input hidden type="text" name='.$record["Field"].' value="'.$this->hidden[$record["Field"]].'">';
            return true;
        }
        else{
            return false;
        }
    }
    function renameField($field,$toValue){
        $this->rename[$field]=$toValue;
        
    }
    private function renameFieldPrint(){
            print_r($this->rename);
    }
    function endForm($buttonLabel){
        $this->form=$this->form.'<button type="submit">'.$buttonLabel.'</button></form>';
    }
    function echoForm(){
        echo($this->form);
    }
    function printForm(){
        //echo(htmlspecialchars($this->phpUpdateHeader));
        echo(htmlspecialchars($this->form));
    }
    function writeFormToFile($fileName){
        
        $split=str_split($this->form);
        $file=fopen($fileName,'w');
        $length=count($split);
        $tabcount=0;
        fwrite($file,"<?php");
        foreach($this->phpUpdateHeader as $var){
            fwrite($file,$var."\r\n");
        }
        fwrite($file,"?>");
        fwrite($file,"\r\n");
        for($i=0;$i<$length;$i++){
            if($split[$i]=='<'){
                if($split[$i+1]=='/'){
                $tab=str_repeat("\t",$tabcount);
                fwrite($file,$split[$i]);
                $tabcount--;
                }
                else if($split[$i+1]=='?'){
                    fwrite($file,$split[$i]);
                $tabcount--;
                }
                else{
                $tabcount++;
                $tab=str_repeat("\t",$tabcount);
                fwrite($file,"\r\n");
                fwrite($file,$tab.$split[$i]);
                }
            }
            else{
                fwrite($file,$split[$i]);
            }
        }
        fclose($file);
    }

}
?>