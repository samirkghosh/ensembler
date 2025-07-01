<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */


if ( ! function_exists('get_settings')){
    function get_settings($key)
    {
      $CI =& get_instance();
      $query = $CI->db->get_where('system_settings', array(key => $key));
      return $query->row()->value;
    }
}


if ( ! function_exists('validate_mobile'))
{
	function validate_mobile($mobile)
    {   
        $cc = '264';
        $m_l = 9 ;
        $mobile = preg_replace("/[^0-9]/", '', $mobile);
        if(strlen($mobile) < $m_l){
            return false ;
        }
        if(strlen($mobile) > 12){
            return false ;
        }

        // add country code
        if(strlen($mobile) == $m_l){
            return $cc.$mobile;                                                                 
        }

        if(strlen($mobile) > $m_l){
            if(strpos($mobile, $cc) == true){
                $left_length = strlen(str_replace($cc, "", $mobile)) ;
                if($left_length != $m_l || $left_length > $m_l){
                    return false ;    
                }
                if(strlen(str_replace($cc, "", $mobile)) == $m_l){
                    return $mobile;
                }
                        
            }

            if(strpos($mobile, $cc) ==false){
                return $cc.substr($mobile,-$m_l);
            }
        }
        return $mobile ;  
    }


}


if ( ! function_exists('ddmmyyyy_date'))
{
	function ddmmyyyy_date($date)
    {
        if(strpos($date, "/")){
            if(strlen($date) >10)
                $date = date('d-m-Y H:i:s', strtotime($date)) ;
            else
                $date = date('d-m-Y', strtotime($date)) ;

        }
        elseif(strpos($date, "-")){
            if(strlen($date) >10)
                $date = date('d-m-Y H:i:s', strtotime($date)) ;
            else
                $date = date('d-m-Y', strtotime($date)) ;
        }
        return  $date ; 
    }
}


function validate_mobile_number($string){
    //eliminate every char except 0-9
    $justNums = preg_replace("/[^0-9]/", '', $string);

    if(strlen($justNums) < 12) return false;
    if(strlen($justNums) > 12) return false;
            
    // check country code.
    if(strlen($justNums) == '12' ){
        if(substr($justNums, 0, 3) != '264') return false;
    }
    return true ;
}


function access_denied() {
    redirect('dashboard/unauthorized');
}

// ------------------------------------------------------------------------
/* End of file user_helper.php */
/* Location: ./system/helpers/user_helper.php */
