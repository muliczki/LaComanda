<?php
require_once './models/Producto.php';
require_once './models/Sector.php';
require_once './models/DetallePedido.php';
require_once './models/ConsultasPDO.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Pedido;
use \App\Models\Producto as Producto;
use \App\Models\Sector as Sector;
use \App\Models\DetallePedido as DetallePedido;


class ProductoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombreProducto = $parametros['peticion']['nombreProducto'];
    $precio = $parametros['peticion']['precio'];
    $idSector = Sector::where('sector','=',$parametros['peticion']['sectorDesc'])->first();

    // Creamos el producto
    $producto = new Producto();
    $producto->nombre_producto = $nombreProducto;
    $producto->id_sector = $idSector['id'];
    $producto->precio = $precio;
    $producto->save();

    $payload = json_encode(array("mensaje" => "Producto ". $nombreProducto ." agregado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos producto por nombre
    $nombre = $args['nombreProducto'];
    $producto = Producto::where('nombre_producto', $nombre)->first();
    $payload = json_encode($producto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::all();
    $payload = json_encode(array("listaProductos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $productoCodigo = $parametros['peticion']['codigoProducto'];
    $precio = $parametros['peticion']['precio'];

    $producto = Producto::where("id","=",$productoCodigo)->first();

    $producto->precio = $precio;
    $producto->save();

    $payload = json_encode(array("mensaje" => "Pedido ".$productoCodigo. " actualizado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];
    // Buscamos el id
    $item = Producto::find($id);
    
    // Borramos si existe
    if(!is_null($item)){
      $item->delete();
      $mensaje = "Item borrado con exito";
    }else{
      $mensaje = "El ID no existe";
    }

    $payload = json_encode(array("mensaje" => $mensaje));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  // public function CargarCsv($request, $response, $args)
  // {
  //   $linea = 0;
  //   //Abrimos nuestro archivo

  //   $path = '../app/files/'.$args['nombreArchivo'];
  //   $archivo = fopen($path, "r");
  //   //Lo recorremos
  //   while (($datos = fgetcsv($archivo, ",")) == true) 
  //   {
  //     //el archivo tiene titulo
  //     if($linea!=0)
  //     {
  //       $num = count($datos);
  //       $linea++;
  //       //Recorremos las columnas de esa linea
  //       for ($columna = 0; $columna < $num; $columna++) 
  //       {
  //         if($columna == 0)
  //         {
  //           $nombreProd = $datos[$columna];
  //         }else{
  //           $precio = $datos[$columna];
  //         }
  //       }

  //       if(!is_null($nombreProd))
  //       {
  //         $producto = new Producto();
  //         $producto->nombre_producto = $nombreProd;
  //         $producto->precio = (int)$precio;
  //         $producto->id_sector = 1;
  //         $producto->save();
  //       }
        
  //     }else{
  //       $linea++;
  //     }
      
  //   }
  //   //Cerramos el archivo
  //   fclose($archivo);

  //   $payload = json_encode(array("mensaje" => "Productos agregados con exito"));

  //   $response->getBody()->write($payload);
  //   return $response
  //     ->withHeader('Content-Type', 'application/json');
  // }

  // public function ExportarCsv($request, $response, $args)
  // {
  //   $path = '../app/files/'.$args['nombreArchivo'];
  //   $archivo_csv = fopen($path, 'w');

  // if($archivo_csv)
  // {
  //   fputs($archivo_csv, "id;nombre_producto;id_sector;precio".PHP_EOL);  

  //   $result = \Illuminate\Database\Capsule\Manager::select('select * from productos');

  //   foreach ($result as $fila) {
  //     $id = $fila->id;
  //     $nombre_producto = $fila->nombre_producto;
  //     $id_sector = $fila->id_sector;
  //     $precio = $fila->precio;

  //     fputs($archivo_csv,$id.';'.$nombre_producto.';'.$id_sector.';'.$precio.PHP_EOL);
  //   }

  //   fclose($archivo_csv);
  // }else{

  //   echo "El archivo no existe o no se pudo crear";

  // }

  // $payload = json_encode(array("mensaje" => "Csv creado con exito"));

  //   $response->getBody()->write($payload);
  //   return $response
  //     ->withHeader('Content-Type', 'application/json');

  // }


  public function TraerProductoMasVendido($request, $response, $args)
  {
    
    // $producto = DetallePedido::
    // select("id")->
    // groupBy('id')->
    // orderByRaw('count(id_producto) DESC')->first();

    // $nombre = Producto::where('id','=',$producto['id'])->first()['nombre'];


    // $payload = json_encode(array("hortalizaMasVendida" => $nombre));
    // $response->getBody()->write($payload);
    // return $response
    //   ->withHeader('Content-Type', 'application/json');
  }
}
