## Префикс

Задаётся один раз в начале проекта и не меняется. Спроси у пользователя если не знаешь. Никогда не хардкодь skb_, wpf_ или любой другой — только через константы.

В functions.php (единственное место определения):
  define( 'THEME_PREFIX',  'skb'  );   без подчёркивания, для JS/CSS
  define( 'THEME_PREFIX_', 'skb_' );   с подчёркиванием, для PHP/хуков/опций

Форматы:
- PHP функции, хуки, опции, мета — {prefix}_ → skb_get_courses()
- CSS-классы — {prefix}- → .skb-card
- JS переменные/объекты — {PREFIX} camelCase → skbData, skbForms
- ACF group key — group_{prefix}_{name} → group_skb_contacts
- ACF field key — field_{prefix}_{name} → field_skb_price
- CPT slug — {prefix}_{name} → skb_course

В коде всегда THEME_PREFIX_ вместо строки. get_option( THEME_PREFIX_ . 'settings' ) — OK. get_option( 'skb_settings' ) — только в define().

## Карта базы знаний

Полный индекс: WP_Stack.md

- Старт проекта — Core/Theme_Init.md (константы, структура папок, порядок require_once, чеклист)
- Страница с уникальным дизайном — Core/Page_Templates.md (Pattern A)
- ACF поля — ACF/ACF_Pro.md. JSON sync — Core/Page_Templates.md, раздел ACF JSON Sync
- REST / AJAX — API/REST_API.md. Новые проекты — REST (register_rest_route), возврат HTML — admin-ajax.php
- Формы и заявки — Forms/Forms_Notifications.md (CPT-логи, Email, Telegram, Bitrix24, bulk delete)
- Настройки темы (телефон, соцсети, скрипты) — Forms/Theme_Settings.md. Хелпер: {prefix}_get_setting()
- Безопасность — Security/Security.md (нонсы, санитизация, заголовки) и Security/Spam_Protection.md (rate limit, honeypot, timing)
- Навигация / меню — Core/Navigation.md
- Изображения — Frontend/Images.md (wp_get_attachment_image, WebP, srcset, lazy/eager)
- SEO — Frontend/SEO.md (Open Graph, Schema.org, canonical)
- Производительность — Performance/Performance.md (кеш, Core Web Vitals, LCP, CLS, defer)
- CSS — Frontend/CSS.md (Variables, BEM, Grid, @layer, Container Queries)
- Доступность — Frontend/Accessibility.md (ARIA, focus trap, skip link, keyboard nav)
- Cookie banner — Frontend/Cookies.md (GDPR, server-side render, без CLS)
- Cron — Core/WP_Cron.md (wp_schedule_event, ротация логов)
- Отладка и деплой — Core/Debug_Deploy.md (WP_DEBUG, логи, WP-CLI, чеклист)

## Создание страницы

Структура (Pattern A):
- templates/page-{slug}.php — чистый шаблон, только get_template_part()
- template-parts/sections/{slug}/*.php — секции, {prefix}_field() внутри
- inc/acf/page-{slug}.php — PHP bootstrap группы ACF (регистрируется на acf/init)
- acf-json/group_{prefix}_{slug}.json — генерируется ACF UI

Порядок:
1. templates/page-{slug}.php с Template Name, только get_template_part().
   В выпадашке «Шаблон» появляется сам по заголовку Template Name — functions.php НЕ трогаем.
2. template-parts/sections/{slug}/*.php — {prefix}_field() внутри.
3. inc/acf/page-{slug}.php — bootstrap с group/field keys, регистрация на acf/init.
   Подхватывается авто-загрузчиком inc/acf.php (glob inc/acf/*.php) — require вручную НЕ нужен.
4. CSS в assets/css/components/{prefix}-{slug}.css + @import в assets/css/style.css (layer components).

Добавление страницы = только файлы в теме (или в дочерней теме клиента). Ядро и
functions.php не редактируются. Никакого массива $acf_pages нет.

Полный сценарий — Core/Page_Templates.md, раздел «ИНСТРУКЦИЯ ДЛЯ АГЕНТОВ».

## CPT (кастомные типы записей)

Регистрация типа — PHP-модуль, поля — ACF UI/JSON. Не регистрировать CPT через
ACF UI: ключи-хеши ломают конвенцию и тип зависит от плагина.

Структура (drop-in):
- inc/cpt.php — фабрика wpf_register_cpt() + авто-загрузка inc/cpt/*.php (ядро + child)
- inc/cpt/{name}.php — сам тип, регистрируется на init через {prefix}_register_cpt()
- поля — ACF UI, location: post_type == {prefix}_{name}, JSON в acf-json/

Включить тип = положить inc/cpt/{name}.php. Отключить = удалить файл. Не на каждом
проекте есть портфолио/услуги — поэтому типы опциональны и не живут в ядре жёстко.

Фабрика префиксует ключ ({prefix}_{name}) и даёт дефолты (public, show_in_rest,
has_archive, supports, чистый URL-slug). Пример — inc/cpt/portfolio.php.
Шаблоны CPT по Pattern A: archive-{prefix}_{name}.php / single-{prefix}_{name}.php
(только get_template_part), разметка в template-parts/sections/{name}/.

После добавления/удаления CPT — пересохранить Настройки → Постоянные ссылки (сброс rewrite).

## Тиражирование (ядро + дочерние темы)

Ядро (родительская тема) обновляется одинаково у всех клиентов и не редактируется
под проект. Клиентское — в дочерней теме: page-шаблоны, секции, drop-in модули
inc/cpt/*.php и inc/acf/*.php, CSS. Авто-загрузчики (inc/cpt.php, inc/acf.php)
читают и ядро, и дочернюю тему. ACF сохраняет JSON в дочернюю тему (save_json),
грузит из обеих (load_json) — правки клиента переживают обновление ядра.

## Критические правила

PHP

Каждый файл начинается с:
  declare(strict_types=1);
  if ( ! defined( 'ABSPATH' ) ) exit;

GET из ACF — только через хелпер с фоллбэком ({prefix}_field, определён в inc/template-functions.php):
  $value = {prefix}_field( '{prefix}_price', $post_id, 0 );
get_field() напрямую — без фоллбэка и без проверки function_exists, не использовать.

POST в admin — всегда wp_unslash():
  $raw = wp_unslash( $_POST[ THEME_PREFIX_ . 'opt' ] ?? [] );
Без wp_unslash слэши накапливаются на каждом сохранении.

Нормализация переносов строк (textarea):
  $code = preg_replace( '/\R/u', "\n", $code );
Не использовать str_replace("\r","\n", str_replace("\r\n",...)) — \r\n превращается в \n\n.

Вывод — всегда экранировать:
  esc_html(), esc_url(), esc_attr(), wp_kses_post() для HTML.

ACF

Регистрация группы — только PHP или JSON, не UI-экспорт. Ключи:
  group key:  group_{prefix}_{entity}
  field key:  field_{prefix}_{entity}_{name}
  name:       {prefix}_{entity}_{name}

Привязка page-template:
  'location' => [[[ 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/page-{slug}.php' ]]]

JS

Данные из PHP — только через глобальный объект (wp_add_inline_script):
  const url = {PREFIX}Data.restUrl;
Хардкод URL запрещён.

Fetch к REST — всегда с нонсом:
  headers: { 'X-WP-Nonce': {PREFIX}Data.nonce }

GET — без Content-Type (иначе CORS preflight).
POST — с 'Content-Type': 'application/json'.

CSS

BEM строго: .{prefix}-block__element--modifier
Цвета/отступы — только через variables.css (переменные с префиксом --{prefix}-):
  color: var(--{prefix}-color-primary);  OK
  color: #2563eb;                        только внутри variables.css
Inline style= во фронте запрещён (искл.: HTML-письма и экраны админки — там CSS-слой не грузится).
Новый CSS-компонент — @import в assets/css/style.css, layer(components).

## Чего делать нельзя

- get_field() в templates/*.php — шаблон должен быть чистым
- Хардкод префикса в строках (кроме define) — рефакторинг сломает всё
- Content-Type: application/json на GET — CORS preflight без причины
- str_replace на \r и \r\n подряд — даёт двойные строки
- $_POST без wp_unslash — слэши накапливаются
- display:none для honeypot — боты игнорируют
- Редактировать ACF-группы на prod — JSON и БД разойдутся
- Регистрировать CPT через ACF UI — ключи-хеши и зависимость от плагина; CPT — PHP-модулем в inc/cpt/
- Редактировать functions.php при добавлении страницы/CPT — всё подхватывается авто-загрузчиками
- Inline style= во фронте (секции, шаблоны) — только классы + variables.css
- wp_, acf_, post_ как префикс — зарезервированы

## Быстрые ответы

Телефон/email/адрес в шаблоне:
  $phone = {prefix}_get_setting( 'contact_phone' );
Настраивается в WP Admin → Настройки темы → Контакты.

Подключение PHP-файла — в functions.php через require_once, в правильном порядке (Core/Theme_Init.md):
  require_once THEME_DIR . '/inc/my-module.php';

Хуки регистрируются внутри подключаемых файлов, не в functions.php. В functions.php — только define и require_once.

jQuery — нет, vanilla JS. Если WP-компонент требует, подключается через wp_enqueue_script с зависимостью ['jquery'], но своего кода на jQuery не пишем.

Проверка ACF на фронте:
  if ( function_exists( 'get_field' ) ) { ... }
Или через хелпер, он проверяет сам: {prefix}_field( 'key', $post_id, $default ).
