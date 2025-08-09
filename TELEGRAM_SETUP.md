# 🤖 Integración de Telegram - Ferretería Ferros

Esta documentación explica cómo configurar y usar la integración de Telegram para recibir notificaciones automáticas del sistema de ventas.

## 📋 Tabla de Contenidos

1. [Configuración del Bot de Telegram](#configuración-del-bot-de-telegram)
2. [Configuración del Proyecto](#configuración-del-proyecto)
3. [Pruebas de Integración](#pruebas-de-integración)
4. [Tipos de Notificaciones](#tipos-de-notificaciones)
5. [Solución de Problemas](#solución-de-problemas)
6. [API Reference](#api-reference)

## 🔧 Configuración del Bot de Telegram

### Paso 1: Crear un Bot

1. **Abrir Telegram** y buscar `@BotFather`
2. **Iniciar conversación** con BotFather
3. **Enviar comando** `/newbot`
4. **Seguir las instrucciones**:
   - Proporcionar un nombre para el bot (ej: "Ferretería Ferros Notifications")
   - Proporcionar un username único (ej: "ferros_notifications_bot")
5. **Guardar el token** que te proporciona BotFather

### Paso 2: Obtener Chat ID

#### Opción A: Para chat privado
1. **Iniciar conversación** con tu bot
2. **Enviar cualquier mensaje** al bot
3. **Visitar en navegador**: `https://api.telegram.org/bot<TU_TOKEN>/getUpdates`
4. **Buscar el `chat_id`** en la respuesta JSON

#### Opción B: Para grupo
1. **Agregar el bot al grupo**
2. **Enviar un mensaje** en el grupo
3. **Visitar en navegador**: `https://api.telegram.org/bot<TU_TOKEN>/getUpdates`
4. **Buscar el `chat_id`** del grupo (será un número negativo)

### Paso 3: Probar la Conexión

Visita en tu navegador:
```
https://api.telegram.org/bot<TU_TOKEN>/sendMessage?chat_id=<TU_CHAT_ID>&text=Prueba+directa+desde+navegador
```

Si recibes el mensaje, la configuración es correcta.

## ⚙️ Configuración del Proyecto

### Paso 1: Variables de Entorno

Agregar al archivo `.env`:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=tu_token_aqui
TELEGRAM_CHAT_ID=tu_chat_id_aqui

# Configuración de notificaciones (opcional)
TELEGRAM_NOTIFY_VENTAS=true
TELEGRAM_NOTIFY_STOCK_LOW=true
TELEGRAM_NOTIFY_ERRORS=false

# Configuración avanzada (opcional)
TELEGRAM_STOCK_THRESHOLD=10
TELEGRAM_RETRY_ENABLED=true
TELEGRAM_MAX_RETRY_ATTEMPTS=3
TELEGRAM_RETRY_DELAY=5
```

### Paso 2: Verificar Instalación

Ejecutar el comando de prueba:

```bash
php artisan telegram:test
```

### Paso 3: Probar Notificación de Venta

```bash
php artisan telegram:test --venta
```

## 🧪 Pruebas de Integración

### Comandos Disponibles

```bash
# Prueba básica de conexión
php artisan telegram:test

# Enviar mensaje personalizado
php artisan telegram:test --message="Hola desde Laravel"

# Enviar notificación de venta de prueba
php artisan telegram:test --venta

# Mostrar información del bot
php artisan telegram:test --info
```

### Verificar Logs

Los logs de Telegram se guardan en `storage/logs/laravel.log`:

```bash
tail -f storage/logs/laravel.log | grep -i telegram
```

## 📱 Tipos de Notificaciones

### 1. Notificaciones de Ventas

**Cuándo se envían**: Después de registrar una venta exitosamente

**Contenido**:
- 🛒 Emoji de carrito
- Fecha y hora de la venta
- Vendedor responsable
- Total de la venta
- Lista de productos vendidos
- Confirmación de procesamiento

**Ejemplo**:
```
🛒 NUEVA VENTA REGISTRADA

📅 Fecha: 15/12/2024 14:30:25
👤 Vendedor: Juan Pérez
💰 Total: $1,250.75

📋 Productos:
• Cable Eléctrico 2.5mm - 10 metros - $450.00
• Interruptor Simple - 5 unidades - $800.75

✅ Venta procesada correctamente
```

### 2. Notificaciones de Stock Bajo

**Cuándo se envían**: Cuando un producto alcanza el umbral mínimo

**Contenido**:
- ⚠️ Emoji de advertencia
- Nombre del producto
- Stock actual
- Tipo de venta
- Acción requerida

### 3. Notificaciones de Errores

**Cuándo se envían**: Errores críticos del sistema (configurable)

**Contenido**:
- ❌ Emoji de error
- Descripción del error
- Fecha y hora
- Contexto del error

## 🔍 Solución de Problemas

### Error: "Telegram no está configurado"

**Causa**: Variables de entorno faltantes o incorrectas

**Solución**:
1. Verificar que `TELEGRAM_BOT_TOKEN` y `TELEGRAM_CHAT_ID` estén en `.env`
2. Ejecutar `php artisan config:clear`
3. Reiniciar el servidor

### Error: "No se pudo conectar con la API de Telegram"

**Causa**: Token inválido o bot deshabilitado

**Solución**:
1. Verificar que el token sea correcto
2. Verificar que el bot esté activo en BotFather
3. Probar la URL directa en navegador

### Error: "Chat not found"

**Causa**: Chat ID incorrecto o bot no agregado al chat

**Solución**:
1. Verificar el chat ID
2. Asegurar que el bot esté en el chat/grupo
3. Enviar un mensaje al bot para activarlo

### Error: "Forbidden: bot was blocked by the user"

**Causa**: Usuario bloqueó el bot

**Solución**:
1. Desbloquear el bot en Telegram
2. Enviar `/start` al bot
3. Verificar permisos del bot

## 📚 API Reference

### TelegramService

#### Métodos Principales

```php
// Enviar mensaje simple
$telegram->sendMessage('Hola mundo');

// Enviar notificación de venta
$telegram->sendVentaNotification($ventaData);

// Enviar notificación de stock bajo
$telegram->sendStockLowNotification($productoData);

// Verificar configuración
$telegram->isConfigured();

// Probar conexión
$telegram->testConnection();

// Obtener información del bot
$telegram->getBotInfo();
```

#### Estructura de Datos

**Venta Data**:
```php
$ventaData = [
    'total' => 1250.75,
    'usuario' => 'Juan Pérez',
    'fecha' => '15/12/2024 14:30:25',
    'productos' => [
        [
            'nombre' => 'Cable Eléctrico 2.5mm',
            'cantidad' => 10,
            'tipo' => 'metros',
            'subtotal' => 450.00
        ]
    ]
];
```

**Producto Data**:
```php
$productoData = [
    'nombre' => 'Cable Eléctrico 2.5mm',
    'stock' => 5,
    'tipo_venta' => 'metros'
];
```

### Configuración

El archivo `config/telegram.php` contiene toda la configuración:

```php
// Habilitar/deshabilitar notificaciones
'notifications' => [
    'ventas' => ['enabled' => true],
    'stock_low' => ['enabled' => true],
    'errors' => ['enabled' => false],
],

// Configuración de mensajes
'messages' => [
    'emojis' => [
        'venta' => '🛒',
        'stock_low' => '⚠️',
        'error' => '❌',
    ],
    'format' => [
        'parse_mode' => 'HTML',
    ],
],
```

## 🚀 Próximas Mejoras

- [ ] Notificaciones de corte de caja
- [ ] Notificaciones de inventario
- [ ] Comandos de Telegram para consultar stock
- [ ] Webhooks para recibir comandos
- [ ] Plantillas personalizables de mensajes
- [ ] Notificaciones programadas
- [ ] Integración con múltiples chats

## 📞 Soporte

Para problemas o preguntas sobre la integración de Telegram:

1. Revisar los logs en `storage/logs/laravel.log`
2. Ejecutar `php artisan telegram:test --info`
3. Verificar la configuración en `config/telegram.php`
4. Consultar la documentación oficial de Telegram Bot API

---

**Versión**: 1.0  
**Última actualización**: Diciembre 2024  
**Autor**: Sistema Ferretería Ferros 