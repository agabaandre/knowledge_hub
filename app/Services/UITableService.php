<?php
namespace App\Services;

class UITableService{


    private $phpGrid = null;
    private $gridOpts=[];

    public function __construct()
    {
        require_once PHPGRID_LIBPATH.'inc/jqgrid_dist.php';
        
        $this->phpGrid = new \jqgrid(config('uitable.db_config'));
        $this->gridOpts["forceFit"]  = true;
        $this->gridOpts["autowidth"] = true;
        $this->gridOpts["toolbar"]   = "both";
        //$this->gridOpts["multiselect"] = true;
        $this->gridOpts["altRows"] = true;
        $this->gridOpts["edit_options"]["checkOnSubmit"] = true;
        $this->gridOpts["edit_options"]["position"] = "center";

        $this->gridOpts["add_options"] = array('width'=>'820');
        $this->gridOpts["edit_options"] = array('width'=>'820');
        $this->gridOpts["view_options"] = array('width'=>'820');

        
        $this->phpGrid->set_actions(array(
            "add"=>true,
           // "edit"=>true,
             "clone"=>true,
            //"bulkedit"=>true,
            "delete"=>true,
            "view"=>true,
            "rowactions"=>true,
            "export"=>true,
            "import"=>true,
            "autofilter" => true,
            "search" => "simple",
            //"inlineadd" => true,
            "showhidecolumns" => true
        ));
        
    }


    function get_ui_table($table_name=null,$columns=false,$query=false,$options=[]){

        //ini_set('memory_limit', '5024M');

        $g    = $this->phpGrid;
        $opts = $this->gridOpts;
        $g_options = array_merge($opts,$options);

        if($columns)
        $g->set_columns($columns);

        if($query)
        $g->select_command=$query;

        $g->set_options($g_options);
        $g->table = $table_name;

        $out = $g->render("list1");

        return $out;
    }

}