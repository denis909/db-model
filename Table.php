<?php

namespace Denis909\Db;

use Exception;

abstract class Table
{
    
    protected $_db;

    public function __construct(Db $db)
    {
        $this->_db = $db;
    }

    abstract static function tableName();

    abstract static function primaryKey();

    public function getPrimaryKey($data)
    {
        $primaryKey = static::primaryKey();

        if (is_array($primaryKey))
        {
            $return = [];

            foreach($primaryKey as $column)
            {
                if (!$data->$column)
                {
                    return null;
                }

                $return[$column] = $data->$column;
            }

            return $return;
        }
        
        $return = $data->{$primaryKey};

        if (!$return)
        {
            return null;
        }

        return $return;
    }

    public function wherePrimaryKey($pk)
    {
        $primaryKey = static::primaryKey();

        if (is_array($pk))
        {
            return $pk;
        }

        if (is_array($primaryKey))
        {
            throw new Exception('Primary key not valid.');
        }

        return [$primaryKey => $pk];
    }

    public function findByPk($pk)
    {
        return $this->_db->findOne(static::tableName(), $this->wherePrimaryKey($pk));
    }

    public function insert($data)
    {
        return $this->_db->insert(static::tableName(), (array) $data);
    }

    public function update($data)
    {
        $pk = $this->getPrimaryKey($data);

        if (!$pk)
        {
            throw new Exception('Primary key not defined.');
        }

        return $this->_db->update(static::tableName(), $data, $this->wherePrimaryKey($pk));
    }    

    public function save($data)
    {
        $pk = $this->getPrimaryKey($data);

        if ($pk)
        {
            return $this->update($data);
        }
        else
        {
            return $this->insert($data);
        }
    }

    public function delete($data) : bool
    {
        $pk = $this->primaryKey($data);

        if (!$pk)
        {
            throw new Exception('Primary key is not defined.');
        }

        return $this->_db->delete(static::tableName(), $this->wherePrimaryKey($pk));
    }

    public function findAll($where = null, $params = [], $suffix = null) : array
    {
        return $this->_db->findAll(static::tableName(), $where, $params, $suffix);
    }

    public function findOne($where = null, $params = [], $suffix = '')
    {
        return $this->_db->findOne(static::tableName(), $where, $params, $suffix);
    }
    
}