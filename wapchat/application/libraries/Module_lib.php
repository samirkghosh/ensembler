<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Module_lib {

    private $allModules = array();
    protected $modules;
    var $perm_category;

    function __construct() {
        $this->CI = & get_instance();
        $this->modules = array();
        self::loadModule(); //Initiate the userroles
    }

    function loadModule() {
        $this->allModules = $this->CI->Module_model->get();
        // log_message('error', json_encode($this->allModules));
        
        // log_message('error', 'print all modules');
        // log_message('error', json_encode($this->modules));

        

        if (!empty($this->allModules)) {
            foreach ($this->allModules as $mod_key => $mod_value) {
                if ($mod_value->is_active == 1) {
                    $this->modules[$mod_value->short_code] = true;
                } else {

                    $this->modules[$mod_value->short_code] = false;
                }
                
                // log_message('error', 'print all modules INSIDE OF LLOOP');
                // log_message('error', json_encode($this->modules));
            }
        }

        // log_message('error', 'print all modules Ot side of loop ');
        // log_message('error', json_encode($this->modules));
    }

    function hasActive($module = null) {
        // log_message('error', 'In side hasActive function ');
        // log_message('error', json_encode($this->modules));
        // log_message('error', $this->modules[$module]);

        if ($this->modules[$module]) {
            return true;
        }

        return false;
    }

}
