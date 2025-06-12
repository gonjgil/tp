<?php

class Database
{

    private $conn;

    function __construct($servername, $username, $dbname, $password)
    {
        $this->conn = new Mysqli($servername, $username, $password, $dbname) or die("Error de conexion " . mysqli_connect_error());
    }

    public function query($sql)
    {
        $result = $this->conn->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function execute($sql)
    {
        $this->conn->query($sql);
    }

    function __destruct()
    {
        $this->conn->close();
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function getConnection(): mysqli
    {
        return $this->conn;
    }

}