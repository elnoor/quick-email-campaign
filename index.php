<?php
session_start();

// destroy any old data
$_SESSION = array();
session_destroy();

if(isset($_GET['test'])){
    setcookie("test", "true", time()+3600, "/");   // expires in 1 hour
}
else if(isset($_COOKIE['test'])){
    setcookie("test", "", time() - 3600);   // kill cookie
}

require("includes/header.php");
?>


    <div class="text-center pb-5">
      <!--<img class="d-block mx-auto mb-4" src="assets/img/logo.png" alt="" width="72" height="57">-->
      <h2>Email Campaign</h2>
      <p class="lead">Talking about ethnic minority rights is a taboo among Iran’s opposition and journalists. Most of them are against ethnic minority rights in Iran. We do not have a voice. Our voice is concealed by Persian journalists who believe that hiding ethnic issues in Iran is their biggest duty. We are asking you to support ethnic minority Azeri in Iran</p>
    </div>
      
    <h4 class="mb-3">Your details</h4>
    <form action="preview.php" method="post">
      <div class="row g-3">
        <div class="col-6">
          <label for="firstName" class="form-label">First name *</label>
          <input name="firstName" type="text" class="form-control" id="firstName" placeholder="John" value="" required>
        </div>

        <div class="col-6">
          <label for="lastName" class="form-label">Last name *</label>
          <input name="lastName" type="text" class="form-control" id="lastName" placeholder="Doe" value="" required>
        </div>

        <div class="col-12 col-md-4">
          <label for="email" class="form-label">Email address *</label>
          <input name="email" type="email" class="form-control" id="email" placeholder="you@example.com">
        </div>

        <div class="col-6 col-md-4">
          <label for="postal" class="form-label">Postal code *</label>
          <input name="postal" type="text" class="form-control" id="postal" placeholder="A1A1A1" required>
        </div>

        <div class="col-6 col-md-4">
          <label for="phone" class="form-label">Phone number</label>
          <input name="phone" type="text" class="form-control" id="phone" placeholder="9876543210">
        </div>
      </div>
      
      <button name="submit" type="submit" class="w-100 btn btn-primary btn-lg my-4">Preview Letter</button>
    </form>
    
<?php require("includes/footer.php") ?>
