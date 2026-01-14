<?php
/**
 * Simple SMTP Mail Class for Gmail
 * Works without PHPMailer - uses PHP sockets for SMTP
 */

class SimpleSMTP {
    private $host;
    private $port;
    private $secure;
    private $username;
    private $password;
    private $socket;
    
    public function __construct($host, $port, $secure, $username, $password) {
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function connect() {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $host = ($this->secure === 'ssl') ? 'ssl://' . $this->host : $this->host;
        
        // Use stream_socket_client for better SSL/TLS support
        $this->socket = @stream_socket_client(
            $host . ':' . $this->port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$this->socket) {
            return false;
        }
        
        stream_set_timeout($this->socket, 30);
        $response = fgets($this->socket, 515);
        return substr($response, 0, 3) == '220';
    }
    
    public function sendCommand($command, $expectedCode) {
        fputs($this->socket, $command . "\r\n");
        $response = fgets($this->socket, 515);
        return substr($response, 0, 3) == $expectedCode;
    }
    
    public function authenticate() {
        // EHLO
        if (!$this->sendCommand("EHLO " . $this->host, "250")) {
            return false;
        }
        
        // Read all EHLO responses
        while ($line = fgets($this->socket, 515)) {
            if (substr($line, 3, 1) == " ") break;
        }
        
        // STARTTLS
        if ($this->secure === 'tls') {
            if (!$this->sendCommand("STARTTLS", "220")) {
                return false;
            }
            
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            // EHLO again after TLS
            if (!$this->sendCommand("EHLO " . $this->host, "250")) {
                return false;
            }
            
            while ($line = fgets($this->socket, 515)) {
                if (substr($line, 3, 1) == " ") break;
            }
        }
        
        // AUTH LOGIN
        if (!$this->sendCommand("AUTH LOGIN", "334")) {
            return false;
        }
        
        // Username
        if (!$this->sendCommand(base64_encode($this->username), "334")) {
            return false;
        }
        
        // Password
        if (!$this->sendCommand(base64_encode($this->password), "235")) {
            return false;
        }
        
        return true;
    }
    
    public function send($fromEmail, $fromName, $toEmail, $subject, $message) {
        // MAIL FROM
        if (!$this->sendCommand("MAIL FROM: <" . $fromEmail . ">", "250")) {
            return false;
        }
        
        // RCPT TO
        if (!$this->sendCommand("RCPT TO: <" . $toEmail . ">", "250")) {
            return false;
        }
        
        // DATA
        if (!$this->sendCommand("DATA", "354")) {
            return false;
        }
        
        // Email headers and body
        $emailData = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
        $emailData .= "To: <" . $toEmail . ">\r\n";
        $emailData .= "Subject: " . $subject . "\r\n";
        $emailData .= "MIME-Version: 1.0\r\n";
        $emailData .= "Content-Type: text/html; charset=UTF-8\r\n";
        $emailData .= "\r\n";
        $emailData .= $message . "\r\n";
        $emailData .= ".\r\n";
        
        fputs($this->socket, $emailData);
        $response = fgets($this->socket, 515);
        
        if (substr($response, 0, 3) != "250") {
            return false;
        }
        
        // QUIT
        $this->sendCommand("QUIT", "221");
        
        return true;
    }
    
    public function close() {
        if ($this->socket) {
            fclose($this->socket);
        }
    }
}

