"use strict";
var ModeloParcial;
(function (ModeloParcial) {
    let xhttp = new XMLHttpRequest();
    //MostrarUsuarios();
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
                console.log(respuesta); //respuesta.mensaje
                alert(respuesta);
            }
        };
    }
    ModeloParcial.AgregarUsuarioJSON = AgregarUsuarioJSON;
    function MostrarUsuariosJSON() {
        xhttp.open("GET", "./BACKEND/ListadoUsuariosJSON.php", true);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = xhttp.responseText;
                let usuariosJson = JSON.parse(respuesta);
                const contenerdorTabla = (document.getElementById("divTabla"));
                contenerdorTabla.innerHTML = "";
                const tabla = document.createElement("table");
                const tbody = document.createElement("tbody");
                const thead = document.createElement("thead");
                for (const key in usuariosJson[0]) {
                    const th = document.createElement("th");
                    let text = document.createTextNode(key.toUpperCase());
                    th.appendChild(text);
                    thead.appendChild(th);
                }
                usuariosJson.forEach((usuario) => {
                    const tr = document.createElement("tr");
                    for (const key in usuario) {
                        const td = document.createElement("td");
                        let text = document.createTextNode(usuario[key]);
                        td.appendChild(text);
                        tr.appendChild(td);
                    }
                    tbody.appendChild(tr);
                });
                tabla.appendChild(thead);
                tabla.appendChild(tbody);
                contenerdorTabla.appendChild(tabla);
            }
        };
        xhttp.send();
    }
    ModeloParcial.MostrarUsuariosJSON = MostrarUsuariosJSON;
    function VerificarUsuarioJSON() {
        let correo = document.getElementById("correo").value;
        let clave = document.getElementById("clave").value;
        let usuario = { correo: correo, clave: clave };
        xhttp.open("POST", "./BACKEND/VerificarUsuarioJSON.php", true);
        let form = new FormData();
        form.append("usuario_json", JSON.stringify(usuario));
        xhttp.send(form);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = xhttp.responseText;
                console.log(respuesta);
                alert(respuesta);
            }
        };
    }
    ModeloParcial.VerificarUsuarioJSON = VerificarUsuarioJSON;
    ///////////////////////////////////////////////
    //////////////   USUARIO DB     ///////////////
    function AgregarUsuario() {
        let nombre = document.getElementById("nombre").value;
        let correo = document.getElementById("correo").value;
        let clave = document.getElementById("clave").value;
        let id_perfil = (document.getElementById("cboPerfiles")).value;
        xhttp.open("POST", "./BACKEND/AltaUsuario.php", true);
        let form = new FormData();
        form.append("nombre", nombre);
        form.append("correo", correo);
        form.append("clave", clave);
        form.append("id_perfil", id_perfil);
        xhttp.send(form);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = xhttp.responseText;
                console.log(respuesta);
                alert(respuesta);
            }
        };
    }
    ModeloParcial.AgregarUsuario = AgregarUsuario;
    function MostrarUsuarios() {
        xhttp.open("GET", "./BACKEND/ListadoUsuarios.php", true);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                const contenerdorTabla = (document.getElementById("divTabla"));
                let respuesta = xhttp.responseText;
                contenerdorTabla.innerHTML = respuesta;
                console.log(respuesta);
                alert(respuesta);
            }
        };
        xhttp.send();
    }
    ModeloParcial.MostrarUsuarios = MostrarUsuarios;
    function Modificar(usuario) {
        //cargo los campos
        document.getElementById("id").value = usuario.id;
        document.getElementById("nombre").value = usuario.nombre;
        document.getElementById("correo").value = usuario.correo;
        document.getElementById("clave").value = usuario.clave;
        document.getElementById("cboPerfiles").value = usuario.id_perfil;
        document.getElementById("id").disabled = true;
    }
    ModeloParcial.Modificar = Modificar;
    function ModificarUsuario() {
        const inputId = document.getElementById("id");
        const inputNombre = document.getElementById("nombre");
        const inputCorreo = document.getElementById("correo");
        const inputClave = document.getElementById("clave");
        const inputCboPerfiles = (document.getElementById("cboPerfiles"));
        let id = parseInt(inputId.value);
        let nombre = inputNombre.value;
        let correo = inputCorreo.value;
        let clave = inputClave.value;
        let id_perfil = parseInt(inputCboPerfiles.value);
        const usuario = {
            id: id,
            nombre: nombre,
            correo: correo,
            clave: clave,
            id_perfil: id_perfil,
        };
        xhttp.open("POST", "./BACKEND/ModificarUsuario.php", true);
        let form = new FormData();
        form.append("usuario_json", JSON.stringify(usuario));
        xhttp.send(form);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
                if (respuesta.exito) {
                    MostrarUsuarios();
                }
                else {
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);
                }
                inputId.disabled = false;
                inputId.value = "";
                inputNombre.value = "";
                inputCorreo.value = "";
                inputClave.value = "";
                inputCboPerfiles.value = "";
            }
        };
    }
    ModeloParcial.ModificarUsuario = ModificarUsuario;
    function EliminarUsuario(usuario) {
        let confirmacion = confirm(`Desea eliminar el usuario con nombre "${usuario.nombre}" y correo "${usuario.correo}" ?`);
        if (confirmacion) {
            xhttp.open("POST", "./BACKEND/EliminarUsuario.php", true);
            let form = new FormData();
            form.append("id", usuario.id.toString());
            form.append("accion", "borrar");
            xhttp.send(form);
            xhttp.onreadystatechange = () => {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    let respuesta = JSON.parse(xhttp.responseText);
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);
                    MostrarUsuarios();
                }
            };
        }
    }
    ModeloParcial.EliminarUsuario = EliminarUsuario;
    ////////////////////////////////////////////////////////
    //////////////   EMPLEADOS FOTO Y DB     ///////////////
    function MostrarEmpleado() {
        xhttp.open("GET", "./BACKEND/ListadoEmpleados.php", true);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                const contenerdorTabla = (document.getElementById("divTablaEmpleados"));
                let respuesta = xhttp.responseText; //contiene la tabla
                contenerdorTabla.innerHTML = respuesta;
            }
        };
        xhttp.send();
    }
    ModeloParcial.MostrarEmpleado = MostrarEmpleado;
    function AgregarEmpleado() {
        let nombre = document.getElementById("nombre").value;
        let correo = document.getElementById("correo").value;
        let clave = document.getElementById("clave").value;
        let id_perfil = (document.getElementById("cboPerfiles")).value;
        let sueldo = document.getElementById("sueldo").value;
        let foto = document.getElementById("foto");
        xhttp.open("POST", "./BACKEND/AltaEmpleado.php", true);
        let form = new FormData();
        form.append("nombre", nombre);
        form.append("correo", correo);
        form.append("clave", clave);
        form.append("id_perfil", id_perfil);
        form.append("sueldo", sueldo);
        form.append("foto", foto.files[0]);
        //para poder pasar la foto
        xhttp.setRequestHeader("enctype", "multipart/form-data");
        xhttp.send(form);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
                MostrarEmpleado();
            }
        };
    }
    ModeloParcial.AgregarEmpleado = AgregarEmpleado;
    function ModificarEmp() {
        const inpId = document.getElementById("id");
        const inpNombre = document.getElementById("nombre");
        const inpCorreo = document.getElementById("correo");
        const inpClave = document.getElementById("clave");
        const cboPerfiles = (document.getElementById("cboPerfiles"));
        const inpSueldo = document.getElementById("sueldo");
        let foto = document.getElementById("foto");
        let id = parseInt(inpId.value);
        let nombre = inpNombre.value;
        let correo = inpCorreo.value;
        let clave = inpClave.value;
        let id_perfil = parseInt(cboPerfiles.value);
        let sueldo = parseInt(inpSueldo.value);
        const empleado = {
            id: id,
            nombre: nombre,
            correo: correo,
            clave: clave,
            id_perfil: id_perfil,
            sueldo: sueldo,
            pathFoto: "",
        };
        xhttp.open("POST", "./BACKEND/ModificarEmpleado.php", true);
        xhttp.setRequestHeader("enctype", "multipart/form-data");
        let form = new FormData();
        form.append("empleado_json", JSON.stringify(empleado));
        form.append("foto", foto.files[0]);
        xhttp.send(form);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
                if (respuesta.exito) {
                    MostrarEmpleado();
                }
                else {
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);
                }
                inpId.disabled = false;
                inpId.value = "";
                inpNombre.value = "";
                inpCorreo.value = "";
                inpClave.value = "";
                cboPerfiles.value = "";
                inpSueldo.value = "";
            }
        };
    }
    ModeloParcial.ModificarEmp = ModificarEmp;
    function ModificarEmpleado(empleado) {
        document.getElementById("id").disabled = true;
        document.getElementById("id").value = empleado.id;
        document.getElementById("nombre").value = empleado.nombre;
        document.getElementById("correo").value = empleado.correo;
        document.getElementById("clave").value = empleado.clave;
        document.getElementById("cboPerfiles").value = empleado.id_perfil;
        document.getElementById("sueldo").value = empleado.sueldo;
        document.getElementById("imgFoto").src = empleado.foto;
    }
    ModeloParcial.ModificarEmpleado = ModificarEmpleado;
    function EliminarEmpleado(empleado) {
        let confirmacion = confirm(`Desea eliminar el empleado con nombre "${empleado.nombre}" y sueldo "${empleado.sueldo}" ?`);
        if (confirmacion) {
            xhttp.open("POST", "./BACKEND/EliminarEmpleado.php", true);
            let form = new FormData();
            form.append("id", empleado.id.toString());
            xhttp.send(form);
            xhttp.onreadystatechange = () => {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    let respuesta = JSON.parse(xhttp.responseText);
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);
                    MostrarEmpleado();
                }
            };
        }
    }
    ModeloParcial.EliminarEmpleado = EliminarEmpleado;
})(ModeloParcial || (ModeloParcial = {}));
//# sourceMappingURL=manejadora.js.map