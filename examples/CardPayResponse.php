<?php

require 'init.php';

use TBEPayment\CardPay;

$key = '31323334353637383930313233343536373839303132333435363738393031323132333435363738393031323334353637383930313233343536373839303132';

$response = (new CardPay())->response($key);

if(!$response) {
    echo 'ERROR';
} else {
    echo $response;
}

?>
