<?php

namespace OmidZahed\LaravelRequestDocs\SwaggerService;

class helper
{
    public static function get_controller_file_path_from_controller_dir($object){
     return   str_replace("App\Http\Controllers\\","",$object["controller_full_path"]);
    }

}
