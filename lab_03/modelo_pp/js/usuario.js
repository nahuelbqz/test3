"use strict";
/// <reference path="./persona.ts" />
var Entidades;
(function (Entidades) {
    class Usuario extends Entidades.Persona {
        constructor(nombre, correo, id, id_perfil, perfil) {
            super(nombre, correo);
            this.id = id;
            this.id_perfil = id_perfil;
            this.perfil = perfil;
        }
        ToJSON() {
            let retorno = super.ToString() + `{"id":${this.id},"id_perfil":${this.id_perfil}},"perfil":${this.perfil}}`;
            return JSON.parse(retorno);
        }
    }
    Entidades.Usuario = Usuario;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=usuario.js.map