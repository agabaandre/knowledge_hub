<?php
//render-front-main website
if (!function_exists('has_pending_transaction')) {

    function has_pending_transaction(){

       $ci =& get_instance();
      $pending_trans = $ci->transactionsmodel->get(['client_id'=>user_session()->id,'tran_status'=>'Pending']);

      return (count($pending_trans) > 0);
    }
}
