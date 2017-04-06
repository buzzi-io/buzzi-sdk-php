<?php

namespace Buzzi\Events;

class Ecommerce
{
    const BROWSE_ABANDONMENT = 'buzzi.ecommerce.browse-abandonment';
    const CART_ABANDONMENT   = 'buzzi.ecommerce.cart-abandonment';
    const CART_PURCHASE      = 'buzzi.ecommerce.cart-purchase';
    const EMAIL_SUBSCRIPTION = 'buzzi.ecommerce.email-subscription';
    const ENTRY_PAGE_VIEW    = 'buzzi.ecommerce.entry-page-view';
    const PRODUCT_VIEW       = 'buzzi.ecommerce.product-view';
    const SITE_SEARCH        = 'buzzi.ecommerce.site-search';
    const TEST               = 'buzzi.ecommerce.test';
    const USER_REGISTRATION  = 'buzzi.ecommerce.user-registration';
    const WISH_LIST_ITEM     = 'buzzi.ecommerce.wish-list-item';
}