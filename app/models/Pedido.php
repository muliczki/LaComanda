<?php

class Pedido
{
    public $id;
    public $codigo_pedido;
    public $producto;
    public $nombre_cliente;
    public $id_estado_pedido;
    public $id_sector;
    //public $foto; VER
    public $fecha_solicitud;
    public $fecha_actualizacion;
    public $fecha_finalizacion;
    public $id_mesa;
    public $precio;

    public function crearPedido()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fecha = date ("Y-m-d H:i:s");

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo_pedido, producto, nombre_cliente, id_estado_pedido, id_sector, fecha_solicitud, fecha_actualizacion, id_mesa, precio) 
        VALUES (:codigo,:producto, :nombre, :idEstado, :idSector, :fechaS, :fechaA, :idMesa, :precio)");
        $consulta->bindValue(':codigo', $this->codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':producto', $this->producto, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':idEstado', $this->id_estado_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':idSector', $this->id_sector, PDO::PARAM_INT);
        $consulta->bindValue(':fechaS', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':fechaA', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_pedido, producto, nombre_cliente, id_estado_pedido, id_sector, fecha_solicitud, fecha_actualizacion, id_mesa, precio FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_pedido, producto, nombre_cliente, id_estado_pedido, id_sector, fecha_solicitud, fecha_actualizacion, id_mesa, precio FROM pedidos WHERE codigo_pedido = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($producto,$minutos)
    {
        if(is_int((int)$minutos))
        {
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $ahora =  date ("Y-m-d H:i:s");
            $fechaActual = new DateTime();
            $fechaFin = $fechaActual->modify('+'.$minutos.' minute');

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            //id_estado_pedido = 2 (EN PREPARACION)
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET fecha_finalizacion = :fechaFin, fecha_actualizacion = :fechaAct, id_estado_pedido=2 WHERE codigo_pedido = :codigo");
            $consulta->bindValue(':fechaFin', date_format($fechaFin, 'Y-m-d H:i:s'));
            $consulta->bindValue(':fechaAct', $ahora, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $producto, PDO::PARAM_STR);
            $consulta->execute();
        }
    }

    // public static function borrarUsuario($usuario)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    //     $fecha = new DateTime(date("d-m-Y"));
    //     $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    //     $consulta->execute();
    // }


    public static function crearCodigoPedido()
    {
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 5);
    }
    

    public static function TraerIdMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM mesas WHERE codigo_mesa = :codigo");
        $consulta->bindValue(':codigo', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA

        for ($i=0; $i < count($objetos); $i++) { 
            foreach ($objetos[$i] as $key => $value) {
                $id= $value;
            }
        }
    
        return $id;
    }

    public static function TraerIdSector($nombreSector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM sectores WHERE sector = :sector");
        $consulta->bindValue(':sector', $nombreSector, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA

        for ($i=0; $i < count($objetos); $i++) { 
            foreach ($objetos[$i] as $key => $value) {
                $id= $value;
            }
        }
    
        return $id;
    }

}