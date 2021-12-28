<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates;

use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates\OA_Parameter;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates\OA_RequestBody;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates\OA_Response;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates\OA_Security;

class OA_SubTemplate extends Template {

    protected function genereate($data)
    {

        $oa_security=(new OA_Security($data))->render();
        $oa_response=(new OA_Response($data))->render();
        $parameters=(new OA_Parameter($data))->render();
        $body=(new OA_RequestBody($data))->render();
        return $oa_response .$parameters. $body.$oa_security;
    }
}
