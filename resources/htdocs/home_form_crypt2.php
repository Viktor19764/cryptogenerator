<?php
$post_message=NULL;
$post_shifr=NULL;
$lang_mes=2011;
$site_help="";
$decr="";
$form_shifr="";
$number_m="";
$small_key="";
$input="";
$output="";
$stan=NULL;
$input_name="";
$stan_for_file_input="";
$last_shifr_button="";
$input_force="";
$last_out="";
if(isset($_POST["message"])) {$post_message=$_POST["message"]; $post_message=substr($post_message,0,$lang_mes)." "; }//обмеження даних з форми
if(isset($_POST["shifr"])) $post_shifr=htmlspecialchars($_POST["shifr"]);
//echo strlen($_POST["message"]);
if(strlen($_POST["message"])>$lang_mes+6) {$site_help="<h2>Повідомлення обрізано до розміру 2 кБ!<br>Опрацювати?</h2>"; 
$input="<input type=\"submit\" value=\"Опрацювати\">";  $input_name="message";
}
else {
//$post_shifr=htmlspecialchars($_POST["shifr"]);
//var_dump($_POST);
//echo "<br>";
include 'home_xor2.php';}

?>

<!DOCTYPE HTML>
<?php
$cookie_name = "pas";
if((isset($_POST['pas']))&&$_POST['pas']=='123'&&$_POST['pas']!="") {$cookie_value = '123';
setcookie($cookie_name, $cookie_value, time() + (3600), "/");} // 86400 = 1 day
if(isset($_POST['out'])) {$cookie_value = ' ';
setcookie($cookie_name, $cookie_value, time() - (1), "/");}
//print_r($_COOKIE);
?>
<html>
 <head>
  <meta charset="utf-8">
  <title>Тег FORM</title>
<script>
   function copyf() { var copyText = document.getElementById("Input");
  copyText.select();
  document.execCommand("Copy");
  

   }
  </script>
 </head>
 <body>

<div style="float: left;">
<form action=home_form_crypt2.php method=POST>
<input type=password name=pas>
<input type=submit value="Логін"></form><?php 
if(!isset($_COOKIE[$cookie_name])) {echo "Укажіть  пароль"; 
exit; 
}?></div>
<div style="float: left;  position: relative;">
<form action=home_form_crypt2.php method=POST><input type=hidden name=out value=" ">
<input type=submit value="Вийти">
</form></div>




<div style="position: fixed; top: 0; right: 0;"><?php echo $number_m%3+1;  ?></div>
<div style="width: 100%; float: left; text-align: center; padding: 0px; margin: 0px;">
<?php  echo $site_help; echo $small_key;  ?></div>
<div style="width: 50%; float: left; ">
 <form action="home_form_crypt2.php" method="post"><?php  echo $input_force; ?></form>
 <form action="home_form_crypt2.php" method="post" style="width: 100%;" >
       <textarea name="<?php echo $input_name; ?>" maxlength="7500" cols="100" rows="20" style="width: 100%;">
<?php echo substr($post_message,0,strlen($post_message)-1);       ?>
</textarea>
 <?php  echo $input; //перша форма
?>
 </form>
</div>

<div style="width: 49%; float: right;">
<form action="home_form_crypt2.php" method="post" style="width: 100%;">
  
  
  <?php  echo $output;  ?>
   <textarea name="shifr" maxlength="7500" cols="100" rows="20" style="width: 100%;" id="Input" type="file">


<?php
if (strlen($form_shifr)>=1) echo $form_shifr;
if(isset($_POST["last"])) {
$last=fopen("home_x","rb");
$last_out=fread($last,filesize("home_x"));
fclose($last);
echo $last_out;
}
?>
</textarea>
 </form>
</div>
<?php if (($last_out!="")||(strlen($form_shifr)>2)&&(isset($_POST["message"]))) echo "<div style=\"float: right;\">
<form action=\"\">
    <input type=\"button\" value=\"Скопіювати\" onclick=\"copyf()\" >
  </form> 
</div>";    ?>
<?php   echo $last_shifr_button;      ?>
<div style="width: 100%; float: left;">
 <form action="home_form_crypt2.php" method="post" style="width: 100%;" >
  
  <p>Контроль<Br>
   <textarea name="kontrol" maxlength="7500" cols="100" rows="20" style="width: 100%;">
<?php //$decr=str_ireplace ( "<" , "" , $decr );
echo $decr;       ?>
</textarea></p>
  
 </form>
</div>

 </body>
</html>


