<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 

    function trim_decimal($value, $precision)
    {
        $precision = (int)$precision;

        $factor  = pow(10, $precision);
        $epsilon = 1e-10;
        $value   = ((int)(($value + $epsilon) * $factor)) / $factor;

        // Handle scientific notation
        if (stripos((string)$value, 'e') !== false) {
            $value = sprintf('%.20f', $value);
            $value = rtrim($value, '0');
            $value = rtrim($value, '.');
        }

        $value = (string)$value;

        if ($precision === 0) {
            return explode('.', $value)[0];
        }

        if (strpos($value, '.') === false) {
            return $value . '.' . str_repeat('0', $precision);
        }

        list($int, $dec) = explode('.', $value, 2);

        $dec = substr($dec, 0, $precision);
        $dec = str_pad($dec, $precision, '0');

        return $int . '.' . $dec;
    }

    

    function formatMetalWeight($value)
    {
        // $CI =& get_instance();
        // $roundoff = $CI->session->userdata('metal_wgt_roundoff');
        // $decimal = (int)$CI->session->userdata('metal_wgt_decimal');
        $CI =& get_instance();
        //  $CI =& get_instance();
        $CI->load->model('login_model');
        $settings = $CI->login_model->branch_settings();
        $roundoff = isset($settings['metal_wgt_roundoff']) ? $settings['metal_wgt_roundoff'] : '';
        $decimal  = isset($settings['metal_wgt_decimal']) ? $settings['metal_wgt_decimal'] : '';

        if ($roundoff == 0) {
            return trim_decimal($value, $decimal);
        } else {
            return number_format((float)$value, $decimal, '.', '');
        }
    }
    

