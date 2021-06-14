<?php

use \App\Models\Producto as Producto;
require_once './classes/Pdf.php';


class Archivo{

    public function CrearPdf($request, $response, $args)
    {
        $pdf = new PDF();
        $lista = [];
        //nombre de columnas tabla
        $arrayColumnas = ["id","nombre_producto","id_sector","precio"];

        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,0,"Lista de productos",0,20,"c");
        $pdf->Ln(20);

        if(isset($request->getQueryParams()['idProducto'])){
            //traigo por id si esta seteado el parametro ID
            $id = $request->getQueryParams()['idProducto'];
            $producto = Producto::where('id','=',$id)->first();

            if(!is_null($producto)){
                for ($i=0; $i < count($arrayColumnas); $i++) { 
                    $texto = $arrayColumnas[$i] . ':     ' . $producto[$arrayColumnas[$i]];
                    $pdf->Cell(40,0,$texto);
                    $pdf->Ln(10);
                }
            }else{
                $payload = json_encode(array("mensaje" => "El ID no existe"));
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json');
            }

        }else{ 
            // traigo todas
            $lista = Producto::all();
            foreach ($lista as $item) {
                for ($i=0; $i < count($arrayColumnas); $i++) { 
                    $texto = $arrayColumnas[$i] . ':     ' . $item[$arrayColumnas[$i]];
                    $pdf->Cell(40,0,$texto);
                    $pdf->Ln(10);
                }
                $pdf->Ln(30);
            }
            
        }

        $filename="./files/productos.pdf";
        $pdf->Output($filename,"F");

        $payload = json_encode(array("mensaje" => "PDF creado"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

        
    }

    public function CrearCsv($request, $response, $args)
    {
        //pasar por la ruta, no por param
        $path = '../app/files/'.$args['nombreArchivo'];
        $archivo_csv = fopen($path, 'w');
        $mensaje = "El archivo no existe o no se pudo crear";

        if($archivo_csv)
        {
            fputs($archivo_csv, "id;nombre_producto;precio;id_sector".PHP_EOL);  

            $result = \Illuminate\Database\Capsule\Manager::select('select * from productos');

            foreach ($result as $fila) {
            $id = $fila->id;
            $nombre = $fila->nombre_producto;
            $precio = $fila->precio;
            $idSector = $fila->id_sector;
            // $foto = $fila->foto;

            fputs($archivo_csv,$id.';'.$nombre.';'.$precio.';'.$idSector.PHP_EOL);
            }
            $mensaje ="Csv creado con exito";
            fclose($archivo_csv);
        }

        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

  
    public function CargarDatosCsv($request, $response, $args)
    {
      $linea = 0;
      //Abrimos nuestro archivo
  
      $path = '../app/files/'.$args['nombreArchivo'];
      $archivo = fopen($path, "r");
      //Lo recorremos
      while (($datos = fgetcsv($archivo, ",")) == true) 
      {
        //el archivo tiene titulo
        if($linea!=0)
        {
          $num = count($datos);
          $linea++;
          //Recorremos las columnas de esa linea
          for ($columna = 0; $columna < $num; $columna++) 
          {
            if($columna == 0)
            {
              $nombreProd = $datos[$columna];
            }else{
              $precio = $datos[$columna];
            }
          }
  
          if(!is_null($nombreProd))
          {
            $producto = new Producto();
            $producto->nombre_producto = $nombreProd;
            $producto->precio = (int)$precio;
            $producto->id_sector = 1;
            $producto->save();
          }
          
        }else{
          $linea++;
        }
        
      }
      //Cerramos el archivo
      fclose($archivo);
  
      $payload = json_encode(array("mensaje" => "Productos agregados con exito"));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }


}

?>