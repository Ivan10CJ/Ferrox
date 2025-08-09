<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

/**
 * Comando para probar la integración con Telegram
 * 
 * Este comando permite probar la conectividad con la API de Telegram
 * y enviar mensajes de prueba para verificar la configuración.
 * 
 * Uso: php artisan telegram:test [--message="Mensaje personalizado"]
 */
class TestTelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test 
                            {--message= : Mensaje personalizado para enviar}
                            {--info : Mostrar información del bot}
                            {--venta : Enviar notificación de venta de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la integración con Telegram Bot API';

    /**
     * Servicio de Telegram
     * 
     * @var TelegramService
     */
    protected $telegramService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🤖 Probando integración con Telegram...');
        $this->newLine();

        // Verificar configuración
        if (!$this->telegramService->isConfigured()) {
            $this->error('❌ Telegram no está configurado correctamente.');
            $this->line('Por favor, configura las siguientes variables en tu archivo .env:');
            $this->line('  - TELEGRAM_BOT_TOKEN=tu_token_aqui');
            $this->line('  - TELEGRAM_CHAT_ID=tu_chat_id_aqui');
            return 1;
        }

        $this->info('✅ Configuración de Telegram detectada.');
        $this->newLine();

        // Mostrar información del bot si se solicita
        if ($this->option('info')) {
            $this->showBotInfo();
            return 0;
        }

        // Probar conectividad
        if (!$this->telegramService->testConnection()) {
            $this->error('❌ No se pudo conectar con la API de Telegram.');
            $this->line('Verifica que el token sea válido y que el bot esté activo.');
            return 1;
        }

        $this->info('✅ Conexión con Telegram exitosa.');
        $this->newLine();

        // Enviar mensaje de prueba
        try {
            if ($this->option('venta')) {
                $this->sendTestVentaNotification();
            } else {
                $this->sendTestMessage();
            }
            
            $this->info('✅ Mensaje enviado exitosamente.');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar mensaje: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Muestra información del bot
     */
    private function showBotInfo()
    {
        try {
            $botInfo = $this->telegramService->getBotInfo();
            
            $this->info('📋 Información del Bot:');
            $this->line('  Nombre: ' . $botInfo['first_name']);
            $this->line('  Username: @' . $botInfo['username']);
            $this->line('  ID: ' . $botInfo['id']);
            $this->line('  Puede unirse a grupos: ' . ($botInfo['can_join_groups'] ? 'Sí' : 'No'));
            $this->line('  Puede leer mensajes: ' . ($botInfo['can_read_all_group_messages'] ? 'Sí' : 'No'));
            $this->line('  Soporta inline: ' . ($botInfo['supports_inline_queries'] ? 'Sí' : 'No'));
            
        } catch (\Exception $e) {
            $this->error('❌ Error al obtener información del bot: ' . $e->getMessage());
        }
    }

    /**
     * Envía un mensaje de prueba
     */
    private function sendTestMessage()
    {
        $message = $this->option('message') ?? $this->getDefaultTestMessage();
        
        $this->line('📤 Enviando mensaje de prueba...');
        $this->line('Mensaje: ' . $message);
        
        $this->telegramService->sendMessage($message);
    }

    /**
     * Envía una notificación de venta de prueba
     */
    private function sendTestVentaNotification()
    {
        $this->line('📤 Enviando notificación de venta de prueba...');
        
        $ventaData = [
            'total' => 1250.75,
            'usuario' => 'Usuario de Prueba',
            'fecha' => now()->format('d/m/Y H:i:s'),
            'productos' => [
                [
                    'nombre' => 'Cable Eléctrico 2.5mm',
                    'cantidad' => 10,
                    'tipo' => 'metros',
                    'subtotal' => 450.00
                ],
                [
                    'nombre' => 'Interruptor Simple',
                    'cantidad' => 5,
                    'tipo' => 'unidades',
                    'subtotal' => 800.75
                ]
            ]
        ];
        
        $this->telegramService->sendVentaNotification($ventaData);
    }

    /**
     * Obtiene el mensaje de prueba por defecto
     * 
     * @return string
     */
    private function getDefaultTestMessage(): string
    {
        return "🧪 <b>PRUEBA DE INTEGRACIÓN</b>\n\n" .
               "✅ El sistema de notificaciones de Telegram está funcionando correctamente.\n\n" .
               "📅 Fecha: " . now()->format('d/m/Y H:i:s') . "\n" .
               "🔧 Sistema: Ferretería Ferros\n\n" .
               "Este es un mensaje de prueba para verificar la configuración.";
    }
} 