<?php

include('./config/db.php');

global $email_verified, $email_already_verified, $activation_error;

if(!empty($_GET['token'])){
    $token = $_GET['token'];
} else {
    $token = '';
}

if($token != ''){
    $sqlQuery = mysqli_query($conn, "SELECT * FROM user WHERE token = '$token'");
    $countRow = mysqli_num_rows($sqlQuery);

    if($countRow == 1 ){
        while($rowData = mysqli_fetch_array($sqlQuery)){
            $is_active = $rowData['is_active'];
            if($is_active == 0){
                $update = mysqli_query($conn, "UPDATE user SET is_active = '1' WHERE token = '$token'");
                if($update){
                    $email_verified = '<div class="alert alert-success">L\'email a été vérfié avec succès.</div>';
                } 
            } else {
                    $email_already_verified = '<div class="alert alert-danger">L\'email a déjà été vérifié.</div>';
            }
        }
    } else {
        $activation_error = '<div class="alert alert-danger">Erreur d\'activation de votre email.</div>';
    }
}

?>