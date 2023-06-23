
$(()=>{

    VerificarJWT();

    ListarJuguetes();
    //AltaJuguetes();

 });

function VerificarJWT() {
    
    //RECUPERO DEL LOCALSTORAGE
    let jwt = localStorage.getItem("jwt");

    $.ajax({
        type: 'GET',
        url: URL_API + "login",//voy a verificar para ver si esta OK
        dataType: "json",
        data: {},
        headers : {'Authorization': 'Bearer ' + jwt},//teniendo en cuenta que lo pase por bearer
        async: true
    })
    .done(function (obj_rta:any) {

        console.log(obj_rta);

        if(obj_rta.exito){//si el token fue exito true

            let usuario = obj_rta.jwt.usuario;

            let alerta:string = ArmarAlert("<br>" + JSON.stringify(usuario) + "<br>");

            $("#nombre_usuario").html(usuario.nombre_usuario);
        }
        else{//si fue false lo devuelvo al index para que loguee

            let alerta:string = ArmarAlert(obj_rta.mensaje, "danger");

            setTimeout(() => {
                $(location).attr('href', URL_BASE + "/login.html");
            }, 1500);
        }
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        
        let retorno = JSON.parse(jqXHR.responseText);

        let alerta:string = ArmarAlert(retorno.mensaje, "danger");

    });    
}


function ListarJuguetes() {
 
     $("#listado_juguetes").on("click", ()=>{
         ObtenerListadoJuguetes();
     });
 }
 
 function ObtenerListadoJuguetes() {
    
     $("#divTablaIzq").html("");
 
     let jwt = localStorage.getItem("jwt");
 
     $.ajax({
         type: 'GET',
         url: URL_API + "listarJuguetesBD",
         dataType: "json",
         data: {},
         headers : {'Authorization': 'Bearer ' + jwt},
         async: true
     })
     .done(function (resultado:any) {
 
         if(resultado.exito)
         {
             let tabla:string = ArmarTablaJuguetes(resultado.dato);
             $("#divTablaIzq").html(tabla).show(1000);
         }
         else
         {
             console.log("Token invalido");
             alert("Token invalido");
 
             setTimeout(() => {
                 $(location).attr("href", "./login.html");
               }, 2000);
         }       
     })
     .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
 
         //let retorno = JSON.parse(jqXHR.responseText);
        //let alerta:string = ArmarAlert(retorno.mensaje, "danger");
 
        console.log("Token invalido");
        alert("Token invalido");
 
        setTimeout(() => {
            $(location).attr("href", "./login.html");
          }, 2000);
 
         
     });    
 }
 
 function ArmarTablaJuguetes(juguetes:[]) : string 
 {   
     let tabla:string = '<table class="table table-success table-striped table-hover">';
     tabla += '<tr><th>MARCA</th><th>PRECIO</th><th>FOTO</th></tr>';
 
     if(juguetes.length == 0)
     {
         tabla += '<tr><td>---</td><td>---</td><td>---</td><td>---</td><th>---</td></tr>';
     }
     else
     {
         juguetes.forEach((toy : any) => {
             tabla += "<tr>";
             for (const key in toy) {
                 if(key != "path_foto") {
                     tabla += "<td>"+toy[key]+"</td>";
                 } else if(key == "path_foto"){
                     tabla += "<td><img src='"+URL_API+ toy.path_foto+"' width='50px' height='50px'></td>";
                 }
             }
             // tabla += "<td><a href='#' class='btn' data-action='modificar-usuario' data-obj_user='"+JSON.stringify(user)+"' title='Modificar'"+
             // " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-edit'></span></a>";
             // tabla += "<a href='#' class='btn' data-action='eliminar-usuario' data-obj_user='"+JSON.stringify(user)+"' title='Eliminar'"+
             // " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-times'></span></a>";
             // tabla += "</td>";
             tabla += "</tr>";    
         });
     }
 
     tabla += "</table>";
 
     return tabla;
 }
 