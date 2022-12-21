<?php

//ajax_action.php

include('Examination.php');

require_once('../class/class-phpmailer.php')

$exam = new Examination;

$current_datetime = date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')));

if(isset($_POST['page']))
{
	if($_POST['page'] == 'register')
	{
		if($_POST['action'] == 'check_email')
		{
			$exam->query = "
			SELECT * FROM admin_table 
			WHERE admin_email_address = '".trim($_POST["email"])."'
			";

			$total_row = $exam->total_row();

			if($total_row == 0)
			{
				$output = array(
					'success'	=>	true
				);

				echo json_encode($output);
			}
		}

		if($_POST['action'] == 'register')
		{
			$admin_verification_code = md5(rand());

			$receiver_email = $_POST['admin_email_address'];

			$exam->data = array(
				':admin_email_address'		=>	$receiver_email,
				':admin_password'			=>
					password_hash($_POST['admin_password'], PASSWORD_DEFAULT),
				':admin_verification_code' 	=>	$admin_verification_code,
				':admin_type' 				=> 'sub_master',
				':admin_created_on'			=>	$current_datetime 	
			);

			$exam->query = "
			INSERT INTO admin_table
			(admin_email_address, admin_password,
				admin_verification_code, admin_type, admin_created_on)
			VALUES
			(:admin_email_address, :admin_password,
				:admin_verification_code, :admin_type, :admin_created_on)
			";

			$exam->execute_query();

			$subject = 'Online Examination Registration Verification';

			$body = '
			<p>Thank you for registering</p>
			<p>This is a verification eMail, please click the link to verify your eMail address by clicking this <a href="'.$exam->home_page.'verify_email.php?type=master&code='.$admin_verification_code.'" target="_blank"><b>link</b></a>.</p>
			<p>In case if you have any difficulty pleaser eMail us.</p>
			<p>Thank you,</p>
			<p>Online Examination System</p>
			';

			$exam->send_email($receiver_email, $subject, $body);

			$output = array(
				'success'	=>	true
			);

			echo json_encode($output);
		}
	}
}

?>

