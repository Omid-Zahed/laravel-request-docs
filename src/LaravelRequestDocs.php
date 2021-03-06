<?php

namespace OmidZahed\LaravelRequestDocs;


use Route;
use ReflectionMethod;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Exception;

class LaravelRequestDocs
{

    public function getDocs()
    {

        $docs = [];
        $excludePatterns = config('request-docs.hide_matching') ?? [];
        $controllersInfo = $this->getControllersInfo();
        $controllersInfo = $this->appendRequestRules($controllersInfo);
        foreach ($controllersInfo as $controllerInfo) {
            try {
                $exclude = false;
                foreach ($excludePatterns as $regex) {
                    $uri = $controllerInfo['uri'];
                    if (preg_match($regex, $uri)) {
                        $exclude = true;
                    }
                }
                if (!$exclude) {
                    $docs[] = $controllerInfo;
                }
            } catch (Exception $exception) {
                continue;
            }
        }


        return array_filter($docs);
    }

    public function sortDocs(array $docs, $sortBy = 'default'): Array
    {
        if ($sortBy === 'route_names') {
            sort($docs);
            return $docs;
        }
        $sorted = [];
        foreach ($docs as $key => $doc) {
            if (in_array('GET', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('POST', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('PUT', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('PATCH', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        foreach ($docs as $key => $doc) {
            if (in_array('DELETE', $doc['methods'])) {
                $sorted[] = $doc;
            }
        }
        return $sorted;
    }

    protected function hasAuthToken($route){
        $route_middleware=!is_array($route->action['middleware']) ? [$route->action['middleware']] : $route->action['middleware'];

        $middleware_need_token= config("request-docs.middleware_need_token",['auth:api']);

        foreach ($middleware_need_token as $middleware):
            if (in_array($middleware,$route_middleware))return true;
        endforeach;
    }
    public function getControllersInfo(): Array
    {
        $controllersInfo = [];
        $routes = collect(Route::getRoutes());
        foreach ($routes as $route) {
            try {
                /// Show Pnly Controller Name
                $controllerFullPath = explode('@', $route->action['controller'])[0];
                $getStartWord = strrpos(explode('@', $route->action['controller'])[0], '\\') + 1;
                $controllerName = substr($controllerFullPath, $getStartWord);

                /// Has Auth Token
                $hasAuthToken = $this->hasAuthToken($route);
                //get class info
                $method= explode('@', $route->action['controller'])[1];
                $rc = new \ReflectionClass($controllerFullPath);

                $document_text=$rc->getMethod($method)->getDocComment();

                $document="";
                if($document_text){
                    $regex="/(?<=<swagger>)(.|\w\s)+(?=<\/swagger>)/";
                    preg_match($regex, $document_text, $matches);
                    $document=$matches[0]??"";
                }
//                if($controllerName=="LeitnerController" and $method=="store"){
////                dd($document_text,$document);
//                }


                $controllersInfo[] = [
                    "method_doc"=>$document,
                    'uri'                   => $route->uri,
                    'methods'               => $route->methods,
                    'middlewares'           => !is_array($route->action['middleware']) ? [$route->action['middleware']] : $route->action['middleware'],
                    'controller'            => $controllerName,
                    'controller_full_path'  => $controllerFullPath,
                    'method'                =>$method,
                    'rules'                 => [],
                    'docBlock'              => $document,
                    'bearer'                => $hasAuthToken
                ];
            } catch (Exception $e) {
                continue;
            }
        }

        return $controllersInfo;
    }

    public function appendRequestRules(Array $controllersInfo)
    {
        foreach ($controllersInfo as $index => $controllerInfo) {
            $controller       = $controllerInfo['controller_full_path'];
            $method           = $controllerInfo['method'];
            $reflectionMethod = new ReflectionMethod($controller, $method);
            $params           = $reflectionMethod->getParameters();

            foreach ($params as $param) {
                if (!$param->getType()) {
                    continue;
                }
                $requestClassName = $param->getType()->getName();
                $requestClass = null;
                try {
                    $requestClass = new $requestClassName();
                } catch (\Throwable $th) {
                    //throw $th;
                }
                if ($requestClass instanceof FormRequest) {
                    $controllersInfo[$index]['rules'] = $this->flattenRules($requestClass->rules());
                    $controllersInfo[$index]['docBlock'] = $this->lrdDocComment($reflectionMethod->getDocComment());
                }
            }
        }
        return $controllersInfo;
    }

    public function lrdDocComment($docComment): string
    {
        $lrdComment = "";
        $counter = 0;
        foreach (explode("\n", $docComment) as $comment) {
            $comment = trim($comment);
            // check contains in string
            if (Str::contains($comment, '@lrd')) {
                $counter++;
            }
            if ($counter == 1 && !Str::contains($comment, '@lrd')) {
                if (Str::startsWith($comment, '*')) {
                    $comment = trim(substr($comment, 1));
                }
                // remove first character from string
                $lrdComment .= $comment . "\n";
            }
        }
        return $lrdComment;
    }

    // get text between first and last tag
    private function getTextBetweenTags($docComment, $tag1, $tag2)
    {
        $docComment = trim($docComment);
        $start = strpos($docComment, $tag1);
        $end = strpos($docComment, $tag2);
        $text = substr($docComment, $start + strlen($tag1), $end - $start - strlen($tag1));
        return $text;
    }

    public function flattenRules($mixedRules)
    {
        $rules = [];
        foreach ($mixedRules as $attribute => $rule) {
            if (is_object($rule)) {
                $rule = get_class($rule);
                $rules[$attribute][] = $rule;
            } else if (is_array($rule)) {
                $rulesStrs = [];
                foreach ($rule as $ruleItem) {
                    if (is_object($ruleItem)) {
                        method_exists($ruleItem, '__toString') ? $rulesStrs[] = $ruleItem->__toString() : $rulesStrs[] = get_class($ruleItem);
                    } else {
                        $rulesStrs[] = $ruleItem;
                    }

                }
                $rules[$attribute][] = implode("|", $rulesStrs);
            } else {
                $rules[$attribute][] = $rule;
            }
        }
        return $rules;
    }
}
