<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;
use Cake\Event\Event;
use App\Form\CheckoutForm;
use Cake\Routing\Router;
use Stripe;

Stripe\Stripe::setApiKey('sk_test_51JZh4ISGTdsIvCiOGF45lu668Or85CY2z3chbVuFeNIidkA1hq7Sef8J6DHbShDaJCxZBsg7g0iaCxXfwoOYyAHF00uT4SzpHM');

class CartsController extends AppController
{
    
    public function beforeFilter(Event $event){
        parent::beforeFilter($event);
        $this->loadComponent('ProductApi');
    }

    private function session(){
        return $this->request->session();
    }

    

    public function add($id = null){
        
        // validate product from api
        $isValidProduct = True;
        if($id == null) $isValidProduct = False;
        if($isValidProduct){
            $res = $this->ProductApi->getProducts($id);
            $isValidProduct = $res['isOk'] && array_key_exists('id', $res['product']);
        }

        // add to session card
        if($isValidProduct){
            $items = [$id => 1]; // ['productId' => 'quantity']
            if($this->session()->check('Cart')){
                $items = $this->session()->read('Cart');
                if( array_key_exists($id, $items) ) $items[$id] = $items[$id] + 1;
                else $items[$id] = 1;
            } 
            $this->session()->write('Cart', $items);
            $this->Flash->success('Product Added to cart');
        }else{
            $this->Flash->error('Invalid Product');
        }
        return $this->redirect( $this->referer() );
    }

    public function removeItem($id = null){
        if($this->session()->check('Cart') && $id != null){
            $items = $this->session()->read('Cart');
            if( array_key_exists($id, $items) ) unset($items[$id]);
            $this->session()->write('Cart',$items);
        }
        $this->Flash->success('Product removed successfully.');
        return $this->redirect( $this->referer() );
    }

    /**
     * List cart item and checkout cart
     */
    public function index(){
        $itemLines = [];
        $checkout = $this->checkoutForm(); // guest form
        $items = $this->cartItemList(); // fetch items in cart
        $this->set(compact('items','checkout'));
    }

    public function cartItemList(){
        $items = []; // products in list
        if($this->session()->check('Cart')){
            // geeting detail of each product in cart using api
            foreach ($this->session()->read('Cart') as $id => $quantity){
                $resp = $this->ProductApi->getProducts($id);
                if($resp['isOk']){ // if valid product or resposne add to cartlist
                    $items[$id] = $resp['product'];
                    $items[$id]['quantity'] = $quantity;
                }
            }
        }
        return $items;
    }

    /**
     * checkout form for guest
     */
    private function checkoutForm(){
        $checkout = new CheckoutForm();
        if ($this->request->is('post')) {
            $this->session()->write('guest',$this->request->data); // store guest user in session till payment success
            $this->paymentSubmit(); // strip payment system
        }
        if($this->session()->check('guest')) $checkout->setData($this->session()->read('guest'));
        return $checkout;
    }

    /* strip payment system */
    public function paymentSubmit(){
        $itemsForStripe = [];
        $items = $this->cartItemList();
        if(count($items) > 0 ){
            $itemsForStripe = [];
            foreach($items as $id => $item){

                // temp fix for amount
                #$item['price'] = 22.3;
                $val = number_format((float)$item['price'], 2, '.', '');
                $amount = (int)str_replace('.','',$val);

                $product = ['name' => $item['title'], 
                    'images' => [$item['image']],
                    'amount' => $amount,
                    'currency' => 'inr',
                    'quantity' => $item['quantity'] ];

                array_push($itemsForStripe,$product);
            }
        }else{
            $this->Flash->error('Please try again.');
            return $this->redirect( $this->referer() );
        }

        $checkout_session = Stripe\Checkout\Session::create([
            'line_items' => $itemsForStripe,
            'payment_method_types' => [
              'card',
            ],
            'mode' => 'payment',
            'success_url' => Router::url(['action' => 'success'], TRUE),
            'cancel_url' => Router::url(['action' => 'cancel'], TRUE),
          ]);
          return $this->redirect($checkout_session->url);
    }

    public function success(){
        $this->session()->delete('Cart');
        $guest = $this->session()->read('guest');
        $this->session()->delete('guest');
        $this->set(compact('guest'));
    }

    public function cancel(){
        
    }

}
