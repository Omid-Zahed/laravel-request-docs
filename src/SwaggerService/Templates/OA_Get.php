<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates;

class OA_Get extends Template {
    protected  $template_string='/**
 * @OA\Get(
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
        $description="get method for controller ".$data["controller"];
        if(!empty($data["method_doc"])) {  $description=$data["method_doc"];}
        $data_replace=[
            "{path}"=>"/".$data["uri"],
            "{operationId}"=>$data["uri"]."__get",
            "{tags}"=>'"'.$data["controller"].'"',
            "{description}"=>$description,
            "{child}"=>(new OA_SubTemplate($data))->render()
        ];
        return str_replace(array_keys($data_replace),array_values($data_replace),$this->template_string);
    }
}
