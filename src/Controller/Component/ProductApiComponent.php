<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;

class ProductApiComponent extends Component{


    private $baseUrl = 'https://fakestoreapi.com';

    public function getProducts($id = null){
        $http = new Client();
        if($id == null){
            $products = [];
            $response = $http->get($this->baseUrl.'/products');
            $isOk = $response->isOk();
            if($isOk) $products = $response->getJson();
            return ['isOk' => $isOk, 'products' => $products];
        }else{
            $product = null;
            $response = $http->get($this->baseUrl.'/products/'.$id);
            if($response->isOk()) $product = $response->getJson();
            return ['isOk' => $response->isOk(), 'product' => $product];
        }
    }


}
?>
