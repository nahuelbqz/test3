"use strict";
var Entidades;
(function (Entidades) {
    class Neumatico {
        constructor(marca, medidas, precio) {
            this.marca = marca;
            this.medidas = medidas;
            this.precio = precio;
        }
        ToString() {
            return `"marca":"${this.marca}", "medidas":"${this.medidas}", "precio":"${this.precio}"`;
        }
        ToJSON() {
            return "{" + this.ToString() + "}";
        }
    }
    Entidades.Neumatico = Neumatico;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=neumatico.js.map