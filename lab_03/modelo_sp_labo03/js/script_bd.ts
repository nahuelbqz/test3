$(()=>{

    VerificarJWT();//apenas carga la pagina verifica el token para ver si es valido

    AdministrarVerificarJWT();//llama a verificarJWT

    AdministrarLogout();//remuevo el jwt del local storage

    AdministrarListar();

    AdministrarAgregar();

});


function VerificarJWT() {
    
    //RECUPERO DEL LOCALSTORAGE
    let jwt = localStorage.getItem("jwt");

    $.ajax({
        type: 'GET',
        url: URL_API + "verificar_token",//voy a verificar para ver si esta OK
        dataType: "json",
        data: {},
        headers : {'Authorization': 'Bearer ' + jwt},//teniendo en cuenta que lo pase por bearer
        async: true
    })
    .done(function (obj_rta:any) {

        console.log(obj_rta);

        if(obj_rta.exito){//si el token fue exito true

            let app = obj_rta.jwt.api;
            let usuario = obj_rta.jwt.usuario;

            let alerta:string = ArmarAlert(app + "<br>" + JSON.stringify(usuario));

            $("#divResultado").html(alerta).toggle(2000);
            $("#rol").html(usuario.rol);
        }
        else{//si fue false lo devuelvo al index para que loguee

            let alerta:string = ArmarAlert(obj_rta.mensaje, "danger");

            $("#divResultado").html(alerta).toggle(2000);

            setTimeout(() => {
                $(location).attr('href', URL_BASE + "index.html");
            }, 1500);
        }
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {

        let retorno = JSON.parse(jqXHR.responseText);

        let alerta:string = ArmarAlert(retorno.mensaje, "danger");

        $("#divResultado").html(alerta).show(2000);
    });    
}

function AdministrarVerificarJWT() {
    
    $("#verificarJWT").on("click", ()=>{

        VerificarJWT();//llama a verificarJWT para comprobar si es valido
    });
}

function AdministrarLogout() {

    $("#logout").on("click", ()=>{

        localStorage.removeItem("jwt");//remuevo el jwt del local storage

        let alerta:string = ArmarAlert('Usuario deslogueado!');
    
        $("#divResultado").html(alerta).show(2000);

        setTimeout(() => {
            $(location).attr('href', URL_BASE + "index.html");//lo mando al login para que loguee dnuevo
        }, 1500);

    });

}

function AdministrarListar() {

    $("#listar_producto").on("click", ()=>{
        ObtenerListadoProductos();
    });
}

function AdministrarAgregar() {

    $("#alta_producto").on("click", ()=>{
        ArmarFormularioAlta();
    });
}

function ObtenerListadoProductos() {
   
    $("#divResultado").html("");

    let jwt = localStorage.getItem("jwt");

    $.ajax({
        type: 'GET',
        url: URL_API + "productos_bd",
        dataType: "json",
        data: {},
        headers : {'Authorization': 'Bearer ' + jwt},
        async: true
    })
    .done(function (resultado:any) {//devuelve un array de obj json

        console.log(resultado);

        let tabla:string = ArmarTablaProductos(resultado);//armo la tabla

        $("#divResultado").html(tabla).show(1000);//muestro en el div la tabla

        //data accion = los datos de esa linea
        $('[data-action="modificar"]').on('click', function (e) {//al btn modificar del listado en evento click
            
            let obj_prod_string : any = $(this).attr("data-obj_prod");
            let obj_prod = JSON.parse(obj_prod_string);

            let formulario = MostrarForm("modificacion", obj_prod);//muestro from modificacion
        
            $("#cuerpo_modal_prod").html(formulario);           
        });
   
        $('[data-action="eliminar"]').on('click', function (e) {//al btn eliminar del listado en evento click

            let obj_prod_string : any = $(this).attr("data-obj_prod");
            let obj_prod = JSON.parse(obj_prod_string);

            let formulario = MostrarForm("baja", obj_prod);
        
            $("#cuerpo_modal_prod").html(formulario);
        });
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {

        let retorno = JSON.parse(jqXHR.responseText);

        let alerta:string = ArmarAlert(retorno.mensaje, "danger");

        $("#divResultado").html(alerta).show(2000);
    });    
}

function ArmarTablaProductos(productos:[]) : string 
{   
    let tabla:string = '<table class="table table-dark table-hover">';
    tabla += '<tr><th>CÓDIGO</th><th>MARCA</th><th>PRECIO</th><th>FOTO</th><th style="width:110px">ACCIONES</th></tr>';

    if(productos.length == 0)
    {
        tabla += '<tr><td>---</td><td>---</td><td>---</td><td>---</td><th>---</td></tr>';
    }
    else
    {
        productos.forEach((prod : any) => {

            tabla += "<tr><td>"+prod.codigo+"</td><td>"+prod.marca+"</td><td>"+prod.precio+"</td>"+
            "<td><img src='"+URL_API+prod.path+"' width='50px' height='50px'></td><th>"+
            "<a href='#' class='btn' data-action='modificar' data-obj_prod='"+JSON.stringify(prod)+"' title='Modificar'"+
            " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-edit'></span></a>"+
            "<a href='#' class='btn' data-action='eliminar' data-obj_prod='"+JSON.stringify(prod)+"' title='Eliminar'"+
            " data-toggle='modal' data-target='#ventana_modal_prod' ><span class='fas fa-times'></span></a>"+
            "</td></tr>";
        });
    }

    tabla += "</table>";

    return tabla;
}

function ArmarFormularioAlta()
{
    $("#divResultado").html("");

    let formulario = MostrarForm("alta");

    $("#divResultado").html(formulario).show(1000);
}

function MostrarForm(accion:string, obj_prod:any=null) : string 
{
    let funcion = "";
    let encabezado = "";
    let solo_lectura = "";
    let solo_lectura_pk = "";

    switch (accion) {
        case "alta":
            funcion = 'Agregar(event)';
            encabezado = 'AGREGAR PRODUCTO';
            break;

         case "baja":
            funcion = 'Eliminar(event)';
            encabezado = 'ELIMINAR PRODUCTO';
            solo_lectura = "readonly";
            solo_lectura_pk = "readonly";
            break;
    
        case "modificacion":
            funcion = 'Modificar(event)';
            encabezado = 'MODIFICAR PRODUCTO';
            solo_lectura_pk = "readonly";
            break;
    }

    let codigo = "";
    let marca = "";
    let precio = "";
    let path = URL_BASE+"/img/usr_default.jpg";

    if (obj_prod !== null) 
    {
        codigo = obj_prod.codigo;
        marca = obj_prod.marca;
        precio = obj_prod.precio;
        path = URL_API + obj_prod.path;       
    }

    let form:string = '<h3 style="padding-top:1em;">'+encabezado+'</h3>\
                        <div class="row justify-content-center">\
                            <div class="col-md-8">\
                                <form class="was-validated">\
                                    <div class="form-group">\
                                        <label for="codigo">Código:</label>\
                                        <input type="text" class="form-control " id="codigo" value="'+codigo+'" '+solo_lectura_pk+' required>\
                                    </div>\
                                    <div class="form-group">\
                                        <label for="marca">Título:</label>\
                                        <input type="text" class="form-control" id="marca" placeholder="Ingresar marca"\
                                            name="marca" value="'+marca+'" '+solo_lectura+' required>\
                                        <div class="valid-feedback">OK.</div>\
                                        <div class="invalid-feedback">Valor requerido.</div>\
                                    </div>\
                                    <div class="form-group">\
                                        <label for="precio">Precio:</label>\
                                        <input type="number" class="form-control" id="precio" placeholder="Ingresar precio" name="precio"\
                                            value="'+precio+'" '+solo_lectura+' required>\
                                        <div class="valid-feedback">OK.</div>\
                                        <div class="invalid-feedback">Valor requerido.</div>\
                                    </div>\
                                    <div class="form-group">\
                                        <label for="foto">Foto:</label>\
                                        <input type="file" class="form-control" id="foto" name="foto" '+solo_lectura+' required>\
                                        <div class="valid-feedback">OK.</div>\
                                        <div class="invalid-feedback">Valor requerido.</div>\
                                    </div>\
                                    <div class="row justify-content-between"><img id="img_prod" src="'+path+'" width="400px" height="200px"></div><br>\
                                    <div class="row justify-content-between">\
                                        <input type="button" class="btn btn-danger" data-dismiss="modal" value="Cerrar">\
                                        <button type="submit" class="btn btn-primary" data-dismiss="modal" onclick="'+funcion+'" >Aceptar</button>\
                                    </div>\
                                </form>\
                            </div>\
                        </div>';

    return form;
}

function Agregar(e:any):void 
{  
    e.preventDefault();

    let jwt = localStorage.getItem("jwt");//recupero la info del jwt 

    //recupero la info + la foto
    let codigo = $("#codigo").val();
    let marca = $("#marca").val();
    let precio = $("#precio").val();
    let foto: any = (<HTMLInputElement>document.getElementById("foto"));

    let form = new FormData();
    form.append("obj", JSON.stringify({"codigo":codigo, "marca":marca, "precio":precio}));
    form.append("foto", foto.files[0]);

    $.ajax({
        type: 'POST',
        url: URL_API + "productos_bd",
        dataType: "text",//json o text
        cache: false,
        contentType: false,
        processData: false,
        data: form,
        headers : {'Authorization': 'Bearer ' + jwt},
        async: true
    })
    .done(function (resultado:any) {//retorna text q dice si se mostro o no

        console.log(resultado);

        let alerta:string = ArmarAlert(resultado);

        $("#divResultado").html(alerta);//lo muestro
        
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {

        let retorno = JSON.parse(jqXHR.responseText);

        let alerta:string = ArmarAlert(retorno.mensaje, "danger");

        $("#divResultado").html(alerta);

    });    
}

function Modificar(e:any):void 
{  
    e.preventDefault();

    let jwt = localStorage.getItem("jwt");//recupero token

    //recupero info de los param que quiero modificar
    let codigo = $("#codigo").val();
    let marca = $("#marca").val();
    let precio = $("#precio").val();
    let foto: any = (<HTMLInputElement>document.getElementById("foto"));

    let form = new FormData();
    form.append("obj", JSON.stringify({"codigo":codigo, "marca":marca, "precio":precio}));//aca le paso el obj y JSON, no json puro con el otro caso
    form.append("foto", foto.files[0]);//la foto

    $.ajax({
        type: 'POST',
        url: URL_API + "productos_bd/modificar",//a productos modificar
        dataType: "text",
        cache: false,
        contentType: false,
        processData: false,
        data: form,
        headers : {'Authorization': 'Bearer ' + jwt},//encabezado jwt
        async: true
    })
    .done(function (resultado:any) {

        console.log(resultado);

        ObtenerListadoProductos();//refresco el listado

        $("#cuerpo_modal_prod").html("");
        
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {

        let retorno = JSON.parse(jqXHR.responseText);

        let alerta:string = ArmarAlert(retorno.mensaje, "danger");

        $("#divResultado").html(alerta);

    });    
}

function Eliminar(e:any):void 
{
    e.preventDefault();

    let jwt = localStorage.getItem("jwt");//recupero el jwt del localstorage

    let codigo = $("#codigo").val();//recupero el codigo unicamente

    $.ajax({
        type: 'POST',
        url: URL_API + "productos_bd/eliminar",
        dataType: "text",
        data: {"codigo":codigo},//el json con codigo unicamente
        headers : {'Authorization': 'Bearer ' + jwt},
        async: true
    })
    .done(function (resultado:any) {

        console.log(resultado);

        ObtenerListadoProductos();//actualizo listado
        
        $("#cuerpo_modal_prod").html("");
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {//si falla 

        let retorno = JSON.parse(jqXHR.responseText);

        let alerta:string = ArmarAlert(retorno.mensaje, "danger");

        $("#divResultado").html(alerta);

    });    
}