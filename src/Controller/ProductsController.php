<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;

class ProductsController extends AppController
{
    
    public function index(){
        
        

        $this->loadComponent('ProductApi');
        $resp = $this->ProductApi->getProducts();
        $products = $resp['products'];
        $isOk = $resp['isOk'];
        $this->set(compact('products','isOk'));
    }
}
