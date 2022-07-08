<?php
include_once("function\conexaobd.php");
include_once("function\functions.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<!-- Coloque esta tag onde você deseja que o chat do Live Helper apareça. -->
<div id="lhc_chatbox_embed_container" ></div>

<!-- Coloque esta tag depois da tag do módulo do chat do Live Helper -->
<script type="text/javascript">
var LHCChatboxOptionsEmbed = {hashchatbox:'empty',identifier:'default'};
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
po.src = '//suporte.idoze.com.br/index.php/eng/chatbox/embed/(chat_height)/220';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>

<?php
function generateAutoLoginLink($params){

     $dataRequest = array();
     $dataRequestAppend = array();

     // Destination ID
     if (isset($params['r'])){
         $dataRequest['r'] = $params['r'];
         $dataRequestAppend[] = '/(r)/'.rawurlencode(base64_encode($params['r']));
     }

     // User ID
     if (isset($params['u']) && is_numeric($params['u'])){
         $dataRequest['u'] = $params['u'];
         $dataRequestAppend[] = '/(u)/'.rawurlencode($params['u']);
     }

     // Username
     if (isset($params['l'])){
         $dataRequest['l'] = $params['l'];
         $dataRequestAppend[] = '/(l)/'.rawurlencode($params['l']);
     }

     if (!isset($params['l']) && !isset($params['u'])) {
         throw new Exception('Username or User ID has to be provided');
     }

     // Expire time for link
     if (isset($params['t'])){
         $dataRequest['t'] = $params['t'];
         $dataRequestAppend[] = '/(t)/'.rawurlencode($params['t']);
     }
     $hashValidation = sha1($params['secret_hash'].sha1($params['secret_hash'].implode(',', $dataRequest)));

     return "index.php/user/autologin/{$hashValidation}".implode('', $dataRequestAppend);
}
?>

<a target="_blank" href="http://suporte.idoze.com.br/<?php echo generateAutoLoginLink(array('r' => 'chat/chattabs', 'u' => 1,/* 'l' => 'admin', *//* 't' => time() + 50000 */ 'secret_hash' => 'i12@loginch@t'))?>">Login me</a>

</body>
</html>
