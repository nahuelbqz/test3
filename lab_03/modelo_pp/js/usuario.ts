/// <reference path="./persona.ts" />

namespace Entidades{
    export class Usuario extends Persona
    {
        public id:number;
        public id_perfil:number;
        public perfil:string;

        public constructor(nombre:string, correo:string, id:number, id_perfil:number, perfil:string)
        {
            super(nombre,correo);
            
            this.id = id;
            this.id_perfil = id_perfil;
            this.perfil = perfil;
        }

        public ToJSON():JSON //ANY
        {
            let retorno:string = super.ToString() + `{"id":${this.id},"id_perfil":${this.id_perfil}},"perfil":${this.perfil}}`;
            
            return JSON.parse(retorno);
        }


    }
}