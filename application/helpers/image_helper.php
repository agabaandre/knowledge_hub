<?php


if(!function_exists('apply_water_mark')){

 function apply_water_mark($imagepath,$vertical="bottom",$horizontal="left"){

		$config['image_library'] = 'gd2';
		$config['source_image'] = $imagepath;
		//$config['wm_text'] = ' Uganda';
		$config['wm_type'] = 'overlay';
		$config['wm_overlay_path'] = './assets/images/daswhite.png';             
		//$config['wm_font_color'] = 'ffffff';
		$config['wm_opacity'] = 40;
		$config['wm_vrt_alignment'] = 'bottom';
		$config['wm_hor_alignment'] = 'left';
		//$config['wm_padding'] = '50';

		$ci =& get_instance();

		$ci->load->library('image_lib');
		$ci->image_lib->initialize($config);

		return $ci->image_lib->watermark();
	}

}


?>