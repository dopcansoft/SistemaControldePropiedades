<?
	set_time_limit(0);
	$i=0;
	$host = "127.0.0.1";
	$port = "25003";
	$socket = socket_create(AF_INET, SOCK_STREAM, 0);//getprotobyname('tcp'));
	socket_bind($socket, $host, $port) or die("Error al vincular socket con ip en ese cliente");
	echo socket_strerror(socket_last_error());
	socket_listen($socket);
	socket_accept($socket);
	$i=0;
	/*while(true){
		//$client[++$i] = socket_accept($socket);
		$message = json_decode(socket_read($client[$i], 1024));
		echo $message["message"];
		$response = array(
				"result"=>"SUCCESS",
				"message"=>"Mensaje recibido: ".$message,
				"data"=>"datos"
		);
		$mensajejson = json_encode($response);
		$message = "Hola usuario: ".$message["user"]."\n";
		socket_write($client[$i], $mensajejson, 1024);
		socket_close($client[$i]); 
	}
	socket_close($socket);*/
	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/deamon.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
?>