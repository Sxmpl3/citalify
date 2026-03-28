# citalia — Plataforma de Reservas para Negocios de Servicios Locales

> **Gestión de citas online con recordatorios automáticos por WhatsApp para peluquerías, clínicas, fisioterapeutas y todo negocio que trabaje con agenda.**

---

## Índice

1. [Resumen del Proyecto](#resumen-del-proyecto)
2. [El Problema](#el-problema)
3. [La Solución](#la-solución)
4. [Público Objetivo](#público-objetivo)
5. [Funcionalidades](#funcionalidades)
6. [Stack Técnico](#stack-técnico)
7. [Arquitectura de la Base de Datos](#arquitectura-de-la-base-de-datos)
8. [Planes de Pago](#planes-de-pago)
9. [Fases de Desarrollo](#fases-de-desarrollo)
10. [Modelo de Negocio](#modelo-de-negocio)
11. [Proyección de Ingresos](#proyección-de-ingresos)
12. [Estrategia de Captación](#estrategia-de-captación)
13. [Expansión Futura](#expansión-futura)

---

## Resumen del Proyecto

**citalia** es un SaaS de gestión de citas y reservas online orientado a pequeños negocios de servicios locales en España y Latinoamérica. Permite a cualquier negocio tener su propia página de reservas en minutos, gestionar su agenda desde un panel sencillo y enviar recordatorios automáticos a sus clientes por WhatsApp o SMS, reduciendo las ausencias no avisadas (no-shows) hasta en un 60%.

A diferencia de soluciones como Calendly o SimplyBook, citalia está diseñado desde el primer día para el pequeño empresario hispanohablante: interfaz en español, precios accesibles, configuración en menos de 10 minutos y soporte directo.

**No utiliza APIs de inteligencia artificial.** Toda la lógica es determinista, lo que garantiza márgenes elevados, fiabilidad total y coste operativo predecible.

---

## El Problema

Los negocios de servicios locales —peluquerías, clínicas estéticas, fisioterapeutas, academias, talleres mecánicos— gestionan sus citas de forma caótica:

- **WhatsApp y llamadas de voz** como canal principal: mensajes perdidos, confirmaciones manuales, historial desorganizado.
- **No-shows frecuentes**: un cliente que no se presenta supone una hora de trabajo perdida sin recuperar. En un negocio de 8 citas diarias, 2 no-shows semanales equivalen a más de 1.500€ perdidos al año.
- **Sin visibilidad**: el propietario no sabe cuáles son sus horas más demandadas, qué servicios generan más ingresos ni quiénes son sus clientes más fieles.
- **Soluciones actuales inadecuadas**: las herramientas existentes están pensadas para empresas con personal técnico, tienen interfaces en inglés o son demasiado caras para un autónomo.

---

## La Solución

citalia ofrece tres cosas fundamentales:

**1. Página de reservas propia**
Cada negocio obtiene un enlace único (`citalia.es/mi-peluqueria`) donde sus clientes pueden ver la disponibilidad real y reservar en 30 segundos, sin necesidad de llamar ni enviar mensajes.

**2. Panel de gestión de agenda**
Vista diaria, semanal y mensual. Gestión de servicios, precios, duraciones y empleados. El propietario puede bloquear horas, añadir citas manuales y ver el estado de cada reserva.

**3. Recordatorios automáticos por WhatsApp y SMS**
24 horas antes de cada cita, el cliente recibe un mensaje automático desde el número centralizado de citalia con el nombre del negocio, el servicio reservado y la hora. Puede confirmar o cancelar con una respuesta, y el sistema actualiza la agenda en tiempo real.

---

## Público Objetivo

### Primario
- Peluquerías y barberías
- Clínicas de estética y uñas
- Fisioterapeutas y ostéopatas en consulta privada
- Psicólogos y coaches independientes
- Academias de idiomas y clases particulares

### Secundario
- Talleres mecánicos con cita previa
- Veterinarias pequeñas
- Estudios de tatuajes y piercing
- Entrenadores personales

### Perfil del cliente ideal
Autónomo o pequeño negocio con 1-5 empleados, entre 10 y 50 citas semanales, que actualmente gestiona su agenda por WhatsApp o teléfono y siente el dolor de los no-shows y la desorganización.

---

## Funcionalidades

### MVP (versión 1.0)

- [ ] Registro y onboarding guiado (menos de 10 minutos)
- [ ] Configuración de servicios con nombre, duración y precio
- [ ] Configuración de horarios y días laborables
- [ ] Página pública de reservas con URL personalizada
- [ ] Panel de agenda (vista diaria y semanal)
- [ ] Recordatorio automático por WhatsApp/SMS 24h antes de la cita
- [ ] Gestión de respuestas: el cliente confirma o cancela vía mensaje
- [ ] Notificación al negocio cuando hay una cancelación
- [ ] Historial de citas por cliente

### Versión 1.5

- [ ] Múltiples empleados con agendas independientes
- [ ] Colores y personalización básica de la página de reservas
- [ ] Estadísticas: horas punta, servicios más reservados, tasa de no-shows
- [ ] Bloqueos de agenda (vacaciones, festivos)
- [ ] Segundo recordatorio 2 horas antes (configurable)

### Versión 2.0

- [ ] Pago online en el momento de la reserva (Stripe)
- [ ] Sistema de reseñas internas
- [ ] Widget embebible en página web propia del negocio
- [ ] App móvil para el panel de gestión (PWA)
- [ ] Integración con Google Calendar

---

## Stack Técnico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Base de datos | MySQL 8 |
| Cola de mensajes | Laravel Queues + Redis |
| Mensajería WhatsApp | Meta Cloud API / Twilio |
| Mensajería SMS (fallback) | Twilio SMS |
| Pagos | Stripe (suscripciones + cobro a clientes finales) |
| Almacenamiento | Laravel Storage + S3 compatible |
| Servidor | VPS (Hetzner o DigitalOcean) |
| Deploy | Laravel Forge + Nginx |

### Decisiones técnicas clave

**¿Por qué Laravel?**
Ecosistema maduro para SaaS multi-tenant, Cashier para gestión de suscripciones Stripe, sistema de colas nativo para los envíos programados de mensajes, y documentación extensa para escalar el equipo.

**¿Por qué un número centralizado para WhatsApp?**
La Meta Cloud API bajo un único número permite enviar plantillas aprobadas a todos los clientes de todos los negocios registrados en citalia. Cada mensaje incluye el nombre del negocio en el cuerpo del texto. El coste por mensaje es de aproximadamente 0,05€, absorbible desde el plan Pro.

**¿Por qué no IA?**
Toda la lógica es determinista: reglas de disponibilidad, colas de mensajes programados con `delay()`, base de datos relacional. Sin costes variables de API de terceros, sin latencia impredecible, sin dependencia de modelos externos.

---

## Arquitectura de la Base de Datos

```
businesses
  id, name, slug, phone, email, logo, address, timezone
  plan_id, stripe_customer_id, trial_ends_at, created_at

employees
  id, business_id, name, email, color

services
  id, business_id, name, duration_minutes, price, color, is_active

schedules
  id, employee_id, day_of_week (0-6), open_time, close_time

schedule_blocks
  id, employee_id, starts_at, ends_at, reason

bookings
  id, business_id, employee_id, service_id
  customer_name, customer_phone, customer_email
  starts_at, ends_at
  status (pending | confirmed | cancelled | no_show)
  reminder_sent_at, confirmation_received_at
  created_at

reminder_logs
  id, booking_id, channel (whatsapp | sms), sent_at, status, response

plans
  id, name, price_monthly, max_employees, max_bookings_monthly
  whatsapp_reminders (bool), statistics (bool), online_payment (bool)
```

---

## Planes de Pago

### Básico — 19 €/mes

Ideal para autónomos con agenda personal.

- 1 empleado
- Servicios ilimitados
- Página de reservas con URL personalizada
- Panel de agenda (vista diaria y semanal)
- Hasta 100 citas al mes
- Recordatorios por email
- Soporte por email

---

### Pro — 39 €/mes

Para negocios que quieren reducir no-shows y ganar visibilidad.

- Hasta 3 empleados
- Citas ilimitadas
- Todo lo del plan Básico
- **Recordatorios automáticos por WhatsApp** (ilimitados)
- Segundo recordatorio configurable
- Estadísticas básicas (horas punta, servicios más reservados)
- Soporte prioritario por email y chat

---

### Equipo — 69 €/mes

Para negocios con varios profesionales y alto volumen.

- Hasta 10 empleados
- Citas ilimitadas
- Todo lo del plan Pro
- Estadísticas avanzadas (tasa de no-shows, clientes recurrentes, ingresos estimados)
- Personalización avanzada de la página de reservas
- Widget embebible en página web propia
- Soporte telefónico

---

### Comparativa de planes

| Característica | Básico | Pro | Equipo |
|---|:---:|:---:|:---:|
| Precio mensual | 19 € | 39 € | 69 € |
| Empleados | 1 | 3 | 10 |
| Citas al mes | 100 | Ilimitadas | Ilimitadas |
| Recordatorio email | ✓ | ✓ | ✓ |
| Recordatorio WhatsApp | — | ✓ | ✓ |
| Estadísticas | — | Básicas | Avanzadas |
| Widget embebible | — | — | ✓ |
| Soporte | Email | Email + chat | Teléfono |

> Todos los planes incluyen **30 días de prueba gratuita** sin necesidad de tarjeta de crédito.

---

## Fases de Desarrollo

### Fase 0 — Validación (semanas 1-2)

**Objetivo:** confirmar que el dolor existe antes de escribir código.

- Landing page estática con formulario de lista de espera
- Visita presencial a 15-20 negocios locales (peluquerías, fisios)
- Script de entrevista: ¿cómo gestionas tus citas? ¿cuántos no-shows tienes? ¿cuánto te cuesta?
- Meta: 30 negocios apuntados a la lista de espera y al menos 5 dispuestos a pagar desde el primer día

**Entregables:**
- Landing page publicada
- 30+ emails en lista de espera
- 3-5 pre-ventas confirmadas (con pago simbólico de 1€ para validar intención)

---

### Fase 1 — MVP (semanas 3-8)

**Objetivo:** producto funcional con el flujo mínimo que resuelve el problema principal.

**Sprint 1 (sem. 3-4):** Autenticación y onboarding
- Registro de negocio y configuración inicial
- Gestión de servicios y horarios
- Modelo de datos completo en MySQL

**Sprint 2 (sem. 5-6):** Reservas
- Página pública de reservas (`/slug`)
- Lógica de disponibilidad (sin solapamientos, respetando horarios)
- Panel de agenda vista diaria

**Sprint 3 (sem. 7-8):** Recordatorios y notificaciones
- Integración con Twilio (WhatsApp + SMS)
- Job programado de recordatorio 24h antes
- Gestión de respuestas (confirmación/cancelación)
- Notificación al negocio

**Entregables:**
- MVP desplegado en producción
- 10 negocios beta usando el sistema
- 0 errores críticos en producción

---

### Fase 2 — Lanzamiento (semanas 9-12)

**Objetivo:** primeros clientes de pago y proceso de ventas validado.

- Integración de Stripe con Laravel Cashier (suscripciones mensuales)
- Sistema de prueba gratuita de 30 días
- Panel de métricas básico para el negocio
- Múltiples empleados (plan Equipo)
- Onboarding mejorado basado en feedback de los beta testers

**Entregables:**
- Stripe en producción
- 30+ clientes de pago activos
- Proceso de venta directa documentado y replicable

---

### Fase 3 — Crecimiento (meses 4-8)

**Objetivo:** escalar a 200 clientes de pago con canales de adquisición automatizados.

- SEO local: páginas de aterrizaje por sector ("software reservas peluquería", "agenda online fisioterapeuta")
- Programa de referidos: 1 mes gratis por cada nuevo cliente que traiga un usuario
- Estadísticas avanzadas (plan Equipo)
- Widget embebible para página web propia
- A/B testing en la página de reservas pública

**Entregables:**
- 200 clientes de pago
- Tráfico orgánico desde SEO comenzando a convertir
- Churn mensual por debajo del 3%

---

### Fase 4 — Expansión (meses 9-18)

**Objetivo:** entrar en México y Colombia, duplicar la base de clientes.

- Adaptación de precios por mercado (MXN, COP)
- Número de WhatsApp dedicado por país
- Soporte localizado
- Pago online en el momento de la reserva (Stripe)
- App móvil PWA para gestión desde el teléfono

---

## Modelo de Negocio

### Ingresos

- **Suscripción mensual recurrente (MRR):** fuente principal de ingresos.
- **Futura comisión sobre pagos online:** 0,5% sobre el valor de cada reserva pagada online (fase 4).

### Costes variables principales

| Concepto | Coste estimado |
|---|---|
| Mensaje WhatsApp (Twilio/Meta) | ~0,05 € por mensaje |
| SMS fallback | ~0,07 € por SMS |
| Servidor VPS | 20-40 €/mes (hasta 500 clientes) |
| Stripe fees | 1,4% + 0,25 € por cobro |

### Margen estimado por plan Pro (39 €/mes)

Un cliente Pro con 40 citas mensuales genera 40 mensajes de WhatsApp × 0,05 € = 2 € de coste variable. Margen bruto: 37 € (~95%). Descontando infraestructura prorrateada, margen neto estimado por encima del 70%.

---

## Proyección de Ingresos

| Mes | Clientes | MRR estimado | ARR estimado |
|:---:|:---:|:---:|:---:|
| 3 | 30 | 930 € | — |
| 6 | 100 | 3.500 € | — |
| 9 | 200 | 7.200 € | — |
| 12 | 400 | 15.000 € | 180.000 € |
| 18 | 800 | 30.000 € | 360.000 € |
| 24 | 1.500 | 57.000 € | 684.000 € |

> Proyección conservadora asumiendo ticket medio de 35 €/mes (mezcla de planes).

---

## Estrategia de Captación

### Canal 1 — Venta directa local (meses 1-4)

La forma más rápida de conseguir los primeros 50 clientes. Visitar negocios en persona, mostrar una demo de 5 minutos y ofrecer el primer mes gratis. Un no-show evitado justifica el precio de 3 meses.

**Argumentario clave:**
> "Si tienes 2 no-shows a la semana y cada cita vale 20€, estás perdiendo 160€ al mes. citalia te cuesta 39€."

### Canal 2 — SEO de nicho (meses 3-12)

Páginas de aterrizaje específicas por sector y ciudad:
- `citalia.es/peluquerias` — software gestión citas peluquería
- `citalia.es/fisioterapeutas` — agenda online fisioterapeuta
- `citalia.es/estetica` — reservas online clínica estética

Artículos de blog orientados a los dolores del propietario: "cómo reducir no-shows en tu peluquería", "por qué deberías dejar de gestionar citas por WhatsApp".

### Canal 3 — Referidos (desde mes 4)

Cada cliente que traiga a otro negocio recibe 1 mes gratis. El negocio referido recibe 2 semanas adicionales de prueba. Con un NPS alto, este canal puede generar el 30-40% de los nuevos clientes a coste casi cero.

### Canal 4 — Asociaciones gremiales (mes 6+)

Contactar con asociaciones de peluqueros, colegios de fisioterapeutas y cámaras de comercio locales para ofrecer acuerdos de descuento a sus miembros. Un solo acuerdo puede abrir decenas de clientes de golpe.

---

## Expansión Futura

### Corto plazo (año 1)
- Pago online en la reserva (Stripe Connect para que el dinero vaya directamente al negocio)
- App móvil PWA para gestión de agenda desde el teléfono
- Google Calendar sync bidireccional

### Medio plazo (año 2)
- Expansión a México y Colombia
- API pública para integración con sistemas de punto de venta (TPV)
- Marketplace de negocios: los clientes finales pueden descubrir negocios cercanos en citalia

### Largo plazo (año 3+)
- Financiación externa o bootstrapping hacia los 100k€ MRR
- Adquisición de negocios complementarios (software TPV para salones, CRM para autónomos)

---

## Repositorio y Documentación Técnica

```
citalia/
├── app/
│   ├── Http/Controllers/
│   │   ├── BookingController.php
│   │   ├── BusinessController.php
│   │   └── PublicBookingController.php
│   ├── Jobs/
│   │   └── SendBookingReminder.php
│   ├── Models/
│   │   ├── Booking.php
│   │   ├── Business.php
│   │   ├── Employee.php
│   │   └── Service.php
│   └── Services/
│       ├── AvailabilityService.php
│       └── WhatsAppService.php
├── resources/views/
│   ├── dashboard/
│   ├── booking/          # Vistas públicas de reserva (Blade)
│   └── components/
├── database/migrations/
├── routes/
│   ├── web.php
│   └── api.php           # Para el widget embebible
└── config/
    └── citalia.php
```

---

*Documento generado: 2026 — citalia v1.0 Project Brief*

