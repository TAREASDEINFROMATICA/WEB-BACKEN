from flask import Flask, render_template, request
import mariadb # type: ignore
import pyodbc
from collections import defaultdict


app = Flask(__name__)

# ====================
# CONEXIÓN BD SUR (MariaDB)
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
        return None, f"Error MariaDB: {str(e)}"

# ====================
# CONEXIÓN BD CENTRO (SQL Server)
# ====================
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

# ====================
# CONEXIÓN BD NORTE (SQL Server)
# ====================
def get_db_norte():
    try:
        conn = pyodbc.connect(
            "DRIVER={ODBC Driver 17 for SQL Server};"
            "SERVER=26.34.138.30;"
            "DATABASE=Cementerio_Norte;"
            "UID=sa;"
            "PWD=123456;"
            "TrustServerCertificate=yes;"
        )
        return conn, None
    except pyodbc.Error as e:
        return None, f"Error SQL Server Norte: {str(e)}"

# ====================
# FUNCIONES PARA OBTENER DATOS DE LAS BASES
# ====================
# ====================
# FUNCIÓN PARA OBTENER TABLAS DE UNA BD
# ====================
def get_tablas_bd(tipo_bd, conn):
    tablas = []
    cursor = conn.cursor()
    
    try:
        if tipo_bd == "sur":  # MariaDB
            cursor.execute("SHOW TABLES")
            tablas = [row[0] for row in cursor.fetchall()]
        else:  # SQL Server (Centro o Norte)
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

# ====================
# FUNCIÓN PARA OBTENER CONTENIDO DE UNA TABLA
# ====================
def get_contenido_tabla(tipo_bd, conn, nombre_tabla):
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
        
        # Obtener datos de la tabla (limitado a 100 registros para no sobrecargar)
        cursor.execute(f"SELECT TOP 100 * FROM {nombre_tabla}" if tipo_bd != "sur" else f"SELECT * FROM {nombre_tabla} LIMIT 100")
        datos = cursor.fetchall()
        
    except Exception as e:
        print(f"Error obteniendo contenido de {nombre_tabla}: {str(e)}")
    
    return columnas, datos

# ====================
# RUTA PRINCIPAL
# ====================
@app.route('/')
def index():
    # Obtener información de las 3 bases de datos
    bds_info = []
    
    # Base de datos SUR
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
    
    # Base de datos CENTRO
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
    
    # Base de datos NORTE
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

# ====================
# RUTA PARA VER CONTENIDO DE TABLA
# ====================
@app.route('/tabla/<tipo_bd>/<nombre_tabla>')
def ver_tabla(tipo_bd, nombre_tabla):
    # Conectar a la BD correspondiente
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
    
    # Obtener contenido de la tabla
    columnas, datos = get_contenido_tabla(tipo_bd, conn, nombre_tabla)
    
    # Obtener nombre de la BD para mostrar
    nombres_bd = {
        'sur': 'SUR (MariaDB)',
        'centro': 'CENTRO (SQL Server)',
        'norte': 'NORTE (SQL Server)'
    }
    
    conn.close()
    
    return render_template('tabla.html', 
                         tipo_bd=tipo_bd,
                         nombre_bd=nombres_bd[tipo_bd],
                         nombre_tabla=nombre_tabla,
                         columnas=columnas,
                         datos=datos)

if __name__ == '__main__':
    app.run(debug=True, port=5000)