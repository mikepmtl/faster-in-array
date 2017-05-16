<?php


require_once 'vendor/autoload.php';


define('NUM_USERS', 60000);
define('NUM_SENT', 10000);


class User {

	public $name;
	public $email;
	public $validated;
	public $created_at;
	public $updated_at;

	public function __construct($data) {
		$this->name = $data['name'];
		$this->email = $data['email'];
		$this->validated = $data['validated'];
		$this->created_at = $data['created_at'];
		$this->updated_at = $data['updated_at'];
	}

}


class Sent {

	public $email_id;
	public $email;
	public $subject;
	public $sent_at;

	public function __construct($data) {
		$this->email_id = $data['email_id'];
		$this->email = $data['email'];
		$this->subject = $data['subject'];
		$this->sent_at = $data['sent_at'];
	}

}


/**
 * Faster array lookup than in_array() where the needle is a key of the haystack.
 * Haystack should be in the form of [ 'key' =>  1 ].
 * The value of the haystack is arbitrary since it's just a placeholder. The key is used for the lookup.
 *
 * @param $needle
 * @param $haystack
 *
 * @return bool
 */
function fast_in_array($needle, $haystack) {
	return isset($haystack[$needle]);
}



// Create a Faker instance
$faker = Faker\Factory::create();

// Let's make some users
$users = [];
for($i=0; $i< NUM_USERS; $i++){

	$users[] = new User([
			'name' => $faker->name,
			'email' => $faker->unique()->email,
			'validated' => $faker->boolean(90),
			'created_at' => $faker->date('Y-m-d H:i:s'),
			'updated_at' => $faker->date('Y-m-d H:i:s')
		]
	);

}

$sents = [];
$sent_at = $faker->date('Y-m-d H:i:s');

$users_copy = $users;

for($i=0; $i< NUM_SENT; $i++){

	$user_key = array_rand( $users_copy, 1);
	$user = $users_copy[$user_key];

	$sents[] = new Sent([
		'email_id' => 1001,
		'email' => $user->email,
		'subject' => 'My Test Email',
		'sent_at' => $sent_at
	]);

	unset($users_copy[$user_key]);

}

echo 'Number of users: ' . count($users) . chr(10);
echo 'Number of sents : ' . count($sents) . chr(10);

$start_time = microtime(true);
echo "Start time: " . $start_time . chr(10);

$removed = 0;

// SOLUTION 1

//foreach ( $sents as $sent ){
//	foreach ($users as $key => $user ){
//		if($user->email === $sent->email){
//			unset($users[$key]);
//			$removed++;
//		}
//	}
//}

// SOLUTION 2

$haystack = [];
foreach ($sents as $sent){
	$haystack[$sent->email] = 1;
}

foreach ( $users as $key => $user ){
	if ( fast_in_array( $user->email, $haystack ) ){
		unset($users[$key]);
		$removed++;
	}
}


$end_time = microtime(true);

echo "End time: " . $end_time . chr(10);
echo "Elapsed time: " . ($end_time - $start_time) . chr(10);
echo "Removed: " . $removed . chr(10);
echo "New Users count: " . count($users) . chr(10);
