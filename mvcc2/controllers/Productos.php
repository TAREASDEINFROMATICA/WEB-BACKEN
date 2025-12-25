<?php
	
	class ProductosController {
		
		public function __construct(){
			require_once "models/ProductosModel.php";
		}
		
		public function index(){
			
			
			$productos = new Productos_model();
			$data["titulo"] = "Productos";
			$data["productos"] = $productos->get_productos();
			
			require_once "views/productos/productos.php";	
		}
		
		public function nuevo(){
			
			$data["titulo"] = "Productos";
			require_once "views/productos/productos_nuevo.php";
		}
		
		public function guarda(){
			
			$idproducto = $_POST['idproducto'];
			$nombre = $_POST['nombre'];
			$material = $_POST['material'];
			$color = $_POST['color'];
			$precio = $_POST['precio'];
			
			$productos = new Productos_model();
			$productos->insertar($idproducto, $nombre, $material, $color, $precio);
			$data["titulo"] = "Productos";
			$this->index();
		}
		
		public function modificar($id){
			
			$productos = new Productos_model();
			
			$data["idproducto"] = $id;
			$data["productos"] = $productos->get_producto($id);
			$data["titulo"] = "productos";
			require_once "views/productos/productos_modifica.php";
		}
		
		public function actualizar(){

			$idproducto = $_POST['idproducto'];
			$nombre = $_POST['nombre'];
			$material = $_POST['material'];
			$color = $_POST['color'];
			$precio = $_POST['precio'];

			$productos = new Productos_model();
			$productos->modificar($idproducto, $nombre, $material, $color, $precio);
			$data["titulo"] = "productos";
			$this->index();
		}
		
		public function eliminar($id){
			
			$productos = new Productos_model();
			$productos->eliminar($id);
			$data["titulo"] = "productos";
			$this->index();
		}	
	}
?>