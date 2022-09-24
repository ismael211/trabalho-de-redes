const resultado = result;
const myObj = JSON.parse(resultado);

Swal.close();

if (myObj.status == 'success') {

    Swal.fire({
        title: '<div><span style="font-weight:bold;color:black"><?php echo $l['concluido']; ?>!</span><div>',
        html: myObj.mensagem,
        icon: 'success',
        width: '900px',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })

} else {

    Swal.fire({
        title: '<div><span style="font-weight:bold;color:black"><?php echo $l['atencao']; ?>!</span><div>',
        html: myObj.mensagem,
        icon: 'error',
        width: '900px',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
}