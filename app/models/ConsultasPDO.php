<?php

class ConsultasPdo{

    //MESAS
    public static function TraerIdMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM mesas WHERE codigo_mesa = :codigo");
        $consulta->bindValue(':codigo', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }
    
    public static function TraerIdEstadoMesa($descripcionEstado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM estadosmesas WHERE descripcion = :descr");
        $consulta->bindValue(':descr', $descripcionEstado, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);
        
        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //SECTOR
    public static function TraerIdSector($nombreSector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM sectores WHERE sector = :sector");
        $consulta->bindValue(':sector', $nombreSector, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    public static function TraerIdSectorPersona($idPersona)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT s.id FROM sectores s INNER JOIN personas p  WHERE p.id = :idPersona AND p.id_sector = s.id");
        $consulta->bindValue(':idPersona', $idPersona, PDO::PARAM_INT);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //OCUPACION
    public static function TraerIdOcupacion($nombreOcupacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM ocupaciones WHERE ocupacion = :ocupacion");
        $consulta->bindValue(':ocupacion', $nombreOcupacion, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    public static function TraerIdOcupacionPersona($idPersona)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT o.id FROM ocupaciones o INNER JOIN personas p ON p.id_ocupacion = o.id WHERE p.id = :idPersona");
        $consulta->bindValue(':idPersona', $idPersona, PDO::PARAM_INT);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    public static function TraerDescOcupacion($idOcupacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ocupacion FROM ocupaciones WHERE id = :id");
        $consulta->bindValue(':id', $idOcupacion, PDO::PARAM_INT);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //ESTADO PEDIDO
    public static function TraerIdEstadoPedido($descripcionEstado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM estadospedidos WHERE descripcion = :descr");
        $consulta->bindValue(':descr', $descripcionEstado, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);
        
        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //PERSONA
    public static function TraerIdPersona($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM personas WHERE email = :email");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //CODIGO PEDIDO
    public static function TraerIdCodigoPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM pedidos WHERE codigo_pedido = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //PRODUCTO
    public static function TraerIdProducto($nombreProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM productos WHERE nombre_producto = :nombre");
        $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);
        $consulta->execute();
        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    //DETALLE PEDIDO
    public static function traerIdPrimerDetallePendiente($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.id 
        FROM detallepedidos d INNER JOIN productos p ON d.id_producto = p.id 
        INNER JOIN sectores s ON s.id = p.id_sector 
        WHERE d.id_estado_pedido=1 AND p.id_sector=:idSector 
        ORDER BY d.fecha_solicitud LIMIT 1");

        $consulta->bindValue(':idSector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        $objetos = $consulta->fetchAll(PDO::FETCH_OBJ);

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return self::RecorrerObjetoPdoDevolverId($objetos);
    }

    public static function traerDetallePendiente($idDetalle)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.id as id_detalle, p.nombre_producto as producto 
        FROM detallepedidos d 
        INNER JOIN productos p ON d.id_producto = p.id 
        WHERE d.id=:idDetalle ");

        $consulta->bindValue(':idDetalle', $idDetalle, PDO::PARAM_INT);
        $consulta->execute();

        // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    //CREAR CODIGO PEDIDO
    public static function crearCodigoPedido()
    {
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 5);
    }

    //no la use
    public static function traerUserPass($idPersona)
    {
        // $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // $consulta = $objAccesoDatos->prepararConsulta("SELECT clave
        // FROM usuarios
        // WHERE id_persona=:idPersona ");

        // $consulta->bindValue(':idDetalle', $idPersona, PDO::PARAM_INT);
        // $consulta->execute();

        // // DEVUELVO UN OBJETO CON KEYS = COLUMNAS DE LA CONSULTA
        // return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function RecorrerObjetoPdoDevolverId ($array)
    {
        $id= 0;
        for ($i=0; $i < count($array); $i++) { 
            foreach ($array[$i] as $key => $value) {
                $id= $value;
            }
        }
        return $id;
    }

}



?>
