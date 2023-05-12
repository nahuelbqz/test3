namespace Entidades{
    export class Persona
    {
        public nombre:string;
        public correo:string;
        public clave:string | undefined;

        public constructor(nombre:string, correo:string, clave?:string)
        {
            this.nombre = nombre;
            this.correo = correo;

            if(clave){
                this.clave = clave;
            }
        }

        public ToString():string
        {
            let retorno:string = `{"nombre":${this.nombre},"correo":${this.correo},`;
            return retorno;
        }

    }
}