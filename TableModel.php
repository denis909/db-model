<?php

namespace denis909\db;

use Exception;

class TableModel
{

    protected $_db;

    public $tableName;

    public $primaryKey;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    public function getPrimaryKey($data)
    {
        if (is_array($this->primaryKey))
        {
            $return = [];

            foreach($this->primaryKey as $column)
            {
                if (!$data->$column)
                {
                    return null;
                }

                $return[$column] = $data->$column;
            }

            return $return;
        }
        
        $return = $data->{$this->primaryKey};

        if (!$return)
        {
            return null;
        }

        return $return;
    }

    public function wherePrimaryKey($pk)
    {
        if (is_array($pk))
        {
            return $pk;
        }

        if (is_array($this->primaryKey))
        {
            throw new Exception('Primary key is not valid.');
        }

        return [$this->primaryKey => $pk];
    ]

    public function findByPk($pk)
    {
        return $this->db->findOne($this->tableName, $this->wherePrimaryKey($pk));
    }

    public function insert($data)
    {
        return $this->_db->insert($this->tableName, (array) $data);
    }

    public function update($data)
    {
        $pk = $this->getPrimaryKey($data);

        if (!$pk)
        {
            throw new Exception('Primary key is not defined.');

        }

        return $this->_db->update($this->tableName, (array) $data, $this->wherePrimaryKey($pk));
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

    public function delete($data)
    {
        $pk = $this->getPrimaryKey($data);

        if (!$pk)
        {
            throw new Exception('Primary key is not defined.');
        }

        return $this->_db->delete($this->tableName, $this->wherePrimaryKey($pk));
    }
    
}