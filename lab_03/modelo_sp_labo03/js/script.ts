/// <reference path="../node_modules/@types/jquery/index.d.ts" />


$(()=>{

    $("#btnForm").on("click", (e:any)=>{//boton ENVIAR le asocio el EVENTO click

        e.preventDefault();

        //recupero los datos de los label
        let legajo = $("#legajo").val();
        let apellido = $("#apellido").val();

        //genero el json con los datos
        let dato:any = {};
        dato.legajo = legajo;
        dato.apellido = apellido;

        $.ajax({
            type: 'POST',
            url: URL_API + "login",
            dataType: "json",
            data: dato,
            async: true
        })
        .done(function (obj_ret:any) {//lo que recibo cuando salio bien la peticion

            console.log(obj_ret);
            let alerta:string = "";

            if(obj_ret.exito){
                //GUARDO EN EL LOCALSTORAGE la jwt
                localStorage.setItem("jwt", obj_ret.jwt);                

                alerta = ArmarAlert(obj_ret.mensaje + " redirigiendo al principal.php...");
    
                setTimeout(() => {
                    $(location).attr('href', URL_BASE + "principal.html");//redireccion a principal
                }, 2000);//despues de 2 segundos

            }

            $("#div_mensaje").html(alerta);
            
        })
        .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {//atrapo en el fail los status 400...

            let retorno = JSON.parse(jqXHR.responseText);

            let alerta:string = ArmarAlert(retorno.mensaje, "danger");

            $("#div_mensaje").html(alerta);

        });    

    });

});

