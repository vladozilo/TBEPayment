<?php

require 'init.php';

use TBEPayment\CardPay;

$mid = '9999';
$key = '31323334353637383930313233343536373839303132333435363738393031323132333435363738393031323334353637383930313233343536373839303132';

$amount = '12.5';
$variable_symbol ='1234';
$return_url = 'http://localhost:8888/TBEPayment/examples/index.php';
$customer_name = 'John Doe';

$CP = new CardPay($amount, $variable_symbol, $return_url);
$CP->setCustomerName($customer_name);

$cardPay_Request_Url = $CP->request($mid, $key);

?>

<a href="<?= $cardPay_Request_Url ?>"><?= $cardPay_Request_Url ?></a>
