<?php
namespace OmidZahed\LaravelRequestDocs\SwaggerService\Templates\SubTemplates;


class OA_RequestBody extends \OmidZahed\LaravelRequestDocs\SwaggerService\Templates\Template
{

    protected  $property_template='
*               @OA\Property(
*                   property="{property}",
*                   description="{description}",
*                   type="{type}",
*                   {item}
*               ),

    ';
    protected  $template_string='
*   @OA\RequestBody(
*
*       @OA\MediaType(
*           mediaType="application/x-www-form-urlencoded",
*           @OA\Schema(
*               type="object",
*               required={{required}},
*               {property}
*           )
*       )
*   ),
 ';


    protected $validates_type;


    public function __construct($data)
    {
        $this->validates_type=[
            "string"=>"string",
            "digits"=>"integer",
            "date"=>["string",'format="date-time",'],
            "array"=>function(){
                $item='@OA\Items('.PHP_EOL.'*                 type="string",'.PHP_EOL.'*             )';
                return ["array",$item];
            },
            "unknown"=>function($validate,$string){
                $rule=explode(":",$string);
                if (count($rule)>=2){
                    switch ($rule[0]):
                        case "exists":
                            return "string";
                        case "date_format":
                            return ["date",'format="date-time",'];
                        case "digits_between":
                            $numbers=explode(",",$rule[1]);
                            $more='minimum='.$numbers[0].", ".PHP_EOL.'*             maximum='.$numbers[1].PHP_EOL;
                            return ["integer",$more];
                        default:

                    endswitch;
                }
                if (is_array($validate)&& count($validate)>=2 &&$validate[1]==   "BenSampo\\Enum\\Rules\\EnumValue")return "integer";

                if ($string=="required"){
                    return "string";
                }



                return "unknown";
            }
        ] ;
        parent::__construct($data);
    }

    protected function genereate($data)
    {
        $generate_param_in_query= $this->generate_param_in_query($data);
        $params=$generate_param_in_query[0];
        $required=$generate_param_in_query[1];
        if (empty($params)) return "";
        return str_replace(["{property}","{required}"],[$params,$required],$this->template_string);
    }
    protected function generate_param_in_query($data){
        $string_result='';
        $list_of_required=""; 
        foreach ($data["rules"] as $key=>$value):
            $validate=explode("|",$value[0]);
            $type=$this->get_rules_from_text($value[0]);
            $item="";
            if (is_array($type)){
                $item=$type[1];
                $type=$type[0];
            }
            if ($type=="array")$key=$key.'[]';
            $data_replace=[
                "{property}"=>$key,
                "{description}"=>$value[0],
                
                "{type}"=>$type,
                "{item}"=>$item
            ];
            if (in_array("required",$validate)){
             $list_of_required.='"'.$key.'",';  
            }
            $string_result.= str_replace(array_keys($data_replace),array_values($data_replace),$this->property_template);
        endforeach;


        return [$string_result,$list_of_required];

    }



    private function get_rules_from_text($string){
        $validate=explode("|",$string);
        foreach ( $validate as $key=>$type):
            $type=strtolower($type);
            if (isset($this->validates_type[$type])){
                if (is_callable($this->validates_type[$type]))
                    return $this->validates_type[$type]($validate,$string);
                return $this->validates_type[$type];
            }

        endforeach;
        $unknown=$this->validates_type["unknown"]??"unknown";
        if (is_callable($unknown))return $unknown($validate,$string);
        return $unknown;

    }


}
