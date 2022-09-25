<?php

//require_once('../../inc/class.mail.php');

if(isset($_POST['email'])){
    if($_POST['email']==''){
        echo 'error||Por favor digite um email.';
    }elseif($_POST['assunto']==''){
        echo 'error||Por favor digite um assunto.';
    }elseif($_POST['mensagem']==''){
        echo 'error||Por favor digite uma mensagem.';
    }else{
        $email = $_POST['email'];
        $assunto = $_POST['assunto'];
        $mensagem = $_POST['mensagem'];

        envia_Email($email, $assunto, $mensagem);
    }

}else{
    ?>
    <h1>Você não tem permissão para acessar esta página.</h1>
    <?php
    exit();
}

?>