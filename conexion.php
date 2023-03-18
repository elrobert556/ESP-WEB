<?php 
class BD_PDO
{
	function Ejecutar_Instruccion($instruccion_sql)
	{
		$host = "localhost";
		$usr  = "id19945353_joel";
		$pwd  = "ABCabc123***";
		$db   = "id19945353_espwifi";

		try {
				$conexion = new PDO("mysql:host=$host;dbname=$db;",$usr,$pwd);
		       //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		catch(PDOException $e)
			{
		      echo "Failed to get DB handle: " . $e->getMessage();
		      exit;    
		    }
		 
		 // Asignando una instruccion sql

		 $query=$conexion->prepare($instruccion_sql);
		if(!$query)
		{
			return "Error al mostrar";
		}
		else
		{
			$query->execute();
			while ($result = $query->fetch())
			    {
			        @$rows[] = $result;
			    }	
		}
		return $rows;
	}


	public function listados($consulta_primaria,$consulta_foranea)
	{
		$datos = "";
		$datos_primaria = $this->Ejecutar_Instruccion($consulta_primaria);
		$datos_foranea = $this->Ejecutar_Instruccion($consulta_foranea);
		
		$selected = "";
		foreach ($datos_primaria as $renglon) 
		{
			if ($datos_foranea[0][0]==$renglon[0]) 
			{
				$selected = "Selected";
			}
			else
			{
				$selected = "";
			}
			$datos=$datos.'<option value="'.$renglon[0].'" '.$selected.'>'.$renglon[1].'</option>';
		}
		return $datos;
	}

}