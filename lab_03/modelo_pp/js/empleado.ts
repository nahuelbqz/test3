/// <reference path="./persona.ts" />
/// <reference path="./usuario.ts" />

namespace Entidades{
    export class Empleado extends Usuario
    {
        public id:number;
        public sueldo:number;
        public foto:string;

        public constructor(nombre:string, correo:string, id:number, id_perfil:number, perfil:string,id_emp:number, sueldo:number, foto:string)
        {
            super(nombre,correo,id,id_perfil,perfil,);

            this.id = id_emp;
            this.sueldo = sueldo;
            this.foto = foto;
        }

    }
}