<?php
class Database {
    private string $host='localhost', $db='air_cleaning', $user='root', $pass='';
    public function connect(): PDO {
        return new PDO("mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",$this->user,$this->pass,[
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
        ]);
    }
}
?>