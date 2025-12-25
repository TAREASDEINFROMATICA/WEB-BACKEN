<?php
class LoginCookie {
    private $secure;
    private $httponly;
    private $samesite;

    public function __construct() {
        $this->secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $this->httponly = true;
        $this->samesite = 'Lax';
    }

    // cookie
    public function crearCookieLogin($usuarioId, $username, $recordar = false) {
        try {
            $cookieData = [
                'usuario_id' => (int)$usuarioId,
                'username' => $username,
                'login_time' => time(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'desconocida'
            ];

            $cookieValue = base64_encode(json_encode($cookieData));
            
            // Tiempo
            $expire = $recordar ? time() + (30 * 24 * 60 * 60) : 0; // 30 días 

            $options = [
                'expires' => $expire,
                'path' => '/',
                'secure' => $this->secure,
                'httponly' => $this->httponly,
                'samesite' => $this->samesite
            ];
            //cerrar
            if (setcookie('tv_login_session', $cookieValue, $options)) {
                //guardar cookie
                $_COOKIE['tv_login_session'] = $cookieValue;
                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log('Error creando cookie de login: ' . $e->getMessage());
            return false;
        }
    }

    // Verificar si existe
    public function verificarCookieLogin() {
        if (!isset($_COOKIE['tv_login_session'])) {
            return false;
        }

        try {
            
            $cookieData = json_decode(base64_decode($_COOKIE['tv_login_session']), true);
            
            if (!$this->validarEstructuraCookie($cookieData)) {
                $this->eliminarCookieLogin();
                return false;
            }

            
            $tiempoTranscurrido = time() - $cookieData['login_time'];
            if ($tiempoTranscurrido > (30 * 24 * 60 * 60)) {
                $this->eliminarCookieLogin();
                return false;
            }

            
            if ($cookieData['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? 'desconocido')) {
                $this->eliminarCookieLogin();
                return false;
            }

            return $cookieData;

        } catch (Exception $e) {
            error_log('Error verificando cookie de login: ' . $e->getMessage());
            $this->eliminarCookieLogin();
            return false;
        }
    }

    
    public function eliminarCookieLogin() {
        $options = [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $this->secure,
            'httponly' => $this->httponly,
            'samesite' => $this->samesite
        ];

        setcookie('tv_login_session', '', $options);
        unset($_COOKIE['tv_login_session']);
        return true;
    }

    
    private function validarEstructuraCookie($data) {
        $camposRequeridos = ['usuario_id', 'username', 'login_time', 'user_agent', 'ip'];
        
        foreach ($camposRequeridos as $campo) {
            if (!isset($data[$campo])) {
                return false;
            }
        }
        
        return is_numeric($data['usuario_id']) && 
               is_string($data['username']) && 
               is_numeric($data['login_time']);
    }

    
    public function obtenerUsuarioDeCookie() {
        $cookieData = $this->verificarCookieLogin();
        if (!$cookieData) {
            return null;
        }

        return [
            'id' => $cookieData['usuario_id'],
            'username' => $cookieData['username'],
            'login_time' => $cookieData['login_time']
        ];
    }
}
?>