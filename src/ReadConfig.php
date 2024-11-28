<?php

class readConfig
{
    const fileRead = "..\Config.ini";

    public $configDB;

    public function __construct()
    {            
        $this->ReadFileConfig();
    }

    public function ReadFileConfig()
    {
        try {
            if (file_exists(self::fileRead)) {
                $this->configDB = parse_ini_file(self::fileRead);
            }
        } catch (\Exception $e) {
            echo "Error : Caminho incorreto ou arquivo inexistente para ".self::fileRead;
        }
    }
}