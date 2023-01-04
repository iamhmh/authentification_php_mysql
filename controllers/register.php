<?php 
  include('./config/db.php');

  require_once './lib/vendor/autoload.php';
  
  global $success_msg, $email_exist, $f_NameErr, $l_NameErr, $_emailErr, $_mobileErr, $_passwordErr;
  global $fNameEmptyErr, $lNameEmptyErr, $emailEmptyErr, $mobileEmptyErr, $passwordEmptyErr, $email_verify_err, $email_verify_success;
  
  $_first_name = $_last_name = $_email = $_mobile_number = $_password = "";

  if(isset($_POST["submit"])) {
      $firstname     = $_POST["firstname"];
      $lastname      = $_POST["lastname"];
      $email         = $_POST["email"];
      $mobilenumber  = $_POST["mobilenumber"];
      $password      = $_POST["password"];

      $email_check_query = mysqli_query($conn, "SELECT * FROM user WHERE email = '{$email}' ");
      $rowCount = mysqli_num_rows($email_check_query);


      if(!empty($firstname) && !empty($lastname) && !empty($email) && !empty($mobilenumber) && !empty($password)){
          
          if($rowCount > 0) {
              $email_exist = '
                  <div class="alert alert-danger" role="alert">
                      Email déjà utilisé !
                  </div>
              ';
          } else {
              
              $_first_name = mysqli_real_escape_string($conn, $firstname);
              $_last_name = mysqli_real_escape_string($conn, $lastname);
              $_email = mysqli_real_escape_string($conn, $email);
              $_mobile_number = mysqli_real_escape_string($conn, $mobilenumber);
              $_password = mysqli_real_escape_string($conn, $password);

              
              if(!preg_match("/^[a-zA-Z ]*$/", $_first_name)) {
                  $f_NameErr = '<div class="alert alert-danger">
                          Seuls les lettres et les espaces sont autorisés.
                      </div>';
              }
              if(!preg_match("/^[a-zA-Z ]*$/", $_last_name)) {
                  $l_NameErr = '<div class="alert alert-danger">
                  Seuls les lettres et les espaces sont autorisés.
                      </div>';
              }
              if(!filter_var($_email, FILTER_VALIDATE_EMAIL)) {
                  $_emailErr = '<div class="alert alert-danger">
                          Le format de l\'email est invalide.
                      </div>';
              }
              if(!preg_match("/^[0-9]{10}+$/", $_mobile_number)) {
                  $_mobileErr = '<div class="alert alert-danger">
                          Le format du numéro de téléphone est invalide.
                      </div>';
              }
              if(!preg_match("/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{6,20}$/", $_password)) {
                  $_passwordErr = '<div class="alert alert-danger">
                           Le mot de passe doit être compris entre 6 et 20 caractères, contenir au moins un caractère spécial, minuscule, majuscule et un chiffre.
                      </div>';
              }
              
              
              if((preg_match("/^[a-zA-Z ]*$/", $_first_name)) && (preg_match("/^[a-zA-Z ]*$/", $_last_name)) &&
               (filter_var($_email, FILTER_VALIDATE_EMAIL)) && (preg_match("/^[0-9]{10}+$/", $_mobile_number)) && 
               (preg_match("/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{6,20}$/", $_password))){

                  $token = md5(rand().time());

                  $password_hash = password_hash($password, PASSWORD_BCRYPT);

                  $sql = "INSERT INTO user (firstname, lastname, email, mobilenumber, password, token, is_active,
                  date_time) VALUES ('{$firstname}', '{$lastname}', '{$email}', '{$mobilenumber}', '{$password_hash}', 
                  '{$token}', '0', now())";

                  $sqlQuery = mysqli_query($conn, $sql);
                  
                  if(!$sqlQuery){
                      die("La requête MySQL a échoué!" . mysqli_error($conn));
                  } 

                  if($sqlQuery) {
                      $msg = 'Cliquez sur le lien de vérification ci-dessous : <br><br>
                        <a href="user_verificaiton.php?token='.$token.'"> Cliquez pour vérifier votre email.</a>
                      ';
                      
                      $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                      ->setUsername('yourmail@gmail.com')
                      ->setPassword('yourpassword');

                      $mailer = new Swift_Mailer($transport);

                      $message = (new Swift_Message('Vérifiez votre email'))
                      ->setFrom([$email => $firstname . ' ' . $lastname])
                      ->setTo($email)
                      ->addPart($msg, "text/html")
                      ->setBody('Bonjour');

                      $result = $mailer->send($message);
                        
                      if(!$result){
                          $email_verify_err = '<div class="alert alert-danger">
                                  Une erreur s\'est produite lors de l\'envoi du courriel de vérification.
                          </div>';
                      } else {
                          $email_verify_success = '<div class="alert alert-success">
                                    Un email de vérification a été envoyé à votre adresse email.
                          </div>';
                      }
                  }
              }
          }
      } else {
          if(empty($firstname)){
              $fNameEmptyErr = '<div class="alert alert-danger">
                  Le prénom ne peut pas être vide.
              </div>';
          }
          if(empty($lastname)){
              $lNameEmptyErr = '<div class="alert alert-danger">
                  Le nom de famille ne peut pas être vide.
              </div>';
          }
          if(empty($email)){
              $emailEmptyErr = '<div class="alert alert-danger">
                  L\'adresse email ne peut pas être vide.
              </div>';
          }
          if(empty($mobilenumber)){
              $mobileEmptyErr = '<div class="alert alert-danger">
                  Le numéro de téléphone ne peut pas être vide.
              </div>';
          }
          if(empty($password)){
              $passwordEmptyErr = '<div class="alert alert-danger">
                  Le mot de passe ne peut pas être vide.
              </div>';
          }            
      }
  }
?>
