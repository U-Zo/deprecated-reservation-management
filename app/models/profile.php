<?

class Profile extends AppModel	{

	var $name = 'Profile';
    var $hasMany = array(
        'Cbmc' => array(
            'className' => 'Cbmc',
            'foreignKey' => 'profile_id'
        )
    );
    var $validate = array(
		'lname' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'This field can not be blank',
			),
			'rule2' => array(
				'rule' => array('comparison', '!=', 'LAST NAME'),
				'message' => 'You have to put names'
			)
		)
	);

}

?>