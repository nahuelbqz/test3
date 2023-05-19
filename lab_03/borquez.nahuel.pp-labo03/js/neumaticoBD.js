"use strict";
var Entidades;
(function (Entidades) {
    class NeumaticoBD extends Entidades.Neumatico {
        constructor(marca = "", medidas = "", precio = 0, id = 0, pathFoto = "") {
            super(marca, medidas, precio);
            this.id = id;
            this.pathFoto = pathFoto;
        }
        ToJSON() {
            return "{" + super.ToString() + ", " + `"id":${this.id},` + `"pathFoto":"${this.pathFoto}"}`;
        }
    }
    Entidades.NeumaticoBD = NeumaticoBD;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=neumaticoBD.js.map