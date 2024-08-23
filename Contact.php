<?php
function send_smtp_mail($to, $username, $useremail, $subject, $message, )
{
    $smtpServer = "smtp.mailersend.net";
    $port = 587;
    $username = "MS_sDtbNW@trial-0r83ql3v3dmgzw1j.mlsender.net";
    $password = "nVoCvi6qi10CV63O";
    $from = "trial-0r83ql3v3dmgzw1j.mlsender.net";

    $socket = fsockopen($smtpServer, $port, $errno, $errstr, 30);

    if (!$socket) {
        return "Failed to connect to SMTP server: $errstr ($errno)";
    }
    
    function get_smtp_response($socket)
    {   
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $response;
    }

    $response = get_smtp_response($socket);
    if (strpos($response, "220") === false) {
        return "Unexpected response: $response";
    }

    fputs($socket, "EHLO $smtpServer\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "250") === false) {
        return "Unexpected response to EHLO: $response";
    }

    fputs($socket, "STARTTLS\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "220") === false) {
        return "Unexpected response to STARTTLS: $response";
    }

    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

    fputs($socket, "EHLO $smtpServer\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "250") === false) {
        return "Unexpected response to EHLO after STARTTLS: $response";
    }

    fputs($socket, "AUTH LOGIN\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "334") === false) {
        return "Unexpected response to AUTH LOGIN: $response";
    }

    fputs($socket, base64_encode($username) . "\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "334") === false) {
        return "Unexpected response to username: $response";
    }

    fputs($socket, base64_encode($password) . "\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "235") === false) {
        return "Unexpected response to password: $response";
    }

    fputs($socket, "MAIL FROM: <$from>\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "250") === false) {
        return "Unexpected response to MAIL FROM: $response";
    }

    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "250") === false) {
        return "Unexpected response to RCPT TO: $response";
    }

    fputs($socket, "DATA\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "354") === false) {
        return "Unexpected response to DATA: $response";
    }

    fputs($socket, "Subject: $subject\r\n$headers\r\n\r\n$message\r\n.\r\n");
    $response = get_smtp_response($socket);
    if (strpos($response, "250") === false) {
        return "Unexpected response to email body: $response";
    }

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return "Email sent successfully!";
}


//get data from the contact form
$message_sent = false;
if(isset($_POST['email']) && $_POST['email'] != '') {

    //submit the form
    $username = $_POST['name'];
    $useremail = $_POST['email'];
    $message = $_POST['message'];
    $subject = "Contact Form Email";
    
    $to = "zd78s.test@inbox.testmail.app";
    $body = "";
    $body .= "Form:".$username. "\r\n";
    $body .= "Email:".$useremail. "\r\n";
    $body .= "Message".$message. "\r\n";

    send_smtp_mail($to, $username, $useremail, $subject, $message,);
    $message_sent = true;
  }
  else {
    $invalid_class_name = "form-invalid";
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="styles.css" type="text/css">
    
<style>
 .footer {
    position: relative;
 } 

 @media(max-width: 768px) {
    .footer {
      position: relative;
  }
 }
     
 .container {
    border-radius: 5px;
    padding: 0px;
    display: flex;
    justify-content: center;    
  }

 input[type=text], select, textarea {
    width: 100%; 
    padding: 12px; 
    border: 1px solid #ccc; 
    border-radius: 4px; 
    box-sizing: border-box; 
    margin-top: 6px; 
    margin-bottom: 16px; 
    resize: vertical; 
  }

 input[type=submit] {
    background-color:  #464555;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
 @media screen and (min-width: 768px) {
    .container {
        padding: 10px;
    }
 }

 .form-invalid {
  outline: 2px solid red !important;
 }

.afterr {
    border-radius: 5px;
    padding: 0px;
    display: flex;
    justify-content: center;
    height: 60vh;
    font-size: 2em;
    font-family: serif;
    line-height: 16;
}
</style>
</head>
<body>

    <nav id="navbar" class="">
        <div class="nav-wrapper">
            <div class="logo">
            <a href="index.html">portfolio</a>
        </div>
          
        <ul id="menu">
             <li><a href="index.html">Home</a></li>
             <li><a href="About.html">About</a></li>
             <li><a href="Contact.php">Contact</a></li>
        </ul>
        </div>
   </nav>
   <br><br>

   <?php
   if($message_sent):
   ?>

     <h3 class="afterr">Thanks we'll be in touch...</h3>
     
   <?php 
   else:
   ?>

   <div class="container">
    <form action="Contact.php" method="post">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" placeholder="Your name..">
        <label for="email">Email</label>
      <input <?= $invalid_class_name ?? "" ?> type="text" id="email" name="email" placeholder="Your email..">
      <label for="message">message</label>
      <textarea id="message" name="message" placeholder="Write something.." style="height:200px"></textarea>
      <input type="submit" value="Submit">
    </form>
  </div>

  <?php
  endif;
  ?>



  
  <br><br><br><br><br><br><br><br><br><br>
  
   <footer class="footer">
    <p class="footer-content">©2024</a></p>
  </footer>

</body>
</html>