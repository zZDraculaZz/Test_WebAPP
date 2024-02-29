<?php
interface DBInterface
{
    // singleton
    public static function getInstance($db_name);
    public function createUser($email, $login, $password, $phone);
    public function takeData($select, $table_name, $params=[], $add_params='');
    public function takeArrayData($table_name, $column, $column_id);
    public function updateData($table_name, $column1, $data, $column2, $column2_id);
}