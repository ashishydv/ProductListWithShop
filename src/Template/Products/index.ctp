<div style="margin-top:20px">
    <?php
    if(count($products) == 0 ) echo '<h3>No Product Found</h3>';
    foreach($products as $product){ ?>
        <div class="card">
            <img src="<?php echo $product['image'] ?>" alt="<?php echo $product['title'] ?>" style="width:100%">
            <h1><?= $product['title'] ?></h1>
            <p class="price">₹<?= $product['price'] ?></p>
            <?= $this->Html->link('Add to Cart',['controller' => 'Carts','action' => 'add', $product['id']]) ?>    
        </div> 
    <?php } ?>
</div>