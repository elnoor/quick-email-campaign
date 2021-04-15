<?php
session_start();

require_once 'SendGrid/sendgrid-php.php';
use SendGrid\Mail\Mail;

// Read message document file and replace the necessary values to preview
$message = nl2br(file_get_contents("assets/docs/message.html"));
$message = str_replace("[FULL_NAME]", $_SESSION['fullName'], $message);
$message = str_replace("[MP_NAME]", $_SESSION['mpName'], $message);
$message = str_replace("[EMAIL]", $_SESSION['mpEmail'], $message);

// Set up mail to send
$mail = new Mail();
$mail->setFrom("info@southaz.org", "SouthAz");  // Should be a verified sender (via Sendgrid)
$mail->setReplyTo($_SESSION['email'], $_SESSION['fullName']);
$mail->addTo($_SESSION['mpEmail'], $_SESSION['mpName']);
$mail->setSubject("Message from " . $_SESSION['fullName']);
$mail->addContent("text/html", $message);

$success = true;

try {
    $sendgrid = new \SendGrid("SENDGRID_API_KEY_HERE");
    $response = $sendgrid->send($mail);
    $response->statusCode() . "\n";
    
    // Http response code 20x
    if(!substr($response->statusCode(), 0, 2) == "20"){
        $success = false;        
    }
} catch (Exception $e) {
    $success = false;
}

session_destroy();

require("includes/header.php");

$text = $success ? "Message sent, thank you!" : "Message could not be sent. Try again later please.";
$class = $success ? "success" : "danger";
echo ("<div class='text-center alert alert-{$class}' role='alert'>" . $text . "</div>");
require("includes/footer.php");
?>
