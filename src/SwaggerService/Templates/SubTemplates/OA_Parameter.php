<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates;

use function Couchbase\defaultDecoder;

class OA_Parameter extends \OmidZahed\LaravelRequestDocs\SwaggerService\Templates\Template
{


    protected $template_string_path_parameter= '
 *  @OA\Parameter(
 *         description="{description}",
 *         in="path",
 *         name="{name}",
 *         required=true,
 *     ),
 ';

    protected $validates_type;



    protected function genereate($data)
    {
       $param_url= $this->genereate_param_in_url($data);
        return $param_url;
       }
    protected function genereate_param_in_url($data){

        $parameter_url=$this->get_param_in_url($data["uri"]);
        $string_result="";
        foreach ($parameter_url as $param){
            $data_replace=[
                "{name}"=>$param,
                "{description}"=>$param
            ];
            $string_result.= str_replace(array_keys($data_replace),array_values($data_replace),$this->template_string_path_parameter);

        }

        return $string_result;
    }
    private function get_param_in_url($url){
        preg_match_all("/(?<=\{)[^\}]+(?=\})/",$url,$params_raw,PREG_OFFSET_CAPTURE);
        $params_raw=$params_raw[0];
        $params=[];
        foreach ($params_raw as $param){
            if (count($param))$params[]=$param[0];
        }
        return $params;
    }





}
