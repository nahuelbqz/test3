"use strict";
/// <reference path="./persona.ts" />
/// <reference path="./usuario.ts" />
var Entidades;
(function (Entidades) {
    class Empleado extends Entidades.Usuario {
        constructor(nombre, correo, id, id_perfil, perfil, id_emp, sueldo, foto) {
            super(nombre, correo, id, id_perfil, perfil);
            this.id = id_emp;
            this.sueldo = sueldo;
            this.foto = foto;
        }
    }
    Entidades.Empleado = Empleado;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=empleado.js.map