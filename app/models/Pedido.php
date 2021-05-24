<?php

class Pedido
{
    public $id;
    public $codigo_pedido;
    public $foto; 
    public $id_mesa;
    public $id_estado_mesa;
    public $id_cliente;


    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo_pedido, foto, id_mesa, id_estado_mesa, id_cliente) 
        VALUES (:codigo, :foto, :idMesa, :idEstadoMesa, :idCliente)");
        $consulta->bindValue(':codigo', $this->codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':idEstadoMesa', $this->id_estado_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':idCliente', $this->id_cliente, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_pedido, foto, id_mesa, id_estado_mesa, id_cliente FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_pedido, foto, id_mesa, id_estado_mesa, id_cliente 
        FROM pedidos WHERE codigo_pedido = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedidoEstadoMesa($codigo, $idEstadoMesa )
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET id_estado_mesa = :idEstadoMesa WHERE codigo_pedido = :codigo");
        $consulta->bindValue(':idEstadoMesa', $idEstadoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        
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
    

    

}