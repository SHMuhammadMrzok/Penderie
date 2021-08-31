<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('shopping_cart');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="header-shooping-cart">
          <div class="title-page">
            <h2><?php echo lang('shopping_cart');?> <span>(<?php echo $cart_items_count;?>)</span></h2>
          </div>

        </div>
        <div class="alert-area">
          Some of the products in your cart have run out of stock and have been moved to your <a
            href="my-wishlist.html">Wishlist</a>
          <br />
          Please review your cart before proceeding
        </div>

        <div class="store-order">
          <div class="store-name-title">
            <form accept="#" class="form">
              <input id='store-nike' type="checkbox" placeholder="" />
              <label for="store-nike">Nike Store</label>
              <span class="collapce">
                <b class="plus show-icon">+</b>
                <b class="min">-</b>
              </span>
            </form>
          </div>
          <div class="shopping-cart-container">
            <div class="item-shop-container">
              <div class="row no-gutters">
                <div class="col-md-3">
                  <div class="item-img">
                    <a href="#"><img src="assets/images/item-12.jpg" alt="" /></a>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="info-container">
                    <div class="item-info">
                      <h3>Woman's Ziane Leather Slip-ons</h3>
                      <p class="price">
                        <span class="new-price">342$ </span>
                        <span class="old-price">387$</span>
                      </p>
                      <p class="brand"><a href="#">Nike</a></p>
                      <div class="action-item">
                        <div  class="delet-item">
                          <svg>
                            <use xlink:href="#rebbish"></use>
                          </svg>
                          <span>Remove</span>
                        </div>
                      </div>
                    </div>
                    <div class="qty">
                      <div class="quant">
                        <form method="post" action="#">
                          <div class="numbers-row">
                            <input type="text" id="partridge" value="1">
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>


                </div>
              </div>

            </div>


            <div class="item-shop-container">
              <div class="row no-gutters">
                <div class="col-md-3">
                  <div class="item-img">
                    <a href="#"><img src="assets/images/item-11.jpg" alt="" /></a>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="info-container">
                    <div class="item-info">
                      <h3>Skechers USA Men's Walson Dolen Oxford,Black,7 M US</h3>
                      <p class="price">
                        <span class="new-price">342$ </span>
                        <span class="old-price">387$</span>
                      </p>
                      <p class="brand"><a href="#">Nike</a></p>

                      <div class="action-item">
                        <div  class="delet-item">
                          <svg>
                            <use xlink:href="#rebbish"></use>
                          </svg>
                          <span>Remove</span>
                        </div>
                      </div>
                    </div>
                    <div class="qty">
                      <div class="quant">
                        <form method="post" action="#">
                            <div class="numbers-row">
                                <input type="text" id="partridge" value="1">
                              </div>
                        </form>
                    </div>
                    </div>
                  </div>


                </div>
              </div>

            </div>


          </div>

        </div>


        <div class="store-order">
          <div class="store-name-title">
            <form accept="#" class="form">
              <input id='store-reebook' type="checkbox" placeholder="" />
              <label for="store-reebook">Reebook</label>
              <span class="collapce">
                <b class="plus show-icon">+</b>
                <b class="min">-</b>
              </span>
            </form>
          </div>
          <div class="shopping-cart-container">
            <div class="item-shop-container">
              <div class="row no-gutters">
                <div class="col-md-3">
                  <div class="item-img">
                    <a href="#"><img src="assets/images/item-10.jpg" alt="" /></a>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="info-container">
                    <div class="item-info">
                      <h3>Nike Air Max 200</h3>
                      <p class="price">
                        <span class="new-price">342$ </span>
                        <span class="old-price">387$</span>
                      </p>
                      <p class="brand"><a href="#">Nike</a></p>
                      <div class="action-item">
                        <div  class="delet-item">
                          <svg>
                            <use xlink:href="#rebbish"></use>
                          </svg>
                          <span>Remove</span>
                        </div>
                      </div>
                    </div>
                    <div class="qty">
                      <div class="quant">
                        <form method="post" action="#">
                          <div class="numbers-row">
                            <input type="text" id="partridge" value="1">
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>


                </div>
              </div>

            </div>
          </div>

        </div>

      </div>
      <div class="col-md-4">
        <?php /*<div class="deliver">

          <form action="#">
            <label>Deliver To:</label>
            <select>
              <option>Cairo</option>
              <option>Giza</option>
            </select>
          </form>
          <p>Delivered by <span>Sunday, Sep 8 </span></p>
        </div>
        */?>
        <div class="total-price">
          <div class="copon">
            <form action="#">
              <input type="text" placeholder="Coupon Code" class="form-control" />
              <button>Apply</button>
            </form>
          </div>
          <div class="num-subtotal">
            <p>Subtotal <span><span>EGP</span> 1649.00</span></p>
            <p>Shipping <span>FREE</span></p>
            <p>VAT <span><span>EGP</span>13 </span></p>
          </div>
          <p>Total:<span> (Inclusive of VAT) </span></p>
          <h2><span>EGP</span>172.00</h2>
          <hr />
          <p class="hint">Add 78.00 EGP of "Fulfilled by sneak" items to your order to qualify for FREE Shipping.</p>

        </div>
        <div class="gift">
          <form action="#">
            <div class="checkbox">
              <input type="checkbox" id="gift" />
              <label for="gift">
                Add Gift boxing for your order and make it beautiful
              </label>
            </div>
          </form>
        </div>

        <div class="button-checkout">
          <a href="shopping-cart-address.html">proceed to checkout</a>
        </div>


        <div class="continue-shopping">
          <a href="index.html">CONTINUE SHOPPING</a>
        </div>
      </div>
    </div>
  </div>
</main>
