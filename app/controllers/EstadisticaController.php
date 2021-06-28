<?php
require_once './models/LoginUsers.php';
require_once './models/CambiosEstadosPedidos.php';
require_once './models/Sector.php';
require_once './models/Encuesta.php';
require_once './models/DetallePedido.php';

use \App\Models\CambiosEstadosPedidos as CambiosEstadosPedidos;
use \App\Models\LoginUsers as LoginUsers;
use \App\Models\Sector as Sector;
use \App\Models\DetallePedido as DetallePedido;
use App\Models\Pedido;
use App\Models\Encuesta;

// (Necesito ver en una fecha en particular o en un lapso de tiempo.)
class EstadisticaController{

    public function ListarLogins($request, $response, $args)
    {
        //NO INCLUYO OCUPACION 1 PORQUE SON SOCIOS, NO MOSTRAR SOCIOS
        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];
            $logins = LoginUsers::
            where('id_ocupacion', "<>", 1)->
            whereBetween('fecha_conexion', [$fechaDesde, $fechaHasta." 23:59:59"])->
            get();
            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"loginsEntreFechas" => $logins));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $logins = LoginUsers::
            where('fecha_conexion', "like", $fechaDesde."%")->
            where('id_ocupacion', "<>", 1)->
            get();
            $payload = json_encode(array("fecha" =>$fechaDesde, "loginsPorFecha" => $logins));
        }else{
            $logins = LoginUsers::
            where('id_ocupacion', "<>", 1)->
            get();
            $payload = json_encode(array("totalLogins" => $logins));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarOperacionesPorSector($request, $response, $args)
    {
        $sector = $request->getQueryParams()['sector'];
        $idSector = Sector::where('sector','=',$sector)->first();

        $operaciones = new CambiosEstadosPedidos;
        $operaciones = $operaciones->
        join('detallepedidos', 'cambiosestadospedidos.id_detalle_pedido', '=','detallepedidos.id')->
        join('productos', 'detallepedidos.id_producto', '=','productos.id')->
        select('cambiosestadospedidos.*', 'productos.nombre_producto')->
        where('productos.id_sector', "=", $idSector['id']);

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('cambiosestadospedidos.fecha_registro', [$fechaDesde, $fechaHasta." 23:59:59"])->get();

            $payload = json_encode(array("sector" =>$sector,"cantOperacion" =>count($operaciones),"fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"operEntreFechas" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('cambiosestadospedidos.fecha_registro', "like", $fechaDesde."%")->get();

            $payload = json_encode(array("sector" =>$sector,"cantOperacion" =>count($operaciones),"fecha" =>$fechaDesde, "operPorFecha" => $operaciones));
        }else{
            $operaciones = $operaciones->get();

            $payload = json_encode(array("sector" =>$sector,"cantOperacion" =>count($operaciones),"totalOperac" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function ListarOperacionesPorSectorEmpleado($request, $response, $args)
    {
        $sector = $request->getQueryParams()['sector'];
        $idSector = Sector::where('sector','=',$sector)->first();

        $operaciones = new CambiosEstadosPedidos;
        $operaciones = $operaciones->
        join('detallepedidos', 'cambiosestadospedidos.id_detalle_pedido', '=','detallepedidos.id')->
        join('productos', 'detallepedidos.id_producto', '=','productos.id')->
        select('cambiosestadospedidos.*', 'productos.nombre_producto')->
        where('productos.id_sector', "=", $idSector['id'])->
        orderByRaw('cambiosestadospedidos.id_usuario ASC');

        
        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('cambiosestadospedidos.fecha_registro', [$fechaDesde, $fechaHasta." 23:59:59"])->get();

            $payload = json_encode(array("sector" =>$sector,"cantOperacion" =>count($operaciones),"fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"operEntreFechas" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('cambiosestadospedidos.fecha_registro', "like", $fechaDesde."%")->get();

            $payload = json_encode(array("sector" =>$sector,"cantOperacion" =>count($operaciones),"fecha" =>$fechaDesde, "operPorFecha" => $operaciones));
        }else{
            $operaciones = $operaciones->get();


            $payload = json_encode(array("sector" =>$sector,"cantOperacion" =>count($operaciones),"totalOperac" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function ListarCantidadOperacionesEmpleados($request, $response, $args)
    {
        $operaciones = new CambiosEstadosPedidos;
        $operaciones = $operaciones->
        join('detallepedidos', 'cambiosestadospedidos.id_detalle_pedido', '=','detallepedidos.id')->
        join('productos', 'detallepedidos.id_producto', '=','productos.id')->
        orderByRaw('cambiosestadospedidos.id_usuario ASC')->
        groupBy('cambiosestadospedidos.id_usuario')->
        select('cambiosestadospedidos.id_usuario',CambiosEstadosPedidos::raw('count(cambiosestadospedidos.id_detalle_pedido) as cantidad_operaciones'));

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('cambiosestadospedidos.fecha_registro', [$fechaDesde, $fechaHasta." 23:59:59"])->get();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"operEntreFechas" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('cambiosestadospedidos.fecha_registro', "like", $fechaDesde."%")->get();

            $payload = json_encode(array("fecha" =>$fechaDesde, "operPorFecha" => $operaciones));
        }else{
            $operaciones = $operaciones->get();

            $payload = json_encode(array("totalOperac" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function ProductoMasVendido($request, $response, $args)
    {

        $operaciones = new DetallePedido;
        $operaciones = $operaciones->
        join('productos', 'productos.id', '=','detallepedidos.id_producto')->
        groupBy('detallepedidos.id_producto')->
        select('detallepedidos.id_producto', 'productos.nombre_producto',DetallePedido::raw('count(detallepedidos.id_producto) as cantidad_vendida'))->
        orderByRaw('3 DESC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('detallepedidos.fecha_solicitud', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"productoMasVendido" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('detallepedidos.fecha_solicitud', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "productoMasVendido" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("productoMasVendido" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function ProductoMenosVendido($request, $response, $args)
    {
        $operaciones = new DetallePedido;
        $operaciones = $operaciones->
        join('productos', 'productos.id', '=','detallepedidos.id_producto')->
        groupBy('detallepedidos.id_producto')->
        select('detallepedidos.id_producto', 'productos.nombre_producto',DetallePedido::raw('count(detallepedidos.id_producto) as cantidad_vendida'))->
        orderByRaw('3 ASC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('detallepedidos.fecha_solicitud', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"productoMenosVendido" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('detallepedidos.fecha_solicitud', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "productoMenosVendido" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("productoMenosVendido" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function ProductosCancelados($request, $response, $args)
    {
        $operaciones = new DetallePedido;
        $operaciones = $operaciones->withTrashed()->
        join('productos', 'productos.id', '=','detallepedidos.id_producto')->
        select('productos.nombre_producto' ,'detallepedidos.*')->
        where('id_estado_pedido', '=', 5);
        //ID 5 > PEDIDO CANCELADO

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('detallepedidos.fecha_baja', [$fechaDesde, $fechaHasta." 23:59:59"])->get();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"pedidosCancelados" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('detallepedidos.fecha_baja', "like", $fechaDesde."%")->get();

            $payload = json_encode(array("fecha" =>$fechaDesde, "pedidosCancelados" => $operaciones));
        }else{
            
            $operaciones = $operaciones->get();

            $payload = json_encode(array("pedidosCancelados" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }


    public function ListarFacturaPedido($request, $response, $args)
    {
        $cod = $args['pedido'];
        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
            join('detallepedidos', 'detallepedidos.id_codigo_pedido', '=','pedidos.id')->
            join('productos', 'detallepedidos.id_producto', '=','productos.id')->
            groupBy('detallepedidos.id_codigo_pedido')->
            where('pedidos.codigo_pedido', '=', $cod)->
            whereNull('detallepedidos.fecha_baja')->
            select('pedidos.codigo_pedido', 'mesas.codigo_mesa',Pedido::raw('sum(productos.precio) as total_pagar'))->get();
        
        $payload = json_encode(array("factura" => $operaciones));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }




    public function MesaMasUsada($request, $response, $args)
    {
        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
        groupBy('pedidos.id_mesa')->
        select('pedidos.id_mesa', 'mesas.codigo_mesa',Pedido::raw('count(pedidos.id_mesa) as veces_usada'))->
        orderByRaw('3 DESC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('pedidos.fecha_alta', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"mesaMasUsada" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('pedidos.fecha_alta', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "mesaMasUsada" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("mesaMasUsada" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function MesaMenosUsada($request, $response, $args)
    {

        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
        groupBy('pedidos.id_mesa')->
        select('pedidos.id_mesa', 'mesas.codigo_mesa',Pedido::raw('count(pedidos.id_mesa) as veces_usada'))->
        orderByRaw('3 ASC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('pedidos.fecha_alta', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"mesaMenosUsada" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('pedidos.fecha_alta', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "mesaMenosUsada" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("mesaMenosUsada" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function MasFacturacion($request, $response, $args)
    {

        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
            join('detallepedidos', 'detallepedidos.id_codigo_pedido', '=','pedidos.id')->
            join('productos', 'detallepedidos.id_producto', '=','productos.id')->
            groupBy('pedidos.id_mesa')->
            whereNull('detallepedidos.fecha_baja')->
            select('pedidos.id_mesa', 'mesas.codigo_mesa',Pedido::raw('sum(productos.precio) as facturacion'))->
            orderByRaw('3 DESC');
        
        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('pedidos.fecha_alta', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"mesaMasFacturacion" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('pedidos.fecha_alta', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "mesaMasFacturacion" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("mesaMasFacturacion" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function MenosFacturacion($request, $response, $args)
    {

        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
        join('detallepedidos', 'detallepedidos.id_codigo_pedido', '=','pedidos.id')->
        join('productos', 'detallepedidos.id_producto', '=','productos.id')->
        groupBy('pedidos.id_mesa')->
        whereNull('detallepedidos.fecha_baja')->
        select('pedidos.id_mesa', 'mesas.codigo_mesa',Pedido::raw('sum(productos.precio) as facturacion'))->
        orderByRaw('3 ASC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('pedidos.fecha_alta', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"mesaMenosFacturacion" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('pedidos.fecha_alta', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "mesaMenosFacturacion" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("mesaMenosFacturacion" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }
    

    public function MejorFactura($request, $response, $args)
    {

        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
        join('detallepedidos', 'detallepedidos.id_codigo_pedido', '=','pedidos.id')->
        join('productos', 'detallepedidos.id_producto', '=','productos.id')->
        groupBy('pedidos.id')->
        whereNull('detallepedidos.fecha_baja')->
        select('pedidos.id','pedidos.codigo_pedido', 'mesas.codigo_mesa',Pedido::raw('sum(productos.precio) as factura'))->
        orderByRaw('4 DESC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('pedidos.fecha_alta', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"mejorFactura" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('pedidos.fecha_alta', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "mejorFactura" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();


            $payload = json_encode(array("mejorFactura" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function PeorFactura($request, $response, $args)
    {
        $operaciones = new Pedido;
        $operaciones = $operaciones->join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
                        join('detallepedidos', 'detallepedidos.id_codigo_pedido', '=','pedidos.id')->
                        join('productos', 'detallepedidos.id_producto', '=','productos.id')->
                        groupBy('pedidos.id')->
                        whereNull('detallepedidos.fecha_baja')->
                        select('pedidos.id','pedidos.codigo_pedido', 'mesas.codigo_mesa','pedidos.fecha_alta as fecha_factura',Pedido::raw('sum(productos.precio) as factura'))->
                        orderByRaw('5 ASC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('pedidos.fecha_alta', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"peorFactura" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('pedidos.fecha_alta', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "peorFactura" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();

            $payload = json_encode(array("peorFacturaGeneral" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function MesaMejoresComentarios($request, $response, $args)
    {
        $operaciones = new Encuesta;
        $operaciones = $operaciones->join('pedidos', 'pedidos.id', '=','encuestas.id_pedido')->
                        join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
                        groupBy('encuestas.id')->
                        select('pedidos.id as id_pedido','pedidos.codigo_pedido', 'mesas.codigo_mesa','encuestas.fecha_creacion as fecha_encuesta',Encuesta::raw('sum(encuestas.nota_mesa + encuestas.nota_resto + encuestas.nota_mozo + encuestas.nota_cocinero)/4 as promedio_notas'), 'encuestas.experiencia')->
                        orderByRaw('5 DESC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('encuestas.fecha_creacion', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"mejorPromedioEncuesta" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('encuestas.fecha_creacion', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "mejorPromedioEncuesta" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();
            $payload = json_encode(array("mejorPromedioEncuesta" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function MesaPeoresComentarios($request, $response, $args)
    {
        $operaciones = new Encuesta;
        $operaciones = $operaciones->join('pedidos', 'pedidos.id', '=','encuestas.id_pedido')->
                        join('mesas', 'mesas.id', '=','pedidos.id_mesa')->
                        groupBy('encuestas.id')->
                        select('pedidos.id as id_pedido','pedidos.codigo_pedido', 'mesas.codigo_mesa','encuestas.fecha_creacion as fecha_encuesta',Encuesta::raw('sum(encuestas.nota_mesa + encuestas.nota_resto + encuestas.nota_mozo + encuestas.nota_cocinero)/4 as promedio_notas'), 'encuestas.experiencia')->
                        orderByRaw('5 ASC');

        if(isset($request->getQueryParams()['fecha_desde']) && isset($request->getQueryParams()['fecha_hasta'])){
            
            $fechaDesde = $request->getQueryParams()['fecha_desde'];
            $fechaHasta = $request->getQueryParams()['fecha_hasta'];

            $operaciones = $operaciones->
            whereBetween('encuestas.fecha_creacion', [$fechaDesde, $fechaHasta." 23:59:59"])->first();

            $payload = json_encode(array("fechaDesde" =>$fechaDesde,"fechaHasta" =>$fechaHasta,"peorPromedioEncuesta" => $operaciones));
        
        }else if (isset($request->getQueryParams()['fecha_desde'])) {
            $fechaDesde = $request->getQueryParams()['fecha_desde'];

            $operaciones = $operaciones->
            where('encuestas.fecha_creacion', "like", $fechaDesde."%")->first();

            $payload = json_encode(array("fecha" =>$fechaDesde, "peorPromedioEncuesta" => $operaciones));
        }else{
            
            $operaciones = $operaciones->first();
            $payload = json_encode(array("peorPromedioEncuesta" => $operaciones));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>