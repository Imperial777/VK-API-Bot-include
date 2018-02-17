<?php
function sendMessage($text, $userid, $attach) {
	global $token, $v_api, $filestats, $user_id;
	setLog('Отправлено сообщение:'.$text);
	if(!$userid){
		$userid=$user_id;
	}
	$request_params = array(
		'message' => $text,
		'user_id' => $userid,
		'attachment' => $attach,
		'access_token' => $token,
		'v' => $v_api
		);
	$get_params = http_build_query($request_params);
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
	$stats = Readtxt($filestats);
	$stats=$stats+1;
	Writetxt($stats, $filestats);
}
function Writetxt($text, $file) {
	$fp = fopen ($file, "w");
	fwrite($fp,$text);
	fclose($fp);
}
function Readtxt($file) {
	$handle = fopen($file, "r");
	$contents = fread($handle, filesize($file));
	fclose($handle);
	return $contents;
}
function uploadPhoto($path) {
	global $token, $v_api;
	$data = curlss("https://api.vk.com/method/photos.getMessagesUploadServer", array(
		'access_token' => $token,
		'v' => $v_api
		));
	if($data) {
		$getUrl = json_decode($data, true);
		$url = $getUrl['response']['upload_url'];
		setLog('result'.$url);
			//$path = 'out.jpg';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		if (class_exists('\CURLFile')) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, ['file1' => new \CURLFile($path)]);
		} else {
			curl_setopt($ch, CURLOPT_POSTFIELDS, ['file1' => "@$path"]);
		}
		$upload = curl_exec($ch);
		curl_close($ch);
		if($upload) {
			$upload = json_decode($upload, true);
			$data = curlss("https://api.vk.com/method/photos.saveMessagesPhoto", array(
				'hash' => $upload['hash'],
				'photo' => $upload['photo'],
				'server' => $upload['server'],
				'access_token' => $token,
				'v' => $v_api
				));
			if($data) {
				$data = json_decode($data, true);

				return 'photo'.$data['response'][0]['owner_id'].'_'.$data['response'][0]['id'];
			}
		}
	}
}
function upload($path, $docname) {
	global $token, $v_api, $user_id;
	$data = curlss("https://api.vk.com/method/docs.getMessagesUploadServer", array(
		'type' => 'doc',
		'peer_id' => $user_id,
		'access_token' => $token,
		'v' => $v_api
		));
	if($data) {
		$getUrl = json_decode($data, true);
		$url = $getUrl['response']['upload_url'];
		setLog('result'.$url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		if (class_exists('\CURLFile')) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, ['file1' => new \CURLFile($path)]);
		} else {
			curl_setopt($ch, CURLOPT_POSTFIELDS, ['file1' => "@$path"]);
		}
		$upload = curl_exec($ch);
		curl_close($ch);
		if($upload) {
			$upload = json_decode($upload, true);
			$data = curlss("https://api.vk.com/method/docs.save", array(
				'file' => $upload['file'],
				'title' => $docname,
				'tags' => 'сборка',
				'access_token' => $token,
				'v' => $v_api
				));
			if($data) {
				$data = json_decode($data, true);
				setLog('!!!!!!!qweqwe'.$data['response'][0]['owner_id']);
				setLog('!!!!!!!qweqwe'.$data['response'][0]['id']);
				return 'photo'.$data['response'][0]['owner_id'].'_'.$data['response'][0]['id'];
			}
		}
	}
}
function setLog($message) {
	global $log_enable;
	if($log_enable) {
		$log_file_name = 'logs.txt';

		if(file_exists($log_file_name)) {
			$log = array_diff(explode("\r\n", file_get_contents($log_file_name)), array(''));
		}

		$log[] = date("m.d.Y-H:i:s").' | '.$message;

		if(file_put_contents($log_file_name, implode("\r\n", $log))) {
			return true;
		} else {
			return false;
		}
	}
}
function curlss($url, $params = false) {
	usleep(333333);

	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: ru,uk;q=0.8,en;q=0.6']);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.137 YaBrowser/17.4.1.910 Yowser/2.5 Safari/537.36");
	if($params) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
	}
	$response = curl_exec($ch);
	curl_close($ch);
	setLog($response);
	return $response;
}
function savebd(){
	global $files, $p, $name, $user_name, $start, $ram1, $ram2, $ram3, $ram4;
	$params=array(
		'name'=> $name,
		'p'=> $p,
		'start'=>$start,
		'ram1'=>$ram1,
		'ram2'=>$ram2,
		'ram3'=>$ram3,
		'ram4'=>$ram4,
		);
	setLog('name:'.$name);
	setLog('p:'.$p);
	setLog('start:'.$start);
	setLog('ram1:'.$ram1);
	setLog('ram2:'.$ram2);
	setLog('ram3:'.$ram3);
	setLog('ram4:'.$ram4);
	Writetxt(json_encode($params), $files);
}
function breaks(){
	savebd();
	setLog('-----------------------------------------');
	echo('ok');
	break;
}
function strcode($str, $passw=""){
	$salt = "Dn8*#2n!9j";
	$len = strlen($str);
	$gamma = '';
	$n = $len>100 ? 8 : 2;
	while( strlen($gamma)<$len )
	{
		$gamma .= substr(pack('H*', sha1($passw.$gamma.$salt)), 0, $n);
	}
	return $str^$gamma;
}
function sync_folder($srcdir, $dstdir, $forced = true){
  $sizetotal = 0;

  if(!is_dir($dstdir)) mkdir($dstdir);
  // открываем исходный каталог
  if($curdir = opendir($srcdir)) {
    while($file = readdir($curdir)) {
      if($file != '.' && $file != '..') {
        $srcfile = $srcdir . '/' . $file;
        $dstfile = $dstdir . '/' . $file;
        if(is_file($srcfile)) {
          if(is_file($dstfile))
            $ow = filemtime($srcfile) -
                  filemtime($dstfile);
          else $ow = 1;
          if($ow > 0 || $forced) {
            if(copy($srcfile, $dstfile)) {
              touch($dstfile, filemtime($srcfile)); $num++;
              chmod($dstfile, 0777);
              $sizetotal =
                ($sizetotal + filesize($dstfile));
            }
            else {
            	sendMessage('Ошибка: Не могу скопировать файл'.$srcfile);
            }
          }
        }
      }
    }
    // закрываем ранее открытый каталог
    closedir($curdir);
  }

  sendMessage('Копирование завершено!');

  return true;
}
function zip(){
	global $user_id;
	$zip = new ZipArchive();
	$filesssname = './BD/dowload/'.$user_id.'.rar';
	if (file_exists($filesssname)) {
		unlink($filesssname);
	}
	if ($zip->open($filesssname, ZipArchive::CREATE)!==TRUE) {
	    sendMessage('Error');
	}
	$zip->addEmptyDir('Import to GTA3.img');
	$pathdir='BD/'.$user_id;
	$files = scandir($pathdir);
	foreach ($files as $file):
		if($file != '.' & $file != '..'){
			sendMessage('Добавлен:'.$file);
			$zip -> addFile( $pathdir.'/'.$file, 'Import to GTA3.img/'.$file);
		}
	endforeach;

	//$zip -> addFile( $pathdir.'/'.$files1[3], $files1[3]);

	$zip->setArchiveComment('vk.com/rainwest');
	$zip->addFromString("ReadMe.txt", "Импортируешь все в Gta3.img.\n");
	//$zip->addFromString("2.txt", "#2 Это тестовая строка, добавленная как testfilephp2.txt.\n");
	//$zip->addFile($thisdir . "/too.php","/testfromfile.php");
	$zip->close();
}
function put($dir, $a, $b){
	if (is_dir('BD/'.$dir)){
		if (is_dir('BD/'.$dir.'/id')){
			Writetxt('BD/'.$dir.'/id/'.$a.'.txt', $b);
			Writetxt('BD/'.$dir.'/text/'.$b.'.txt', $a);
		}else{
			mkdir('BD/'.$dir.'/id', 0700);
			mkdir('BD/'.$dir.'/text', 0700);
		}
	} else {
		mkdir('BD/'.$dir, 0700);
		if (is_dir('BD/'.$dir.'/id')){
			Writetxt('BD/'.$dir.'/id/'.$a.'.txt', $b);
			Writetxt('BD/'.$dir.'/text/'.$b.'.txt', $a);
		}else{
			mkdir('BD/'.$dir.'/id', 0700);
			mkdir('BD/'.$dir.'/text', 0700);
		}
	}
}
function stop_message($time){
	global $p;
	$p='1';
	savebd();
	sleep($time);
	$p='0';
}
function start_message($time){
	global $token, $v_api, $user_id;
	$request_params = array(
		'user_id' => $user_id,
		'type' => 'typing',
		'access_token' => $token,
		'v' => $v_api
	);
	$get_params = http_build_query($request_params);
	file_get_contents('https://api.vk.com/method/messages.setActivity?'. $get_params);
	read_message();
	stop_message($time);
}
function read_message(){
	global $token, $v_api, $user_id, $id_message;
	$request_params = array(
		'message_ids' => $id_message,
		'start_message_id' => '1',
		'access_token' => $token,
		'v' => $v_api
	);
	$get_params = http_build_query($request_params);
	file_get_contents('https://api.vk.com/method/messages.markAsRead?'. $get_params);
	setLog('https://api.vk.com/method/messages.markAsRead?'. $get_params);
}
?>
