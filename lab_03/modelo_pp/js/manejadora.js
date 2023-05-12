"use strict";
var ModeloParcial;
(function (ModeloParcial) {
    let xhttp = new XMLHttpRequest();
    /////////////////////////////////////////
    ///////////  USUARIOS JSON  /////////////
    function AgregarUsuarioJSON() {
        let nombre = document.getElementById("nombre").value;
        let correo = document.getElementById("correo").value;
        let clave = document.getElementById("clave").value;
        xhttp.open("POST", "./BACKEND/AltaUsuarioJSON.php", true);
        let form = new FormData();
        form.append("nombre", nombre);
        form.append("correo", correo);
        form.append("clave", clave);
        xhttp.send(form);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = xhttp.responseText;
                console.log(respuesta);
                alert(respuesta);
            }
        };
    }
    ModeloParcial.AgregarUsuarioJSON = AgregarUsuarioJSON;
})(ModeloParcial || (ModeloParcial = {}));
//# sourceMappingURL=manejadora.js.map