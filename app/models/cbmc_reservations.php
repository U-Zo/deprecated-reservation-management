<?php

class CbmcReservations extends AppModel {
    var $name = 'CbmcReservations';
    var $hasMany = array(
        'CbmcComments' => array(
            'className' => 'CbmcComments',
            'foreignKey' => 'reservations_id',
            'dependent' => true
        ),
        'CbmcCustomers' => array(
            'className' => 'CbmcCustomers',
            'dependent' => true,
            'foreignKey' => 'reservations_id'
        ),
        'CbmcPayments' => array(
            'className' => 'CbmcPayments',
            'foreignKey' => 'reservations_id',
            'dependent' => true
        )
    );
}

?>