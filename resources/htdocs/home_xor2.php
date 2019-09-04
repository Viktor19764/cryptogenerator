<?php


function towsend ($mes)//розташування повідомлення у 2000-розмірноу блоці
{
$size_blok=1000*2+60;//розмір блоку у байтах
//випадковий рядок для доповнення до 1000 символів
$rand=hash ( "sha512", time(), TRUE );//випадкові символи, доповнюючі повідомлення до розіру блоку
$min_vidst=time()%11+5;//мінімальний відступ від початку
$size_mes=strlen($mes);
$parn=time()%2;
//
if ($parn==0)
    {
    $start=$min_vidst+time()%(($size_blok-$size_mes)/2);//випадкове початкове положення повідомлення у блоці
     }
else 
     {
      $start=$min_vidst+$size_blok-$size_mes-time()%(($size_blok-$size_mes)/2);
      }
if (strlen($rand)<($size_blok-$size_mes))
        {
        do
            {
             $rand.=hash ( "sha512", $rand, TRUE );
            }
        while (strlen($rand)<($size_blok-$size_mes));
        $rand.=hash ( "sha512", $rand, TRUE );
        $rand.=hash ( "sha512", $rand, TRUE );
       $rand.=hash ( "sha512", $rand, TRUE );
       $rand.=hash ( "sha512", $rand, TRUE );
        $rand.=hash ( "sha512", $rand, TRUE );
       $rand.=hash ( "sha512", $rand, TRUE );

        }
//echo "towsend";
////var_dump(strlen($rand));
//echo "start";
//var_dump($start);
//var_dump($size_mes);
$blok_mes=substr($rand,0, $start).$mes.substr($rand,$start+1-strlen($rand),$size_blok-$start-$size_mes+time()%503);
return $blok_mes;

}///////////////???????????
/*
$key_file=fopen("key","rb");
$key=fread($key_file, filesize("key"));
fclose($key_file);*/
///////////////////////////////////

///////////////////////////////////Шифрування з веб-форми
$mes=" ";///ініцалізація повідомлення
if(!empty($post_message ))
    {
       $mes=$post_message;
    }
///AES


/////////////////////////////////////Варіант шифрування з файлу
/*
$mes_chyst=fopen("test","rb");
$mes=fread($mes_chyst,filesize("test"));
fclose($mes_chyst);
*/
/////////////////////////////////
//додавання 0 байту перед англійських знаків
//echo "mes";
////var_dump($mes);
$mes_0=unpack('C*',$mes);
////var_dump($mes);
//unset ($mes);

//var_dump($mes_0);
//$mes=pack('C*',$mes[1]);
$count_mes=count($mes_0);
$mes2=b'';
for ($i=1; $i<=$count_mes; $i++)
{
$mes2.=pack('n*',$mes_0[$i]);
}

//$mes=implode($mes);
//unset($mes_o);
//echo "mes2";
//$mes2=unpack('C*',$mes2);

////var_dump($mes2);
/////////////////////////Упаковка у блок повідомлення
$blok_mes_0=towsend($mes2);
////////////////////////////Взяття ключа з 1 по 25 - залежно від розміру
$key_from="";


for ($i = 1; $i <= 25; $i++) {
    if(filesize($i)>=strlen($blok_mes_0))
       {  $key_from=$i;
          break; 
        }
}

//if(!empty($post_shifr ))  { $key_from="home_key";}
//else $key_from="home_key";

///////////Блок відкриття файлу ключа
$key_file=fopen($key_from,"rb");
if((filesize($key_from)-strlen($blok_mes_0)-16-5504-2-6)<0){echo "small key"; exit;}

//Зняття  двох байт з початку ключа для вставки їх у тіло блока повідомлення///////////////////////////////
$key_2bits=fread($key_file,2);
//fread($key_file,1);
//$side=unpack('C*',fread($key_file,1));//біт визначення сторони
//$send=unpack('C*',fread($key_file,1));//біт висилки
fread($key_file,5);
$stan=fread($key_file,1);//7-й байт
fread($key_file,filesize($key_from)-strlen($blok_mes_0)-16-5504-2-6);//зміщення вказівника з кінця файлу
$key=fread($key_file, strlen($blok_mes_0)+16);
$hash_mes_file=fread($key_file, 64);//гаш попереднього повідомлення
fclose($key_file);
//////////Установка режиму Передачі чи Прийому
$stan=unpack('C*',$stan);
$stan_for_site=$stan[1];
//Перемикання форм на сайті
if($stan_for_site==119) {$input="<input type=\"submit\" value=\"Розшифрувати\">";  $input_name="shifr"; $site_help="<h2>Очікуйте на повідомлення<br>Після отримання вставте у ліве поле</h2>";
}
if($stan_for_site==0) {$input="<input type=\"submit\" value=\"Опрацювати\">";  $input_name="message";}
//echo "stan";
//var_dump($stan);
//var_dump($key_2bits);
////Подивитися ключ
//echo "key1";
$key1=unpack('C*',$key);
//var_dump($key1);
//Зняття  двох байт з початку ключа для вставки їх у тіло блока повідомлення///////////////////////////////
//$key_2bits=substr($key,0,2);
$key_2bit_array=unpack('C*',$key_2bits);
$number_m=$key_2bit_array[1]*16+$key_2bit_array[2];//перевід байт лічильника у число
$number_m_for_site=$number_m;//число для інформування на сторінці, чи співпадають номери ключа і повідомлення
//$number_m_str=(binary)($number_m);
//$number_m_array=unpack('C',$number_m_str);
//var_dump($number_m);
$number_m_bin=decbin($number_m);
for($i=0;$i<=15;$i++){if(strlen($number_m_bin[$i])==0) $number_m_bin="0".$number_m_bin;}//додавання нулів до 16 знаків у string двійковому представленні лічильника
//$vstavka_lichyln_1="\x5F";
//$vstavka_lichyln_0="\x8F";
//echo "2bits";
//var_dump($number_m_bin);
//var_dump($key_2bit_array);
//Вставлення номеру повідомлення у тіло блоку////////////////////////////////////////////////////////////
$numbers_bits=array(44,46,99,103,108,109,116,531,907,1011,1040,1077,1102,1106,1221,1983);//номери байтів вставки
$blok_mes_1=unpack('C*',$blok_mes_0);
//var_dump($blok_mes_1);
//echo "blok_arr1";
array_splice( $blok_mes_1,count($blok_mes_1),0);//Для оновлення індексів після unpack - оскільки індексація з 1, а потрібно з 0
//var_dump($blok_mes_1);
//var_dump($numbers_bits);
$i=0; $j=0; $l=0;
while($j<=15){
    if($number_m_bin[$j]=="0") {array_splice( $blok_mes_1, $numbers_bits[$j], 0, 0);}
    if($number_m_bin[$j]=="1") {array_splice( $blok_mes_1, $numbers_bits[$j], 0, 1);}
$j+=1;
//$l=$numbers_bits[$j+1]-$numbers_bits[$j];
}
//Спаковування масиву блоку повідомлення назад у рядок
$count_mes=count($blok_mes_1);
$blok_mes="";
for ($i=0; $i<$count_mes; $i++)
{
//sleep(10);
$blok_mes.=pack('C*',$blok_mes_1[$i]);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//$a = substr($a, 0, 5)."-".substr($a, 5, 4)."-".substr($a, 9, 4);
//echo "blok_arr2";
////var_dump($blok_mes);
//$mes2=unpack('C*',$blok_mes);

////var_dump($mes2);
//unset ($mes2);
/*
///???????????????????????????
$key_file=fopen("key","rb");
fseek($key_file,-strlen($blok_mes));//зміщення вказівника з кінця файлу
$key=fread($key_file, strlen($blok_mes));
fclose($key_file);*/
////Подивитися ключ
//echo "key1";
$key1=unpack('C*',$key);
//var_dump($key1);
$shifr="";
//echo "strlen_key";
//var_dump(strlen($key));
//echo "blok_mes";
//var_dump(strlen($blok_mes));

if(strlen($key)>=strlen($blok_mes))//перевірка ключа на довжину, повинен бути довшим за блок повідомлення
{


$shifr=$key ^ $blok_mes;

/////////////////AES
//$mes2=base64_encode($mes2);
include('Aes.php');
//ключ 32 байти
$z = 'abcdefghijuklmno0123456789012345';
//режим CBC ECB CFB OFB
$mode = 'CBC';
//вектор ініціалізації 16 байт
$iv = '1234567890akcdef';
$aes = new Aes($z, $mode, $iv);
$shifr = $aes->encrypt($shifr);
///////////////кінець AES

$shifr=base64_encode($shifr);//закодувати у текстові символи
//////////////////////////

////////////////////Передача шифру у файл
/*
$shifr_file=fopen("shifr","wb");
fwrite($shifr_file, $shifr);
fclose($shifr_file);
*/
}
else {echo "small key"; exit;}
//echo strlen($blok_mes);
//unset ($blok_mes);

////////////////////////////////////////////////////////////////////////////////////Декодування при робочій обстановці- свій файл


//$key_file=fopen("key","rb");
//$key=fread($key_file, filesize("key"));
//fclose($key_file);

/////////////////////ДЕКОДУВАННЯ//////////////////////////////
//////////////////////При роботі з формою скрипт перевіря свою роботу, передаючи на декодування змінну $shifr

if(!empty($post_message ))
    {
       $mes=$shifr;
    }
else if (!empty($post_shifr))
            {
                $mes=$post_shifr;
           
            }
/////////////////з файлу
/*
$shifr_file=fopen("shifr","rb");
$mes=fread($shifr_file,filesize("shifr"));
fclose($shifr_file);
*/



///////////////////////
$mes=base64_decode($mes);

$mes=$aes->decrypt($mes);///AES
//$mes=base64_decode($mes);///AES
$mes_d=$mes;
//echo "shifr_base64_decode";
//var_dump($mes_d);

if(!empty($post_shifr))
{
//////////Блок відкриття файлу ключа
$key_file=fopen($key_from,"rb");
//Зняття  двох байт з початку ключа для вставки їх у тіло блока повідомлення///////////////////////////////
$key_2bits=fread($key_file,2);
//fread($key_file,1);
//$side=unpack('C*',fread($key_file,1));//біт визначення сторони
//$send=unpack('C*',fread($key_file,1));//біт висилки
fread($key_file,5);
$stan=fread($key_file,1);//7-й байт
fread($key_file,filesize($key_from)-strlen($mes_d)-5504-2-6);//зміщення вказівника з кінця файлу без 16  ???
$key=fread($key_file, strlen($mes_d));
//$hash_mes_file=fread($key_file, 64);//гаш попереднього повідомлення
fclose($key_file);  }

/////////////////////////////////////////////// Алгоритм дешифрування

$mes_x=$key ^ $mes_d;



//unset ($mes_d);
//echo "shifr_xor";
//
//var_dump($mes_x);

$blok_mes_0=$mes_x;


////Подивитися ключ
//echo "key2";


   
////Подивитися ключ
//echo "key2";
$key2=unpack('C*',$key);
//var_dump($key2);
                                            ////////////////кінець блоку "якщо не пуста веб-форма вхідної шифровки"

$blok_mes_1=unpack('C*',$blok_mes_0);
//var_dump($blok_mes_1);
//echo "blok_arr";
array_splice( $blok_mes_1,count($blok_mes_1));//Для оновлення індексів після unpack - оскільки індексація з 1, а потрібно з 0
//var_dump($blok_mes_1);

$number_m_incom="";
for($i=0;$i<16;$i++){
$number_m_incom.=$blok_mes_1[$numbers_bits[$i]];///номер повідомлення з тіла повідомлення

}
$number_m_incom=bindec($number_m_incom);
//echo "number_m_incom   ";
//var_dump($number_m_incom);
//if ($number_m_decod!=$number_m_incom){echo "<h1>Не співпадає номер повідомлення</h1>";} ЦЕ вставка на сайт
$i=0; $j=15; $l=0;
/////////////////Цикл безпосередньо вибирання елементів, що відповідають за байти лічильника - з кінця масиву назад на початок 
while($j>=0){
    unset($blok_mes_1[$numbers_bits[$j]]);
    //array_splice( $blok_mes_1, $numbers_bits[$j]);
    $j-=1;
//$l=$numbers_bits[$j+1]-$numbers_bits[$j];
}
$blok_mes_1=array_values($blok_mes_1);//переіндексування ключів
//Спаковування масиву блоку повідомлення назад у рядок
$count_mes=count($blok_mes_1);
$blok_mes="";
for ($i=0; $i<$count_mes; $i++)
{
//sleep(10);
$blok_mes.=pack('C*',$blok_mes_1[$i]);
}



///////////////////////////////////////////////// Частина декодування, спільна для вихідних і вхідних повідомлень
$mes_x=$blok_mes;

$mes3=unpack('C*',$mes_x);

//unset ($mes_x);
//echo "xxx";
//var_dump($mes3);
//array_splice( $mes3,count($mes3));//Для оновлення індексів після unpack - оскільки індексація з 1, а потрібно з 0
$count_mes=count($mes3);
$mes3_0=[];
for($i=0;$i<$count_mes;$i++){$mes3_0[$i]=$mes3[$i+1];}
$mes3=$mes3_0;
$count_mes=count($mes3);
//echo"mes3!!";
//var_dump($mes3);

//echo $count_mes;

/////////////////////////////Цикл викидання непотрібних знаків з тіла повідомлення
/*$i=$count_mes-1;
while ($i >=1)
{
    if($mes3[$i]!=32) {unset($mes3[$i]); $i=$i-1;  if($i<0)  break;}
    else 
    {     if(($mes3[$i]==32)&&($mes3[$i-1])!=NULL) {unset($mes3[$i]); $i--;  if($i<0)  break;}
           else 
               {     if(($mes3[$i]==32)&&($mes3[$i-1])==NULL) 
                            {if ($i>1) {$i=$i-2;    if($i<0)  break;}      
                              else $i--;  if($i<0)  break;}
                      else $i--;  if($i<0)  break;
               }
    }
$i--;
}
while($i>=0)
{
  if (($mes3[$i-1]==NULL)&&($i>=2)&&(($mes3[$i]==208)||($mes3[$i]==209)||($mes3[$i]==210)||($mes3[$i]==211)||($mes3[$i]<128)))                      { unset($mes3[$i-1]); $i=$i-2;   }
  else       { unset($mes3[$i]); $i=$i-1;   }
if (($i==0)&&($mes3[0]==NULL)) unset($mes3[0]);
  

}
*/
$count_mes=count($mes3);
//echo "count_mes";
//var_dump($count_mes);
$mes3=array_values($mes3);
for ($i=1; $i<$count_mes; $i=$i+2){
     //  sleep(10);
   // $ite=intval($mes3[$i]);
   // if (($mes3[$i-1]==NULL)&&(($i+1)<=$count_mes)&&($mes3[$i+1]==NULL)&&(($mes3[$i]==208)||($mes3[$i]==209)||($mes3[$i]==210)||($mes3[$i]==211)||($mes3[$i]<128)))
    if (($i<$count_mes-2)&&($mes3[$i]==0)&&($mes3[$i+2]!=0)) {//прибрати пробіли не з
 unset($mes3[$i]);   $mes3=array_values($mes3);   $i=$i-2;   $count_mes=count($mes3); continue; }//повідомлення
    if (($i==$count_mes-1)&&($mes3[$i-1]!=0)) {unset($mes3[$i]);  $mes3=array_values($mes3);}//для останнього члена
     if (($i<$count_mes-1)&&($mes3[$i-1]==0)&&($mes3[$i+1]==0) or ($mes3[$i-1]==0)&&($mes3[$i]==32))
        {
         unset($mes3[$i-1]);
        $mes3=array_values($mes3);
        $i--;
         $count_mes=count($mes3);///перерахування величини масиву, оскільки видаляються члени і довжина змінюється

        
          }
    else {     //if (($i+1)<=$count_mes) { 
                   unset($mes3[$i-1]);
                   //unset($mes3[$i]);     
                   $mes3=array_values($mes3);
                  $i=$i-2;        
                 $count_mes=count($mes3);///перерахування величини масиву, оскільки видаляються члени і довжина змінюється
                          //  }
                      
                 }
   //if ($mes3[$i]==NULL) {unset($mes3[$i]); $i=$i-1;}
////Для останнього символа
  /*  if (($mes3[$i-1]==NULL)&&($mes3[$i]==32)&&($i==$count_mes))
        {
         unset($mes3[$i-1]);
        
          }
    else {      
                   unset($mes3[$i-1]);
                   unset($mes3[$i]);            
                      
             }*/

}

//////////////кінець циклу викидання зайвихсимволів
//echo "splise";
//var_dump($mes3);
$mes3=array_values($mes3);//переіндексування ключів
//echo "xyz0";
//var_dump($mes3);
$count_mes=count($mes3);
$mes4="";
for ($i=0; $i<$count_mes; $i++)
{
//sleep(10);
$mes4.=pack('C*',$mes3[$i]);
}


$mes4="\xEF\xBB\xBF".$mes4;
//echo "xyz";
//unset ($mes3);
////var_dump($mes4);
//echo "xyz2";
//
$decr=$mes4;
//unset ($mes4);
//$decr=base64_encode($decr);
//$decr=urlencode($decr);
//$decr = iconv("UTF-8","UTF-8//IGNORE",$decr);
//$decr = preg_replace('/[\x00-\x1F\x7F]/u', '', $decr);
//$decr=preg_replace('/[\x00-\x1f]/', '', $decr);
//$decr = preg_replace ("/[^a-zA-ZА-Яа-я0-9]/","",$decr);
//"/[^a-zA-Zа-яА-Я0-9]/u"
//$decr = preg_replace ('/[^ a-zA-Z0-9\n\"\/\+\!\(\)\.\,\[\]\{\}\:\_\¶\#\<\>\&\©\$\;\=\x0Db\'\xE2b\x80b\x99b\?абвгґдеєёжзиіїйклмнопрстуфчхцшщъьыэюяАБВГҐДЕЄЁЖЗИІЇЧЙКЛМНОПРСТУФХЦШЩЬЪЫЭЮЯ\-]*/i',"",$decr);//!!!
$decr = preg_replace ('/[^ \x00-\x7FабвгґдеєёжзиіїйклмнопрстуфчхцшщъьыэюяАБВГҐДЕЄЁЖЗИІЇЧЙКЛМНОПРСТУФХЦШЩЬЪЫЭЮЯ\-]*/i',"",$decr);//!!!
//echo "decr1";
////var_dump($decr);


$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
$decr=preg_replace($regex, '$1', $decr);

///AES
//$decr="\xEF\xBB\xBF".$decr;// вставка байт
//echo "decr3";
////var_dump($decr);
//echo "test_decr";
//var_dump(unpack('C*',$decr));
//$decr = preg_replace ("/[^\xd0\x80-\xd3\xbf]/","",$decr);

//$decr = preg_replace ('/[\x00-\x1F\x7F-\xA0\xAD]/',"",$decr);
////////////////////////////Розшифрування у файл
/*
$decr_file=fopen("decr","wb");
fwrite($decr_file, $decr);
fclose($decr_file);
*/

//echo "decr".strlen($decr);
//echo "post_message".strlen($post_message);
$form_shifr="";//змінна для форми шифровки у веб формі
if($stan_for_site!=119) $site_help="<h2>Набрати?</h2>";
if($stan_for_site==255) { $site_help="<h2>1Немає відповіді? Можете  вислати повторно останнє повідомлення</h2>";}
///////////////////////Ділянка після успішного дешифрування
//////////Записування гешу з форми написання 
if(!empty($post_message)) {
$hash_mes=hash( "sha512", $post_message, TRUE);
if($hash_mes==$hash_mes_file) $site_help="<h2>Це повідомлення уже було щойно зашифроване<br>Введіть інше</h2>"; 
}
if(!empty($post_shifr)) {
if ($number_m_for_site!=$number_m_incom) {$input="<input type=\"submit\" value=\"Розшифрувати\">";  $input_name="shifr";  $site_help="<h1>Не співпадає номер повідомлення</h1>"; $last_shifr_button="";}
}

////////////////////Вивід додаткового повідомлення, якщо деякі знаки не відображаються
if((strlen($decr)/strlen($post_message)*10>5)&&(strlen($decr)/strlen($post_message)<1)){
$site_help="<h2>Деякі символи не збереглися. Все одно зашифрувати?</h2>";
$input="<input type=\"hidden\" name=\"force\"><input type=\"submit\" value=\"Опрацювати\">";  $input_name="message";
}
if ((empty($post_shifr))&&($stan_for_site==255) ) {$input="<input type=\"submit\" value=\"Розшифрувати\">";  $input_name="shifr"; $site_help="<h2>2Немає відповіді? Можете повторно вислати останнє повідомлення</h2>";
$last_shifr_button="<div style=\" float: right;\">
<form action=\"home_form_crypt2.php\" method=\"post\" >
     <input type=\"radio\" name=\"last\" value=\"yes\">
  <input type=\"submit\" value=\"Вернути останню шифровку\">
   </form></div>";}
if ($stan_for_site==0) {$site_help="<h2>Набрати?</h2>";  $input="<input type=\"submit\" value=\"Опрацювати\">";  $input_name="message";
}

//if((((strlen($decr)==strlen($post_message))&&(strlen($post_message)>1))&&($hash_mes!=$hash_mes_file)) || ((strlen($decr)>=2)&&(strlen($post_shifr)>1))&&($number_m_for_site==$number_m_incom) ||
if((((strlen($post_message)>1))&&($hash_mes!=$hash_mes_file)) || ((strlen($decr)>=2)&&(strlen($post_shifr)>1))&&($number_m_for_site==$number_m_incom) ||
 isset($_POST["force"]))//5-мінімальна довжина дешифровки 3 БОМ байти, 1 байт повідомлення і 1 пробіл після повідомлення
{
$form_shifr=$shifr;
$input_force="";//закриття додаткової кнопки зашифрування при неповній кількості символів

if(!empty($post_message)) $site_help="<h2>Успішно зашифровано!</h2>"; 
if(!empty($post_shifr)) {$site_help="<h2>Успішно прийнято! Набрати наступне?</h2>";  $form_shifr=$decr;}
if ($number_m_for_site!=$number_m_incom) {$site_help="<h1>Не співпадає номер повідомлення</h1>"; $last_shifr_button="";}
//Якщо довжина повідомлення у контрольному полі не менша за писане повідомлення, то запис копії вихідної шифровки у файл х
if(strlen($post_message)>=2)  {
$x_file=fopen("home_x", "w");
$x=fwrite($x_file, $shifr);
fclose($x_file);    }
/// і Збільшення лічильника ключа на 1
//echo "лічильник";
//var_dump(strlen($decr));
$number_m+=1;
// і запис у перші два байти файлу ключа при натисканні "готово"
if($number_m<=15) {$erst='000';}
if(($number_m>15)&&($number_m<=255)) {$erst='00';}
else if(($number_m>255)&&($number_m<=4095)) {$erst='0';}
//зміна сьомого  байту 0  1
$stan=unpack('C*',$stan);
if(($stan[1]==255)||($stan[1]==119)) {$stan_for_file='00'; $stan_for_file_input="0";}
if($stan[1]==0) {$stan_for_file='FF'; $stan_for_file_input="1";}
//echo "stan_1";
//var_dump($stan);  //var_dump($stan[1]);

$key_file=fopen($key_from,"r+b");
$mes=fwrite($key_file, pack('H*', $erst.dechex($number_m)));//лічильник у перші 2 байти
//echo "ftell1   ".ftell($key_file)."   ";


fread($key_file,5);
//echo "ftell2   ".ftell($key_file)."   ";
$stan_f=fwrite($key_file, pack('H*', $stan_for_file));
//echo "ftell3   ".ftell($key_file)."   ";
//ftruncate($key_file, filesize($key_from)-strlen($blok_mes)-5504);
//fseek($key_file,-strlen($blok_mes)-16-5504);//зміщення вказівника з кінця файлу
fread($key_file,filesize($key_from)-strlen($blok_mes)-16-5504-2-6);
//echo "ftell2   ".ftell($key_file)."   ";
for($i=0; $i<86; $i++) {fwrite($key_file, $hash_mes); }
fclose($key_file);
$key_file=fopen($key_from,"r+b");
if((filesize($key_from)-strlen($blok_mes)-16)>0){
ftruncate($key_file, filesize($key_from)-strlen($blok_mes)-16);}

fclose($key_file);
//echo "hash".$hash_mes."<br>hash";
// echo $hash_mes_file."<br>".strlen($hash_mes);    
//var_dump($hash_mes_file); 

//var_dump($blok_mes); 

//Перемикання форм на сайті
if($stan_for_file_input=="1") {$input="<input type=\"submit\" value=\"Розшифрувати\">";  $input_name="shifr"; }
if($stan_for_file_input=="0") {$input="<input type=\"submit\" value=\"Опрацювати\">";  $input_name="message";}
//echo "stan";
//var_dump($stan);

}





//Вирівнювання лічильника








//print "\n".strlen("А");
?>
