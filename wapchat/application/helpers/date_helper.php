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


if ( ! function_exists('yyyymmdd_date'))
{
	function yyyymmdd_date($date)
    {
        if(strpos($date, "/")){
            if(strlen($date) >10)
                $date = date('Y-m-d H:i:s', strtotime($date)) ;
            else
                $date = date('Y-m-d', strtotime($date)) ;

        }
        elseif(strpos($date, "-")){
            if(strlen($date) >10)
                $date = date('Y-m-d H:i:s', strtotime($date)) ;
            else
                $date = date('Y-m-d', strtotime($date)) ;
        }
        return  $date ; 
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

// ------------------------------------------------------------------------
/* End of file user_helper.php */
/* Location: ./system/helpers/user_helper.php */
