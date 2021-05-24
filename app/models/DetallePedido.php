<?php

class DetallePedido
{
    public $id;
    public $id_codigo_pedido;
    public $id_producto;
    public $id_estado_pedido;
    public $fecha_solicitud;
    public $fecha_actualizacion;
    public $fecha_estimada;
    public $fecha_finalizacion;
    public $id_responsable;

    public function crearDetallePedido()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fecha = date ("Y-m-d H:i:s");

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO detallepedidos (id_codigo_pedido, id_producto, id_estado_pedido, fecha_solicitud, fecha_actualizacion, id_responsable) 
        VALUES (:idCodigo,:idProducto, :idEstado, :fechaS, :fechaA, :idResponsable)");
        $consulta->bindValue(':idCodigo', $this->id_codigo_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', $this->id_estado_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':fechaS', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':fechaA', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':idResponsable', $this->id_responsable, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_codigo_pedido, id_producto, id_estado_pedido, fecha_solicitud, fecha_actualizacion, fecha_finalizacion, fecha_estimada, id_responsable FROM detallepedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'DetallePedido');
    }

    public static function obtenerDetallePedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.id, d.id_codigo_pedido, d.id_producto, d.id_estado_pedido, d.fecha_solicitud, d.fecha_actualizacion, d.fecha_finalizacion, d.fecha_estimada, d.id_responsable FROM detallepedidos d INNER JOIN pedidos p WHERE p.codigo_pedido = :codigo AND d.id_codigo_pedido = p.id");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'DetallePedido');
    }


    //FALTA VER QUE HACER CON LAS FECHAS
    public static function modificarDetallePedido($idProducto,$minutos, $idResponsable, $idEstadoPedido) 
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $ahora =  date ("Y-m-d H:i:s");

        if(is_int((int)$minutos) && $minutos!=0)
        {
            $fechaActual = new DateTime();
            $fechaFin = $fechaActual->modify('+'.$minutos.' minute');

            $consulta = $objAccesoDato->prepararConsulta("UPDATE detallepedidos SET fecha_estimada = :fechaEst, fecha_actualizacion = :fechaAct, id_estado_pedido=:idEstado, id_responsable = :idResponsable 
            WHERE id = :idProducto");
            $consulta->bindValue(':fechaEst', date_format($fechaFin, 'Y-m-d H:i:s'));
            $consulta->bindValue(':fechaAct', $ahora, PDO::PARAM_STR);
            $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $consulta->bindValue(':idResponsable', $idResponsable, PDO::PARAM_INT);
            $consulta->bindValue(':idEstado', $idEstadoPedido, PDO::PARAM_INT);
            $consulta->execute();
        }else 
        {

            $consulta = $objAccesoDato->prepararConsulta("UPDATE detallepedidos SET fecha_actualizacion = :fechaAct, id_estado_pedido=:idEstado, id_responsable = :idResponsable 
            WHERE id = :idProducto");
            $consulta->bindValue(':fechaAct', $ahora, PDO::PARAM_STR);
            $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $consulta->bindValue(':idResponsable', $idResponsable, PDO::PARAM_INT);
            $consulta->bindValue(':idEstado', $idEstadoPedido, PDO::PARAM_INT);
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

}