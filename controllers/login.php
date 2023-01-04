<?php

include('./config/db.php');

global $wrongPwdErr, $accountNotExistErr, $emailPwdErr, $verificationRequiredErr, $email_empty_err, $pass_empty_err;

if (isset($_POST['login'])) {
    $email_signin = $_POST['email_signin'];
    $password_signin = $_POST['password_signin'];

    $user_email = filter_var($email_signin, FILTER_SANITIZE_EMAIL);
    $pswd = mysqli_real_escape_string($conn, $password_signin);

    $sql = "SELECT * From user WHERE email = '{$email_signin}' ";
    $query = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($query);

    if (!$query) {
        die("Requête SQL échouée: " . mysqli_error($conn));
    }

    if (!empty($email_signin) && !empty($password_signin)) {
        if (!preg_match("/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{6,20}$/", $pswd)) {
            $wrongPwdErr = '<div class="alert alert-danger">
                        Le mot de passe doit contenir au moins 6 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial.
                    </div>';
        }
        if ($rowCount <= 0) {
            $accountNotExistErr = '<div class="alert alert-danger">
                        Le compte n\'existe pas.
                    </div>';
        } else {
            while ($row = mysqli_fetch_array($query)) {
                $id = $row['id'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $email = $row['email'];
                $mobilenumber = $row['mobilenumber'];
                $pass_word = $row['password'];
                $token = $row['token'];
                $is_active = $row['is_active'];
            }

            $password = password_verify($password_signin, $pass_word);

            if ($is_active == '1') {
                if ($email_signin == $email && $password_signin == $password) {
                    header("Location: ./dashboard.php");

                    $_SESSION['id']            = $id;
                    $_SESSION['firstname']     = $firstname;
                    $_SESSION['lastname']      = $lastname;
                    $_SESSION['email']         = $email;
                    $_SESSION['mobilenumber']  = $mobilenumber;
                    $_SESSION['token']         = $token;
                    echo $firstname;

                } else {
                    $emailPwdErr = '<div class="alert alert-danger">
                                L\'email ou le mot de passe est incorrect.
                            </div>';
                }
            } else {
                $verificationRequiredErr = '<div class="alert alert-danger">
                            La vérification de votre email est requise.
                        </div>';
            }

        }

    } else {
        if (empty($email_signin)) {
            $email_empty_err = "<div class='alert alert-danger email_alert'>
                            L\'email n\'est pas fourni.
                    </div>";
        }
        if (empty($password_signin)) {
            $pass_empty_err = "<div class='alert alert-danger email_alert'>
                            Le mot de passe n\'est pas fourni.
                        </div>";
        }
    }
}
?>