<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates;

use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\Template;

class OA_Security extends Template {

    protected $template_string='
/**
/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )

 */';



    protected function genereate($data)
    {
      return $this->template_string;
    }
}
