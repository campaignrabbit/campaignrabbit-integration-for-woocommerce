<?php

namespace CampaignRabbit\WooIncludes\Helper;

class FileHandler{

    private $file_name;

    public function __construct()
    {
        $this->file_name=plugin_dir_path(__FILE__) . 'logger/log.txt';
    }

    public function append($data){
        $handle = fopen($this->file_name, 'a');
        if ($handle) {
            fwrite($handle, "\n".$data);
        } else {
            $contents = "\n".'Log Created';
            //Save our content to the file.
            file_put_contents($this->file_name, $contents);
        }
    }

    public function getAsArray(){

        $data=file($this->file_name, FILE_IGNORE_NEW_LINES);;

        return $data;
    }

    public function exists(){
        return file_exists($this->file_name);
    }


}