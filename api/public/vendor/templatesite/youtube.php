<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>YouTube Test</title>
</head>

<body>
<?php
$id = "";
$url = "";  
if(isset($_REQUEST['url'])){
	$url = $_REQUEST['url'];
}
if($url!=""){
	parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
	
	if($my_array_of_vars['v']){
		$id = $my_array_of_vars['v'];
	}else{
		$id = $url;
	}
}


?>
<form action="youtube.php" enctype="application/x-www-form-urlencoded" method="get">
<label for="id">ID Youtube</label> <input name="url" type="text" value="<?php echo $url ?>" size="150" />&nbsp;&nbsp;
<input name="enviar" type="submit" value="Consultar" />
</form>
<br />
<hr>
<?php
/*
latest short format: http://youtu.be/NLqAF9hrVbY
iframe: http://www.youtube.com/embed/NLqAF9hrVbY
iframe (secure): https://www.youtube.com/embed/NLqAF9hrVbY
object param: http://www.youtube.com/v/NLqAF9hrVbY?fs=1&hl=en_US
object embed: http://www.youtube.com/v/NLqAF9hrVbY?fs=1&hl=en_US
watch: http://www.youtube.com/watch?v=NLqAF9hrVbY
users: http://www.youtube.com/user/Scobleizer#p/u/1/1p3vcRhsYGo
ytscreeningroom: http://www.youtube.com/ytscreeningroom?v=NRHVzbJVx8I
any/thing/goes!: http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/2/PPS-8DMrAn4
any/subdomain/too: http://gdata.youtube.com/feeds/api/videos/NLqAF9hrVbY
more params: http://www.youtube.com/watch?v=spDj54kf-vY&feature=g-vrec
query may have dot: http://www.youtube.com/watch?v=spDj54kf-vY&feature=youtu.be
nocookie domain: http://www.youtube-nocookie.com
*/
// Linkify youtube URLs which are not already links.
function linkifyYouTubeURLs($text) {
    $text = preg_replace('~(?#!js YouTubeId Rev:20160125_1800)
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://          # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)?  # Optional subdomain.
        (?:                # Group host alternatives.
          youtu\.be/       # Either youtu.be,
        | youtube          # or youtube.com or
          (?:-nocookie)?   # youtube-nocookie.com
          \.com            # followed by
          \S*?             # Allow anything up to VIDEO_ID,
          [^\w\s-]         # but char before ID is non-ID char.
        )                  # End host alternatives.
        ([\w-]{11})        # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)       # Assert next char is non-ID or EOS.
        (?!                # Assert URL is not pre-linked.
          [?=&+%\w.-]*     # Allow URL (query) remainder.
          (?:              # Group pre-linked alternatives.
            [\'"][^<>]*>   # Either inside a start tag,
          | </a>           # or inside <a> element text contents.
          )                # End recognized pre-linked alts.
        )                  # End negative lookahead assertion.
        [?=&+%\w.-]*       # Consume any URL (query) remainder.
        ~ix', '<a href="http://www.youtube.com/watch?v=$1">YouTube link: $1</a>',
        $text);
    return $text;
}
// Linkify youtube URLs which are not already links.
function getIDYoutube($url) {
    $url = preg_replace('~(?#!js YouTubeId Rev:20160125_1800)
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://          # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)?  # Optional subdomain.
        (?:                # Group host alternatives.
          youtu\.be/       # Either youtu.be,
        | youtube          # or youtube.com or
          (?:-nocookie)?   # youtube-nocookie.com
          \.com            # followed by
          \S*?             # Allow anything up to VIDEO_ID,
          [^\w\s-]         # but char before ID is non-ID char.
        )                  # End host alternatives.
        ([\w-]{11})        # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)       # Assert next char is non-ID or EOS.
        (?!                # Assert URL is not pre-linked.
          [?=&+%\w.-]*     # Allow URL (query) remainder.
          (?:              # Group pre-linked alternatives.
            [\'"][^<>]*>   # Either inside a start tag,
          | </a>           # or inside <a> element text contents.
          )                # End recognized pre-linked alts.
        )                  # End negative lookahead assertion.
        [?=&+%\w.-]*       # Consume any URL (query) remainder.
        ~ix', '$1',
        $url);
    return $url;
}

$id = getIDYoutube($url);

$key = "AIzaSyCfePtJosxmXcQPTeOZ49lsUqmH-Q0n5ZQ";
// returns a single line of JSON that contains the video title. Not a giant request.
$videoTitle = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=" . $key . "&fields=items(id,snippet(title,channelId,description,thumbnails))&part=snippet");
//echo var_dump($videoTitle);



// despite @ suppress, it will be false if it fails
if ($videoTitle) {
	$json = json_decode($videoTitle, true);
	if(count($json['items'])<=0){
		echo "Nenhum video";
		die();
	}
			
	echo "id=" . $id;
	"<br>";
	echo "<hr>";

	
	
	echo "<br>";
	echo "Titulo do video:<br>" .  $json['items'][0]['snippet']['title'];
	echo "<br><br>";
	echo "Descrição do video:<br>" .  $json['items'][0]['snippet']['description'];
	$channelId = $json['items'][0]['snippet']['channelId'];
	//echo $channelId;
	echo "Imagem:<br>" . $json['items'][0]['snippet']['thumbnails']['standard']['url'];
	echo "<br><img src=\"" . $json['items'][0]['snippet']['thumbnails']['standard']['url'] . "\" border=\"0\" />";	
	echo "<br><br>";
	
	$videoChannel = file_get_contents("https://www.googleapis.com/youtube/v3/channels?id=".$channelId."&key=" . $key . "&fields=items(id,snippet(title,description,thumbnails))&part=snippet");
	//echo $videoChannel;
	$jsonChannel = json_decode($videoChannel, true);
	//echo var_dump($jsonChannel);

	echo "<br>";
	echo "Titulo do canal:<br><b>" . $jsonChannel['items'][0]['snippet']['title'] . "</b>";
	echo "<br><br>";
	echo "Descrição do canal:<br>" . $jsonChannel['items'][0]['snippet']['description'];
	echo "<br><br>";
	echo "Imagem:<br>" . $jsonChannel['items'][0]['snippet']['thumbnails']['default']['url'];
	echo "<br><img src=\"" . $jsonChannel['items'][0]['snippet']['thumbnails']['default']['url'] . "\" border=\"0\" />";
	echo "<hr>";
	
};


?>

</body>
</html>