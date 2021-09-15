<h1>Shopping Cart</h1>
<?php 
if(count($items) == 0){
    echo '<h3>Cart is Empty</h3>';
}else{ ?>
<div class="shopping-cart">
    <div class="column-labels">
        <label class="product-image">Image</label>
        <label class="product-details">Product</label>
        <label class="product-price">Price</label>
        <label class="product-quantity">Quantity</label>
        <label class="product-removal">Remove</label>
        <label class="product-line-price">Total</label>
    </div>

    <?php
    $total = 0;
    foreach($items as $id => $data){
        $total += $data['price'] * $data['quantity'];
    ?>

        <div class="product">
            <div class="product-image">
                <?= $this->Html->image($data['image'],['alt' => $data['title']]) ?>
            </div>
            <div class="product-details">
                <div class="product-title"><?= $data['title'] ?></div>
                <p class="product-description"></p>
            </div>
            <div class="product-price"><?= $data['price'] ?></div>
            <div class="product-quantity">
                <!-- <input type="number" value="4" min="1"> -->
                <?= $data['quantity'] ?>
            </div>
            
            <div class="product-removal">
                <?= $this->Html->link('Remove',['action' => 'removeItem', $id]) ?>
            </div>
            <div class="product-line-price"><?= $data['price'] * $data['quantity'] ?></div>
        </div>
        
    <?php }  ?>

        <div class='grand-total'>Total: ₹<?= $total; ?></div> 
        <div style="clear:both"></div>
    <?= $this->Form->create($checkout) ?>
    <?= $this->Form->control('name')?>
    <?= $this->Form->control('email')?>
    <?= $this->Form->button(__('Checkout')) ?>
    <?= $this->Form->end() ?>
</div>
<?php } ?>