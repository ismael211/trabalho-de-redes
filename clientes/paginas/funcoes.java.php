<div class="col">

    <label for="anexo" class="btn btn-outline-primary mb-1"><i data-feather="file"></i><?php echo $l['p']['ticket']['anexo']; ?></label>
    <input name="anexo" type="file">

</div>

<div class="col">

    <label for="anexo" class="btn btn-outline-primary mb-1"><i data-feather="file"></i><?php echo $l['p']['ticket']['anexo']; ?></label>
    <input id="anexo" name="anexo[]" type="file" multiple style=" width: 0.1px; height: 0.1px; opacity: 0; overflow: hidden; position: absolute; z-index: -1;">

</div>

<script>
    // Este é um elemento que ficará visível apenas quando o usuário estiver
    // levando algum elemento para sua droppable

    zone
    const droppableZoneSign = document.getElementById('droppable-zone-sign')

    document.addEventListener('dragenter', event => {
        droppableZoneSign.classList.add('droppable')
    })

    document.addEventListener('dragleave', event => {
        droppableZoneSign.classList.remove('droppable')
    })

    document.addEventListener('dragover', event => {
        event.stopPropagation();
        event.preventDefault();
        droppableZoneSign.classList.add('droppable') // isso adiciona o sinal de mais (+) ao lado do cursor para indicar ao usuário
        // que uma ação diferente será tomada
        event.dataTransfer.dropEffect = 'copy';
    })

    document.addEventListener('drop', event => {
        outputEl.classList.remove('droppable')
        event.stopPropagation();
        event.preventDefault(); // trata o filesList
        appendFiles(event.dataTransfer.files)
    })
</script>

<form action="class" id="myForm">
    <input type="file" id="inpFile"><br>
    <button type="submit">Upload File</button>
</form>

<script>
    const myForm = document.getElementById("myForm");
    const inpFile = document.getElementById("inpFile");

    myForm.addEventListener("submit", e => {
        e.preventDefault();

        const endpoint = "upload.php";
        const formData = new FormData();

        formData.append("inpFile", inpFile.files[0]);

        fetch(endpoint, {
            method: "post",
            body: formData
        }).catch(console.error);

    });
</script>

<script type="text/javascript">
    function enviar() {

        //blockui('Enviando Ticket', 0) //Bloqueia a tela por tempo infinito

        var atencao = document.getElementById('atencao').value //variavel para usar como titulo


        var valor_em_texto = document.getElementById('mensagem_tickets').value //pega o valor do textarea
        document.getElementById('msg_tickets').value = valor_em_texto //Coloca o valor no input de cima


        $.post('paginas/Suporte.Tickets.Cria.php', $('#form_ticket input'), function(retorno) {


            console.log(retorno)

            const arrayretorno = retorno.split(",");
            var status = arrayretorno[0].split(":");
            var mensagem = arrayretorno[1].split(":");


            if (status[1] == '"erro"') {

                setTimeout(function() {
                    Swal.fire({
                        icon: 'info',
                        title: atencao,
                        text: mensagem[1],
                    })
                }, 1000);
                blockui('Enviando Ticket', 100) //Cancela o bloqueio de tela

            } else {

                swal(mensagem[1], 'success') //Chama o swal que redireciona para outra pagina

            }


        }, 'html');


    }

    function swal(mensagem, tipo) {

        var atencao = document.getElementById('atencao').value


        Swal.fire({
            icon: tipo,
            title: atencao,
            text: mensagem,
            confirmButtonText: 'OK',
        }).then((result) => {
            /* Read more about isConfirmed*/
            if (result.isConfirmed) {
                window.location.href = "index.php?pagina=app-ticket";
            }
        })
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#div_row_success').hide();
        $('#div_row_error').hide();
        max_size_file = 500000; // => MAX File size
        // => Every time you change file on form
        $("input:file").change(function() {
            var fileInput = $(this); // => Gets the file data
            if (fileInput.get(0).files.length) { // => Check if there is a file
                var fileSize = fileInput.get(0).files[0].size; // => Value in bytes
                if (fileSize > max_size_file) { // => Check if the file size is bigger tham MAX
                    alertErrorShow('Your file size is bigger then ' + max_size_file + ' KB');
                    return false; // => Ends action
                } else {
                    alertSuccessShow('Great Job');
                    return false; // => Ends action
                }
            } else {
                alertErrorShow('You have to choose one file');
                return false; // => Ends action
            }
        });

        // => Execute on click submit buttom
        $("#submit_button").click(function() {
            var fileInput = $('#file_input'); // => Gets the file data
            if (fileInput.get(0).files.length) { // => Check if there is a file
                var filename = fileInput.get(0).files[0].name; // => Gets the file name
                $("#list_group_span_name").html(filename);
                $("#list_group_span_size").html(fileInput.get(0).files[0].size + ' bytes');
                $("#list_group_span_type").html(fileInput.get(0).files[0].type);
                $("#list_group_span_ext").html(filename.split('.').pop());
                $("#list_group_span_modified").html(fileInput.get(0).files[0].lastModifiedDate);
                return false; // => This "return false" is just becase i dont want to send de request
            } else {
                alertErrorShow('You have to choose one file');
                return false; // => Ends action
            }
        });
    });

    // => Just show alerts
    function alertErrorShow(message) {
        $('#div_row_success').hide();
        $('#div_alert_span_error').html(message);
        $('#div_row_error').show();
    }

    // => Just show alerts
    function alertSuccessShow(message) {
        $('#div_row_error').hide();
        $('#div_alert_span_success').html(message);
        $('#div_row_success').show();
    }
</script>