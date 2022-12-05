<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Autentificadora
{
    private static string $secret_key = 'ClaveSuperSecreta@';
    private static array $encrypt = ['HS256'];
    //private static string $aud = "";
    
    public static function crearJWT(mixed $data, int $exp = (60*5)) : string
    {
        $time = time();
        //self::$aud = self::aud();

        $token = array(
        	'iat'=>$time,
            'exp' => $time + $exp,
            //'aud' => self::$aud,
            'usuario' => $data,
            'alumno'=> "NAHUEL_BORQUEZ",
            'parcial'=> "SP_PROG 2022"
        );

        return JWT::encode($token, self::$secret_key,"HS256");
    }
    
    public static function verificarJWT(string $token) : stdClass
    {
        $datos = new stdClass();
        $datos->status = 403;
        $datos->mensaje = "";

        try
        {
            if( ! isset($token))
            {
                $datos->mensaje = "Token vacío!!!";
            }
            else
            {
                $decode = JWT::decode(
                    $token,
                    new Key(self::$secret_key, "HS256")    //ALGORITMO DE CODIFICACION
                );

                // if($decode->aud !== self::aud())
                // {
                //     throw new Exception("Usuario inválido!!!");
                // }
                //else
                //{
                $datos->status = 200;
                $datos->mensaje = "Token OK";
                //} 
            }          
        } 
        catch (Exception $e) 
        {
            $datos->mensaje = "Token inválido!!! - " . $e->getMessage();
        }
    
        return $datos;
    }
    
    public static function obtenerPayLoad(string $token) : object
    {
        $datos = new stdClass();
        $datos->exito = FALSE;
        $datos->payload = NULL;
        $datos->mensaje = "";

        try{

            $datos->payload = JWT::decode(
                                            $token,
                                            new Key(self::$secret_key, "HS256")     //ALGORITMO DE CODIFICACION
                                        );
            $datos->exito = TRUE;

        }catch (Exception $e){ 

            $datos->mensaje = $e->getMessage();
        }

        return $datos;
    }
    
    // private static function aud() : string
    // {
    //     $aud = new stdClass();
    //     $aud->ip_visitante = "";

    //     if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    //         $aud->ip_visitante = $_SERVER['HTTP_CLIENT_IP'];
    //     } 
    //     elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //         $aud->ip_visitante = $_SERVER['HTTP_X_FORWARDED_FOR'];
    //     } else {
    //         $aud->ip_visitante = $_SERVER['REMOTE_ADDR'];//La dirección IP desde la cual está viendo la página actual el usuario.
    //     }
        
    //     $aud->user_agent = @$_SERVER['HTTP_USER_AGENT'];
    //     $aud->host_name = gethostname();
        
    //     return json_encode($aud);//sha1($aud);
    // }

}


