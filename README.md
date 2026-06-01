# WP Frame
Тема WordPress по стандарту **WP Stack**: PHP 8.2+, кастомные свойства CSS и BEM, Vanilla JS (нативные ES-модули через `type="module"`, без сборки). Готова к работе с **ACF Pro** для полей заявок.

Документация архитектуры хранится в вашей базе WP Stack (Obsidian).

## Возможности

- Формы через REST (`/wp-json/myt/v1/form/submit`), журнал заявок (CPT), каналы Email / Telegram / Bitrix24  
- Защита форм (rate limit, honeypot, timing token, referer)  
- Страница настроек в админке (каналы, cookies, контакты, соцсети, скрипты в head)  
- Стили в `assets/css/`, скрипты в `assets/js/`, модули в `assets/js/modules/`

## Локальная разработка

```bash
composer install
npm install
composer lint:wpcs    # PHP Coding Standards
composer make-pot     # languages/wp-frame.pot
npm run lint:js       # ES-модули отдаются как есть, сборки нет
npm run lint:css
```

## Лицензия

GNU General Public License v2 or later. См. [LICENSE](LICENSE).
