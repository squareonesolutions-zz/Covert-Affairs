<?

/*
	Configure this script by putting the following tags in your form:
	
		Note:  Your user must enter this value
		<input type="text" name="email_address"/>  
		
		<input type="hidden" name="mailto" value="YOUR_EMAIL_ADDRESS_HERE"/>
		<input type="hidden" name="subject" value="YOUR_SUBJECT_HERE"/>
		<input type="hidden" name="return_to" value="THE URL OF THE PAGE YOU WANT YOUR USER TO BE SENT TO AFTER COMPLETING THE FORM"/>
	
	The following tags are optional.  Use them if you want to send the person who 
	completed your form an email confirming that they sent it.

		<input type="hidden" name="send_thankyou" value="true"/>
		<input type="hidden" name="thankyou_message" value="WHATEVER MESSAGE YOU WANT TO SEND TO YOUR CUSTOMER"/>
		<input type="hidden" name="thankyou_subject" value="WHATEVER SUBJECT YOU WANT THE MESSAGE TO HAVE"/>
		<input type="hidden" name="thankyou_from" value="THE EMAIL ADDRESS YOU WANT TO SEND THE MESSAGE AS"/>
	
	Make sure your form action is "formmail.php", and don't forget to add a submit button!

		<form name="theForm" method="post" action="formmail.php">
		
	Enjoy!
	Steve Szettella & the 4word systems team.
	
*/
	$from = $_REQUEST['email_address'];
	
	
	$keys = array_keys($_REQUEST);
	for ($i=0; $i<sizeof($keys); $i++){
		$msg = $msg . $keys[$i] .'  :  '. $_REQUEST[$keys[$i]] ."\n";	
	}

	$to = $_REQUEST['mailto'];
	$subject = $_REQUEST['subject'];
	mail($to, $subject, $msg, 'From: '.$from);
	$thankyouEmail = $_REQUEST['send_thankyou'];
	if ($thankyouEmail == 'Y' || $thankyouEmail == 'true' || $thankyouEmail == "TRUE" || $thankyouEmail=="True") {
		$thankyou_subject = $_REQUEST['thankyou_subject'];
		$thankyou_message = $_REQUEST['thankyou_message'];
		$thankyou_from = $_REQUEST['thankyou_from'];
		mail($from, $thankyou_subject, $thankyou_message, "From: ". $thankyou_from);
	}	
	$returnTo = $_REQUEST['return_to'];
	echo $returnTo;
?>
<html>
<head>
<script language="JavaScript">
document.location = "<? echo $returnTo; ?>";
</script></head>
<body>
</body>
</html>