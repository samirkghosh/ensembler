<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
define('THEMES_DIR', 'themes');
define('BASE_URI', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

class MY_Controller extends CI_Controller
{

    protected $langs = array();

    public function __construct()
    {

        parent::__construct();
        //$this->config->load('license');
        //$this->load->library('auth');
        $this->load->library('module_lib');
      
        $this->load->helper('directory');
        //$this->load->model('setting_model');
        // if ($this->session->has_userdata('admin')) {
        //     $admin    = $this->session->userdata('admin');
        //     $language = ($admin['language']['language']);
        // } else if ($this->session->has_userdata('student')) {
        //     $student  = $this->session->userdata('student');
        //     $language = ($student['language']['language']);
        // } else {
        // }
            $language = "english";

        $lang_array = array('form_validation_lang');
        $map        = directory_map(APPPATH . "./language/" . $language . "/app_files");
        foreach ($map as $lang_key => $lang_value) {
            $lang_array[] = 'app_files/' . str_replace(".php", "", $lang_value);
        }

        $this->load->language($lang_array, $language);
        $this->form_validation->set_error_delimiters('<span class="error">', '</span>');
    }



}

class Admin_Controller extends MY_Controller
{
    protected $aaaa = false;
    public function __construct()
    {
        parent::__construct();
        // $this->auth->is_logged_in();
        // $this->check_license();
        $this->load->library('rbac');
    }



   /* public function check_license()
    {

       $license = $this->config->item('SSLK');

        if (!empty($license)) {

            $regex = "/^[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-/";
          
        
            if (preg_match($regex, $license)) {
              $valid_string = $this->aes->validchk('encrypt', base_url());
 
                if (strpos($license, $valid_string) !== false) {

                    true; //valid
                }else{
                       $this->update_ss_routine();
                }
            } else {
             
             $this->update_ss_routine();

            }

        }

    }
       
    public function update_ss_routine(){
        $license = $this->config->item('SSLK');
        $fname         = APPPATH . 'config/license.php';
        $update_handle = fopen($fname, "r");
        $content       = fread($update_handle, filesize($fname));
        $file_contents = str_replace('$config[\'SSLK\'] = \'' . $license . '\'', '$config[\'SSLK\'] = \'\'', $content);
        $update_handle = fopen($fname, 'w') or die("can't open file");
        if (fwrite($update_handle, $file_contents)) {

        }
        fclose($update_handle);

        $this->config->set_item('SSLK', '');
    }*/


    public function check_input($data='')
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function checkDateFormat($date, $seprator)
    {
        $strd = explode("$seprator", $date);
        if(strlen(trim($date)) == '8'){
            if($strd['2'] == '20'){
                $m = $strd['0'] ;
                $d = $strd['1'] ;
                $y = $strd['2'].'20' ;
                $date = $y .'-'. $m .'-'. $d ;
            }
            return $date ;
        } 
            $m = $strd['0'] ;
            $d = $strd['1'] ;
            $y = $strd['2'];
            $date = $y .'-'. $m .'-'. $d ;
        return $date ;  
    }   

    function validate_mobile($mobile)
    {   
        $cc = '264';
        $m_l = 9 ;
        if(strlen($mobile) < $m_l){
            $this->form_validation->set_message('validate_mobile', 'Invalid mobile');
            return false ;
        }

        // if length is but to check with countury code
        if(strlen($mobile) > $m_l){
            $length = substr($mobile, -9) ;
            $get_code = substr($mobile, 0, -9) ;
            if($get_code==$cc && strlen($length) == $m_l ){
                return $mobile ;
            }
            if($get_code!=$cc && strlen($length) == $m_l ){
                return $cc.$length; 
            }
        }
        return $mobile ;   
    }

    public function getUniqueArrayColumnWiseFromArray($array, $column_name)
    {
        if(count($array) > 0  && strlen($column_name) > 0 ){
            $array = array_column($array, $column_name);
            return array_unique($array);    
        }
        return "Both Parameters are required.";
    }

    public function isLoggedin($value='')
    {
        if($this->session->userdata('admin'))
            return true ;
        else
            return false ;
    }

    /*
    Get date Diff Between Two Days 
    */
    public function daydifference($from, $to)
    {
        $date1=date_create(date('Y-m-d', strtotime($from)));
        $date2=date_create(date('Y-m-d', strtotime($to)));
        $diff=date_diff($date1,$date2);
        return $diff->format("%a");
    }

    public function add_date($date, $days)
    {
        $date = date_create(date('Y-m-d', strtotime($date)));
        date_add($date,date_interval_create_from_date_string($days." days"));
        return date_format($date,"Y-m-d");
    }


    public function getDaysInMonth($date)
    {   
        $ddarr = [];
        $year = date('Y');
        $month = substr($date, 3,2 );   //01-02-2020
        
        $days=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $date =  $year.'-'.$month.'-'.'01' ;
        for ($i=0; $i < $days ; $i++) { 
            array_push($ddarr, $date) ;
            $date = $this->add_date($date, "1");
        }
        return $ddarr ;
    }



    function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

}

class Student_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->auth->is_logged_in_user('student');
    }

}

class Public_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

}

class Parent_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->auth->is_logged_in_user('parent');
    }

}

class Front_Controller extends CI_Controller
{

    protected $data           = array();
    protected $school_details = array();
    protected $parent_menu    = '';
    protected $page_title     = '';
    protected $theme_path     = '';
    protected $front_setting  = '';

    public function __construct()
    {

        parent::__construct();

        $this->check_installation();
        if ($this->config->item('installed') == true) {
            $this->db->reconnect();
        }

        $this->school_details = $this->setting_model->getSchoolDetail();

        $this->load->model('frontcms_setting_model');
        $this->front_setting = $this->frontcms_setting_model->get();
        if (!$this->front_setting) {
            redirect('site/userlogin');
        } else {

            if (!$this->front_setting->is_active_front_cms) {
                redirect('site/userlogin');
            }
        }
        $this->theme_path = $this->front_setting->theme;
        //================
        $language = ($this->school_details->language);
        $this->load->helper('directory');
        $lang_array = array('form_validation_lang');
        $map        = directory_map(APPPATH . "./language/" . $language . "/app_files");
        foreach ($map as $lang_key => $lang_value) {
            $lang_array[] = 'app_files/' . str_replace(".php", "", $lang_value);
        }

        $this->load->language($lang_array, $language);
        //===============

        $this->load->config('ci-blog');
    }

    protected function load_theme($content = null, $layout = true)
    {

        $this->data['main_menus']     = '';
        $this->data['school_setting'] = $this->school_details;
        $this->data['front_setting']  = $this->front_setting;
        $menu_list                    = $this->cms_menu_model->getBySlug('main-menu');
        $footer_menu_list             = $this->cms_menu_model->getBySlug('bottom-menu');
        if (count($menu_list > 0)) {
            $this->data['main_menus'] = $this->cms_menuitems_model->getMenus($menu_list['id']);
        }

        if (count($footer_menu_list > 0)) {
            $this->data['footer_menus'] = $this->cms_menuitems_model->getMenus($footer_menu_list['id']);
        }
        $this->data['header'] = $this->load->view('themes/' . $this->theme_path . '/header', $this->data, true);

        $this->data['slider'] = $this->load->view('themes/' . $this->theme_path . '/home_slider', $this->data, true);

        $this->data['footer'] = $this->load->view('themes/' . $this->theme_path . '/footer', $this->data, true);

        $this->base_assets_url = 'backend/' . THEMES_DIR . '/' . $this->theme_path . '/';

        $this->data['base_assets_url'] = BASE_URI . $this->base_assets_url;

        // if ($layout == true) {
        $this->data['content'] = (is_null($content)) ? '' : $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/' . $content, $this->data, true);
        $this->load->view(THEMES_DIR . '/' . $this->theme_path . '/layout', $this->data);
        // } else {
        //     $this->load->view(THEMES_DIR . '/' . $this->config->item('ci_blog_theme') . '/' . $content, $this->data);
        // }
    }

    private function check_installation()
    {

        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                redirect(base_url() . 'install/start');
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }

}




