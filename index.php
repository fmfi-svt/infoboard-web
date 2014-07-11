<?php
function presentP($m) {
  global $thumbx,$thumby,$fullx,$fully,$picturedir,$dest;
  prepareP("$picturedir/$m","$dest/t_$m",$thumbx,$thumby);
  prepareP("$picturedir/$m","$dest/f_$m",$fullx,$fully);

  echo "<div class=\"item\"><a href=\"$dest/f_$m\" class=\"full_view picture\"><img src=\"$dest/t_$m\"></a></div>";
}

function presentV($m) {
  global $thumbx,$thumby,$fullx,$fully,$picturedir,$dest,$videodir;

  $mf = preg_replace('/[.].*$/', '.mp4', $m);
  prepareV("$videodir/$m","$dest/t_$mf",$thumbx,$thumby);
  prepareV("$videodir/$m","$dest/f_$mf",$fullx,$fully);
  echo '<div class="item"><a href="'.$dest.'/f_'.$mf.'" fullx="'.$fullx.'" fully="'.$fully.'"class="full_view video"><video muted loop autoplay width="'.$thumbx.'" height="'.$thumby.'"><source src="'.$dest.'/t_'.$mf.'" type="video/mp4">Your browser does not support the video tag.</video></a></div>';
}

function prepareV($src,$dst,$sizex,$sizey) {
  if (file_exists($dst) && filemtime($src)<=filemtime($dst)) return;
  $command="/usr/bin/ffmpeg -i $src -s ${sizex}x${sizey} $dst";
  exec("$command");
  chmod($dst,0644);
}

function prepareP($src,$dst,$sizex,$sizey) {
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
$videodir="";
$dest="temp";



$msg = array('pictures' => array(),
             'videos' => array());
?>

<html>
  <head>
    <title>Aktuality FMFI UK</title>
    <link rel="stylesheet" href="style.css" />
    <link href='http://fonts.googleapis.com/css?family=Dosis:600' rel='stylesheet' type='text/css'>
  </head>
  <body>

    <h1>Aktuality FMFI UK</h1>

    <div id="container">

    <?php
    foreach (glob("$scheduledir/*.txt") as $filename) {
      $f = fopen("$filename","r");
      while (($line = fgets($f)) !== false) {
        $line = trim($line); 
        if (preg_match("/^#/",$line)) continue;
        $parts = preg_split("/\s+/",$line);
        if (count($parts) == 4){
          if (time()>=mytime($parts[1],0) && time()<=mytime($parts[2],1)) {
             array_push($msg['pictures'],$parts[0]);	 
          }
        }

        if (count($parts) == 3){
          if (time()>=mytime($parts[1],0) && time()<=mytime($parts[2],1)) {
             array_push($msg['videos'],$parts[0]);   
          }
        }
      }
      fclose($f);
    }

    foreach ($msg['pictures'] as $m) {
      presentP($m);
    }

    foreach ($msg['videos'] as $m) {
      presentV($m);
    }
    ?>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script type="text/javascript" charset="utf-8">
      $('#container').isotope({
        itemSelector: '.item',
        layoutMode: 'masonry',
        masonry: {
          columnWidth: 370,
          isFitWidth: true
        },
      })

      $("a.full_view").click(function(event){
        event.preventDefault();

        if($("#full_view").length){
          $("#full_view").remove();
          $("#overlay").remove();
        }

        event.stopPropagation();

        function width(){
           return window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth||0;
        }
        function height(){
           return window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight||0;
        }

        $("body").append('<div id="overlay"> </div>');

        if($(this).hasClass('picture')){
          $("body").append("<div id='full_view'><img src='"+ this.href +"' alt='Image.' /></div>");
          $("#full_view > img").load(function(){
          $("#full_view")
          .css("top", Math.max(0, ((height() - $("#full_view").outerHeight(true)) / 2) + 
                                    $(window).scrollTop()) + "px")
          .css("left", Math.max(0, ((width() - $("#full_view").outerWidth(true)) / 2) + 
                                     $(window).scrollLeft()) + "px")
          .fadeIn("fast");
        });  
        }

        if($(this).hasClass('video')){
          $("body").append("<div id='full_view'><video muted loop autoplay width='"+$(this).attr('fullx')+"' height='"+$(this).attr('fully')+"'><source src='"+ this.href +"' type='video/mp4'>Your browser does not support the video tag.</video></div>");

          $("#full_view")
          .css("top", Math.max(0, ((height() - $("#full_view").outerHeight(true)) / 2) + 
                                    $(window).scrollTop()) + "px")
          .css("left", Math.max(0, ((width() - $("#full_view").outerWidth(true)) / 2) + 
                                     $(window).scrollLeft()) + "px")
          .fadeIn("fast");
          
        }               
      }); 

      $(document).click(function() {
        $("#full_view").remove();
        $("#overlay").remove();
      });
    </script>
  </body>
</html>