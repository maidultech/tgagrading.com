<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Service\CanadaPostService;
use Illuminate\Http\Request;
use Mitrik\Shipping\ServiceProviders\Address\Address;
use Mitrik\Shipping\ServiceProviders\Box\BoxCollection;
use Mitrik\Shipping\ServiceProviders\Box\BoxMetric;
use Mitrik\Shipping\ServiceProviders\Measurement\Weight;
use Mitrik\Shipping\ServiceProviders\ServiceUPS\ServiceUPS;
use Mitrik\Shipping\ServiceProviders\ServiceUPS\ServiceUPSCredentials;

class TestController extends Controller
{
    function index(){
        
        dd($this->track(),Order::find('98'));
    }

    function track(){
        $credentials = new CanadaPostController();
        return $credentials->track(98);
    }


    
}
