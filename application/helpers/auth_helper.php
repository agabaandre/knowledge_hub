<?php
//render-front-main website
if (!function_exists('user_can_old')) {
    function user_can_old($slag)
    {
        $ci = &get_instance();
        $permissions = user_session()->permissions;
        foreach ($permissions  as $key => $permission) {
            if ($permission->slag == $slag)
                return true;
        }
        return false;
    }
}

if (!function_exists('user_can')) {
    function user_can($slag)
    {
        $ci = &get_instance();
        $permissions = $ci->usersmodel->userperms(user_session()->id);
        foreach ($permissions  as $key => $permission) {
            if ($permission->slag == $slag)
                if ($permission->has_permission)
                    return true;
                else
                    return false;
            else;
        }
        return false;
    }
}

if (!function_exists('user_perms')) {
    function user_perms()
    {
        $ci = &get_instance();
        $perms =  $ci->usersmodel->userperms(user_session()->id);
        foreach ($perms as $key => $perm) {
            $perms[$perm->slag] = $perm;
        }
        $_SESSION['user_perms'] = $perms;
        //die(json_encode($_SESSION['user_perms']['settings_module']));
        return $perms;
    }
}

if (!function_exists('get_capture_image')) {

    function get_capture_image()
    {

        $ci = CI_INSTANCE();

        $ci->load->helper('captcha');
        //assets/fonts/captcha/captcha.ttf

        $config = array(
            'img_path'      => 'captcha/',
            'img_url'       => base_url('captcha/'),
            'font_path'     => FCPATH . 'captcha/fonts/Raleway-Bold.ttf', //
            'img_width'     => '150',
            'img_height'    => 40,
            'word_length'   => 5,
            'font_size'     => 15,
            'img_id'        => 'capt_image',
            'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(84, 84, 84),
                'grid' => array(196, 194, 194)
            )
        );

        $cap = create_captcha($config);

        $captcha_data = array(
            'captcha_time'  => (isset($cap['time'])) ? $cap['time'] : time(),
            'ip_address'    => $ci->input->ip_address(),
            'word'          => $cap['word']
        );

        $query = $ci->db->insert_string('captcha', $captcha_data);
        $ci->db->query($query);

        return $cap['image'];
    }
}


if (!function_exists('captcha_valid')) {

    function captcha_valid()
    {

        $ci = CI_INSTANCE();

        $expiration = time() - 1600; // Two hour limit
        $ci->db->where('captcha_time < ', $expiration)
            ->delete('captcha');

        // Then see if a captcha exists:
        $sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
        $binds = array($ci->input->post('captcha'), $ci->input->ip_address(), $expiration);
        $query = $ci->db->query($sql, $binds);
        $row = $query->row();

        return ($row->count == 0) ? false : true;
    }
}

if (!function_exists('get_user_agent')) {

    function get_user_agent()
    {

        $ci = CI_INSTANCE();
        $ci->load->library('user_agent');

        if ($ci->agent->is_browser()) {
            $agent = $ci->agent->browser() . ' ' . $ci->agent->version();
        } elseif ($ci->agent->is_robot()) {
            $agent = $ci->agent->robot();
        } elseif ($ci->agent->is_mobile()) {
            $agent = $ci->agent->mobile();
        } else {
            $agent = 'Unidentified User Agent';
        }

        return (object) ['agent' => $agent, 'platform' => $ci->agent->platform()];
    }
}

if (!function_exists('log_trail')) {

    function log_trail($action, $desc, $old_data = [], $new_data = [])
    {

        $ci = CI_INSTANCE();

        $data = array(
            'user' => user_session()->names,
            'user_data' => json_encode(user_session()),
            'action' => $action,
            'description' => $desc,
            'old_data' => json_encode($old_data),
            'new_data' => json_encode($new_data),
            'ip_address' => $ci->input->ip_address(),
            'user_agent' => get_user_agent()->agent,
            'platform' => get_user_agent()->platform
        );

        $ci->db->insert("audit_trail", $data);
    }
}
