<?php
namespace App\Services;

interface AIModel{

    function prompt($question);
    function summarize($resource,$language=null,$additional_prompt=null);
    function compare($first_resource,$other_resource,$additional_prompt=null);

}