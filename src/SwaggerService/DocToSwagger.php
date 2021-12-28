<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService;







use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_Delete;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_Get;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_Info;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_Post;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_Put;
use OmidZahed\LaravelRequestDocs\SwaggerService\Templates\OA_Security;
use Symfony\Component\HttpFoundation\File\File;

class DocToSwagger
{
    protected   $methods=["Post"=>OA_Post::class,"Get"=>OA_Get::class,"Put"=>OA_Put::class,"Delete"=>OA_Delete::class];
    protected $title,$version,$file_path;
    public function __construct($title,$version,$file_path)
    {
        $this->title=$title;
        $this->version=$version;
        $this->file_path=$file_path;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }
    public function setMethods( $method,$class_name): void
    {
        $this->methods[$method] = $class_name;
    }

    protected function generate($object){
       $result=null;
        foreach ($this->methods as $method=>$class):
            (!in_array(strtoupper($method),$object["methods"]))?:$result=(new $class($object))->render();
        endforeach;
       return $result;
    }
    protected function generateAll($list_object){
        $hash_list=[];
        $result=[];
        foreach ($list_object as $object):
            $hash=hash("sha256",$object["uri"]."_".$object["methods"][0]);
             if (in_array($hash,$hash_list)){continue;}
            $hash_list[]=$hash;
            $result[]=$this->generate($object);
        endforeach;

        return $result;
   }
    public function generate_file($object){
        $result_string="<?php \n";
        $swagger_docs_object=$this->generateAll($object);
        $info=(new OA_Info($this->title,$this->version))->render();
        $result_string .= implode('', $swagger_docs_object);
        $result_string.=$info;
        $result_string.=$this->get_security($object);
        if (!file_exists($this->file_path)){
            mkdir($this->file_path);
        }
        $file= fopen($this->file_path."/swagger.php","w");
        fwrite($file,$result_string);
        fclose($file);
        return true;


    }

    public function get_security($data){
       return (new OA_Security($data))->render();

    }



}
