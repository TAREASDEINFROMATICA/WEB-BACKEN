from flask import Flask, render_template, request, redirect, url_for, flash, jsonify
import mariadb 
import pyodbc
from datetime import datetime
import json

app = Flask(__name__)
app.secret_key = 'clave_secreta_para_flash_messages'

# ====================
# CONEXIONES A LAS BASES DE DATOS (Mismo que antes)
# ====================
def get_db_sur():
    try:
        conn = mariadb.connect(
            host="26.22.192.77",
            port=3307,
            user="root",
            password="12345",
            database="cementerio_sur"
        )
        return conn, None
    except mariadb.Error as e:
        return None, f"Error MariaDB Sur: {str(e)}"

def get_db_centro():
    try:
        conn = pyodbc.connect(
            "DRIVER={ODBC Driver 17 for SQL Server};"
            "SERVER=26.109.74.33;"
            "DATABASE=Cementerio_Central;"
            "UID=sa;"
            "PWD=12345;"
            "TrustServerCertificate=yes;"
        )
        return conn, None
    except pyodbc.Error as e:
        return None, f"Error SQL Server Central: {str(e)}"

def get_db_norte():
    try:
        conn = pyodbc.connect(
            "DRIVER={ODBC Driver 17 for SQL Server};"
            "SERVER=26.35.201.190;"
            "DATABASE=Cementerio_Norte;"
            "UID=sa;"
            "PWD=123;"
            "TrustServerCertificate=yes;"
        )
        return conn, None
    except pyodbc.Error as e:
        return None, f"Error SQL Server Norte: {str(e)}"

# ====================
# CONFIGURACIÓN DE FRAGMENTACIÓN (Mismo que antes)
# ====================
FRAGMENTACION_CONFIG = {
    'Cementerio': {
        'tipo': 'replica',
        'columnas': ['id_cementerio', 'nombre', 'zona', 'direccion', 'tipo'],
        'primary_key': 'id_cementerio'
    },
    'Responsable': {
        'tipo': 'horizontal',
        'columnas': ['id_responsable', 'nombre', 'apellido', 'telefono', 'ciudad', 'zona'],
        'primary_key': 'id_responsable',
        'fragmento_sur': "ciudad IN ('Lima', 'Arequipa', 'Cusco')",
        'fragmento_norte': "ciudad IN ('Trujillo', 'Chiclayo', 'Piura')"
    },
    'Nicho': {
        'tipo': 'horizontal',
        'columnas': ['id_nicho', 'codigo_nicho', 'tipo_nicho', 'estado', 'id_cementerio', 'ciudad', 'zona'],
        'primary_key': 'id_nicho',
        'fragmento_sur': "ciudad IN ('Lima', 'Arequipa', 'Cusco')",
        'fragmento_norte': "ciudad IN ('Trujillo', 'Chiclayo', 'Piura')"
    },
    'Difunto_InfoPersonal': {
        'tipo': 'vertical',
        'columnas_centro': ['id_difunto', 'nombre', 'apellido', 'fecha_fallecimiento', 'ciudad', 'zona'],
        'columnas_nodos': ['id_difunto', 'nombre', 'apellido', 'fecha_fallecimiento', 'ciudad'],
        'primary_key': 'id_difunto'
    },
    'Difunto_InfoFallecimiento': {
        'tipo': 'vertical',
        'columnas_centro': ['id_difunto', 'causa_fallecimiento', 'id_responsable', 'id_nicho'],
        'columnas_nodos': ['id_difunto', 'causa_fallecimiento', 'id_responsable', 'id_nicho'],
        'primary_key': 'id_difunto'
    },
    'Pago_Mixto': {
        'tipo': 'mixto',
        'columnas_centro': ['id_pago', 'fecha_pago', 'monto', 'tipo_pago', 'metodo_pago', 'zona'],
        'columnas_nodos': ['id_pago', 'fecha_pago', 'monto', 'tipo_pago', 'metodo_pago'],
        'primary_key': 'id_pago'
    
    },
    'Traslado_Mixto': {
        'tipo': 'mixto',
        'columnas_centro': ['id_traslado', 'fecha', 'motivo', 'cementerio_origen', 'cementerio_destino', 'zona_origen', 'zona_destino'],
        'columnas_nodos': ['id_traslado', 'fecha', 'motivo', 'cementerio_origen', 'cementerio_destino'],
        'primary_key': 'id_traslado'
    }
}

# ====================
# FUNCIONES DE SINCRONIZACIÓN PARA ELIMINAR
# ====================
def eliminar_replica(tabla, id_valor):
    """Elimina de todas las bases (Réplica)"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    resultados = {}
    
    # Eliminar de CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['centro'] = f"Error conexión: {error}"
    
    # Eliminar de SUR
    conn, error = get_db_sur()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['sur'] = True
        except Exception as e:
            conn.rollback()
            resultados['sur'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['sur'] = f"Error conexión: {error}"
    
    # Eliminar de NORTE
    conn, error = get_db_norte()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['norte'] = True
        except Exception as e:
            conn.rollback()
            resultados['norte'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['norte'] = f"Error conexión: {error}"
    
    return resultados
def obtener_zona_registro(tabla, id_valor):
    """Obtiene la zona de un registro para fragmentación mixta"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    conn, error = get_db_centro()
    if not conn:
        return None
    
    cursor = conn.cursor()
    try:
        # Intentar diferentes columnas de zona según la tabla
        if 'zona' in config['columnas_centro']:
            cursor.execute(f"SELECT zona FROM {tabla} WHERE {primary_key} = ?", [id_valor])
        elif 'zona_origen' in config['columnas_centro']:
            cursor.execute(f"SELECT zona_origen FROM {tabla} WHERE {primary_key} = ?", [id_valor])
        else:
            return None
        
        row = cursor.fetchone()
        if row and row[0]:
            zona = str(row[0]).strip().upper()
            if zona in ['SUR', 'NORTE']:
                return zona
            # Si la zona está en español, traducir
            elif zona == 'SOUTH':
                return 'SUR'
            elif zona == 'NORTH':
                return 'NORTE'
            return zona
        return None
    except Exception as e:
        print(f"Error obteniendo zona: {str(e)}")
        return None
    finally:
        conn.close()
def eliminar_horizontal(tabla, id_valor):
    """Elimina de fragmento horizontal según zona"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    # Primero obtener la zona del registro
    zona = obtener_zona_registro(tabla, id_valor)
    
    resultados = {}
    
    # Eliminar de CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['centro'] = f"Error conexión: {error}"
    
    # Eliminar del nodo correspondiente
    if zona == 'SUR':
        conn, error = get_db_sur()
    elif zona == 'NORTE':
        conn, error = get_db_norte()
    else:
        # Si no tiene zona definida, intentamos en ambos
        resultados['nodo'] = "Zona no definida, no se elimina de nodos"
        return resultados
    
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['nodo'] = f"Eliminado en {zona}"
        except Exception as e:
            conn.rollback()
            resultados['nodo'] = f"Error en {zona}: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['nodo'] = f"Error conexión {zona}: {error}"
    
    return resultados

def eliminar_vertical(tabla, id_valor):
    """Elimina de fragmento vertical"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    resultados = {}
    
    # Eliminar de CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['centro'] = f"Error conexión: {error}"
    
    # Eliminar de SUR
    conn, error = get_db_sur()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['sur'] = True
        except Exception as e:
            conn.rollback()
            resultados['sur'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['sur'] = f"Error conexión: {error}"
    
    # Eliminar de NORTE
    conn, error = get_db_norte()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['norte'] = True
        except Exception as e:
            conn.rollback()
            resultados['norte'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['norte'] = f"Error conexión: {error}"
    
    return resultados

def eliminar_mixto(tabla, id_valor):
    """Elimina de fragmento mixto"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    # Obtener zona del registro
    zona = obtener_zona_registro(tabla, id_valor)
    
    resultados = {}
    
    # Eliminar de CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['centro'] = f"Error conexión: {error}"
    
    # Eliminar del nodo correspondiente
    if zona == 'SUR':
        conn, error = get_db_sur()
    elif zona == 'NORTE':
        conn, error = get_db_norte()
    else:
        # Si no tiene zona, intentar en ambos
        resultados['nodo'] = "Zona no definida"
        # Intentar en SUR
        conn, error = get_db_sur()
        if conn:
            cursor = conn.cursor()
            try:
                cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
                conn.commit()
                resultados['sur'] = "Eliminado en SUR"
            except:
                pass
            finally:
                conn.close()
        
        # Intentar en NORTE
        conn, error = get_db_norte()
        if conn:
            cursor = conn.cursor()
            try:
                cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
                conn.commit()
                resultados['norte'] = "Eliminado en NORTE"
            except:
                pass
            finally:
                conn.close()
        
        return resultados
    
    if conn:
        cursor = conn.cursor()
        try:
            cursor.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            conn.commit()
            resultados['nodo'] = f"Eliminado en {zona}"
        except Exception as e:
            conn.rollback()
            resultados['nodo'] = f"Error en {zona}: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['nodo'] = f"Error conexión {zona}: {error}"
    
    return resultados

# ====================
# FUNCIONES DE SINCRONIZACIÓN PARA ACTUALIZAR
# ====================
def actualizar_replica(tabla, id_valor, nuevos_datos):
    """Actualiza en todas las bases (Réplica)"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    resultados = {}
    
    # Filtrar datos según columnas
    datos_filtrados = {k: v for k, v in nuevos_datos.items() if k in config['columnas']}
    
    # Actualizar en CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_filtrados.keys()])
            valores = list(datos_filtrados.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['centro'] = f"Error conexión: {error}"
    
    # Actualizar en SUR
    conn, error = get_db_sur()
    if conn:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_filtrados.keys()])
            valores = list(datos_filtrados.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['sur'] = True
        except Exception as e:
            conn.rollback()
            resultados['sur'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['sur'] = f"Error conexión: {error}"
    
    # Actualizar en NORTE
    conn, error = get_db_norte()
    if conn:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_filtrados.keys()])
            valores = list(datos_filtrados.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['norte'] = True
        except Exception as e:
            conn.rollback()
            resultados['norte'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['norte'] = f"Error conexión: {error}"
    
    return resultados

def actualizar_horizontal(tabla, id_valor, nuevos_datos):
    """Actualiza en fragmento horizontal"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    # Verificar si cambió la ciudad (que podría cambiar el fragmento)
    if 'ciudad' in nuevos_datos:
        # Determinar nuevo fragmento
        nueva_ciudad = nuevos_datos['ciudad']
        nuevos_datos['zona'] = 'SUR' if nueva_ciudad in ['Lima', 'Arequipa', 'Cusco'] else 'NORTE'
    
    resultados = {}
    
    # Actualizar en CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in nuevos_datos.keys()])
            valores = list(nuevos_datos.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    else:
        resultados['centro'] = f"Error conexión: {error}"
    
    # Actualizar en el nodo correspondiente
    # Primero necesitamos saber en qué nodo está actualmente
    zona_actual = obtener_zona_registro(tabla, id_valor)
    
    if zona_actual:
        if zona_actual == 'SUR':
            conn, error = get_db_sur()
            nodo_nombre = 'SUR'
        elif zona_actual == 'NORTE':
            conn, error = get_db_norte()
            nodo_nombre = 'NORTE'
        else:
            resultados['nodo'] = "Zona no reconocida"
            return resultados
        
        if conn:
            cursor = conn.cursor()
            try:
                # Para nodos, no actualizamos la columna 'zona'
                datos_nodo = {k: v for k, v in nuevos_datos.items() if k != 'zona'}
                set_clause = ', '.join([f"{k} = ?" for k in datos_nodo.keys()])
                valores = list(datos_nodo.values()) + [id_valor]
                query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
                cursor.execute(query, valores)
                conn.commit()
                resultados['nodo'] = f"Actualizado en {nodo_nombre}"
            except Exception as e:
                conn.rollback()
                resultados['nodo'] = f"Error en {nodo_nombre}: {str(e)}"
            finally:
                conn.close()
        else:
            resultados['nodo'] = f"Error conexión {nodo_nombre}: {error}"
    
    return resultados

def actualizar_vertical(tabla, id_valor, nuevos_datos):
    """Actualiza en fragmento vertical"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    resultados = {}
    
    # Determinar qué columnas van a cada lugar
    datos_centro = {k: v for k, v in nuevos_datos.items() if k in config.get('columnas_centro', [])}
    datos_nodos = {k: v for k, v in nuevos_datos.items() if k in config.get('columnas_nodos', [])}
    
    # Actualizar en CENTRO
    conn, error = get_db_centro()
    if conn and datos_centro:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_centro.keys()])
            valores = list(datos_centro.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error: {str(e)}"
        finally:
            conn.close()
    elif not datos_centro:
        resultados['centro'] = "No hay datos para actualizar en CENTRO"
    
    # Actualizar en SUR
    conn, error = get_db_sur()
    if conn and datos_nodos:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_nodos.keys()])
            valores = list(datos_nodos.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['sur'] = True
        except Exception as e:
            conn.rollback()
            resultados['sur'] = f"Error: {str(e)}"
        finally:
            conn.close()
    elif not datos_nodos:
        resultados['sur'] = "No hay datos para actualizar en SUR"
    
    # Actualizar en NORTE
    conn, error = get_db_norte()
    if conn and datos_nodos:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_nodos.keys()])
            valores = list(datos_nodos.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['norte'] = True
        except Exception as e:
            conn.rollback()
            resultados['norte'] = f"Error: {str(e)}"
        finally:
            conn.close()
    elif not datos_nodos:
        resultados['norte'] = "No hay datos para actualizar en NORTE"
    
    return resultados
def actualizar_mixto(tabla, id_valor, nuevos_datos):
    """Actualiza en fragmento mixto con manejo de cambio de zona"""
    config = FRAGMENTACION_CONFIG[tabla]
    primary_key = config['primary_key']
    
    # Obtener zona ACTUAL del registro desde CENTRO
    zona_actual = obtener_zona_registro(tabla, id_valor)
    
    # Determinar nueva zona si se cambió
    nueva_zona = None
    if 'zona' in nuevos_datos:
        nueva_zona = nuevos_datos['zona'].upper()
    elif 'zona_origen' in nuevos_datos and 'zona_origen' in config['columnas_centro']:
        nueva_zona = nuevos_datos['zona_origen'].upper()
    
    # Si no hay nueva zona explícita, mantener la actual
    if not nueva_zona:
        nueva_zona = zona_actual
    
    resultados = {}
    
    # 1. Separar datos para centro y nodos según configuración
    datos_centro = {}
    datos_nodos = {}
    
    for col, valor in nuevos_datos.items():
        if col in config['columnas_centro']:
            datos_centro[col] = valor
        if col in config['columnas_nodos']:
            datos_nodos[col] = valor
    
    # Asegurar que la zona esté en datos_centro
    if nueva_zona and 'zona' in config['columnas_centro']:
        datos_centro['zona'] = nueva_zona
    elif nueva_zona and 'zona_origen' in config['columnas_centro']:
        datos_centro['zona_origen'] = nueva_zona
    
    # 2. Actualizar en CENTRO (siempre)
    conn, error = get_db_centro()
    if conn and datos_centro:
        cursor = conn.cursor()
        try:
            set_clause = ', '.join([f"{k} = ?" for k in datos_centro.keys()])
            valores = list(datos_centro.values()) + [id_valor]
            query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
            cursor.execute(query, valores)
            conn.commit()
            resultados['centro'] = True
        except Exception as e:
            conn.rollback()
            resultados['centro'] = f"Error en CENTRO: {str(e)}"
        finally:
            conn.close()
    elif not datos_centro:
        resultados['centro'] = "No hay datos para actualizar en CENTRO"
    else:
        resultados['centro'] = f"Error conexión CENTRO: {error}"
    
    # 3. Manejar la actualización en los nodos
    # Caso 1: Si cambió la zona, debemos mover el registro
    if zona_actual != nueva_zona and zona_actual in ['SUR', 'NORTE']:
        # 3a. Eliminar del nodo anterior
        if zona_actual == 'SUR':
            conn_old, error_old = get_db_sur()
        else:
            conn_old, error_old = get_db_norte()
        
        if conn_old:
            cursor_old = conn_old.cursor()
            try:
                cursor_old.execute(f"DELETE FROM {tabla} WHERE {primary_key} = ?", [id_valor])
                conn_old.commit()
                resultados['eliminado_anterior'] = f"Eliminado de {zona_actual}"
            except Exception as e:
                conn_old.rollback()
                resultados['eliminado_anterior'] = f"Error eliminando de {zona_actual}: {str(e)}"
            finally:
                conn_old.close()
        
        # 3b. Insertar en el nuevo nodo
        if nueva_zona == 'SUR':
            conn_new, error_new = get_db_sur()
            nodo_nombre = 'SUR'
        elif nueva_zona == 'NORTE':
            conn_new, error_new = get_db_norte()
            nodo_nombre = 'NORTE'
        else:
            resultados['nuevo_nodo'] = "Nueva zona no válida"
            return resultados
        
        if conn_new and datos_nodos:
            cursor_new = conn_new.cursor()
            try:
                # Preparar datos para insertar (con ID)
                datos_para_insertar = datos_nodos.copy()
                datos_para_insertar[primary_key] = id_valor
                
                placeholders = ', '.join(['?' for _ in datos_para_insertar])
                columns = ', '.join(datos_para_insertar.keys())
                query = f"INSERT INTO {tabla} ({columns}) VALUES ({placeholders})"
                cursor_new.execute(query, list(datos_para_insertar.values()))
                conn_new.commit()
                resultados['insertado_nuevo'] = f"Insertado en {nodo_nombre}"
            except Exception as e:
                conn_new.rollback()
                resultados['insertado_nuevo'] = f"Error insertando en {nodo_nombre}: {str(e)}"
            finally:
                conn_new.close()
        elif not datos_nodos:
            resultados['insertado_nuevo'] = f"No hay datos para insertar en {nodo_nombre}"
        else:
            resultados['insertado_nuevo'] = f"Error conexión {nodo_nombre}: {error_new}"
    
    # Caso 2: Si no cambió la zona, solo actualizar en el nodo actual
    elif zona_actual in ['SUR', 'NORTE'] and zona_actual == nueva_zona:
        if zona_actual == 'SUR':
            conn, error = get_db_sur()
            nodo_nombre = 'SUR'
        else:
            conn, error = get_db_norte()
            nodo_nombre = 'NORTE'
        
        if conn and datos_nodos:
            cursor = conn.cursor()
            try:
                set_clause = ', '.join([f"{k} = ?" for k in datos_nodos.keys()])
                valores = list(datos_nodos.values()) + [id_valor]
                query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
                cursor.execute(query, valores)
                conn.commit()
                resultados['actualizado_nodo'] = f"Actualizado en {nodo_nombre}"
            except Exception as e:
                conn.rollback()
                resultados['actualizado_nodo'] = f"Error en {nodo_nombre}: {str(e)}"
            finally:
                conn.close()
        elif not datos_nodos:
            resultados['actualizado_nodo'] = f"No hay datos para actualizar en {nodo_nombre}"
        else:
            resultados['actualizado_nodo'] = f"Error conexión {nodo_nombre}: {error}"
    
    # Caso 3: Si no había zona definida, intentar en ambos nodos
    else:
        resultados['nodos'] = "Zona no definida previamente, intentando en todos los nodos"
        
        # Intentar en SUR
        conn_sur, error_sur = get_db_sur()
        if conn_sur and datos_nodos:
            cursor_sur = conn_sur.cursor()
            try:
                # Primero verificar si existe
                cursor_sur.execute(f"SELECT 1 FROM {tabla} WHERE {primary_key} = ?", [id_valor])
                if cursor_sur.fetchone():
                    # Actualizar si existe
                    set_clause = ', '.join([f"{k} = ?" for k in datos_nodos.keys()])
                    valores = list(datos_nodos.values()) + [id_valor]
                    query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
                    cursor_sur.execute(query, valores)
                else:
                    # Insertar si no existe
                    datos_para_insertar = datos_nodos.copy()
                    datos_para_insertar[primary_key] = id_valor
                    placeholders = ', '.join(['?' for _ in datos_para_insertar])
                    columns = ', '.join(datos_para_insertar.keys())
                    query = f"INSERT INTO {tabla} ({columns}) VALUES ({placeholders})"
                    cursor_sur.execute(query, list(datos_para_insertar.values()))
                
                conn_sur.commit()
                resultados['sur'] = "Actualizado/Insertado en SUR"
            except Exception as e:
                conn_sur.rollback()
                resultados['sur'] = f"Error en SUR: {str(e)}"
            finally:
                conn_sur.close()
        
        # Intentar en NORTE
        conn_norte, error_norte = get_db_norte()
        if conn_norte and datos_nodos:
            cursor_norte = conn_norte.cursor()
            try:
                # Primero verificar si existe
                cursor_norte.execute(f"SELECT 1 FROM {tabla} WHERE {primary_key} = ?", [id_valor])
                if cursor_norte.fetchone():
                    # Actualizar si existe
                    set_clause = ', '.join([f"{k} = ?" for k in datos_nodos.keys()])
                    valores = list(datos_nodos.values()) + [id_valor]
                    query = f"UPDATE {tabla} SET {set_clause} WHERE {primary_key} = ?"
                    cursor_norte.execute(query, valores)
                else:
                    # Insertar si no existe
                    datos_para_insertar = datos_nodos.copy()
                    datos_para_insertar[primary_key] = id_valor
                    placeholders = ', '.join(['?' for _ in datos_para_insertar])
                    columns = ', '.join(datos_para_insertar.keys())
                    query = f"INSERT INTO {tabla} ({columns}) VALUES ({placeholders})"
                    cursor_norte.execute(query, list(datos_para_insertar.values()))
                
                conn_norte.commit()
                resultados['norte'] = "Actualizado/Insertado en NORTE"
            except Exception as e:
                conn_norte.rollback()
                resultados['norte'] = f"Error en NORTE: {str(e)}"
            finally:
                conn_norte.close()
    
    return resultados

# ====================
# FUNCIONES PARA OBTENER TABLAS (Mismo que antes)
# ====================
def get_tablas_bd(tipo_bd, conn):
    tablas = []
    cursor = conn.cursor()
    
    try:
        if tipo_bd == "sur":  # MariaDB
            cursor.execute("SHOW TABLES")
            tablas = [row[0] for row in cursor.fetchall()]
        else:  # SQL Server
            cursor.execute("""
                SELECT TABLE_NAME 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_TYPE = 'BASE TABLE'
                ORDER BY TABLE_NAME
            """)
            tablas = [row[0] for row in cursor.fetchall()]
    except Exception as e:
        print(f"Error obteniendo tablas: {str(e)}")
    
    return tablas

def get_contenido_tabla(tipo_bd, conn, nombre_tabla, limit=100):
    cursor = conn.cursor()
    datos = []
    columnas = []
    
    try:
        # Obtener nombres de columnas
        if tipo_bd == "sur":  # MariaDB
            cursor.execute(f"DESCRIBE {nombre_tabla}")
            columnas_info = cursor.fetchall()
            columnas = [col[0] for col in columnas_info]
        else:  # SQL Server
            cursor.execute(f"""
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = '{nombre_tabla}' 
                ORDER BY ORDINAL_POSITION
            """)
            columnas = [row[0] for row in cursor.fetchall()]
        
        # Obtener datos
        if tipo_bd != "sur":
            cursor.execute(f"SELECT TOP {limit} * FROM {nombre_tabla}")
        else:
            cursor.execute(f"SELECT * FROM {nombre_tabla} LIMIT {limit}")
        
        datos = cursor.fetchall()
        
    except Exception as e:
        print(f"Error obteniendo contenido de {nombre_tabla}: {str(e)}")
    
    return columnas, datos
def determinar_fragmento(tabla, datos):
    """Determina si el dato va al fragmento SUR o NORTE"""
    config = FRAGMENTACION_CONFIG.get(tabla)
    
    if not config:
        return 'centro'
    
    if config['tipo'] == 'horizontal':
        ciudad = datos.get('ciudad', '')
        
        # Debug: Mostrar qué ciudad se está evaluando
        print(f"DEBUG - Tabla: {tabla}, Ciudad recibida: '{ciudad}'")
        
        # Ciudades del SUR - manejar diferentes formatos
        ciudades_sur = ['Lima', 'Arequipa', 'Cusco', 'lima', 'arequipa', 'cusco', 'LIMA', 'AREQUIPA', 'CUSCO']
        # Ciudades del NORTE
        ciudades_norte = ['Trujillo', 'Chiclayo', 'Piura', 'trujillo', 'chiclayo', 'piura', 'TRUJILLO', 'CHICLAYO', 'PIURA']
        
        # Normalizar la ciudad (quitar espacios, convertir a título)
        ciudad_normalizada = ciudad.strip().title() if ciudad else ""
        
        if ciudad_normalizada in ['Lima', 'Arequipa', 'Cusco']:
            print(f"DEBUG - Ciudad {ciudad_normalizada} -> SUR")
            return 'sur'
        elif ciudad_normalizada in ['Trujillo', 'Chiclayo', 'Piura']:
            print(f"DEBUG - Ciudad {ciudad_normalizada} -> NORTE")
            return 'norte'
        else:
            print(f"DEBUG - Ciudad {ciudad_normalizada} no reconocida -> CENTRO")
            return 'centro'
    
    elif config['tipo'] == 'mixto':
        # Para Pago_Mixto, usar zona si está presente
        if 'zona' in datos:
            zona = datos['zona'].upper()
            if zona == 'SUR':
                return 'sur'
            elif zona == 'NORTE':
                return 'norte'
        
        # Si no hay zona, usar algún criterio por defecto
        if 'id_pago' in datos:
            # Si el ID es par -> SUR, impar -> NORTE (ejemplo)
            return 'sur' if int(datos['id_pago']) % 2 == 0 else 'norte'
        else:
            return 'centro'
    
    return 'centro'
# ====================
# FUNCIONES PARA INSERTAR (Mismo que antes)
# ====================
def insertar_replica(tabla, datos):
    config = FRAGMENTACION_CONFIG[tabla]
    columnas = config['columnas']
    datos_filtrados = {k: v for k, v in datos.items() if k in columnas}
    
    # Insertar en CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_filtrados])
            query = f"INSERT INTO {tabla} ({', '.join(datos_filtrados.keys())}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_filtrados.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
            return False, f"Error en CENTRO: {str(e)}"
        finally:
            conn.close()
    
    # Insertar en SUR
    conn, error = get_db_sur()
    if conn:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_filtrados])
            query = f"INSERT INTO {tabla} ({', '.join(datos_filtrados.keys())}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_filtrados.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
        finally:
            conn.close()
    
    # Insertar en NORTE
    conn, error = get_db_norte()
    if conn:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_filtrados])
            query = f"INSERT INTO {tabla} ({', '.join(datos_filtrados.keys())}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_filtrados.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
        finally:
            conn.close()
    
    return True, "Réplica insertada en todas las bases"


def insertar_horizontal(tabla, datos):
    fragmento = determinar_fragmento(tabla, datos)
    
    # DEBUG
    print(f"DEBUG insertar_horizontal - Tabla: {tabla}, Fragmento determinado: {fragmento}")
    print(f"DEBUG insertar_horizontal - Datos recibidos: {datos}")
    
    config = FRAGMENTACION_CONFIG[tabla]
    datos['zona'] = fragmento.upper()
    
    # Insertar en CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos])
            query = f"INSERT INTO {tabla} ({', '.join(datos.keys())}) VALUES ({placeholders})"
            print(f"DEBUG - Query CENTRO: {query}")
            print(f"DEBUG - Valores CENTRO: {list(datos.values())}")
            cursor.execute(query, list(datos.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
            return False, f"Error en CENTRO: {str(e)}"
        finally:
            conn.close()
    
    # Insertar en fragmento
    if fragmento == 'sur':
        conn, error = get_db_sur()
    elif fragmento == 'norte':
        conn, error = get_db_norte()
    else:
        print(f"DEBUG - Fragmento no reconocido: {fragmento}. Insertando solo en CENTRO.")
        return True, f"Insertado solo en CENTRO (fragmento: {fragmento})"
    
    if conn:
        cursor = conn.cursor()
        try:
            datos_nodo = {k: v for k, v in datos.items() if k != 'zona'}
            placeholders = ', '.join(['?' for _ in datos_nodo])
            query = f"INSERT INTO {tabla} ({', '.join(datos_nodo.keys())}) VALUES ({placeholders})"
            print(f"DEBUG - Query {fragmento.upper()}: {query}")
            print(f"DEBUG - Valores {fragmento.upper()}: {list(datos_nodo.values())}")
            cursor.execute(query, list(datos_nodo.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
            return False, f"Error en {fragmento.upper()}: {str(e)}"
        finally:
            conn.close()
    
    return True, f"Insertado en CENTRO y {fragmento.upper()}"
def insertar_vertical(tabla, datos):
    config = FRAGMENTACION_CONFIG[tabla]
    datos_centro = {k: v for k, v in datos.items() if k in config.get('columnas_centro', [])}
    datos_nodos = {k: v for k, v in datos.items() if k in config.get('columnas_nodos', [])}
    
    # Insertar en CENTRO
    conn, error = get_db_centro()
    if conn and datos_centro:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_centro])
            query = f"INSERT INTO {tabla} ({', '.join(datos_centro.keys())}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_centro.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
            return False, f"Error en CENTRO: {str(e)}"
        finally:
            conn.close()
    
    # Insertar en SUR
    conn, error = get_db_sur()
    if conn and datos_nodos:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_nodos])
            query = f"INSERT INTO {tabla} ({', '.join(datos_nodos.keys())}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_nodos.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
        finally:
            conn.close()
    
    # Insertar en NORTE
    conn, error = get_db_norte()
    if conn and datos_nodos:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_nodos])
            query = f"INSERT INTO {tabla} ({', '.join(datos_nodos.keys())}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_nodos.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
        finally:
            conn.close()
    
    return True, "Fragmento vertical insertado"
def insertar_mixto(tabla, datos):
    config = FRAGMENTACION_CONFIG[tabla]
    
    # Determinar fragmento
    fragmento = determinar_fragmento(tabla, datos)
    
    # PREPARAR DATOS PARA CENTRO (SQL Server)
    datos_centro = {}
    for col in config['columnas_centro']:
        if col in datos:
            datos_centro[col] = datos[col]
    
    # Asegurar que 'zona' esté en datos_centro si es parte de columnas_centro
    if 'zona' in config['columnas_centro'] and 'zona' not in datos_centro:
        datos_centro['zona'] = fragmento.upper()
    
    # PREPARAR DATOS PARA NODOS (SUR/NORTE) - sin columna 'zona'
    datos_nodos = {}
    for col in config['columnas_nodos']:
        if col in datos:
            datos_nodos[col] = datos[col]
    
    # INSERTAR EN CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_centro])
            columns = ', '.join(datos_centro.keys())
            query = f"INSERT INTO {tabla} ({columns}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_centro.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
            return False, f"Error en CENTRO: {str(e)}"
        finally:
            conn.close()
    else:
        return False, f"Error conexión CENTRO: {error}"
    
    # INSERTAR EN EL FRAGMENTO CORRESPONDIENTE
    if fragmento == 'sur':
        conn, error = get_db_sur()
        nodo_nombre = 'SUR'
    elif fragmento == 'norte':
        conn, error = get_db_norte()
        nodo_nombre = 'NORTE'
    else:
        # Si no es sur ni norte, no insertar en nodos
        return True, f"Insertado solo en CENTRO (fragmento: {fragmento})"
    
    if conn:
        cursor = conn.cursor()
        try:
            placeholders = ', '.join(['?' for _ in datos_nodos])
            columns = ', '.join(datos_nodos.keys())
            query = f"INSERT INTO {tabla} ({columns}) VALUES ({placeholders})"
            cursor.execute(query, list(datos_nodos.values()))
            conn.commit()
        except Exception as e:
            conn.rollback()
            return False, f"Error en {nodo_nombre}: {str(e)}"
        finally:
            conn.close()
    else:
        return False, f"Error conexión {nodo_nombre}: {error}"
    
    return True, f"Insertado en CENTRO y {nodo_nombre}"
#-------------------------------
# NUEVAS RUTAS PARA EDITAR/ACTUALIZAR
# ====================
@app.route('/editar/<tabla>/<int:id_valor>', methods=['GET', 'POST'])
def editar_registro(tabla, id_valor):
    config = FRAGMENTACION_CONFIG.get(tabla, {})
    primary_key = config.get('primary_key', 'id')
    
    if request.method == 'GET':
        # Obtener el registro actual desde CENTRO
        conn, error = get_db_centro()
        if error:
            flash(f'Error de conexión: {error}', 'danger')
            return redirect(url_for('ver_tabla', tipo_bd='centro', nombre_tabla=tabla))
        
        cursor = conn.cursor()
        try:
            cursor.execute(f"SELECT * FROM {tabla} WHERE {primary_key} = ?", [id_valor])
            columnas = [desc[0] for desc in cursor.description]
            registro = cursor.fetchone()
            
            if not registro:
                flash('Registro no encontrado', 'danger')
                return redirect(url_for('ver_tabla', tipo_bd='centro', nombre_tabla=tabla))
            
            # Convertir a diccionario
            datos_actuales = dict(zip(columnas, registro))
            
            return render_template('editar.html', 
                                 tabla=tabla,
                                 id_valor=id_valor,
                                 datos=datos_actuales,
                                 config=config)
            
        except Exception as e:
            flash(f'Error al obtener registro: {str(e)}', 'danger')
            return redirect(url_for('ver_tabla', tipo_bd='centro', nombre_tabla=tabla))
        finally:
            conn.close()
    
    # POST: Procesar actualización
    nuevos_datos = request.form.to_dict()
    
    # Obtener tipo de fragmentación
    tipo_fragmentacion = config.get('tipo')
    
    if tipo_fragmentacion == 'replica':
        resultados = actualizar_replica(tabla, id_valor, nuevos_datos)
    elif tipo_fragmentacion == 'horizontal':
        resultados = actualizar_horizontal(tabla, id_valor, nuevos_datos)
    elif tipo_fragmentacion == 'vertical':
        resultados = actualizar_vertical(tabla, id_valor, nuevos_datos)
    elif tipo_fragmentacion == 'mixto':
        resultados = actualizar_mixto(tabla, id_valor, nuevos_datos)
    else:
        flash(f'Tipo de fragmentación desconocido para {tabla}', 'danger')
        return redirect(url_for('editar_registro', tabla=tabla, id_valor=id_valor))
    
    # Verificar resultados
    if 'centro' in resultados and resultados['centro'] is True:
        flash('Registro actualizado exitosamente', 'success')
        return redirect(url_for('ver_tabla', tipo_bd='centro', nombre_tabla=tabla))
    else:
        mensaje_error = f"Error al actualizar: {resultados}"
        flash(mensaje_error, 'danger')
        return redirect(url_for('editar_registro', tabla=tabla, id_valor=id_valor))

# ====================
# RUTA PARA ELIMINAR (ACTUALIZADA)
# ====================
@app.route('/eliminar/<tabla>/<int:id_valor>', methods=['POST'])
def eliminar_registro(tabla, id_valor):
    config = FRAGMENTACION_CONFIG.get(tabla, {})
    tipo_fragmentacion = config.get('tipo')
    
    if tipo_fragmentacion == 'replica':
        resultados = eliminar_replica(tabla, id_valor)
    elif tipo_fragmentacion == 'horizontal':
        resultados = eliminar_horizontal(tabla, id_valor)
    elif tipo_fragmentacion == 'vertical':
        resultados = eliminar_vertical(tabla, id_valor)
    elif tipo_fragmentacion == 'mixto':
        resultados = eliminar_mixto(tabla, id_valor)
    else:
        return jsonify({'success': False, 'message': 'Tipo de fragmentación desconocido'})
    
    # Verificar si se eliminó correctamente del CENTRO
    if 'centro' in resultados and resultados['centro'] is True:
        mensaje = f'Registro eliminado exitosamente. {resultados}'
        flash(mensaje, 'success')
        return jsonify({'success': True, 'message': mensaje})
    else:
        mensaje_error = f'Error al eliminar: {resultados}'
        flash(mensaje_error, 'danger')
        return jsonify({'success': False, 'message': mensaje_error})

# ====================
# RUTAS PRINCIPALES (Mismo que antes)
# ====================
@app.route('/')
def index():
    bds_info = []
    
    # Base SUR
    conn_sur, error_sur = get_db_sur()
    if conn_sur:
        tablas_sur = get_tablas_bd("sur", conn_sur)
        bds_info.append({
            'nombre': 'SUR (MariaDB)',
            'tipo': 'sur',
            'tablas': tablas_sur,
            'error': None,
            'count_tablas': len(tablas_sur)
        })
        conn_sur.close()
    else:
        bds_info.append({
            'nombre': 'SUR (MariaDB)',
            'tipo': 'sur',
            'tablas': [],
            'error': error_sur,
            'count_tablas': 0
        })
    
    # Base CENTRO
    conn_centro, error_centro = get_db_centro()
    if conn_centro:
        tablas_centro = get_tablas_bd("centro", conn_centro)
        bds_info.append({
            'nombre': 'CENTRO (SQL Server)',
            'tipo': 'centro',
            'tablas': tablas_centro,
            'error': None,
            'count_tablas': len(tablas_centro)
        })
        conn_centro.close()
    else:
        bds_info.append({
            'nombre': 'CENTRO (SQL Server)',
            'tipo': 'centro',
            'tablas': [],
            'error': error_centro,
            'count_tablas': 0
        })
    
    # Base NORTE
    conn_norte, error_norte = get_db_norte()
    if conn_norte:
        tablas_norte = get_tablas_bd("norte", conn_norte)
        bds_info.append({
            'nombre': 'NORTE (SQL Server)',
            'tipo': 'norte',
            'tablas': tablas_norte,
            'error': None,
            'count_tablas': len(tablas_norte)
        })
        conn_norte.close()
    else:
        bds_info.append({
            'nombre': 'NORTE (SQL Server)',
            'tipo': 'norte',
            'tablas': [],
            'error': error_norte,
            'count_tablas': 0
        })
    
    return render_template('index.html', bds_info=bds_info)

@app.route('/tabla/<tipo_bd>/<nombre_tabla>')
def ver_tabla(tipo_bd, nombre_tabla):
    conn = None
    if tipo_bd == 'sur':
        conn, error = get_db_sur()
    elif tipo_bd == 'centro':
        conn, error = get_db_centro()
    elif tipo_bd == 'norte':
        conn, error = get_db_norte()
    else:
        return "Tipo de BD no válido", 400
    
    if error:
        return f"Error de conexión: {error}", 500
    
    columnas, datos = get_contenido_tabla(tipo_bd, conn, nombre_tabla)
    
    nombres_bd = {
        'sur': 'SUR (MariaDB)',
        'centro': 'CENTRO (SQL Server)',
        'norte': 'NORTE (SQL Server)'
    }
    
    tipo_fragmentacion = FRAGMENTACION_CONFIG.get(nombre_tabla, {}).get('tipo', 'desconocido')
    
    conn.close()
    
    return render_template('tabla.html', 
                         tipo_bd=tipo_bd,
                         nombre_bd=nombres_bd[tipo_bd],
                         nombre_tabla=nombre_tabla,
                         columnas=columnas,
                         datos=datos,
                         tipo_fragmentacion=tipo_fragmentacion)

@app.route('/insertar/<tabla>', methods=['GET', 'POST'])
def insertar(tabla):
    if request.method == 'GET':
        config = FRAGMENTACION_CONFIG.get(tabla, {})
        return render_template('insertar.html', 
                             tabla=tabla, 
                             config=config,
                             tipos_fragmentacion=['replica', 'horizontal', 'vertical', 'mixto'])
    
    datos = request.form.to_dict()
    tipo_fragmentacion = FRAGMENTACION_CONFIG.get(tabla, {}).get('tipo')
    
    if tipo_fragmentacion == 'replica':
        success, mensaje = insertar_replica(tabla, datos)
    elif tipo_fragmentacion == 'horizontal':
        success, mensaje = insertar_horizontal(tabla, datos)
    elif tipo_fragmentacion == 'vertical':
        success, mensaje = insertar_vertical(tabla, datos)
    elif tipo_fragmentacion == 'mixto':
        success, mensaje = insertar_mixto(tabla, datos)
    else:
        flash(f'Tipo de fragmentación desconocido para {tabla}', 'danger')
        return redirect(url_for('insertar', tabla=tabla))
    
    if success:
        flash(mensaje, 'success')
        return redirect(url_for('ver_tabla', tipo_bd='centro', nombre_tabla=tabla))
    else:
        flash(f'Error: {mensaje}', 'danger')
        return redirect(url_for('insertar', tabla=tabla))

@app.route('/estado')
def estado():
    estado_info = {}
    
    for tabla in FRAGMENTACION_CONFIG.keys():
        estado_info[tabla] = {}
        
        # Contar en CENTRO
        conn, error = get_db_centro()
        if conn:
            cursor = conn.cursor()
            try:
                cursor.execute(f"SELECT COUNT(*) FROM {tabla}")
                estado_info[tabla]['centro'] = cursor.fetchone()[0]
            except:
                estado_info[tabla]['centro'] = 'Error'
            finally:
                conn.close()
        
        # Contar en SUR
        conn, error = get_db_sur()
        if conn:
            cursor = conn.cursor()
            try:
                cursor.execute(f"SELECT COUNT(*) FROM {tabla}")
                estado_info[tabla]['sur'] = cursor.fetchone()[0]
            except:
                estado_info[tabla]['sur'] = 'Error'
            finally:
                conn.close()
        
        # Contar en NORTE
        conn, error = get_db_norte()
        if conn:
            cursor = conn.cursor()
            try:
                cursor.execute(f"SELECT COUNT(*) FROM {tabla}")
                estado_info[tabla]['norte'] = cursor.fetchone()[0]
            except:
                estado_info[tabla]['norte'] = 'Error'
            finally:
                conn.close()
    
    return render_template('estado.html', estado_info=estado_info, config=FRAGMENTACION_CONFIG)

@app.route('/simple_debug/<tabla>')
def simple_debug(tabla):
    conn, error = get_db_centro()
    if error:
        return f"Error conexión: {error}"
    
    cursor = conn.cursor()
    
    try:
        cursor.execute(f"""
            SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = '{tabla}'
            ORDER BY ORDINAL_POSITION
        """)
        estructura = cursor.fetchall()
        
        resultado = f"<h1>Estructura de {tabla}</h1><ul>"
        for col in estructura:
            resultado += f"<li>{col[0]} - {col[1]} - NULL: {col[2]}</li>"
        resultado += "</ul>"
        
        conn.close()
        return resultado
    except Exception as e:
        return f"Error: {str(e)}"




# ====================
# FUNCIONES DE CONSULTA ESPECÍFICAS (CORREGIDAS)
# ====================

def convertir_filas_a_lista(filas):
    """Convierte filas de la base de datos a listas serializables"""
    resultado = []
    for fila in filas:
        # Convertir cada fila a lista
        resultado.append([str(item) if item is not None else None for item in fila])
    return resultado

def consulta_cementerios_por_zona():
    """Consulta 1: ¿Qué zonas tienen más cementerios?"""
    resultados = {}
    
    # Consultar en CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            # Contar cementerios por zona
            cursor.execute("""
                SELECT zona, COUNT(*) as cantidad
                FROM Cementerio 
                GROUP BY zona
                ORDER BY cantidad DESC
            """)
            cementerios_por_zona_raw = cursor.fetchall()
            cementerios_por_zona = convertir_filas_a_lista(cementerios_por_zona_raw)
            resultados['cementerios_por_zona'] = cementerios_por_zona
            
            # Obtener lista completa de cementerios
            cursor.execute("SELECT id_cementerio, nombre, zona, tipo FROM Cementerio ORDER BY zona, nombre")
            todos_cementerios_raw = cursor.fetchall()
            todos_cementerios = convertir_filas_a_lista(todos_cementerios_raw)
            resultados['todos_cementerios'] = todos_cementerios
            
            # Zona con más cementerios
            if cementerios_por_zona:
                zona_mas_cementerios = cementerios_por_zona[0][0]
                cantidad_mas_cementerios = int(cementerios_por_zona[0][1]) if cementerios_por_zona[0][1] else 0
                resultados['zona_mas_cementerios'] = {
                    'zona': str(zona_mas_cementerios),
                    'cantidad': cantidad_mas_cementerios
                }
                
        except Exception as e:
            resultados['error'] = f"Error en consulta cementerios: {str(e)}"
        finally:
            conn.close()
    
    return resultados

def consulta_difuntos_por_cementerio():
    """Consulta 2: ¿Qué cementerio tiene más difuntos?"""
    resultados = {}
    
    # Consultar en CENTRO para obtener información de nichos
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            # Primero, contar difuntos por nicho (usando Difunto_InfoFallecimiento)
            cursor.execute("""
                SELECT ni.id_cementerio, c.nombre as nombre_cementerio, c.zona,
                       COUNT(df.id_difunto) as cantidad_difuntos
                FROM Difunto_InfoFallecimiento df
                JOIN Nicho ni ON df.id_nicho = ni.id_nicho
                JOIN Cementerio c ON ni.id_cementerio = c.id_cementerio
                GROUP BY ni.id_cementerio, c.nombre, c.zona
                ORDER BY cantidad_difuntos DESC
            """)
            
            difuntos_por_cementerio_raw = cursor.fetchall()
            difuntos_por_cementerio = convertir_filas_a_lista(difuntos_por_cementerio_raw)
            resultados['difuntos_por_cementerio'] = difuntos_por_cementerio
            
            # Cementerio con más difuntos
            if difuntos_por_cementerio:
                cementerio_mas_difuntos = difuntos_por_cementerio[0]
                resultados['cementerio_mas_difuntos'] = {
                    'id_cementerio': str(cementerio_mas_difuntos[0]),
                    'nombre': str(cementerio_mas_difuntos[1]),
                    'zona': str(cementerio_mas_difuntos[2]),
                    'cantidad_difuntos': int(cementerio_mas_difuntos[3]) if cementerio_mas_difuntos[3] else 0
                }
            
            # Contar difuntos totales por zona
            cursor.execute("""
                SELECT c.zona, COUNT(df.id_difunto) as total_difuntos
                FROM Difunto_InfoFallecimiento df
                JOIN Nicho ni ON df.id_nicho = ni.id_nicho
                JOIN Cementerio c ON ni.id_cementerio = c.id_cementerio
                GROUP BY c.zona
                ORDER BY total_difuntos DESC
            """)
            
            difuntos_por_zona_raw = cursor.fetchall()
            difuntos_por_zona = convertir_filas_a_lista(difuntos_por_zona_raw)
            resultados['difuntos_por_zona'] = difuntos_por_zona
            
            # Zona con más difuntos
            if difuntos_por_zona:
                zona_mas_difuntos = difuntos_por_zona[0]
                resultados['zona_mas_difuntos'] = {
                    'zona': str(zona_mas_difuntos[0]),
                    'total_difuntos': int(zona_mas_difuntos[1]) if zona_mas_difuntos[1] else 0
                }
                
        except Exception as e:
            resultados['error'] = f"Error en consulta difuntos: {str(e)}"
        finally:
            conn.close()
    
    return resultados

def consulta_difuntos_por_zona():
    """Consulta 3: ¿Qué zona tiene más difuntos?"""
    resultados = {}
    
    # Consultar información de difuntos por zona desde CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            # Contar difuntos por zona usando Difunto_InfoPersonal
            cursor.execute("""
                SELECT zona, COUNT(*) as total_difuntos
                FROM Difunto_InfoPersonal
                WHERE zona IS NOT NULL AND zona != ''
                GROUP BY zona
                ORDER BY total_difuntos DESC
            """)
            
            difuntos_por_zona_raw = cursor.fetchall()
            difuntos_por_zona = convertir_filas_a_lista(difuntos_por_zona_raw)
            resultados['difuntos_por_zona_detalle'] = difuntos_por_zona
            
            # Obtener detalles de difuntos por zona (limitar resultados)
            cursor.execute("""
                SELECT TOP 50 dip.zona, dip.nombre, dip.apellido, dip.fecha_fallecimiento,
                       dif.causa_fallecimiento, c.nombre as nombre_cementerio
                FROM Difunto_InfoPersonal dip
                LEFT JOIN Difunto_InfoFallecimiento dif ON dip.id_difunto = dif.id_difunto
                LEFT JOIN Nicho n ON dif.id_nicho = n.id_nicho
                LEFT JOIN Cementerio c ON n.id_cementerio = c.id_cementerio
                WHERE dip.zona IS NOT NULL AND dip.zona != ''
                ORDER BY dip.zona, dip.fecha_fallecimiento DESC
            """)
            
            detalles_difuntos_raw = cursor.fetchall()
            detalles_difuntos = convertir_filas_a_lista(detalles_difuntos_raw)
            resultados['detalles_difuntos'] = detalles_difuntos
            
            # Calcular totales
            total_difuntos = sum(int(row[1]) if row[1] else 0 for row in difuntos_por_zona)
            resultados['total_difuntos_sistema'] = total_difuntos
            
            # Zona con más difuntos
            if difuntos_por_zona:
                zona_top = difuntos_por_zona[0]
                zona_top_cantidad = int(zona_top[1]) if zona_top[1] else 0
                porcentaje = round((zona_top_cantidad / total_difuntos * 100), 2) if total_difuntos > 0 else 0
                resultados['zona_top_difuntos'] = {
                    'zona': str(zona_top[0]),
                    'cantidad': zona_top_cantidad,
                    'porcentaje': porcentaje
                }
                
        except Exception as e:
            resultados['error'] = f"Error en consulta zona difuntos: {str(e)}"
        finally:
            conn.close()
    
    return resultados

def consulta_traslados_por_zona():
    """Consulta 4: ¿Cuántos traslados se realizaron en cada zona?"""
    resultados = {}
    
    # Consultar traslados desde CENTRO
    conn, error = get_db_centro()
    if conn:
        cursor = conn.cursor()
        try:
            # Contar traslados por zona de origen
            cursor.execute("""
                SELECT zona_origen, COUNT(*) as traslados_salida
                FROM Traslado_Mixto
                WHERE zona_origen IS NOT NULL AND zona_origen != ''
                GROUP BY zona_origen
                ORDER BY traslados_salida DESC
            """)
            
            traslados_origen_raw = cursor.fetchall()
            traslados_origen = convertir_filas_a_lista(traslados_origen_raw)
            resultados['traslados_por_origen'] = traslados_origen
            
            # Contar traslados por zona de destino
            cursor.execute("""
                SELECT zona_destino, COUNT(*) as traslados_llegada
                FROM Traslado_Mixto
                WHERE zona_destino IS NOT NULL AND zona_destino != ''
                GROUP BY zona_destino
                ORDER BY traslados_llegada DESC
            """)
            
            traslados_destino_raw = cursor.fetchall()
            traslados_destino = convertir_filas_a_lista(traslados_destino_raw)
            resultados['traslados_por_destino'] = traslados_destino
            
            # Traslados entre zonas específicas (SUR-NORTE, NORTE-SUR)
            # Usamos STRING_AGG en lugar de GROUP_CONCAT (SQL Server)
            cursor.execute("""
                SELECT 
                    zona_origen,
                    zona_destino,
                    COUNT(*) as cantidad
                FROM Traslado_Mixto
                WHERE zona_origen IN ('SUR', 'NORTE') 
                  AND zona_destino IN ('SUR', 'NORTE')
                GROUP BY zona_origen, zona_destino
                ORDER BY cantidad DESC
            """)
            
            traslados_entre_zonas_raw = cursor.fetchall()
            traslados_entre_zonas = convertir_filas_a_lista(traslados_entre_zonas_raw)
            resultados['traslados_entre_zonas'] = traslados_entre_zonas
            
            # Totales generales
            total_traslados = sum(int(row[2]) if row[2] else 0 for row in traslados_entre_zonas)
            resultados['total_traslados'] = total_traslados
            
            # Detalles de traslados recientes
            cursor.execute("""
                SELECT TOP 20 id_traslado, fecha, motivo, 
                       cementerio_origen, cementerio_destino,
                       zona_origen, zona_destino
                FROM Traslado_Mixto
                ORDER BY fecha DESC
            """)
            
            traslados_recientes_raw = cursor.fetchall()
            traslados_recientes = convertir_filas_a_lista(traslados_recientes_raw)
            resultados['traslados_recientes'] = traslados_recientes
            
            # Estadísticas por mes (SQL Server usa STRING_AGG)
            cursor.execute("""
                SELECT 
                    FORMAT(fecha, 'yyyy-MM') as mes,
                    COUNT(*) as cantidad
                FROM Traslado_Mixto
                GROUP BY FORMAT(fecha, 'yyyy-MM')
                ORDER BY mes DESC
            """)
            
            traslados_por_mes_raw = cursor.fetchall()
            traslados_por_mes = convertir_filas_a_lista(traslados_por_mes_raw)
            resultados['traslados_por_mes'] = traslados_por_mes
                
        except Exception as e:
            resultados['error'] = f"Error en consulta traslados: {str(e)}"
        finally:
            conn.close()
    
    return resultados

# ====================
# RUTAS PARA LAS CONSULTAS (CORREGIDAS)
# ====================

@app.route('/consultas')
def consultas():
    """Página principal de consultas"""
    return render_template('consultas.html')

@app.route('/api/consulta_cementerios')
def api_consulta_cementerios():
    """API: Consulta cementerios por zona"""
    try:
        resultados = consulta_cementerios_por_zona()
        return jsonify({
            'success': True,
            'data': resultados
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/api/consulta_difuntos_cementerio')
def api_consulta_difuntos_cementerio():
    """API: Consulta difuntos por cementerio"""
    try:
        resultados = consulta_difuntos_por_cementerio()
        return jsonify({
            'success': True,
            'data': resultados
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/api/consulta_difuntos_zona')
def api_consulta_difuntos_zona():
    """API: Consulta difuntos por zona"""
    try:
        resultados = consulta_difuntos_por_zona()
        return jsonify({
            'success': True,
            'data': resultados
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/api/consulta_traslados_zona')
def api_consulta_traslados_zona():
    """API: Consulta traslados por zona"""
    try:
        resultados = consulta_traslados_por_zona()
        return jsonify({
            'success': True,
            'data': resultados
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

if __name__ == '__main__':
    app.run(debug=True, port=5000)