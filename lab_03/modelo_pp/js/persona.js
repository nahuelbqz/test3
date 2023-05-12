"use strict";
var Entidades;
(function (Entidades) {
    class Persona {
        constructor(nombre, correo, clave) {
            this.nombre = nombre;
            this.correo = correo;
            if (clave) {
                this.clave = clave;
            }
        }
        ToString() {
            let retorno = `{"nombre":${this.nombre},"correo":${this.correo},`;
            return retorno;
        }
    }
    Entidades.Persona = Persona;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=persona.js.map