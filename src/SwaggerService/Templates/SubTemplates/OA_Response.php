<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates;

class OA_Response extends \Rakutentech\LaravelRequestDocs\SwaggerService\Templates\Template
{

    protected  $template_string='*     @OA\Response(
 *         response={response},
 *         description="{description}",
 *         @OA\JsonContent()
 *     ),
 ';
    protected function genereate($data)
    {
        $data_replace=[
            "{response}"=>$data["response_code"]??200,
            "{description}"=>$data["response_description"]??"response request"
        ];
        return str_replace(array_keys($data_replace),array_values($data_replace),$this->template_string);
    }

}
