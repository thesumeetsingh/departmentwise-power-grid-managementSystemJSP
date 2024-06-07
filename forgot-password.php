<?php
date_default_timezone_set('Asia/Kolkata');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email=$_POST["email"];
    $token= bin2hex(random_bytes(16));
    $token_hash= hash("sha256", $token);
    $expiry= date("Y-m-d H:i:s",time()+(60*30));
    $currentTime=date("Y-m-d H:i:s",time());
    include "connection.php";
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE EMAILADD = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        //email exists in database
        $user = $result->fetch_assoc();
        $username=$user['USERNAME'];
        $firstName=$user['FIRSTNAME'];
        $lastName=$user['LASTNAME'];
    }


    $sql= "UPDATE user_details 
        SET reset_token_hash=?,
            reset_token_expires_at=?
            WHERE EMAILADD=?";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expiry, $email);
    $stmt->execute();
    // $result = $stmt->get_result();
    // 57c55057fc37553a56c52af79db9b35fac6366b27d760dbcd16c1c06d08a920c
    if ($conn->affected_rows){
        $mail=require __DIR__."/mailer.php";
        $mail->setFrom("noreplay@jindalsteel.com");
        $mail->addAddress($email);
        $mail->Subject="Password Reset";
        $mail-> Body=<<<END
        Dear  <b> $firstName $lastName </b>,<br>
        A request was generate to reset password for you JSP Power Grid web portal account on <b> $currentTime. </b><br>
        This link is valid for 30 minutes. (till $expiry)
        <br>
        Click <a href="http://localhost/webportal/createpassword.php?token=$token">Here</a> to reset your password. <br><br>

        <i>If this request was not generated by you, simply ignore this message.<i>

        Thanks and Regards.
        END;

        try{
            $mail->send();
        }catch(Exception $e){
            echo "Message could not be sent. Mailer error: {$mail-> ErrorInfo}";
        }
    }
    echo "<script>
            alert('Message sent. Please check your inbox.');
            window.location.href = 'login.php';
          </script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forgot Password</title>
<link rel="icon" type="image/x-icon" href="/images/favicon.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

.ftco-section {
    min-height: 100%;
}

/* .wrap {
    display: flex;
    align-items: stretch;
    height: 100%;
} */
.ftco-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; 
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

    </style>
</head>

<body>
    <section class="ftco-section" style="background: rgb(177,176,160);
    background: linear-gradient(90deg, rgba(177,176,160,1) 0%, rgba(204,162,114,1) 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <div class="img" style="background-image: url(images/bg-1.jpg);">
                        </div>
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4 pt-2">Forgot Password</h3>
                                </div>
                                <div class="w-100">
                                    <p class="social-media d-flex justify-content-end">
                                        <a href="#" class="d-flex align-items-center justify-content-center"><img
                                                src="images/Jindal logo Revised.png" width="110"></a>
                                    </p>
                                </div>
                            </div>
                            <form  method="POST" class="forgot-password-form" action="forgot-password.php">
                                <div class="form-group mb-3">
                                    <label class="label" for="email">EMAIL</label>
                                    <input id="email" name="email" type="text" class="form-control" placeholder="Enter Email" required>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="form-control btn btn-primary rounded submit px-3">SEND EMAIL</button>
                                </div>
                            </form>
                            <p class="text-center">Not a member? <a href="signup.php">Sign Up</a></p>
                            <p class="text-center">Back to Login? <a href="login.php">Log In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap core JS and other libraries -->
    
	 <!-- Bootstrap core JS-->
	 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
 
	 <!-- JavaScript Libraries -->
	 <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
	 <script src="js/main.js"></script>
	 
    <!-- Include necessary JS libraries and scripts -->
</body>

</html>

<script>
             var email = document.getElementById("email").value.trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }
</script>
