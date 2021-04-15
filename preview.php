<?php
session_start();

$errors = [];

if(!isset($_POST['submit'])){
    header("location: 404.php");
    die();
}else{
    
    $firstName = $_POST['firstName'];
    if(!isset($firstName) || strlen($firstName) < 3){
        array_push($errors, "First name is not valid");
    }else{
        $firstName = htmlentities($firstName);
    }
    
    $lastName = $_POST['lastName'];
    if(!isset($lastName) || strlen($lastName) < 3){
        array_push($errors, "Last name is not valid");
    }else{
        $lastName = htmlentities($lastName);
    }
    
    $email = $_POST['email'];
    if(!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        array_push($errors, "Email is not valid");
    }else{
        $email = htmlentities($email);
    }
    
    $postal = $_POST['postal'];
    if(isset($postal)){
        // remove "-" or " "
        $postal = strtoupper(str_replace("-", "", str_replace(" ", "", $postal)));  
    }else $postal = "";
    
    if(strlen($postal) != 6){
        array_push($errors, "Postal code is not valid. It has to be 6 characters long with only letters and numbers");
    }
    else{
        $postal = htmlentities($postal);
    }
    
    // optional
    $phone = $_POST['phone'];
    if(isset($phone) && !empty($phone)){
        // remove non numeric characters
        $phone = preg_replace("/[^0-9]/", "", $phone);
        
        if(strlen($phone) < 10){
            array_push($errors, "Phone number is not valid. It has to consist of minimum 10 numbers.");
        }
        else{
            $phone = htmlentities($phone);
        }
    }
}

if(count($errors) == 0){
    try{
        // Rest API call to get the email and name of MP
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://represent.opennorth.ca/postcodes/{$postal}/?sets=federal-electoral-districts");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($json);
        $data = $data -> representatives_centroid[0];
        $mpName = $data -> name;
        $mpEmail = $data -> email;
        
        if($mpEmail == null || empty($mpEmail)){
            array_push($errors, "Error! Couldn't fetch MP's email address.");
        }else{
            // Read message document file and replace the necessary values to preview
            $message = nl2br(file_get_contents("assets/docs/message.html"));
            $fullName = $firstName . " " . $lastName;
            $message = str_replace("[FULL_NAME]", $fullName, $message);
            $message = str_replace("[MP_NAME]", $mpName, $message);
            $message = str_replace("[EMAIL]", $email, $message);
            $message = str_replace("[PHONE]", $phone, $message);
            
            // Store details in the session to pass to Send page
            $_SESSION['fullName'] = $fullName;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            
            // If test mode is on emails to self not to MP
            $test = isset($_COOKIE['test']) && $_COOKIE['test'] == "true";
            $_SESSION['mpName'] = $test ? "TEST MP NAME" : $mpName;
            $_SESSION['mpEmail'] = $test? "yashar.hakkakpur@gmail.com" : $mpEmail;
        }
    }
    catch(Exception $e){
        array_push($errors, "Something went wrong while fetching the MP's details based on postal code.");
    }
}

// Header should come after all Session modifications
require("includes/header.php");

if($test){
    echo "<div class='alert alert-warning' role='alert'>Test mode is on</div>";
}

if(count($errors) > 0){
    foreach ($errors as $err) {
        echo "<div class='alert alert-danger' role='alert'>" . $err . "</div>";
    }
    echo "<button class='btn btn-outline-secondary w-100' onclick='window.history.back()'>Go Back</button>";
    die();
}

?>
    
    <div class="row">
        <div class="col-12"><?php echo $message ?></div>
        <div class="col-6">
            <button class='btn btn-outline-secondary w-100' onclick='window.history.back()'>Go Back</button>
        </div>
        <form action="send.php" method="post" class="col-6">
            <button type="submit" class='btn btn-primary w-100'>Send Message</button>
        </form>
    </div>


<?php
    require("includes/footer.php");
?>


