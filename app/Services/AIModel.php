<?php
namespace App\Services;

interface AIModel{

    function prompt($question);
    function summarize($resource,$language=null);
    function compare($first_resource,$other_resource);

}