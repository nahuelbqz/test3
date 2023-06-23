
const express = require('express');

const app = express();

app.set('puerto', 2023);

app.get('/', (request:any, response:any)=>{
    response.send('GET - servidor NodeJS');
});

//AGREGO FILE SYSTEM
const fs = require('fs');

//AGREGO JSON
app.use(express.json());

//AGREGO JWT
const jwt = require("jsonwebtoken");

//SE ESTABLECE LA CLAVE SECRETA PARA EL TOKEN
app.set("key", "cl@ve_secreta");

app.use(express.urlencoded({extended:false}));

//AGREGO MULTER
const multer = require('multer');

//AGREGO MIME-TYPES
const mime = require('mime-types');

//AGREGO STORAGE
const storage = multer.diskStorage({

    destination: "public/juguetes/fotos/",
});

const upload = multer({

    storage: storage
});

//AGREGO CORS (por default aplica a http://localhost)
const cors = require("cors");

//AGREGO MW 
app.use(cors());

//DIRECTORIO DE ARCHIVOS ESTÁTICOS
app.use(express.static("public"));


//AGREGO MYSQL y EXPRESS-MYCONNECTION
const mysql = require('mysql');
const myconn = require('express-myconnection');
const db_options = {
    host: 'localhost',
    port: 3306,
    user: 'root',
    password: '',
    database: 'jugueteria_bd'
};

app.use(myconn(mysql, db_options, 'single'));

//##############################################################################################//
//RUTAS PARA LOS MIDDLEWARES DEL JWT
//##############################################################################################//

const verificar_usuario = express.Router(); // GENERO RUTAS
const verificar_jwt = express.Router();
const alta_baja = express.Router();
const modificar = express.Router();


verificar_usuario.use((request:any, response:any, next:any)=>{ // NEXT =  PROXIMO CALLEABLE

    let obj = request.body;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("SELECT * FROM usuarios WHERE correo = ? and clave = ?  ", [obj.correo, obj.clave], (err:any, rows:any)=>{

            if(err) throw("Error en consulta de base de datos.");

            if(rows.length == 1){//si encuentra ese usuario es 1 y entra
                response.obj_usuario = rows[0];
                //SE INVOCA AL PRÓXIMO CALLEABLE
                next();
            }
            else
            {
                response.status(403).json({
                    exito : false,
                    mensaje : "Correo y/o clave incorrectos.",
                    jwt : null
                });
            }
           
        });
    });
});

app.post("/login", verificar_usuario, (request:any, response:any, obj:any)=>{ // CREAR TOKEN

    //SE RECUPERA EL USUARIO DEL OBJETO DE LA RESPUESTA
    const user = response.obj_usuario;

    //SE CREA EL PAYLOAD CON LOS ATRIBUTOS QUE NECESITAMOS
    const payload = { 
        usuario: {
            id: user.id,
            correo : user.correo,
            nombre : user.nombre,
            apellido : user.apellido,
            foto : user.foto,
            perfil : user.perfil
        },
        alumno: "Nahuel Borquez",
        dni_alumno: "42340058"
    };

    //SE FIRMA EL TOKEN CON EL PAYLOAD Y LA CLAVE SECRETA
    const token = jwt.sign(payload, app.get("key"), { // CREACION DEL TOKEN, metodo sign
        expiresIn : "2m" // m = minutos , y = anios , d = dias
    });

    response.json({
        exito : true,
        mensaje : "JWT creado!!",
        jwt : token
    });

});


verificar_jwt.use((request:any, response:any, next:any)=>{

    //SE RECUPERA EL TOKEN DEL ENCABEZADO DE LA PETICIÓN
    let token = request.headers["x-access-token"] || request.headers["authorization"];
    
    if (! token) {
        response.status(401).send({
            error: "El JWT es requerido!!!"
        });
        return;
    }

    if(token.startsWith("Bearer ")){ // BEARER ES UNA DE LAS FORMAS DE MANDAR TOKEN
        token = token.slice(7, token.length);
    }

    if(token){
        //SE VERIFICA EL TOKEN CON LA CLAVE SECRETA
        jwt.verify(token, app.get("key"), (error:any, decoded:any)=>{ // VERIFICACION DEL TOKEN, metodo verify

            if(error){
                return response.json({
                    exito: false,
                    mensaje:"El JWT NO es válido!!!",
                    status: 403//agrego el status
                });
            }
            else{
                //EL TOKEN ESTA OK
                //console.log("middleware verificar_jwt");

                //SE AGREGA EL TOKEN AL OBJETO DE LA RESPUESTA
                response.jwt = decoded;
                //SE INVOCA AL PRÓXIMO CALLEABLE
                next();
            }
        });
    }
});

app.get('/login', verificar_jwt, (request:any, response:any)=>{ // verificar_jwt middleware
    response.json({exito:true, string:"Logueo exitoso!", jwt: response.jwt, status:200});
});



///////////////////////////////////////////////////
///////////////////////////////////////////////////
//////////  PARTE 2 BACKEND - Node.js   ///////////


//AGREGAR JUEGUETE BD
app.post("/agregarJugueteBD",upload.single("foto"), verificar_jwt, (request: any, response: any) => {
    let obj_retorno = {
        exito: false,
        mensaje: "No se pudo agregar el Juguete a la BD",
    };
    
    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let juguete_obj = JSON.parse(request.body.juguete_json);
    let path: string = file.destination + juguete_obj.marca + "." + extension;
    
    console.log(path);
    
    juguete_obj.path_foto = path.split("public/")[1];
    
    request.getConnection((err: any, conn: any) => {
        if (err) throw "Error al conectarse a la base de datos.";
    
        conn.query(
        "INSERT INTO juguetes set ?",
        [juguete_obj],
        (err: any, rows: any) => {
            if (err) {
                console.log(err);
                throw "Error en consulta de base de datos.";
            }
                obj_retorno.exito = true;
                obj_retorno.mensaje = "Se pudo agregar correctamente el Juguete";
    
                //Guardo la foto cuando se haya guardado el Juguete en la BD
                fs.renameSync(file.path, path);
    
                response.json(obj_retorno);
            }
        );
    });
});
  
    //LISTAR JUGUETES BD
app.get("/listarJuguetesBD",verificar_jwt, (request: any, response: any) => {
    let obj_retorno = {
      exito: false,
      mensaje: "No se encuentran juguetes en la BD",
      dato: {},
      status: 424,//cambio el estado
    };
  
    request.getConnection((err: any, conn: any) => {
        if (err) throw "Error al conectarse a la base de datos.";
  
        conn.query("SELECT * FROM juguetes", (err: any, rows: any) => {
            if (err) throw "Error en consulta de base de datos.";
  
            if (rows.length == 0) {
                response.status(obj_retorno.status).json(obj_retorno);
            } else {
                obj_retorno.exito = true;
                obj_retorno.mensaje = "Listado de Juguetes";
                obj_retorno.dato = rows;
                obj_retorno.status = 200;
  
                response.status(obj_retorno.status).json(obj_retorno);
            }
        });
    });
});

//BORRAR JUGUETE BD por ID
app.delete("/toys",verificar_jwt,(request: any, response: any) => {
    let obj_retorno = {
      exito: false,
      mensaje: "No se pudo eliminar el Juguete a la BD",
      status: 418,
    };
  
    let id_juguete = JSON.parse(request.body.id_juguete);
    let path_foto: string = "public/";
  
    request.getConnection((err: any, conn: any) => {
      if (err) throw "Error al conectarse a la base de datos.";
  
      //Obtengo el path de la foto del producto a ser eliminado
      conn.query(
        "SELECT path_foto FROM juguetes WHERE id = ?",
        [id_juguete],
        (err: any, result: any) => {
          if (err) throw "Error en consulta de base de datos.";
  
          if (result.length != 0) {
            //console.log(result[0].foto);
            path_foto += result[0].path_foto;
          }
        }
      );
    });
  
    request.getConnection((err: any, conn: any) => {
      if (err) throw "Error al conectarse a la base de datos.";
        
      //borro el juguete de la bd donde coincide id
      conn.query(
        "DELETE FROM juguetes WHERE id = ?",
        [id_juguete],
        (err: any, rows: any) => {
          if (err) {
            console.log(err);
            throw "Error en consulta de base de datos.";
          }
  
          //elimino la foto
          if (fs.existsSync(path_foto) && path_foto != "public/") {
            fs.unlink(path_foto, (err: any) => {
              if (err) throw err;
              console.log(path_foto + " fue borrado.");
            });
          }
  
          if (rows.affectedRows == 0) {
            response.status(obj_retorno.status).json(obj_retorno);
          } else {
            obj_retorno.exito = true;
            obj_retorno.mensaje = "Juguete eliminado correctamente!";
            obj_retorno.status = 200;
            response.status(obj_retorno.status).json(obj_retorno);
          }
        }
      );
    });
});

//MODIFICAR JUGUETE BD por id
app.post("/toys",upload.single("foto"),verificar_jwt ,(request: any, response: any) => {
  
    let obj_retorno = {
      exito: false,
      mensaje: "No se pudo modificar el Juguete a la BD",
      status: 418,
    };

    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let juguete_obj = JSON.parse(request.body.juguete);  
    let path: string = file.destination + juguete_obj.marca + "_modificacion." + extension;

    juguete_obj.path = path.split("public/")[1];
    
    let obj_modif : any = {};
    //para excluir el ID
    obj_modif.marca = juguete_obj.marca;
    obj_modif.precio = juguete_obj.precio;
    obj_modif.path_foto = juguete_obj.path;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("UPDATE juguetes SET ? WHERE id = ?", [obj_modif, juguete_obj.id_juguete], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            if (rows.affectedRows > 0)//si lo modifico cambio el path
            {
                fs.renameSync(file.path, path);  
                obj_retorno.status = 200;
                obj_retorno.exito = true;
                obj_retorno.mensaje = "El juguete fue modificado correctamente";                           
            }          

            response.status(obj_retorno.status).json(obj_retorno);           

        });
    });
   
});





app.listen(app.get('puerto'), ()=>{
    console.log('Servidor corriendo sobre puerto:', app.get('puerto'));
});