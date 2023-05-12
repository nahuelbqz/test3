namespace ModeloParcial{

        let xhttp:XMLHttpRequest = new XMLHttpRequest();

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
                    console.log(respuesta);
                    alert(respuesta);
                }
            };
        }

        //terminar el listado
        export function ListadoUsuariosJSON() 
        {
            xhttp.open("GET", "./BACKEND/ListadoUsuariosJSON.php", true);

            xhttp.send();

            xhttp.onreadystatechange = () => 
            {
                if (xhttp.readyState == 4 && xhttp.status == 200) 
                {                    
                    let respuesta = xhttp.responseText
                    console.log(respuesta);
                    alert(respuesta);
                }
            };	
        }
}