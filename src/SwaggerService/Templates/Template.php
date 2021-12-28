<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates;

abstract class Template
{
    protected $data;
    public function __construct($data)
    {
       $this->data=$data;
    }
    public function setData($data){
        $this->data=$data;
        return $this;
    }
    public function getData(){return $this->data;}
    public function render(){
        return $this->genereate($this->data);
    }
    abstract protected function genereate($data);
}
