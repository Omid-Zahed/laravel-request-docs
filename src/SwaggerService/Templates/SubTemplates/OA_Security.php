<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates;

class OA_Security extends \OmidZahed\LaravelRequestDocs\SwaggerService\Templates\Template
{
    protected  $template_string=
'*    security={
 *      {"apiAuth": {}}
 *   }
  ';
    protected function genereate($data)
    {
        if (!$data["bearer"])return "";
        return $this->template_string;
    }

}
