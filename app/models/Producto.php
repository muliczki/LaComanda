<?php

class Producto
{
    public $id;
    public $nombre_producto;
    public $id_sector;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre_producto,id_sector) 
        VALUES ( :nombre,:idSector)");
        $consulta->bindValue(':nombre', $this->nombre_producto, PDO::PARAM_STR);
        $consulta->bindValue(':idSector', $this->id_sector, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre_producto,  id_sector FROM productos ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre_producto, id_sector FROM productos WHERE nombre_producto = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    // public static function modificarProducto($nombre)
    // {
    //     if(is_int((int)$minutos))
    //     {
    //         date_default_timezone_set('America/Argentina/Buenos_Aires');
    //         $ahora =  date ("Y-m-d H:i:s");
    //         $fechaActual = new DateTime();
    //         $fechaFin = $fechaActual->modify('+'.$minutos.' minute');

    //         $objAccesoDato = AccesoDatos::obtenerInstancia();
    //         //id_estado_pedido = 2 (EN PREPARACION)
    //         $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET fecha_finalizacion = :fechaFin, fecha_actualizacion = :fechaAct, id_estado_pedido=2 WHERE codigo_pedido = :codigo");
    //         $consulta->bindValue(':fechaFin', date_format($fechaFin, 'Y-m-d H:i:s'));
    //         $consulta->bindValue(':fechaAct', $ahora, PDO::PARAM_STR);
    //         $consulta->bindValue(':codigo', $producto, PDO::PARAM_STR);
    //         $consulta->execute();
    //     }
    // }

    // public static function borrarUsuario($usuario)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    //     $fecha = new DateTime(date("d-m-Y"));
    //     $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    //     $consulta->execute();
    // }
    

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