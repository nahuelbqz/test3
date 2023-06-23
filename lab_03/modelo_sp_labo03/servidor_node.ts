
const express = require('express');

const app = express();

app.set('puerto', 4321);

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

    destination: "public/fotos/",
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
    database: 'productos_usuarios_node'
};

app.use(myconn(mysql, db_options, 'single'));

//##############################################################################################//
//RUTAS PARA LOS MIDDLEWARES DEL JWT
//##############################################################################################//

//genero una const verificar, tiene que ser obj tipo ruta 
/*const verificar_jwt = express.Router();

//aca la uso
verificar_jwt.use((request:any, response:any, next:any)=>{

    //SE RECUPERA EL TOKEN DEL ENCABEZADO DE LA PETICIÓN
    let token = request.headers["x-access-token"] || request.headers["authorization"];
    
    if (! token) {
        response.status(401).send({
            error: "El JWT es requerido!!!"
        });
        return;//salgo para que no llegue al verbo
    }

    //aca recupero el token si existe
    if(token.startsWith("Bearer ")){
        token = token.slice(7, token.length);
    }

    if(token){
        //SE VERIFICA EL TOKEN CON LA CLAVE SECRETA
        jwt.verify(token, app.get("key"), (error:any, decoded:any)=>{

            if(error){
                return response.json({
                    exito: false,
                    mensaje:"El JWT NO es válido!!!"
                });
            }
            else{

                console.log("middleware verificar_jwt");

                //SE AGREGA EL TOKEN AL OBJETO DE LA RESPUESTA
                response.jwt = decoded;
                //SE INVOCA AL PRÓXIMO CALLEABLE
                next();
            }
        });
    }
});

const solo_admin = express.Router();

solo_admin.use(verificar_jwt, (request:any, response:any, next:any)=>{

    console.log("middleware solo_admin");

    //SE RECUPERA EL TOKEN DEL OBJETO DE LA RESPUESTA
    let usuario = response.jwt;

    if(usuario.perfil == "administrador"){
        //SE INVOCA AL PRÓXIMO CALLEABLE
         next();
    }
    else{
        return response.json({
            mensaje:"NO tiene perfil de 'ADMINISTRADOR'"
        });
    }
   
}
*/

/*, function (request:any, response:any, next:any) {
    console.log('Request Type:', request.method);
    next();
  }*/ //);


//##############################################################################################//
//RUTAS PARA EL TEST DE JWT
//##############################################################################################//

//#01
/*
app.post("/crear_token", (request:any, response:any)=>{

    if((request.body.usuario == "admin" || request.body.usuario == "user") && request.body.clave == "123456"){

        //SE CREA EL PAYLOAD CON LOS ATRIBUTOS QUE NECESITAMOS
        const payload = { 
            exito : true,
            usuario: request.body.usuario,
            perfil: request.body.usuario == "admin" ? "administrador" : "usuario",

        };

        //SE FIRMA EL TOKEN CON EL PAYLOAD Y LA CLAVE SECRETA
        const token = jwt.sign(payload, app.get("key"), {
            expiresIn : "3600"//expira milisegundos
        });

        response.json({
            mensaje : "JWT creado",
            jwt : token
        });
    }
    else{
        response.json({
            mensaje : "Usuario no registrado",
            jwt : null
        });
    }

});
//#02        //verificar_jwt: esta es una fucion que verifica el jwt, si verifica entra al verbo.
app.get('/verificar_token', verificar_jwt, (request:any, response:any)=>{
    
    response.json({exito:true, jwt: response.jwt});
});
//#03
app.get('/admin', solo_admin, (request:any, response:any)=>{
    
    response.json(response.jwt);
});
*/

//##############################################################################################//
//RUTAS PARA LOS MIDDLEWARES DEL JWT
//##############################################################################################//

const verificar_usuario = express.Router(); // GENERAN RUTAS
const verificar_jwt = express.Router();
const alta_baja = express.Router();
const modificar = express.Router();


verificar_usuario.use((request:any, response:any, next:any)=>{ // NEXT =  PROXIMO CALLEABLE

    let obj = request.body;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("SELECT * FROM usuarios WHERE legajo = ? and apellido = ?  ", [obj.legajo, obj.apellido], (err:any, rows:any)=>{

            if(err) throw("Error en consulta de base de datos.");

            if(rows.length == 1){//si encuentra ese usuario es 1 y entra
                response.obj_usuario = rows[0];
                //SE INVOCA AL PRÓXIMO CALLEABLE
                next();
            }
            else
            {
                response.status(401).json({
                    exito : false,
                    mensaje : "Legajo y/o apellido incorrectos.",
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
            id : user.id,
            apellido : user.apellido,
            nombre : user.nombre,
            rol : user.rol
        },
        api : "productos_usuarios_node"
    };

    //SE FIRMA EL TOKEN CON EL PAYLOAD Y LA CLAVE SECRETA
    const token = jwt.sign(payload, app.get("key"), { // CREACION DEL TOKEN, metodo sign
        expiresIn : "5m" // m = minutos , y = anios , d = dias
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
                    mensaje:"El JWT NO es válido!!!"
                });
            }
            else{
                //EL TOKEN ESTA OK
                console.log("middleware verificar_jwt");

                //SE AGREGA EL TOKEN AL OBJETO DE LA RESPUESTA
                response.jwt = decoded;
                //SE INVOCA AL PRÓXIMO CALLEABLE
                next();
            }
        });
    }
});

app.get('/verificar_token', verificar_jwt, (request:any, response:any)=>{ // verificar_jwt middleware
    response.json({exito:true, jwt: response.jwt});
});

/*
alta_baja.use(verificar_jwt, (request:any, response:any, next:any)=>{

    console.log("middleware alta_baja");

    //SE RECUPERA EL TOKEN DEL OBJETO DE LA RESPUESTA
    let obj = response.jwt;

    console.log(obj.usuario);
    
    if(obj.usuario.perfil == "propietario"){
        //SE INVOCA AL PRÓXIMO CALLEABLE
         next();
    }
    else{
        return response.status(401).json({
            mensaje:"NO tiene el perfil necesario para realizar la acción."
        });
    }
});


modificar.use(verificar_jwt, (request:any, response:any, next:any)=>{
  
    console.log("middleware modificar");

    //SE RECUPERA EL TOKEN DEL OBJETO DE LA RESPUESTA
    let obj = response.jwt;

    if(obj.usuario.perfil == "propietario" || obj.usuario.perfil == "supervisor"){
        //SE INVOCA AL PRÓXIMO CALLEABLE
        next();
    }
    else{
        return response.status(401).json({
            mensaje:"NO tiene el perfil necesario para realizar la acción."
        });
    }   
});
*/

// CRUD USUARIOS **************************************************************************************
// Agregar usuario
// falta middleware alta_baja
app.post('/usuarios', alta_baja, upload.single("foto"), (request:any, response:any)=>{
   
    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo agregar el usuario",
        "status": 418
    }

    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let usuario = JSON.parse(request.body.usuario);
    let path : string = file.destination + usuario.correo + "." + extension;

    fs.renameSync(file.path, path);

    usuario.foto = path.split("public/")[1];

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("INSERT INTO usuarios set ?", [usuario], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            obj_respuesta.exito = true;
            obj_respuesta.mensaje = "Usuario agregado!";
            obj_respuesta.status = 200;

            response.status(obj_respuesta.status).json(obj_respuesta);
        });

    });
});

// Listar usuarios
app.get('/usuarios', verificar_jwt, (request:any, response:any)=>{

    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se encontraron usuarios",
        "dato": {},
        "status": 424
    }

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("SELECT * FROM usuarios", (err:any, rows:any)=>{

            if(err) throw("Error en consulta de base de datos.");

            if(rows.length == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            } else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Usuarios encontrados!";
                obj_respuesta.dato = rows;
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });

});
 
// Modificar usuario
app.post('/usuarios/modificar', upload.single("foto"), modificar, (request:any, response:any)=>{
    
    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo modificar el usuario",
        "status": 418
    }

    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let usuario = JSON.parse(request.body.usuario);
    let path : string = file.destination + usuario.correo + "." + extension;

    fs.renameSync(file.path, path);

    usuario.foto = path.split("public/")[1];

    let usuario_modif : any = {};
    //para excluir la pk (id)
    usuario_modif.correo = usuario.correo;
    usuario_modif.clave = usuario.clave;
    usuario_modif.nombre = usuario.nombre;
    usuario_modif.apellido = usuario.apellido;
    usuario_modif.perfil = usuario.perfil;
    usuario_modif.foto = usuario.foto;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("UPDATE usuarios set ?  WHERE id = ?", [usuario_modif, usuario.id], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            if(rows.affectedRows == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            } else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Usuario modificado!";
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }

        });
    });
});

// Eliminar usuario
app.post('/usuarios/eliminar', alta_baja, (request:any, response:any)=>{
   
    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo eliminar el usuario",
        "status": 418
    }

    let obj = request.body;
    let path_foto : string = "public/";

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        // obtengo el path de la foto del usuario a ser eliminado
        conn.query("SELECT foto FROM usuarios WHERE id = ?", [obj.id], (err:any, result:any)=>{

            if(err) throw("Error en consulta de base de datos.");

            if(result.length != 0) {
                //console.log(result[0].foto);
                path_foto += result[0].foto;
            }
        });
    });

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("DELETE FROM usuarios WHERE id = ?", [obj.id], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            if(fs.existsSync(path_foto) && path_foto != "public/") {
                fs.unlink(path_foto, (err:any) => {
                    if (err) throw err;
                    console.log(path_foto + ' fue borrado.');
                });    
            }

            if(rows.affectedRows == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            } else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Usuario Eliminado!";
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });
});


//**************************************************************************************
//**************************************************************************************
// CRUD PRODUCTOS (o otra entidad)**************************************************************************************
// LISTAR 

app.get('/productos_bd', verificar_jwt, (request:any, response:any)=>{

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("SELECT * from productos", (err:any, rows:any)=>{

            if(err) throw("Error en consulta de base de datos.");

            response.send(JSON.stringify(rows));
        });
    });

});

//AGREGAR          //el alta baja es una verificacion de si es adm o supervisor
app.post('/productos_bd', /*alta_baja,*/ verificar_jwt ,upload.single("foto"), (request:any, response:any)=>{
   
    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let obj = JSON.parse(request.body.obj);
    let path : string = file.destination + obj.codigo + "." + extension;

    fs.renameSync(file.path, path);

    obj.path = path.split("public/")[1];

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("INSERT into productos set ?", [obj], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            response.send("Producto agregado a la bd.");
        });
    });
});

//MODIFICAR                 //sacar o quitar mw modificar
app.post('/productos_bd/modificar', modificar, upload.single("foto"), (request:any, response:any)=>{
    
    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let obj = JSON.parse(request.body.obj);
    let path : string = file.destination + obj.codigo + "." + extension;

    fs.renameSync(file.path, path);

    obj.path = path.split("public/")[1];

    let obj_modif : any = {};
    //para excluir la pk (codigo)
    obj_modif.marca = obj.marca;
    obj_modif.precio = obj.precio;
    obj_modif.path = obj.path;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("update productos set ? where codigo = ?", [obj_modif, obj.codigo], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            response.send("Producto modificado en la bd.");
        });
    });
});

//ELIMINAR                  //sacar o modificar mw altabaja
app.post('/productos_bd/eliminar', alta_baja, (request:any, response:any)=>{
   
    let obj = request.body;
    let path_foto : string = "public/";

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        //obtengo el path de la foto del producto a ser eliminado
        conn.query("select path from productos where codigo = ?", [obj.codigo], (err:any, result:any)=>{

            if(err) throw("Error en consulta de base de datos.");
            //console.log(result[0].path);
            path_foto += result[0].path;
        });
    });

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("delete from productos where codigo = ?", [obj.codigo], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            fs.unlink(path_foto, (err:any) => {
                if (err) throw err;
                console.log(path_foto + ' fue borrado.');
            });

            response.send("Producto eliminado de la bd.");
        });
    });
});






// OTRO EJEMPLO
// CRUD AUTOS (o otra entidad)**************************************************************************************
// Agregar auto
/*
app.post('/autos', alta_baja, (request:any, response:any)=>{
   
    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo agregar el auto",
        "status": 418
    }

    let auto = JSON.parse(request.body.auto);

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("INSERT INTO autos set ?", [auto], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            obj_respuesta.exito = true;
            obj_respuesta.mensaje = "Auto agregado!";
            obj_respuesta.status = 200;

            response.status(obj_respuesta.status).json(obj_respuesta);
        });

    });
});

// Listar autos
app.get('/autos', verificar_jwt, (request:any, response:any)=>{

    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se encontraron autos",
        "dato": {},
        "status": 424
    }

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("SELECT * FROM autos", (err:any, rows:any)=>{

            if(err) throw("Error en consulta de base de datos.");

            if(rows.length == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            } else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Autos encontrados!";
                obj_respuesta.dato = rows;
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });

});

// Modificar auto
app.put('/autos/modificar', modificar, (request:any, response:any)=>{
    
    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo modificar el auto",
        "status": 418
    }

    let auto : any = {};
    auto.id = request.body.id;
    auto.color = request.body.color;
    auto.marca = request.body.marca;
    auto.precio = request.body.precio;
    auto.modelo = request.body.modelo;
    
    let auto_modif : any = {};
    //para excluir la pk (id) 
    auto_modif.color = auto.color;
    auto_modif.marca = auto.marca;
    auto_modif.precio = auto.precio;
    auto_modif.modelo = auto.modelo;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("UPDATE autos set ?  WHERE id = ?", [auto_modif, auto.id], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            if(rows.affectedRows == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            } else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Auto modificado!";
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }

        });
    });
});

// Eliminar auto
app.delete('/autos/eliminar', alta_baja, (request:any, response:any)=>{
   
    let obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo eliminar el auto",
        "status": 418
    }

    let id = request.body.id;
    let obj : any = {};
    obj.id = id;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("DELETE FROM autos WHERE id = ?", [obj.id], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}

            if(rows.affectedRows == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            } else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Auto Eliminado!";
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });
});
*/

app.listen(app.get('puerto'), ()=>{
    console.log('Servidor corriendo sobre puerto:', app.get('puerto'));
});