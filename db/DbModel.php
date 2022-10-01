<?php

namespace app\core\db;

use app\core\Application;
use app\core\Model;

abstract class DbModel extends Model
{
    abstract public static function tableName() :string;
    abstract public function attributes() :array;
    abstract public static function primaryKey() :string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $fields = implode(',',$attributes);
        $params = implode(',',array_map(fn($at) => ":$at", $attributes));
        $sql = "INSERT INTO $tableName($fields) VALUES ($params);";
        $statement = self::prepare($sql);
        foreach($attributes as $attribute){
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    public static function findOne($where) //[email => email@mail.com, fname => userName]
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        // SELECT * FROM $tablename WHERE email =:email AND fname=:fname;
        $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql;");
        foreach($where as $key => $item){
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);

    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}
