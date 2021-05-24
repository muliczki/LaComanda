<?php

class Persona
{
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $id_sector;
    public $id_ocupacion;
    public $id_estado_empleado;


    public function crearPersona()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO personas (nombre, apellido, email, id_sector, id_ocupacion, id_estado_empleado) 
        VALUES ( :nombre,:apellido, :email, :idSector, :idOcup, :idEstado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':idSector', $this->id_sector, PDO::PARAM_INT);
        $consulta->bindValue(':idOcup', $this->id_ocupacion, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', $this->id_estado_empleado, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, email, id_sector, id_ocupacion, id_estado_empleado FROM personas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Persona');
    }


    public static function obtenerPersona($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, email, id_sector, id_ocupacion, id_estado_empleado FROM personas 
        WHERE email = :email");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Persona');
    }
}

?>