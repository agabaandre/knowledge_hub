<?php




if (!function_exists('read_csv')) {
    function read_csv($path){

        $row = 1;
        $file_data = array();

        if (($handle = fopen($path, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
               
               $file_data[]=$data;
            }

            fclose($handle);
            unlink($path);
         }

         return $file_data;
    }
}


//set flash data
if (!function_exists('make_pdf')) {
    function make_pdf($html,$file_name,$action="D",$landScape=false)
    {
        // Get a reference to the controller object
        $ci =& get_instance();

      
        if($landScape):
            $ci->load->library('ml_pdf',NULL, 'mpdf');
        else:
            $ci->load->library('m_pdf',NULL, 'mpdf');
        endif;


        $PDFContent = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        //$ci->mpdf->pdf->SetWatermarkImage($ci->watermark);
        //$ci->mpdf->pdf->showWatermarkImage = true;

        date_default_timezone_set("Africa/Kampala"); 
        //$ci->mpdf->pdf->SetWatermarkImage($ci->watermark);
        //$ci->mpdf->showWatermarkImage = true;
        ini_set('max_execution_time',0);
        @$ci->mpdf->pdf->WriteHTML($PDFContent); 

        @$ci->mpdf->pdf->SetHTMLFooter("<br><br>Printed/ Accessed on: <b>".date('d F,Y h:i A')."</b><br style='font-size: 9px !imporntant;'>"." Source: ".setting()->title."<br>" .base_url());
        //download it D save F, on in browser I
        @$ci->mpdf->pdf->Output($file_name.".pdf",$action);
        
    }
}
