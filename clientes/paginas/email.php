<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Enviar Email</title>
    <link rel="apple-touch-icon" href="../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/vendors/css/editors/quill/katex.min.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/vendors/css/editors/quill/monokai-sublime.min.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/vendors/css/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Inconsolata&amp;family=Roboto+Slab&amp;family=Slabo+27px&amp;family=Sofia&amp;family=Ubuntu+Mono&amp;display=swap">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/themes/bordered-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/plugins/forms/form-quill-editor.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/pages/app-email.css">
    <!-- END: Page CSS-->

    <script src="../../app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
    <script src="../../app-assets/js/scripts/extensions/ext-component-sweet-alerts.js"></script>


    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../app-assets/css/plugins/extensions/ext-component-sweet-alerts.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../../assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern content-left-sidebar navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="content-left-sidebar">


    <!-- BEGIN: Content-->
    <div class="app-content content email-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="row">
            <div class="col-12">
                <!-- compose email -->
                <div class="card" id="compose-mail">
                    <div class="card-header">
                        <h5 class="modal-title">Enviar email</h5>
                    </div>
                    <div class="card-body flex-grow-1 p-0">
                        <form class="compose-form">
                            <div class="compose-mail-form-field">
                                <label for="email">Para: </label>
                                <input type="text" id="email" class="form-control" placeholder="Email" name="emaill" />
                            </div>
                            <div class="compose-mail-form-field">
                                <label for="assunto">Assunto: </label>
                                <input type="text" id="assunto" class="form-control" placeholder="Assunto" name="Assunto" />
                            </div>
                            <textarea class="form-control" name="mensagem" id="mensagem" cols="80" rows="10" placeholder="Digite uma mensagem" style="margin-left: 20px; margin-top: 10px;"></textarea>
                            <div class="compose-footer-wrapper">
                                <div class="btn-wrapper d-flex align-items-center">
                                    <div class="btn-group dropup mr-1">
                                        <button type="button" class="btn btn-primary" style="float: right;" id="enviar">Enviar</button>
                                    </div>
                                    <!-- add attachment -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ compose email -->
    </div>

    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>


    <!-- BEGIN: Vendor JS-->
    <script src="../../app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="../../app-assets/vendors/js/editors/quill/katex.min.js"></script>
    <script src="../../app-assets/vendors/js/editors/quill/highlight.min.js"></script>
    <script src="../../app-assets/vendors/js/editors/quill/quill.min.js"></script>
    <script src="../../app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="../../app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../../app-assets/js/core/app-menu.js"></script>
    <script src="../../app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->


    <!-- BEGIN: Page JS-->
    <script src="../../app-assets/js/scripts/pages/app-email.js"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>

<script>
    $(document).ready(function() {

        $("#enviar").click(function(e) {

            var email = document.getElementById('email').value;
            var assunto = document.getElementById('assunto').value;
            var mensagem = document.getElementById('mensagem').value;

            var dados = "email=" + email + "&assunto=" + assunto + "&mensagem=" + mensagem;

            e.preventDefault();
            $.ajax({
                method: "POST",
                url: 'envia_email.php',
                data: dados,
                success: function(result) {

                    var data = result.split("||");
                    var status = data[0];

                    alert(status);

                    if (status == 'success') {

                        Swal.fire({
                            title: '<div><span style="font-weight:bold;color:black">Concluido!</span><div>',
                            html: data[1],
                            icon: 'success',
                            width: '900px',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        })

                    } else {

                        Swal.fire({
                            title: '<div><span style="font-weight:bold;color:black">Atenção!</span><div>',
                            html: data[1],
                            icon: 'error',
                            width: '900px',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        })
                    }

                },
            });
        })
    })
</script>