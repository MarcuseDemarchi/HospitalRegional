<?php

require_once "ConnectionDB.php";

class QuerySQL extends Connection
{

    public function consultSQL($sSQL)
    {
        $result = pg_query($this->connectionDB,$sSQL);

        $aLinhas = [];

        while ($row = pg_fetch_assoc($result)){                
            $aLinhas[] = $row['pergunta'];
        }

        return $aLinhas;
    }

    public function insertSQL($table,$aColumns,$aValues)
    {
        $result = pg_query($this->connectionDB,$this->mountInsert($table,$aColumns,$aValues));
    }

    public function deleteRegister(string $sRegister)
    {
        
    }

    public function mountInsert($table,$aColumns,$aValues)
    {
        $insertColumns = "(" . implode(", ", $aColumns) . ")";
        
        $formattedValues = array_map(function($value) {
            return is_string($value) ? "'$value'" : $value;
        }, $aValues);

        $insertValues = "VALUES (" . implode(", ", $formattedValues) . ")";
        return "INSERT INTO {$table} {$insertColumns} {$insertValues};";
    }
}