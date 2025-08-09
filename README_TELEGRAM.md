# 🤖 Integración Telegram - Ferretería Ferros

Integración completa de Telegram Bot API para recibir notificaciones automáticas del sistema de ventas.

## ⚡ Configuración Rápida

### 1. Crear Bot en Telegram
```bash
# Buscar @BotFather en Telegram
# Enviar: /newbot
# Seguir instrucciones y guardar el token
```

### 2. Obtener Chat ID
```bash
# Iniciar conversación con tu bot
# Enviar cualquier mensaje
# Visitar: https://api.telegram.org/bot<TU_TOKEN>/getUpdates
# Buscar "chat_id" en la respuesta
```

### 3. Configurar Variables
```env
# Agregar a tu archivo .env
TELEGRAM_BOT_TOKEN=tu_token_aqui
TELEGRAM_CHAT_ID=tu_chat_id_aqui
```

### 4. Probar Integración
```bash
php artisan telegram:test
```

## 📱 Notificaciones Automáticas

### ✅ Ventas
- Se envían automáticamente después de cada venta
- Incluyen: fecha, vendedor, total, productos

### ⚠️ Stock Bajo
- Alertas cuando productos alcanzan umbral mínimo
- Configurable via `TELEGRAM_STOCK_THRESHOLD`

### ❌ Errores del Sistema
- Notificaciones de errores críticos (opcional)
- Configurable via `TELEGRAM_NOTIFY_ERRORS`

## 🛠️ Comandos Disponibles

```bash
# Prueba básica
php artisan telegram:test

# Mensaje personalizado
php artisan telegram:test --message="Hola desde Laravel"

# Notificación de venta de prueba
php artisan telegram:test --venta

# Información del bot
php artisan telegram:test --info
```

## 📁 Archivos Creados

- `app/Services/TelegramService.php` - Servicio principal
- `app/Console/Commands/TestTelegramCommand.php` - Comando de pruebas
- `config/telegram.php` - Configuración
- `TELEGRAM_SETUP.md` - Documentación completa
- `TELEGRAM_ENV_EXAMPLE.txt` - Ejemplo de variables

## 🔧 Uso en Código

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

// Enviar mensaje simple
$telegram->sendMessage('Hola mundo');

// Enviar notificación de venta
$telegram->sendVentaNotification($ventaData);

// Verificar configuración
if ($telegram->isConfigured()) {
    // Telegram está configurado
}
```

## 📋 Variables de Entorno

| Variable | Descripción | Requerido |
|----------|-------------|-----------|
| `TELEGRAM_BOT_TOKEN` | Token del bot | ✅ |
| `TELEGRAM_CHAT_ID` | ID del chat | ✅ |
| `TELEGRAM_NOTIFY_VENTAS` | Notificaciones de ventas | ❌ |
| `TELEGRAM_NOTIFY_STOCK_LOW` | Notificaciones de stock | ❌ |
| `TELEGRAM_STOCK_THRESHOLD` | Umbral de stock bajo | ❌ |

## 🚨 Solución de Problemas

### Error: "Telegram no está configurado"
```bash
# Verificar variables en .env
php artisan config:clear
```

### Error: "No se pudo conectar"
```bash
# Verificar token en navegador
https://api.telegram.org/bot<TU_TOKEN>/getMe
```

### Error: "Chat not found"
```bash
# Asegurar que el bot esté en el chat
# Enviar /start al bot
```

## 📞 Soporte

- 📖 Documentación completa: `TELEGRAM_SETUP.md`
- 📝 Ejemplo de configuración: `TELEGRAM_ENV_EXAMPLE.txt`
- 🔍 Logs: `storage/logs/laravel.log`
- 🧪 Pruebas: `php artisan telegram:test --info`

---

**✅ Integración lista para usar**  
**📅 Versión**: 1.0  
**🔧 Laravel**: 12.x 