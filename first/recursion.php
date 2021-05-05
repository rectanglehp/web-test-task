<?php

$templates = array(
    array("Id"=>1,"ParentId"=>0,"Atribut"=>"","Text"=>"Корневой шаблон / <%TITLE%>"),
    array("Id"=>2,"ParentId"=>1,"Atribut"=>"<%TITLE%>","Text"=>"Первый подшаблон<br/><%CONTENT%>"),
    array("Id"=>3,"ParentId"=>1,"Atribut"=>"<%TITLE%>","Text"=>"Второй подшаблон<br/><%CONTENT%>"),
    array("Id"=>4,"ParentId"=>2,"Atribut"=>"<%CONTENT%>","Text"=>"<b>Шаблон 4</b>"),
    array("Id"=>5,"ParentId"=>3,"Atribut"=>"<%CONTENT%>","Text"=>"<b>Шаблон 5</b>"),
    array("Id"=>6,"ParentId"=>2,"Atribut"=>"<%CONTENT%>","Text"=>"<b>Шаблон 6</b>"),
    array("Id"=>7,"ParentId"=>0,"Atribut"=>"","Text"=>"Корневой шаблон №2")
);

function getTemplate($id, &$templates){
    foreach($templates as $template) {
        if($template['Id'] == $id) {
            if($template['ParentId'] !== 0) {
                $str = getTemplate($template['ParentId'], $templates);
                return str_replace($template['Atribut'], $template['Text'], $str);
            }
            return $template['Text'];
        }
    }
    return false;
}
echo getTemplate(5, $templates);

?>
