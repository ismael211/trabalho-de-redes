<?php

include('../../inc/class.mail.php');


if(isset($_POST['email'])){
    if($_POST['email']==''){
        echo 'error||Por favor digite um email.';
    }elseif($_POST['assunto']==''){
        echo 'error||Por favor digite um assunto.';
    }elseif($_POST['mensagem']==''){
        echo 'error||Por favor digite uma mensagem.';
    }else{
        
    }

}else{
    echo 'Você não tem permissão para acessar esta página.';
    exit();
}

?>