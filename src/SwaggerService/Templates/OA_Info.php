<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates;

use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\Template;

class OA_Info extends Template {

    protected $template_string='
/**
 * @OA\Info(
 *    title="{title}",
 *    version="{version}",
 * )
 */';

    public function __construct($title,$version)
    {
        parent::__construct(["title"=>$title,"version"=>$version]);
    }

    protected function genereate($data)
    {
        $data_replace=[
            "{title}"=>$data["title"],
            "{version}"=>$data["version"],
        ];
        return str_replace(array_keys($data_replace),array_values($data_replace),$this->template_string);

    }
}
