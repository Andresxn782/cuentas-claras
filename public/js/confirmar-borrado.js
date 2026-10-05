const dialogo = document.getElementById('dialogo-borrar');
const botonCancelar = document.getElementById('cancelar-borrar');
const botonConfirmar = document.getElementById('confirmar-borrar');

// Aquí guardamos el formulario que el usuario quiere enviar
let formularioPendiente = null;

// A cada formulario de borrar le decimos qué hacer al enviarse
document.querySelectorAll('.form-borrar').forEach(function (formulario) {
  formulario.addEventListener('submit', function (evento) {
    evento.preventDefault(); // Paramos el envío
    formularioPendiente = formulario; // Recordamos cuál era
    dialogo.showModal(); // Abrimos la ventana
  });
});

// Cancelar: cerramos la ventana y no hacemos nada más
botonCancelar.addEventListener('click', function () {
  dialogo.close();
});

// Confirmar: enviamos el formulario que estaba esperando
botonConfirmar.addEventListener('click', function () {
  formularioPendiente.submit();
});
