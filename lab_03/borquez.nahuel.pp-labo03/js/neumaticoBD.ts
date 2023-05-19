namespace Entidades {

    export class NeumaticoBD extends Neumatico 
    {
        id : number;
        pathFoto : string;
    
        constructor(marca:string = "", medidas:string = "", precio:number = 0, id:number = 0, pathFoto:string = "")
        {
            super(marca, medidas, precio);
            this.id = id;
            this.pathFoto = pathFoto;
        }
    
        public ToJSON(): string
        {
            return "{" + super.ToString() + ", " + `"id":${this.id},` + `"pathFoto":"${this.pathFoto}"}`;
        }
    
    }
    
}