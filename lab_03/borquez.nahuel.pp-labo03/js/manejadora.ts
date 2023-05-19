/// <reference path="./neumatico.ts" />
/// <reference path="./neumaticoBD.ts" />
/// <reference path="./Iparte2.ts" />
/// <reference path="./Iparte3.ts" />

let xhttp: XMLHttpRequest = new XMLHttpRequest();

namespace PrimerParcial{

    export class Manejadora implements Iparte2 , Iparte3{

    public static AgregarNeumaticoJSON() 
    {
        let marca:string = (<HTMLInputElement>document.getElementById("marca")).value;
        let medidas:string = (<HTMLInputElement>document.getElementById("medidas")).value;
        let precio:string = (<HTMLInputElement>document.getElementById("precio")).value;

        xhttp.open("POST", "./BACKEND/altaNeumaticoJSON.php", true);

        let form:FormData = new FormData();

        form.append("marca", marca);
        form.append("medidas", medidas);
        form.append("precio", precio);

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
            }
        };
    }

    public static MostrarNeumaticosJSON() 
    {
        xhttp.open("GET", "./BACKEND/listadoNeumaticosJSON.php", true);

        xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            let respuesta: string = xhttp.responseText;
            let neumaticosJson = JSON.parse(respuesta);
            console.log(neumaticosJson);

            const contenerdorTabla: HTMLDivElement = <HTMLDivElement>document.getElementById("divTabla");
            contenerdorTabla.innerHTML = "";

            //TABLA
            const tabla: HTMLTableElement = document.createElement("table");

            //thead
            const thead = document.createElement("thead");

            for(const key in neumaticosJson[0]){
                const th = document.createElement("th");
                let text = document.createTextNode(key.toUpperCase());
                th.appendChild(text);
                thead.appendChild(th);
            }

            //tbody
            const tbody = document.createElement("tbody");
            neumaticosJson.forEach((neumatico: any) => {
                const tr = document.createElement("tr");
                
                for(const key in neumatico){
                    const td = document.createElement("td");
                    let text = document.createTextNode(neumatico[key]);
                    td.appendChild(text);
                    tr.appendChild(td);
                }
                tbody.appendChild(tr);
            });
            tabla.appendChild(thead);
            tabla.appendChild(tbody);
            contenerdorTabla.appendChild(tabla); // se inyecta toda la tabla en el contenedor
        }
        };

        xhttp.send();
    }

    public static VerificarNeumaticoJSON()
    {
        let marca:string = (<HTMLInputElement>document.getElementById("marca")).value;
        let medidas:string = (<HTMLInputElement>document.getElementById("medidas")).value;

        xhttp.open("POST", "./BACKEND/verificarNeumaticoJSON.php", true);

        let form:FormData = new FormData();

        form.append("marca", marca);
        form.append("medidas", medidas);

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if(xhttp.readyState == 4 && xhttp.status == 200){
                let respuesta = JSON.parse(xhttp.responseText);
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
            }
        };
    }


    public static AgregarNeumaticoSinFoto()
    {
        let marca:string = (<HTMLInputElement>document.getElementById("marca")).value;
        let medidas:string = (<HTMLInputElement>document.getElementById("medidas")).value;
        let precio:number = parseInt((<HTMLInputElement>document.getElementById("precio")).value);

        let neumatico = new Entidades.Neumatico(marca, medidas, precio);

        xhttp.open("POST", "./BACKEND/agregarNeumaticoSinFoto.php", true);

        let form: FormData = new FormData();

        form.append("neumatico_json", neumatico.ToJSON());

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
            }
        };
    }

    public static MostrarNeumaticosBD(tipoTabla: number)
    {
        xhttp.open("GET", "./BACKEND/ListadoNeumaticosBD.php", true);
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200){
            let respuesta: string = xhttp.responseText;
            let neumaticosJson = JSON.parse(respuesta);
            console.log(neumaticosJson);
    
            const contenerdorTabla: HTMLDivElement = <HTMLDivElement>document.getElementById("divTabla");
            contenerdorTabla.innerHTML = "";
    
            let tablaStr: string = `
            <table>
                <thead>`;
            for (const key in neumaticosJson[0]) {
              tablaStr += `<th>${key.toLocaleUpperCase()}</th>`;
            }
            tablaStr += `<th>ACCIONES</th>`;
            tablaStr += `</thead>`;
    
            tablaStr += `<tbody>`;
            neumaticosJson.forEach((neumatico: any) => {
                tablaStr += `<tr>`;
                for (const key in neumatico) {
                    if (key != "pathFoto") {
                        tablaStr += `<td>${neumatico[key]}</td>`;
                    } else {
                        tablaStr += `<td><img src='./BACKEND${neumatico[key]}' width='50px' alt='img'></td>`;
                    }
                }
    
            let neumaticoStr = JSON.stringify(neumatico);
            if (tipoTabla == 1) {
                tablaStr += `<td> <input type="button" value="Modificar" class="btn btn-info" onclick=PrimerParcial.Manejadora.BtnModificarNeumatico(${neumaticoStr})></td>`;
                tablaStr += `<td> <input type="button" value="Eliminar" class="btn btn-danger" onclick=PrimerParcial.Manejadora.EliminarNeumatico(${neumaticoStr})></td>`;
            } else if (tipoTabla == 2) {
                tablaStr += `<td> <input type="button" value="Modificar" class="btn btn-info" onclick=PrimerParcial.Manejadora.BtnModificarNeumaticoBDFoto(${neumaticoStr})></td>`;
                tablaStr += `<td> <input type="button" value="Eliminar" class="btn btn-danger" onclick=PrimerParcial.Manejadora.BorrarNeumaticoBDFoto(${neumaticoStr})></td>`;
            }
            tablaStr += `</tr>`;
            });
    
            tablaStr += `</tbody>`;
            tablaStr += `</table>`;
            contenerdorTabla.innerHTML = tablaStr;
            }
        };
    
        xhttp.send();
    }



/////////////
/// PARTE2

    EliminarNeumatico(obj: any) :void {}

    public static EliminarNeumatico(obj: any) : void
    {
        let confirmacion = confirm(`Desea eliminar el neumatico con marca "${obj.marca}" y medidas ${obj.medidas}" ?`);

        if(confirmacion){
            xhttp.open("POST", "./BACKEND/eliminarNeumaticoBD.php", true);

            let neumatico = new Entidades.NeumaticoBD(obj.marca, obj.medidas, obj.precio, obj.id);

            let form: FormData = new FormData();

            form.append("neumatico_json", neumatico.ToJSON());

            xhttp.send(form);

            xhttp.onreadystatechange = () => {
                if(xhttp.readyState == 4 && xhttp.status == 200) {
                    let respuesta = JSON.parse(xhttp.responseText);
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);

                    Manejadora.MostrarNeumaticosBD(1);
                }
            };
        }
    }


    ModificarNeumatico(): void {}

    public static ModificarNeumaticoBDSinFoto(): void
    {
        const inpId = <HTMLInputElement>document.getElementById("idNeumatico");
        const inpMarca = <HTMLInputElement>document.getElementById("marca");
        const inpMedidas = <HTMLInputElement>document.getElementById("medidas");
        const inpPrecio = <HTMLInputElement>document.getElementById("precio");
        const inpFoto = <HTMLImageElement>document.getElementById("imgFoto");
    
        let id: number = parseInt(inpId.value);
        let marca: string = inpMarca.value;
        let medidas: string = inpMedidas.value;
        let precio: number = parseInt(inpPrecio.value);
    
        const Neumatico = {
            id: id,
            marca: marca,
            medidas: medidas,
            precio: precio,
        };
    
        xhttp.open("POST", "./BACKEND/modificarNeumaticoBD.php", true);
    
        let form: FormData = new FormData();
    
        form.append("neumatico_json", JSON.stringify(Neumatico));
    
        xhttp.send(form);
    
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
        
                if (respuesta.exito) {
                    Manejadora.MostrarNeumaticosBD(1);
                }
        
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
        
                inpId.value = "";
                inpMarca.value = "";
                inpMedidas.value = "";
                inpPrecio.value = "";
                inpFoto.src = "./neumatico_default.jfif";
            }
        };
    }
    
    public static BtnModificarNeumatico(obj: any): void
    {
        (<HTMLInputElement>document.getElementById("idNeumatico")).value = obj.id;
        (<HTMLInputElement>document.getElementById("marca")).value = obj.marca;
        (<HTMLInputElement>document.getElementById("medidas")).value = obj.medidas;
        (<HTMLInputElement>document.getElementById("precio")).value = obj.precio;

        const img = <HTMLImageElement>document.getElementById("imgFoto");
        if (img) {
            img.src = "./BACKEND" + obj.pathFoto;
        }
    }



////////////
// PARTE3

    VerificarNeumaticoBD() :void { }

    public static VerificarNeumaticoBD() :void 
    {
        let marca: string = (<HTMLInputElement>document.getElementById("marca")).value;
        let medidas: string = (<HTMLInputElement>document.getElementById("medidas")).value;

        let neumatico = new Entidades.NeumaticoBD(marca, medidas);

        xhttp.open("POST", "./BACKEND/verificarNeumaticoBD.php", true);

        let form: FormData = new FormData();

        console.log(neumatico);

        form.append("obj_neumatico", neumatico.ToJSON());

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);

                const contenerdorInfo: HTMLDivElement = <HTMLDivElement>document.getElementById("divInfo");   
                contenerdorInfo.innerText = xhttp.responseText;
        
                console.log(respuesta);
            }
        }
    }

    AgregarNeumaticoBDFoto(): void {}

    public static AgregarNeumaticoBDFoto(): void 
    {
        let marca: string = (<HTMLInputElement>document.getElementById("marca")).value;
        let medidas: string = (<HTMLInputElement>document.getElementById("medidas")).value;
        let precio: string = (<HTMLInputElement>document.getElementById("precio")).value;
        let foto: any = (<HTMLInputElement>document.getElementById("foto"));

        xhttp.open("POST", "./BACKEND/agregarNeumaticoBD.php", true);

        let form: FormData = new FormData();

        form.append("marca", marca);
        form.append("medidas", medidas);
        form.append("precio", precio);
        form.append("foto", foto.files[0]);

        xhttp.setRequestHeader("enctype", "multipart/form-data");

        xhttp.send(form);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
                
                Manejadora.MostrarNeumaticosBD(2);
            }
        };
    }


    BorrarNeumaticoBDFoto(obj: any) :void {}

    public static BorrarNeumaticoBDFoto(obj: any): void 
    {
        let confirmacion = confirm(`Esta seguro de eliminar al neumatico, marca: ${obj.marca} y medidas: ${obj.medidas} ?`);

        if (confirmacion) {
            xhttp.open("POST", "./BACKEND/eliminarNeumaticoBDFoto.php", true);
    
            let form: FormData = new FormData();

            form.append("neumatico_json", JSON.stringify(obj));

            xhttp.send(form);
    
            xhttp.onreadystatechange = () => {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    let respuesta = JSON.parse(xhttp.responseText);
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);

                    Manejadora.MostrarNeumaticosBD(2);
                }
            };
        }
    }


    ModificarNeumaticoBDFoto() :void {}

    public static ModificarNeumaticoBDFoto(): void 
    {
        const inpId = <HTMLInputElement>document.getElementById("idNeumatico");
        const inpMarca = <HTMLInputElement>document.getElementById("marca");
        const inpMedidas = <HTMLInputElement>document.getElementById("medidas");
        const inpPrecio = <HTMLInputElement>document.getElementById("precio");
        const inpFoto = <HTMLImageElement>document.getElementById("imgFoto");
        let foto: any = <HTMLInputElement>document.getElementById("foto");
  
        let id: number = parseInt(inpId.value);
        let marca: string = inpMarca.value;
        let medidas: string = inpMedidas.value;
        let precio: number = parseInt(inpPrecio.value);

        const NeumaticoBD = {
            id: id,
            marca: marca,
            medidas: medidas,
            precio: precio,
        };
  
        xhttp.open("POST", "./BACKEND/modificarNeumaticoBDFoto.php", true);
  
        let form: FormData = new FormData();

        form.append("neumatico_json", JSON.stringify(NeumaticoBD));

        form.append("foto", foto.files[0]);

        xhttp.setRequestHeader("enctype", "multipart/form-data");

        xhttp.send(form);
  
        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                let respuesta = JSON.parse(xhttp.responseText);

                if (respuesta.exito) {
                    Manejadora.MostrarNeumaticosBD(2);
                } else {
                    console.log(respuesta.mensaje);
                    alert(respuesta.mensaje);
                }

                inpId.value = "";
                inpMarca.value = "";
                inpMedidas.value = "";
                inpPrecio.value = "";
                inpFoto.innerText = "";
                //inpFoto.src = "./";
            }
        };
    }
  
    public static BtnModificarNeumaticoBDFoto(obj: any): void 
    {
        (<HTMLInputElement>document.getElementById("idNeumatico")).value = obj.id;
        (<HTMLInputElement>document.getElementById("marca")).value = obj.marca;
        (<HTMLInputElement>document.getElementById("medidas")).value = obj.medidas;
        (<HTMLInputElement>document.getElementById("precio")).value = obj.precio;

        const img = <HTMLImageElement>document.getElementById("imgFoto");
        img.src = "./BACKEND" + obj.pathFoto;
    }



    /*
    ///////////////////////////////////////////////////////////////
    MostrarBorradosJSON2() : void { }

    public static MostrarBorradosJSON() {
        xhttp.open("GET", "./BACKEND/MostrarBorradosJSON.php", true);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
            let respuesta: string = xhttp.responseText;
            let productosJson = JSON.parse(respuesta);
            const contenerdorInfo: HTMLDivElement = <HTMLDivElement>document.getElementById("divInfo");
            contenerdorInfo.innerHTML ="<h4>BORRADOS</h4>" + respuesta;
            console.log(productosJson);
            }
        };
        
        xhttp.send();
    }


    MostrarFotosModificados2() : void { }

    public static MostrarFotosModificados() {
        xhttp.open("GET", "./BACKEND/MostrarFotosDeModificados.php", true);

        xhttp.onreadystatechange = () => {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
            let respuesta: string = xhttp.responseText;
            const contenerdorInfo: HTMLDivElement = <HTMLDivElement>document.getElementById("divTabla");
            contenerdorInfo.innerHTML = "<h4>MODIFICADAS</h4>" + respuesta;
            }
        };

        xhttp.send();
    }
    */


    }//class manejadora
}//namespace
