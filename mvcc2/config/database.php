<?php
	
	class Conectar {
		
		public static function conexion(){
			
			$conexion = new mysqli("localhost", "root", "12345", "bdmueble");
			return $conexion;
			
		}
	}
?>