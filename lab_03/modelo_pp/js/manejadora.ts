namespace ModeloParcial{

    let xhttp:XMLHttpRequest = new XMLHttpRequest();

    //MostrarUsuarios();
    /////////////////////////////////////////
    ///////////  USUARIOS JSON  /////////////
    
    export function AgregarUsuarioJSON() 
    {
        let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
        let correo = (<HTMLInputElement>document.getElementById("correo")).value;
        let clave = (<HTMLInputElement>document.getElementById("clave")).value;
            
        xhttp.open("POST", "./BACKEND/AltaUsuarioJSON.php", true);
            
        let form : FormData = new FormData();
        form.append("nombre", nombre);
        form.append("correo", correo);
        form.append("clave", clave);
    
        xhttp.send(form);
    
        xhttp.onreadystatechange = () =>
        {
            if(xhttp.readyState == 4 && xhttp.status == 200)
            {
                let respuesta = xhttp.responseText
                console.log(respuesta);//respuesta.mensaje
                alert(respuesta);
            }
        };
    }

    export function MostrarUsuariosJSON()
    {
        xhttp.open("GET", "./BACKEND/ListadoUsuariosJSON.php", true);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
            let respuesta:string = xhttp.responseText;
            let usuariosJson = JSON.parse(respuesta);

            const contenerdorTabla : HTMLDivElement = <HTMLDivElement>(document.getElementById("divTabla"));

            contenerdorTabla.innerHTML = "";

            const tabla : HTMLTableElement = document.createElement("table");
            const tbody = document.createElement("tbody");
            const thead = document.createElement("thead");
            
            for (const key in usuariosJson[0]) 
            {
                const th = document.createElement("th");
                let text = document.createTextNode(key.toUpperCase());
                th.appendChild(text);
                thead.appendChild(th);
            }

            usuariosJson.forEach((usuario: any) => {
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

    
    export function VerificarUsuarioJSON() 
    {
        let correo: string = (<HTMLInputElement>document.getElementById("correo")).value;
        let clave: string = (<HTMLInputElement>document.getElementById("clave")).value;

        let usuario = { correo: correo, clave: clave };

        xhttp.open("POST", "./BACKEND/VerificarUsuarioJSON.php", true);

        let form: FormData = new FormData();

        form.append("usuario_json", JSON.stringify(usuario));

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if(xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = xhttp.responseText;
                console.log(respuesta);
                alert(respuesta);
            }
        };
    }


    ///////////////////////////////////////////////
    //////////////   USUARIO DB     ///////////////

    export function AgregarUsuario()
    {
        let nombre: string = (<HTMLInputElement>document.getElementById("nombre")).value;
        let correo: string = (<HTMLInputElement>document.getElementById("correo")).value;
        let clave: string = (<HTMLInputElement>document.getElementById("clave")).value;
        let id_perfil: string = (<HTMLInputElement>(document.getElementById("cboPerfiles"))).value;

        xhttp.open("POST", "./BACKEND/AltaUsuario.php", true);

        let form : FormData = new FormData();
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

    export function MostrarUsuarios()
    {
        xhttp.open("GET", "./BACKEND/ListadoUsuarios.php", true);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                const contenerdorTabla: HTMLDivElement = <HTMLDivElement>(document.getElementById("divTabla"));
                let respuesta: string = xhttp.responseText;

                contenerdorTabla.innerHTML = respuesta;
                console.log(respuesta);
                alert(respuesta);
            }
        };

        xhttp.send();
    }

    export function Modificar(usuario: any) 
    {
        //cargo los campos
        (<HTMLInputElement>document.getElementById("id")).value = usuario.id;
        (<HTMLInputElement>document.getElementById("nombre")).value = usuario.nombre;
        (<HTMLInputElement>document.getElementById("correo")).value = usuario.correo;
        (<HTMLInputElement>document.getElementById("clave")).value = usuario.clave;
        (<HTMLInputElement>document.getElementById("cboPerfiles")).value = usuario.id_perfil;

        (<HTMLInputElement>document.getElementById("id")).disabled = true;
    }

    export function ModificarUsuario() 
    {
        const inputId = <HTMLInputElement>document.getElementById("id");
        const inputNombre = <HTMLInputElement>document.getElementById("nombre");
        const inputCorreo = <HTMLInputElement>document.getElementById("correo");
        const inputClave = <HTMLInputElement>document.getElementById("clave");
        const inputCboPerfiles = <HTMLInputElement>(document.getElementById("cboPerfiles"));

        let id: number = parseInt(inputId.value);
        let nombre: string = inputNombre.value;
        let correo: string = inputCorreo.value;
        let clave: string = inputClave.value;
        let id_perfil: number = parseInt(inputCboPerfiles.value);

        const usuario = {
            id: id,
            nombre: nombre,
            correo: correo,
            clave: clave,
            id_perfil: id_perfil,
        };

        xhttp.open("POST", "./BACKEND/ModificarUsuario.php", true);
    
        let form: FormData = new FormData();
    
        form.append("usuario_json", JSON.stringify(usuario));
        
        xhttp.send(form);
    
        xhttp.onreadystatechange = () => {
            if(xhttp.readyState == 4 && xhttp.status == 200)
            {
                let respuesta = JSON.parse(xhttp.responseText);
                if(respuesta.exito){
                    MostrarUsuarios();
                } else {
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

    export function EliminarUsuario(usuario: any) 
    {
        let confirmacion =
        confirm(`Desea eliminar el usuario con nombre "${usuario.nombre}" y correo "${usuario.correo}" ?`);
    
        if(confirmacion) 
        {
            xhttp.open("POST", "./BACKEND/EliminarUsuario.php", true);
    
            let form: FormData = new FormData();
    
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
  

    ////////////////////////////////////////////////////////
    //////////////   EMPLEADOS FOTO Y DB     ///////////////
    export function MostrarEmpleado() 
    {
        xhttp.open("GET", "./BACKEND/ListadoEmpleados.php", true);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                const contenerdorTabla: HTMLDivElement = <HTMLDivElement>(document.getElementById("divTablaEmpleados"));
                let respuesta: string = xhttp.responseText;//contiene la tabla
                contenerdorTabla.innerHTML = respuesta;
            }
        };

        xhttp.send();
    }

    
    export function AgregarEmpleado()
    {
        let nombre: string = (<HTMLInputElement>document.getElementById("nombre")).value;
        let correo: string = (<HTMLInputElement>document.getElementById("correo")).value;
        let clave: string = (<HTMLInputElement>document.getElementById("clave")).value;
        let id_perfil: string = (<HTMLInputElement>(document.getElementById("cboPerfiles"))).value;
        let sueldo: string = (<HTMLInputElement>document.getElementById("sueldo")).value;
        let foto: any = <HTMLInputElement>document.getElementById("foto");

        xhttp.open("POST", "./BACKEND/AltaEmpleado.php", true);

        let form: FormData = new FormData();

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
            if (xhttp.readyState == 4 && xhttp.status == 200)
            {
                let respuesta = JSON.parse(xhttp.responseText);
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);

                MostrarEmpleado();
            }
        };
    }

    
    export function ModificarEmp()
    {
        const inpId = <HTMLInputElement>document.getElementById("id");
        const inpNombre = <HTMLInputElement>document.getElementById("nombre");
        const inpCorreo = <HTMLInputElement>document.getElementById("correo");
        const inpClave = <HTMLInputElement>document.getElementById("clave");
        const cboPerfiles = <HTMLInputElement>(document.getElementById("cboPerfiles"));
        const inpSueldo = <HTMLInputElement>document.getElementById("sueldo");
        let foto: any = <HTMLInputElement>document.getElementById("foto");

        let id: number = parseInt(inpId.value);
        let nombre: string = inpNombre.value;
        let correo: string = inpCorreo.value;
        let clave: string = inpClave.value;
        let id_perfil: number = parseInt(cboPerfiles.value);
        let sueldo: number = parseInt(inpSueldo.value);

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

        let form: FormData = new FormData();

        form.append("empleado_json", JSON.stringify(empleado));
        form.append("foto", foto.files[0]);

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) 
            {
                let respuesta = JSON.parse(xhttp.responseText);

                if (respuesta.exito) {
                    MostrarEmpleado();
                } else {
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

    export function ModificarEmpleado(empleado: any) 
    {
        (<HTMLInputElement>document.getElementById("id")).disabled = true;

        (<HTMLInputElement>document.getElementById("id")).value = empleado.id;
        (<HTMLInputElement>document.getElementById("nombre")).value = empleado.nombre;
        (<HTMLInputElement>document.getElementById("correo")).value = empleado.correo;
        (<HTMLInputElement>document.getElementById("clave")).value = empleado.clave;
        (<HTMLInputElement>document.getElementById("cboPerfiles")).value = empleado.id_perfil;
        (<HTMLInputElement>document.getElementById("sueldo")).value = empleado.sueldo;
        (<HTMLImageElement>document.getElementById("imgFoto")).src = empleado.foto;

    }


    export function EliminarEmpleado(empleado: any) 
    {
        let confirmacion = confirm(`Desea eliminar el empleado con nombre "${empleado.nombre}" y sueldo "${empleado.sueldo}" ?`);
    
        if (confirmacion) 
        {
            xhttp.open("POST", "./BACKEND/EliminarEmpleado.php", true);

            let form: FormData = new FormData();
        
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


}