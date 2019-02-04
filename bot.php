<? 
define('CALLBACK_API_CONFIRMATION_TOKEN', 'confToken'); 
define('VK_API_ACCESS_TOKEN', 'accesToken');  

define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation'); 
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');  
define('VK_API_ENDPOINT', 'https://api.vk.com/method/');  
define('VK_API_VERSION', '5.89'); // обязательно проставить в вк

$event = json_decode(file_get_contents('php://input'), true); 

switch ($event['type']) { 
   
  case CALLBACK_API_EVENT_CONFIRMATION: 
    echo(CALLBACK_API_CONFIRMATION_TOKEN); 
    break; 
   
  case CALLBACK_API_EVENT_MESSAGE_NEW: 
    $message = $event['object']; 
    $peer_id = $message['peer_id'] ?: $message['user_id']; 
    $regexp = "/![a-zA-Zа-яА-Я0-9]{1,}/u";
  preg_match_all($regexp, $message['text'],$matches,PREG_PATTERN_ORDER);
    $text = $matches[0][0]; //   /![a-z]{1,}/
    $temp = "";
    switch($text)
    {
    case '!хелп':
    case '!help':
      $temp = "!добавить !название_команды 'текст'\n!удалить !название_команды\nСписок существующих команд:\n";
      $command_list = file_get_contents(__DIR__.'/commands/list.txt');
      $temp = $temp . str_replace("\n", " ", $command_list);
      send_message($peer_id, $temp);
      break;
    case '!добавить': 
      $regcommand = "/'.{1,}'/u";
      $command = $matches[0][1];
      preg_match_all($regcommand, $message['text'], $commandtext, PREG_PATTERN_ORDER);
      $readytext = substr($commandtext[0][0], 1,-1); 
      $file_name = __DIR__.'/commands'.'/command_'.md5($command).'.txt';
      if(file_exists($file_name))
      {
        unlink($file_name);
        $file_handler = fopen($file_name,'w+');
        fwrite($file_handler, $readytext);
        fclose($file_handler);
        $temp = "Команда перезаписана"; 
        send_message($peer_id, $temp);
      } else 
      {
        $photo_item = $message['attachments'];
        $photo_item = $photo_item[0];
        if($photo_item['type'] == 'photo')
        {
          $arraySizes = $photo_item['photo']['sizes'];
          $current = array();
          foreach($arraySizes as $size){
            if($size['type'] == 'y'){
              $current = $size;
            }elseif($size['type'] == 'x'){
              $current = $size;
            } 
          } 
          $url = $current['url'];
          $path = __DIR__.'/images/image_'.md5($command).'.png';
          file_put_contents($path, file_get_contents($url));
          send_message($peer_id, 'Команда с картинкой добавлена');
          file_put_contents(__DIR__.'/commands/list.txt', $command."\n",FILE_APPEND);  
          
  
        } else 
        {
        $file_handler = fopen($file_name,'w+');
        fwrite($file_handler, $readytext);
        fclose($file_handler);
        
        $temp = "Команда добавлена\n";
        foreach ($photo_item[0] as $key => $value) {
          $temp = $temp . $key . " => " . $value . "\n";
        }
        file_put_contents(__DIR__.'/commands/list.txt', $command."\n",FILE_APPEND);
        send_message($peer_id, $temp);
        }
        
      } 
      break;
    case '!удалить':
      $text_command_path = __DIR__.'/commands'.'/command_'.md5($matches[0][1]).'.txt';
      if(file_exists($text_command_path)){
      unlink($text_command_path);
      $array_list = file(__DIR__.'/commands/list.txt');
      for ($i=0; $i < count($array_list); $i++) { 
        if($array_list[$i] == $matches[0][1]."\n")
        {
          unset($array_list[$i]);
          break;
        }
      }
      file_put_contents(__DIR__.'/commands/list.txt', $array_list);
      $temp = "Команда удалена";
      send_message($peer_id, $temp);
      } else 
      {

        $text_command_path = __DIR__.'/images'.'/image_'.md5($matches[0][1]).'.png';
        unlink($text_command_path);
      $array_list = file(__DIR__.'/commands/list.txt');
      for ($i=0; $i < count($array_list); $i++) { 
        if($array_list[$i] == $matches[0][1]."\n")
        {
          unset($array_list[$i]);
          break;
        }
      }
      file_put_contents(__DIR__.'/commands/list.txt', $array_list);
      $temp = "Команда с картинкой удалена";
      send_message($peer_id, $temp);

      }
      break;
    default:
      if($text[0] == '!'){
      $file_path = __DIR__.'/commands'.'/command_'.md5($text).'.txt';
      if(file_exists($file_path)){
        $temp = file_get_contents($file_path);
        send_message($peer_id, $temp);
      } else 
      {
        $file_path = __DIR__.'/images/image_'.md5($text).'.png';
        if(file_exists($file_path))
        {
          $apiresult = api('photos.getMessagesUploadServer', array(
            'peer_id' => $peer_id
          ));
          $data = array(
            'file1' => new CURLFile($file_path)
          );
          $ch = curl_init($apiresult['upload_url']);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          $curl_result = curl_exec($ch);
          curl_close($ch);
          $decoded_result = json_decode($curl_result);
          try
          {
          $array_photos = api('photos.saveMessagesPhoto', array(
            
            'photo' => $decoded_result->photo,
            'server' => $decoded_result->server,
            'hash' => $decoded_result->hash
            
          )); } 
          catch(Exception $e)
          {
            send_message($peer_id, 'Exception');
          }
          
          api('messages.send',array( 
    'peer_id' => $peer_id, 
    'attachment' => 'photo'.$array_photos[0]['owner_id'].'_'.$array_photos[0]['id']));
        }
      }
      }
        break;
    }
      header("HTTP/1.1 200 OK"); 
      echo('ok');
      exit();  
    break; 
  
  default: 
    echo('Unsupported event'); 
    break; 
} 

function send_message($peer_id, $message) { 
  api('messages.send', array( 
    'peer_id' => $peer_id, 
    'message' => $message, 
  )); 
} 

function api($method, $params) { 
  $params['access_token'] = VK_API_ACCESS_TOKEN; 
  $params['v'] = VK_API_VERSION; 
  $query = http_build_query($params); 
  $url = VK_API_ENDPOINT . $method . '?' . $query; 
  $curl = curl_init($url); 
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
  $json = curl_exec($curl); 
  $error = curl_error($curl); 
  if ($error) { 
    error_log($error); 
    throw new Exception("Failed {$method} request"); 
  } 
  curl_close($curl); 
  $response = json_decode($json, true); 
  if (!$response || !isset($response['response'])) { 
    error_log($json); 
    throw new Exception("Invalid response for {$method} request"); 
  } 
  return $response['response']; 
}
?>