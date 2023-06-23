"use strict";
var express = require('express');
var app = express();
app.set('puerto', 4321);
app.get('/', function (request, response) {
    response.send('GET - servidor NodeJS');
});
var fs = require('fs');
app.use(express.json());
var jwt = require("jsonwebtoken");
app.set("key", "cl@ve_secreta");
app.use(express.urlencoded({ extended: false }));
var multer = require('multer');
var mime = require('mime-types');
var storage = multer.diskStorage({
    destination: "public/fotos/",
});
var upload = multer({
    storage: storage
});
var cors = require("cors");
app.use(cors());
app.use(express.static("public"));
var mysql = require('mysql');
var myconn = require('express-myconnection');
var db_options = {
    host: 'localhost',
    port: 3306,
    user: 'root',
    password: '',
    database: 'productos_usuarios_node'
};
app.use(myconn(mysql, db_options, 'single'));
var verificar_usuario = express.Router();
var verificar_jwt = express.Router();
var alta_baja = express.Router();
var modificar = express.Router();
verificar_usuario.use(function (request, response, next) {
    var obj = request.body;
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("SELECT * FROM usuarios WHERE legajo = ? and apellido = ?  ", [obj.legajo, obj.apellido], function (err, rows) {
            if (err)
                throw ("Error en consulta de base de datos.");
            if (rows.length == 1) {
                response.obj_usuario = rows[0];
                next();
            }
            else {
                response.status(401).json({
                    exito: false,
                    mensaje: "Legajo y/o apellido incorrectos.",
                    jwt: null
                });
            }
        });
    });
});
app.post("/login", verificar_usuario, function (request, response, obj) {
    var user = response.obj_usuario;
    var payload = {
        usuario: {
            id: user.id,
            apellido: user.apellido,
            nombre: user.nombre,
            rol: user.rol
        },
        api: "productos_usuarios_node"
    };
    var token = jwt.sign(payload, app.get("key"), {
        expiresIn: "5m"
    });
    response.json({
        exito: true,
        mensaje: "JWT creado!!",
        jwt: token
    });
});
verificar_jwt.use(function (request, response, next) {
    var token = request.headers["x-access-token"] || request.headers["authorization"];
    if (!token) {
        response.status(401).send({
            error: "El JWT es requerido!!!"
        });
        return;
    }
    if (token.startsWith("Bearer ")) {
        token = token.slice(7, token.length);
    }
    if (token) {
        jwt.verify(token, app.get("key"), function (error, decoded) {
            if (error) {
                return response.json({
                    exito: false,
                    mensaje: "El JWT NO es válido!!!"
                });
            }
            else {
                console.log("middleware verificar_jwt");
                response.jwt = decoded;
                next();
            }
        });
    }
});
app.get('/verificar_token', verificar_jwt, function (request, response) {
    response.json({ exito: true, jwt: response.jwt });
});
app.post('/usuarios', alta_baja, upload.single("foto"), function (request, response) {
    var obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo agregar el usuario",
        "status": 418
    };
    var file = request.file;
    var extension = mime.extension(file.mimetype);
    var usuario = JSON.parse(request.body.usuario);
    var path = file.destination + usuario.correo + "." + extension;
    fs.renameSync(file.path, path);
    usuario.foto = path.split("public/")[1];
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("INSERT INTO usuarios set ?", [usuario], function (err, rows) {
            if (err) {
                console.log(err);
                throw ("Error en consulta de base de datos.");
            }
            obj_respuesta.exito = true;
            obj_respuesta.mensaje = "Usuario agregado!";
            obj_respuesta.status = 200;
            response.status(obj_respuesta.status).json(obj_respuesta);
        });
    });
});
app.get('/usuarios', verificar_jwt, function (request, response) {
    var obj_respuesta = {
        "exito": false,
        "mensaje": "No se encontraron usuarios",
        "dato": {},
        "status": 424
    };
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("SELECT * FROM usuarios", function (err, rows) {
            if (err)
                throw ("Error en consulta de base de datos.");
            if (rows.length == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
            else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Usuarios encontrados!";
                obj_respuesta.dato = rows;
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });
});
app.post('/usuarios/modificar', upload.single("foto"), modificar, function (request, response) {
    var obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo modificar el usuario",
        "status": 418
    };
    var file = request.file;
    var extension = mime.extension(file.mimetype);
    var usuario = JSON.parse(request.body.usuario);
    var path = file.destination + usuario.correo + "." + extension;
    fs.renameSync(file.path, path);
    usuario.foto = path.split("public/")[1];
    var usuario_modif = {};
    usuario_modif.correo = usuario.correo;
    usuario_modif.clave = usuario.clave;
    usuario_modif.nombre = usuario.nombre;
    usuario_modif.apellido = usuario.apellido;
    usuario_modif.perfil = usuario.perfil;
    usuario_modif.foto = usuario.foto;
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("UPDATE usuarios set ?  WHERE id = ?", [usuario_modif, usuario.id], function (err, rows) {
            if (err) {
                console.log(err);
                throw ("Error en consulta de base de datos.");
            }
            if (rows.affectedRows == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
            else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Usuario modificado!";
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });
});
app.post('/usuarios/eliminar', alta_baja, function (request, response) {
    var obj_respuesta = {
        "exito": false,
        "mensaje": "No se pudo eliminar el usuario",
        "status": 418
    };
    var obj = request.body;
    var path_foto = "public/";
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("SELECT foto FROM usuarios WHERE id = ?", [obj.id], function (err, result) {
            if (err)
                throw ("Error en consulta de base de datos.");
            if (result.length != 0) {
                path_foto += result[0].foto;
            }
        });
    });
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("DELETE FROM usuarios WHERE id = ?", [obj.id], function (err, rows) {
            if (err) {
                console.log(err);
                throw ("Error en consulta de base de datos.");
            }
            if (fs.existsSync(path_foto) && path_foto != "public/") {
                fs.unlink(path_foto, function (err) {
                    if (err)
                        throw err;
                    console.log(path_foto + ' fue borrado.');
                });
            }
            if (rows.affectedRows == 0) {
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
            else {
                obj_respuesta.exito = true;
                obj_respuesta.mensaje = "Usuario Eliminado!";
                obj_respuesta.status = 200;
                response.status(obj_respuesta.status).json(obj_respuesta);
            }
        });
    });
});
app.get('/productos_bd', verificar_jwt, function (request, response) {
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("SELECT * from productos", function (err, rows) {
            if (err)
                throw ("Error en consulta de base de datos.");
            response.send(JSON.stringify(rows));
        });
    });
});
app.post('/productos_bd', verificar_jwt, upload.single("foto"), function (request, response) {
    var file = request.file;
    var extension = mime.extension(file.mimetype);
    var obj = JSON.parse(request.body.obj);
    var path = file.destination + obj.codigo + "." + extension;
    fs.renameSync(file.path, path);
    obj.path = path.split("public/")[1];
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("INSERT into productos set ?", [obj], function (err, rows) {
            if (err) {
                console.log(err);
                throw ("Error en consulta de base de datos.");
            }
            response.send("Producto agregado a la bd.");
        });
    });
});
app.post('/productos_bd/modificar', modificar, upload.single("foto"), function (request, response) {
    var file = request.file;
    var extension = mime.extension(file.mimetype);
    var obj = JSON.parse(request.body.obj);
    var path = file.destination + obj.codigo + "." + extension;
    fs.renameSync(file.path, path);
    obj.path = path.split("public/")[1];
    var obj_modif = {};
    obj_modif.marca = obj.marca;
    obj_modif.precio = obj.precio;
    obj_modif.path = obj.path;
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("update productos set ? where codigo = ?", [obj_modif, obj.codigo], function (err, rows) {
            if (err) {
                console.log(err);
                throw ("Error en consulta de base de datos.");
            }
            response.send("Producto modificado en la bd.");
        });
    });
});
app.post('/productos_bd/eliminar', alta_baja, function (request, response) {
    var obj = request.body;
    var path_foto = "public/";
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("select path from productos where codigo = ?", [obj.codigo], function (err, result) {
            if (err)
                throw ("Error en consulta de base de datos.");
            path_foto += result[0].path;
        });
    });
    request.getConnection(function (err, conn) {
        if (err)
            throw ("Error al conectarse a la base de datos.");
        conn.query("delete from productos where codigo = ?", [obj.codigo], function (err, rows) {
            if (err) {
                console.log(err);
                throw ("Error en consulta de base de datos.");
            }
            fs.unlink(path_foto, function (err) {
                if (err)
                    throw err;
                console.log(path_foto + ' fue borrado.');
            });
            response.send("Producto eliminado de la bd.");
        });
    });
});
app.listen(app.get('puerto'), function () {
    console.log('Servidor corriendo sobre puerto:', app.get('puerto'));
});
//# sourceMappingURL=servidor_node.js.map