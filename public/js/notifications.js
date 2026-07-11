/**
 * Sistema de Notificaciones Simple
 * Uso: showNotification('Mensaje', 'success|error|warning|info', duracion_ms)
 */

class NotificationSystem {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Crear contenedor de notificaciones
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
            width: 400px;
            max-width: 400px;
        `;
        document.body.appendChild(this.container);

        // Agregar estilos CSS
        this.addStyles();
    }

    addStyles() {
        const styleId = 'notification-styles';
        if (document.getElementById(styleId)) return;

        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .notification {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
                margin-bottom: 12px;
                padding: 18px 22px 18px 18px;
                pointer-events: auto;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border-left: 4px solid #ddd;
                position: relative;
                overflow: hidden;
                max-width: 100%;
                min-width: 320px;
                width: 380px;
                word-wrap: break-word;
                word-break: break-word;
                hyphens: auto;
                min-height: 80px;
                display: flex;
                flex-direction: column;
            }

            .notification.show {
                transform: translateX(0);
                opacity: 1;
            }

            .notification.hide {
                transform: translateX(100%);
                opacity: 0;
                margin-bottom: 0;
                padding-top: 0;
                padding-bottom: 0;
                max-height: 0;
            }

            .notification.success {
                border-left-color: #10b981;
                background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            }

            .notification.error {
                border-left-color: #ef4444;
                background: linear-gradient(135deg, #fef2f2 0%, #fef2f2 100%);
            }

            .notification.warning {
                border-left-color: #f59e0b;
                background: linear-gradient(135deg, #fffbeb 0%, #fefce8 100%);
            }

            .notification.info {
                border-left-color: #3b82f6;
                background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
            }

            .notification-header {
                display: flex;
                align-items: flex-start;
                margin-bottom: 8px;
                gap: 12px;
                min-height: 50px;
                flex: 1;
            }

            .notification-icon {
                width: 24px;
                height: 24px;
                flex-shrink: 0;
                margin-top: 2px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .notification-content {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                min-height: 46px;
                gap: 2px;
            }

            .notification-title {
                font-weight: 600;
                font-size: 15px;
                margin: 0 0 6px 0;
                color: #374151;
                line-height: 1.3;
                min-height: 20px;
                display: flex;
                align-items: center;
            }

            .notification-message {
                font-size: 14px;
                color: #6b7280;
                margin: 0;
                line-height: 1.6;
                max-height: 120px;
                overflow-y: auto;
                padding-right: 8px;
                min-height: 20px;
                flex: 1;
            }

            .notification-message::-webkit-scrollbar {
                width: 4px;
            }

            .notification-message::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
                border-radius: 2px;
            }

            .notification-message::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.2);
                border-radius: 2px;
            }

            .notification-message::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.3);
            }

            .notification-close {
                position: absolute;
                top: 12px;
                right: 12px;
                background: rgba(0, 0, 0, 0.05);
                border: none;
                font-size: 16px;
                color: #9ca3af;
                cursor: pointer;
                padding: 6px;
                line-height: 1;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
                z-index: 1;
            }

            .notification-close:hover {
                background: rgba(0, 0, 0, 0.1);
                color: #374151;
                transform: scale(1.1);
            }

            .notification-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 2px;
                background: rgba(0, 0, 0, 0.1);
                transition: width linear;
            }

            .notification.success .notification-progress {
                background: #10b981;
            }

            .notification.error .notification-progress {
                background: #ef4444;
            }

            .notification.warning .notification-progress {
                background: #f59e0b;
            }

            .notification.info .notification-progress {
                background: #3b82f6;
            }

            /* Estilos para texto largo */
            .notification-message.long-text {
                max-height: 90px;
                overflow-y: auto;
                position: relative;
            }

            .notification.expanded .notification-message {
                max-height: 200px;
            }

            .notification-expand-btn {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
                border: 1px solid rgba(59, 130, 246, 0.2);
                color: #3b82f6;
                font-size: 11px;
                cursor: pointer;
                padding: 6px 12px;
                margin-top: 8px;
                border-radius: 6px;
                transition: all 0.2s;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                align-self: flex-start;
                width: auto;
                min-width: 80px;
            }

            .notification-expand-btn:hover {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.08));
                border-color: rgba(59, 130, 246, 0.3);
                color: #2563eb;
                transform: translateY(-1px);
            }



            /* Efecto de fade para texto truncado */
            .notification-message .truncated::after {
                content: '';
                position: absolute;
                bottom: 0;
                right: 0;
                width: 30px;
                height: 20px;
                background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.9));
                pointer-events: none;
            }

            @media (max-width: 480px) {
                #notification-container {
                    left: 20px;
                    right: 20px;
                    width: auto;
                    max-width: none;
                    bottom: 10px;
                }
                
                .notification {
                    transform: translateY(100%);
                    padding: 16px 18px 16px 16px;
                    margin-bottom: 8px;
                    min-width: auto;
                    width: 100%;
                }
                
                .notification.show {
                    transform: translateY(0);
                }
                
                .notification.hide {
                    transform: translateY(100%);
                }

                .notification-message {
                    font-size: 12px;
                    max-height: 100px;
                }

                .notification-icon {
                    width: 20px;
                    height: 20px;
                }

                .notification-title {
                    font-size: 13px;
                }
            }
        `;
        document.head.appendChild(style);
    }

    getIcon(type) {
        const icons = {
            success: `<svg fill="currentColor" viewBox="0 0 20 20" style="color: #10b981;">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>`,
            error: `<svg fill="currentColor" viewBox="0 0 20 20" style="color: #ef4444;">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>`,
            warning: `<svg fill="currentColor" viewBox="0 0 20 20" style="color: #f59e0b;">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>`,
            info: `<svg fill="currentColor" viewBox="0 0 20 20" style="color: #3b82f6;">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>`
        };
        return icons[type] || icons.info;
    }

    getTitle(type) {
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };
        return titles[type] || 'Notificación';
    }

    show(message, type = 'info', duration = 6000) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const progressBar = document.createElement('div');
        progressBar.className = 'notification-progress';
        progressBar.style.width = '100%';

        // Detectar si el mensaje es largo
        const isLongMessage = message.length > 100;
        const messageClass = isLongMessage ? 'notification-message long-text' : 'notification-message';
        
        // Truncar mensaje si es muy largo para vista inicial
        let displayMessage = message;
        let expandButton = '';
        
        if (isLongMessage) {
            const truncatedMessage = message.substring(0, 100);
            expandButton = `<button class="notification-expand-btn">Ver más</button>`;
            displayMessage = `<span class="truncated" style="position: relative;">${truncatedMessage}...</span><span class="full-text" style="display: none;">${message}</span>`;
        }

        notification.innerHTML = `
            <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
            <div class="notification-header">
                <div class="notification-icon">${this.getIcon(type)}</div>
                <div class="notification-content">
                    <h4 class="notification-title">${this.getTitle(type)}</h4>
                    <div class="${messageClass}">${displayMessage}</div>
                    ${expandButton}
                </div>
            </div>
        `;
        
        notification.appendChild(progressBar);
        this.container.appendChild(notification);

        // Agregar funcionalidad de expandir para mensajes largos
        if (isLongMessage) {
            const expandBtn = notification.querySelector('.notification-expand-btn');
            if (expandBtn) {
                let isExpanded = false;
                
                expandBtn.addEventListener('click', () => {
                    const truncated = notification.querySelector('.truncated');
                    const fullText = notification.querySelector('.full-text');
                    const messageContainer = notification.querySelector('.notification-message');
                    
                    if (truncated && fullText && messageContainer) {
                        isExpanded = !isExpanded;
                        
                        if (isExpanded) {
                            truncated.style.display = 'none';
                            fullText.style.display = 'inline';
                            expandBtn.innerHTML = "Ver menos <span style='font-size: 8px;'>▲</span>";
                            notification.classList.add('expanded');
                            messageContainer.style.maxHeight = '200px';
                        } else {
                            truncated.style.display = 'inline';
                            fullText.style.display = 'none';
                            expandBtn.innerHTML = "Ver más <span style='font-size: 8px;'>▼</span>";
                            notification.classList.remove('expanded');
                            messageContainer.style.maxHeight = '90px';
                        }
                    }
                });
                
                // Inicializar con el ícono correcto
                expandBtn.innerHTML = "Ver más <span style='font-size: 8px;'>▼</span>";
            }
        }

        // Mostrar con animación
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Ajustar duración para mensajes largos
        if (isLongMessage && duration < 8000) {
            duration = 8000; // Más tiempo para leer
        }

        // Animar barra de progreso
        if (duration > 0) {
            setTimeout(() => {
                progressBar.style.width = '0%';
                progressBar.style.transition = `width ${duration}ms linear`;
            }, 100);

            // Auto-remover
            setTimeout(() => {
                this.hide(notification);
            }, duration);
        }

        return notification;
    }

    hide(notification) {
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }

    success(message, duration = 6000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 6000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 6000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 6000) {
        return this.show(message, 'info', duration);
    }
}

// Inicializar sistema global
let notificationSystem;

// Funciones globales simples
function showNotification(message, type = 'info', duration = 6000) {
    if (!notificationSystem) {
        notificationSystem = new NotificationSystem();
    }
    return notificationSystem.show(message, type, duration);
}

function showSuccess(message, duration = 6000) {
    if (!notificationSystem) {
        notificationSystem = new NotificationSystem();
    }
    return notificationSystem.success(message, duration);
}

function showError(message, duration = 6000) {
    if (!notificationSystem) {
        notificationSystem = new NotificationSystem();
    }
    return notificationSystem.error(message, duration);
}

function showWarning(message, duration = 6000) {
    if (!notificationSystem) {
        notificationSystem = new NotificationSystem();
    }
    return notificationSystem.warning(message, duration);
}

function showInfo(message, duration = 6000) {
    if (!notificationSystem) {
        notificationSystem = new NotificationSystem();
    }
    return notificationSystem.info(message, duration);
}

// Auto-inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        notificationSystem = new NotificationSystem();
    });
} else {
    notificationSystem = new NotificationSystem();
}