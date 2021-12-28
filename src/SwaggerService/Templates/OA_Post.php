<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates;

use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_SubTemplate;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\Template;

class OA_Post extends Template {
    protected  $template_string='/**
 * @OA\Post(
 *      path="{path}",
 *      operationId="{operationId}",
 *      tags={{tags}},
 *      description="{description}",
 {child}
 *     )
 */
 ';
    protected function genereate($data)
    {
        $data_replace=[
            "{path}"=>"/".$data["uri"],
            "{operationId}"=>$data["uri"]."__post",
            "{tags}"=>'"'.$data["controller"].'"',
            "{description}"=>"get method for controller ".$data["controller"],
            "{child}"=>(new OA_SubTemplate($data))->render()
        ];
        return str_replace(array_keys($data_replace),array_values($data_replace),$this->template_string);
    }
}
