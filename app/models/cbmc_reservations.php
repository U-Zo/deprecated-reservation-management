<?php
class CbmcReservations extends AppModel {
    var $name = 'CbmcReservations';

    var $hasAndBelongsToMany = array(
        'Cbmc_customers' => array(
            'className' => 'Cbmc_customers',
            'joinTable' => 'profiles_reservations',
            'foreignKey' => 'reservation_id',
            'associationForeignKey' => 'profile_id',
            'unique' => 'keepExisting',
        )
    );
}
?>