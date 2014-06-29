<?php
function present($m) {
  global $thumbx,$thumby,$fullx,$fully,$picturedir,$dest;
  prepare("$picturedir/$m","$dest/t_$m",$thumbx,$thumby);
  prepare("$picturedir/$m","$dest/f_$m",$fullx,$fully);

  echo "<a href=\"$dest/f_$m\"><img border=1 hspace=20px vspace=20px src=\"$dest/t_$m\"></a>";
}

function prepare($src,$dst,$sizex,$sizey) {
  if (file_exists($dst) && filemtime($src)<=filemtime($dst)) return;
  $command="/usr/bin/convert -resize ${sizex}x${sizey} $src $dst";
  exec("$command");
  chmod($dst,0644);
}

function mytime($s,$high) {
  $parts=explode("-",$s);
  if ($high == 0) return mktime(0,0,0,$parts[1],$parts[2],$parts[0]);
  else return mktime(23,59,59,$parts[1],$parts[2],$parts[0]);
}

$thumbx=320; $thumby=180;
$fullx=768; $fully=432;

$scheduledir="";
$picturedir="";
$dest="temp";

$msg = array();
?>

<html>
  <head>
    <title>Aktuality FMFI UK</title>
  </head>
  <body>

    <h1>Aktuality FMFI UK</h1>

    <p>

    <?php
    foreach (glob("$scheduledir/*.txt") as $filename) {
      $f = fopen("$filename","r");
      while (($line = fgets($f)) !== false) {
        $line = trim($line); 
        if (preg_match("/^#/",$line)) continue;
        $parts = preg_split("/\s+/",$line);
        if (count($parts)!=4) continue;

        if (time()>=mytime($parts[1],0) && time()<=mytime($parts[2],1)) {
           array_push($msg,$parts[0]);	 
        }
      }
      fclose($f);
    }

    foreach ($msg as $m) {
      present($m);
    }
    ?>

    </p>
  </body>
</html>