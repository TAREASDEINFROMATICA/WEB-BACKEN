<?php
	
	class Productos_model {
		
		private $db;
		private $productos;
		
		public function __construct(){
			$this->db = Conectar::conexion();
			$this->productos = array();
		}
		
		public function get_productos()
		{
			$sql = "SELECT * FROM producto";
			$resultado = $this->db->query($sql);
			while($row = $resultado->fetch_assoc())
			{
				$this->productos[] = $row;
			}
			return $this->productos;
		}
		
		public function insertar($idproducto, $nombre, $material, $color, $precio){
			
			$resultado = $this->db->query(
				"INSERT INTO producto 
			(idproducto, nombre, material, color, precio) 
			VALUES 
			('$idproducto', '$nombre', '$material', '$color', '$precio')"
			);
			
		}
		
		public function modificar($idproducto, $nombre, $material, $color, $precio){
			
			$resultado = $this->db->query("UPDATE producto SET idproducto='$idproducto', nombre='$nombre', material='$material', color='$color', precio='$precio' WHERE idproducto = '$idproducto'");			
		}
		
		public function eliminar($id){
			
			$resultado = $this->db->query("DELETE FROM producto WHERE idproducto = '$id'");
			
		}
		
		public function get_producto($id)
		{
			$sql = "SELECT * FROM producto WHERE idproducto='$id' LIMIT 1";
			$resultado = $this->db->query($sql);
			$row = $resultado->fetch_assoc();

			return $row;
		}
	} 
?>