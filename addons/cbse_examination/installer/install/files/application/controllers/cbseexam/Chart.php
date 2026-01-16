<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Chart extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data=[];
     
        $this->load->view('cbseexam/chart/index', $data);
    
    }

}
