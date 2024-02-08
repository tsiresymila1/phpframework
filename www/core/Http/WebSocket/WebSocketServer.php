<?php

namespace Core\Http\WebSocket;

class WebSocketServer
{

    private $events = [];
    private $clients = [];

    public function __construct($port = 4445, $address = '0.0.0.0')
    {
        $this->address = $address;
        $this->port = $port;
        $this->server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    public function On($event, $callback)
    {
        $this->add($event, $callback);
    }

    private function add($key, $value)
    {
        $this->events[$key] = $value;
    }

    private function get($key)
    {
        if (array_key_exists($key, $this->events)) {
            return $this->events[$key];
        } else {
            return null;
        }
    }



    private function onConnect($client)
    {
        $callback = $this->get('connect');
        if ($callback) {
            call_user_func_array($callback, [$client]);
        }
    }

    private function onDisconnect($client)
    {
        $callback = $this->get('disconnect');
        if ($callback) {
            call_user_func_array($callback, [$client]);
        }
    }

    private function onMessage($client, $message)
    {
        $json_data = (array)json_decode($message);
        if (is_array($json_data) && key_exists('event', $json_data) && key_exists('data', $json_data)) {
            $event = $json_data['event'];
            $data = $json_data['data'];
            $callback = $this->get($event);
            if ($callback) {
                call_user_func_array($callback, [$client, $data]);
            }
        }
    }

    private function onError($error)
    {
        $callback = $this->get('error');
        if ($callback) {
            call_user_func_array($callback, [$error]);
        }
    }

    private function formatPrint(array $format = [], string $text = '')
    {
        $codes = [
            'bold' => 1,
            'italic' => 3, 'underline' => 4, 'strikethrough' => 9,
            'black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37,
            'blackbg' => 40, 'redbg' => 41, 'greenbg' => 42, 'yellowbg' => 44, 'bluebg' => 44, 'magentabg' => 45, 'cyanbg' => 46, 'lightgreybg' => 47
        ];
        $formatMap = array_map(function ($v) use ($codes) {
            return $codes[$v];
        }, $format);
        echo "\e[" . implode(';', $formatMap) . 'm' . $text . "\e[0m";
    }
    private function formatPrintLn(array $format = [], string $text = '')
    {
        $this->formatPrint($format, $text);
        echo "\r\n";
    }

    private function echoSuccess($message)
    {
        $this->formatPrintLn(['bold', 'green', 'underline'], $message);
    }
    private function echoError($error)
    {
        $this->formatPrintLn(['red'], $error);
    }
    private function echoInfo($info)
    {
        $this->formatPrintLn(['cyan'], $info);
    }


    public function start()
    {
        $this->echoInfo("\nWebsocket server starting ..... Waiting");
        $server = stream_socket_server("tcp://{$this->address}:{$this->port}", $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);
        if (!$server) {
            $this->echoError("$errstr ($errno)");
            $this->onError($errstr);
        } else {
            $this->echoSuccess("Websocket server ..... Started");
            $this->clients = array($server);
            $write  = NULL;
            $except = NULL;
            while (true) {
                $changed = $this->clients;

                stream_select($changed, $write, $except, 10);

                if (in_array($server, $changed)) {

                    $client = @stream_socket_accept($server);
                    if (!$client) {
                        continue;
                    }
                    $this->clients[] = $client;
                    $ip = stream_socket_get_name($client, true);
                    $this->echoInfo("Client connected from $ip");
                    stream_set_blocking($client, true);
                    $headers = fread($client, 1500);
                    $this->handshake($client, $headers, $this->address, $this->port);
                    stream_set_blocking($client, false);
                    $this->onConnect(new WebSocketClientInstance($client, $this->clients));
                    $found_socket = array_search($server, $changed);
                    unset($changed[$found_socket]);
                }

                foreach ($changed as $changed_socket) {
                    $ip = stream_socket_get_name($changed_socket, true);
                    $buffer = stream_get_contents($changed_socket);
                    $client = new WebSocketClientInstance($changed_socket, $this->clients);
                    if (!$buffer) {
                        $this->echoError("Client Disconnected from $ip");
                        $this->onDisconnect($client);
                        @fclose($changed_socket);
                        $found_socket = array_search($changed_socket, $this->clients);
                        unset($this->clients[$found_socket]);
                    }
                    $unmasked = $this->unmask($buffer);
                    if ($unmasked != "") {
                        // $this->echoInfo("Message from $ip => $unmasked ");
                        $this->onMessage($client, $unmasked);
                    };
                }
            }
            fclose($server);
            $this->echoError("Web socket server ... Closed");
        }
    }

    private function unmask($text)
    {
        $length = @ord($text[1]) & 127;
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
        return $text;
    }



    private function handshake($client, $rcvd, $host, $port)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $rcvd);
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        //hand shaking header
        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ws://$host:$port\r\n" .
            "Sec-WebSocket-Version: 13\r\n" .
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        @fwrite($client, $upgrade);
    }
}


class WebSocketClientInstance
{
    private $client;
    private $clients = [];

    public function __construct($client, $clients = [])
    {
        $this->client = $client;
        $this->clients = $clients;
    }

    private function mask($text)
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        if ($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif ($length > 125 && $length < 65536)
            $header = pack('CCn', $b1, 126, $length);
        elseif ($length >= 65536)
            $header = pack('CCNN', $b1, 127, $length);
        return $header . $text;
    }

    public function send($event, $data)
    {
        $message = ["event" => $event, "data" => $data];
        @fwrite($this->client, $this->mask(json_encode($message)));
    }

    public function emit($data)
    {
        $message = ["event" => 'broadcast', "data" => $data];
        foreach ($this->clients as $changed_socket) {
            @fwrite($changed_socket, $this->mask(json_encode($message)));
        }
    }
}
