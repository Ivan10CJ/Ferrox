# 🕐 Configuración de Zona Horaria - Ciudad de México

## 📋 Resumen

Se ha configurado el sistema para que todas las fechas y horas se registren en la zona horaria de la Ciudad de México (`America/Mexico_City`).

## 🔧 Cambios Realizados

### 1. Configuración de Laravel (`config/app.php`)
```php
'timezone' => 'America/Mexico_City',
```

### 2. AppServiceProvider (`app/Providers/AppServiceProvider.php`)
```php
public function boot(): void
{
    // Configurar zona horaria para Carbon
    Carbon::setLocale('es');
    Carbon::setTimezone('America/Mexico_City');
    
    // Asegurar que todas las fechas se manejen en la zona horaria de México
    date_default_timezone_set('America/Mexico_City');
}
```

### 3. Middleware de Zona Horaria (`app/Http/Middleware/SetTimezone.php`)
- Middleware global que asegura que cada petición use la zona horaria correcta
- Registrado en `app/Http/Kernel.php` para ejecutarse en todas las peticiones

### 4. Helper de Fechas (`app/Helpers/DateTimeHelper.php`)
- Clase helper con métodos para manejar fechas en zona horaria de México
- Métodos principales:
  - `DateTimeHelper::now()` - Hora actual en México
  - `DateTimeHelper::today()` - Fecha actual en México
  - `DateTimeHelper::format()` - Formateo de fechas
  - `DateTimeHelper::toMexicoTime()` - Conversión a zona horaria de México

### 5. Actualización de Controladores
- **CorteController**: Actualizado para usar `DateTimeHelper::now()` y `DateTimeHelper::today()`
- **VentaController**: Actualizado para usar `DateTimeHelper::now()` en registro de ventas

### 6. Comando de Verificación (`app/Console/Commands/CheckTimezoneCommand.php`)
- Comando `php artisan timezone:check` para verificar la configuración

## 🚀 Cómo Usar

### Verificar Configuración
```bash
php artisan timezone:check
```

### Usar Helper en Código
```php
use App\Helpers\DateTimeHelper;

// Hora actual en México
$horaActual = DateTimeHelper::now();

// Fecha actual en México
$fechaActual = DateTimeHelper::today();

// Formatear fecha
$fechaFormateada = DateTimeHelper::format($horaActual, 'd/m/Y H:i:s');
```

## 📊 Impacto en el Sistema

### Antes
- Las fechas se registraban en UTC o zona horaria del servidor
- Posibles inconsistencias en horarios de México

### Después
- ✅ Todas las fechas se registran en zona horaria de México
- ✅ Consistencia en horarios de ingreso, egreso y corte de caja
- ✅ Manejo automático de horario de verano
- ✅ Formato de fechas en español

## 🧪 Pruebas

### 1. Verificar Configuración
```bash
php artisan timezone:check
```

### 2. Probar Registro de Movimientos
1. Ir a Corte de Caja
2. Registrar un ingreso o egreso
3. Verificar que la fecha/hora sea correcta para México

### 3. Probar Registro de Ventas
1. Realizar una venta
2. Verificar que la fecha/hora sea correcta para México

### 4. Probar Corte de Caja
1. Cerrar un corte de caja
2. Verificar que la fecha/hora de cierre sea correcta

## 🔍 Verificación Manual

### En la Base de Datos
```sql
-- Verificar fechas en movimientos de caja
SELECT id, tipo, concepto, created_at, 
       CONVERT_TZ(created_at, 'UTC', 'America/Mexico_City') as hora_mexico
FROM movimientos_caja 
ORDER BY created_at DESC 
LIMIT 5;

-- Verificar fechas en ventas
SELECT id, total, fecha,
       CONVERT_TZ(fecha, 'UTC', 'America/Mexico_City') as hora_mexico
FROM ventas 
ORDER BY fecha DESC 
LIMIT 5;
```

### En el Código
```php
// Verificar zona horaria actual
echo "Zona horaria PHP: " . date_default_timezone_get();
echo "Zona horaria Carbon: " . Carbon::now()->timezone->getName();
echo "Hora actual: " . DateTimeHelper::now()->format('Y-m-d H:i:s');
```

## ⚠️ Consideraciones Importantes

### Horario de Verano
- El sistema maneja automáticamente el horario de verano
- México tiene horario de verano de abril a octubre
- La diferencia con UTC varía entre -5 y -6 horas

### Base de Datos
- Las fechas se almacenan en UTC en la base de datos
- Se convierten automáticamente a zona horaria de México al mostrar
- Esto asegura consistencia y facilita cambios de zona horaria

### Servidor
- Asegúrate de que el servidor tenga configurada la zona horaria correcta
- En Linux: `sudo timedatectl set-timezone America/Mexico_City`
- En Windows: Configurar zona horaria en Panel de Control

## 🛠️ Solución de Problemas

### Error: "Zona horaria incorrecta"
```bash
# Verificar configuración
php artisan timezone:check

# Limpiar caché de configuración
php artisan config:clear
php artisan cache:clear
```

### Error: "Fechas no coinciden"
1. Verificar que el servidor tenga la zona horaria correcta
2. Reiniciar el servidor web
3. Verificar que no haya caché de configuración

### Error: "Diferencia horaria incorrecta"
- Verificar que el servidor tenga actualizada la información de zona horaria
- En sistemas Linux: `sudo apt-get install tzdata` (Ubuntu/Debian)

## 📞 Soporte

Para problemas con la zona horaria:

1. Ejecutar `php artisan timezone:check`
2. Verificar configuración del servidor
3. Revisar logs de Laravel en `storage/logs/laravel.log`
4. Contactar al administrador del sistema

---

**✅ Configuración completada**  
**📅 Zona horaria**: America/Mexico_City  
**🕐 Diferencia con UTC**: -6 horas (horario estándar) / -5 horas (horario de verano) 