<?php
namespace App\Services;

interface AIModel{

    function prompt($question);
    function summarize($resource);
    function compare($first_resource,$other_resource);

}