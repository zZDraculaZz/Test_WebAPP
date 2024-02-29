<?php

class SQLiteDB implements DBInterface
{
    public static $instance = null;
    private SQLite3 $db;

    private function __construct($db_name)
    {
        $this->db = new SQLite3($db_name, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->db->query
        (
            "CREATE TABLE IF NOT EXISTS \"users\"
            (
                'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                'email' VARCHAR UNIQUE NOT NULL,
                'login' VARCHAR UNIQUE NOT NULL,
                'phone' INTEGER UNIQUE NOT NULL, 
                'password' VARCHAR NOT NULL
            )"
        );
    }
    public static function getInstance($db_name): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new SQLiteDB($db_name);
        }
        return self::$instance;
    }
    public function createUser($email, $login, $password, $phone)
    {
        $this->db->exec("BEGIN");
        $statement = $this->db->prepare("INSERT INTO \"users\" (\"email\", \"login\", \"password\", \"phone\" )
                                            VALUES (:email, :login, :password, :phone)");
        $statement->bindValue(':email', $email);
        $statement->bindValue(':login', $login);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':phone', $phone);
        $statement->execute();
        $this->db->exec("COMMIT");
    }
    public function takeData($select, $table_name, $params=[], $add_params=''): array
    {
        $statement = "SELECT {$select} FROM \"{$table_name}\" ";
        if (!empty ($params))
        {
            $i = 0;
            $statement = $statement . 'WHERE ';
            foreach ($params as $column => $data)
            {
                if ($i != count($params)-1)
                {
                    $statement= $statement . "\"{$column}\" = :$column AND ";
                }
                else
                {
                    $statement= $statement . "\"{$column}\" = :$column";
                }
                $i++;
            }
            if(!empty($add_params)){
                $statement = $statement . " $add_params";
            }

            $statement = $this->db->prepare($statement);
            foreach ($params as $column => $data)
            {
                $statement->bindValue(":$column", $data);
            }

        }

        if(!empty($add_params) and empty($params))
        {
            $statement = $statement . " $add_params";
            $statement = $this->db->prepare($statement);
        }

        if(empty($add_params) and empty($params))
        {
            $statement = $this->db->prepare($statement);
        }
        $result = $statement->execute();

        $result_array = $result->fetchArray(SQLITE3_ASSOC);
        $multi_array = array();

        while($result_array !== false)
        {
            array_push($multi_array, $result_array);
            $result_array = $result->fetchArray(SQLITE3_ASSOC);
        }
        return $multi_array;
    }
    public function takeArrayData($table_name, $column, $column_id)
    {
            return $this->db->query("SELECT * FROM \"{$table_name}\" WHERE \"{$column}\" = \"{$column_id}\"")->fetchArray(SQLITE3_ASSOC);
    }
    public function updateData($table_name, $column1, $data, $column2, $column2_id): void
    {
        if ($this->takeArrayData($table_name, $column2, $column2_id))
        {
            $this->db->exec("BEGIN");
            $statement = $this->db->prepare("UPDATE \"{$table_name}\" SET \"{$column1}\" = ? 
                                            WHERE \"{$column2}\" = \"{$column2_id}\" ");
            $statement->bindValue(1, $data);
            $statement->execute();
            $this->db->exec("COMMIT");
        }
    }
}