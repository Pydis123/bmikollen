# BMIKollen

En ren PHP 8.4 MVC-app för vikt-/kcal-/protein-/steg-tracking med multi-user, invite-only, RBAC, DB-sessions, AES-256-GCM-kryptering av vikt och viktrelaterade planvärden, onboarding‑wizard och "Min dag" som kärnvy.

## Krav och stack
- PHP 8.4, MariaDB, Composer
- Router: `nikic/fast-route`
- Miljö: `vlucas/phpdotenv`
- Test: PHPUnit 11
- UI: Tailwind CSS (CDN), Chart.js (CDN)
- Webroot: `/public` (front controller `public/index.php`)

## Installation och Setup

### 1. Vad är "composer install"?
Composer är PHP:s pakethanterare. Appen använder några bibliotek (t.ex. för routing och .env-hantering).
- **Om du har Composer lokalt:** Kör `composer install` i projektroten. Det skapar mappen `vendor/` med alla nödvändiga filer.
- **Om du inte har Composer:** Du kan installera Composer från [getcomposer.org](https://getcomposer.org/). 
- **För Shared Hosting (Loopia m.fl.):** Om du inte kan köra kommandon via SSH på servern, kör `composer install` på din egen dator och ladda sedan upp hela `vendor/`-mappen till servern tillsammans med resten av koden.

### 2. Databas & Tabellprefix
Appen har stöd för tabellprefix (standard: `bmi_`) för att kunna ligga i samma databas som andra applikationer utan krockar.
- **Konfigurera:** Se fältet `DB_PREFIX=bmi_` i din `.env`-fil.
- **Schema:** Om du kör `database/schema.sql` skapas tabellerna med prefixet `bmi_`. Om du ändrar prefixet i `.env` måste du även uppdatera namnen i SQL-filen (eller vice versa).

### 3. Steg-för-steg Setup (Produktion/Loopia)
1. **Filer:** Ladda upp alla mappar (`app`, `public`, `config`, `database`, `scripts`, `views`, `storage`, `vendor`) till din server.
2. **Webroot:** Peka din domän/subdomän till mappen `public/`. (Hos Loopia görs detta i kundzonen under domäninställningar).
3. **.env:** Skapa en fil som heter `.env` i projektroten (samma nivå som `app`-mappen). Använd `.env.example` som mall.
   - Fyll i dina databasuppgifter (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
   - Sätt `DB_PREFIX=bmi_`.
   - Sätt `APP_URL` till din domän (t.ex. `https://bmi.din-sida.se`).
4. **Master Key:** Kör `php scripts/gen_master_key.php` lokalt för att få en nyckel, klistra in den i `.env` som `APP_MASTER_KEY=base64:...`. **Tappa inte bort denna!** Den behövs för att läsa krypterad viktdata.
5. **Databas:** Importera `database/schema.sql` via phpMyAdmin i Loopias kundzon.
6. **Admin:** För att skapa ditt första konto, kör `php scripts/seed_admin.php din-epost@exempel.se ditt-lösenord`. Om du inte kan köra PHP-skript på servern via SSH, kan du köra det lokalt mot din prod-db (genom att tillfälligt sätta prod-uppgifter i din lokala `.env`) eller manuellt lägga in en rad i `bmi_users` och `bmi_user_roles`.

## Installation (lokalt, XAMPP)
1. Klona/töm katalogen och placera projektet i t.ex. `C:/xampp/htdocs/bmikollen`.
2. Kör `composer install` i projektroten.
3. Kopiera `.env.example` till `.env` och uppdatera databasuppgifter.
4. Generera master key (32 bytes) för kryptering:
   - `php scripts/gen_master_key.php` och klistra in raden i `.env`
5. Skapa databas och kör schemat:
   - Skapa DB `bmikollen` (MariaDB)
   - Kör `database/schema.sql`
6. Seeda admin:
   - `php scripts/seed_admin.php admin@bmikollen.test hemligtLösen`
7. Peka Apache/Nginx document root till `.../bmikollen/public`.
8. Surfa till `http://localhost/` och logga in (eller registrera med invite om `ALLOW_PUBLIC_REGISTRATION=0`).

## Säkerhet
- Sessions i DB med egen SessionHandler. Cookies: `httponly`, `samesite=Lax`, `secure` (om https).
- CSRF-skydd på alla POST (hidden `_token`).
- Auth: e‑post + lösenord (Argon2id om möjligt, fallback bcrypt). Login-rate limiting i DB (IP + e‑post). E‑postverifiering (hashade tokens, TTL).
- Invite‑only: admin skapar invites, registrering kräver giltig token (enkelt att slå på publik registrering via `.env`).
- Kryptering: AES‑256‑GCM av daglig vikt och viktrelaterade planvärden (ciphertext + iv + tag som separata kolumner). Master key i `.env` som `APP_MASTER_KEY=base64:...` (exakt 32 bytes rånyckel efter base64‑decode). Logga aldrig hemligheter.
- Loggning: säkerhetshändelser i `storage/logs/app.log`.

## Domänlogik i korthet
- "Min dag" (`/`) visar mål från aktiv plan, dagens loggposter i tidsordning och ett formulär för att lägga till deltaposter: `kcal_delta`, `protein_delta`, `steps_delta` (med tid och label). Heldagssummor beräknas via `SUM` i SQL.
- Dagens vikt (en per datum) lagras krypterad i tabellen `weights`.
- Planer: flera per user, en aktiv i taget. Wizard räknar auto‑kcal med Mifflin–St Jeor + aktivitetsfaktor och intensitets‑underskott enligt din policy. Planändringar auditeras i `plan_audit`.

## Export (multi‑section CSV)
- Endast inloggade, POST med CSRF.
- UI låter dig välja intervall och/eller kryssa datum.
- CSV har två sektioner:

Sektion 1: `DAILY_SUMMARY`
```
date,kcal_total,protein_total,steps_total,weight_kg
2026-02-01,1950,140,8200,72.50
...
```
(tom rad)

Sektion 2: `DAY_LOGS`
```
date,time,kcal_delta,protein_delta,steps_delta,label,source
2026-02-01,08:12:00,450,30,0,Frukost,manual
2026-02-01,12:30:00,650,45,0,Lunch,manual
...
```

## Testning
- PHPUnit 11 konfigurerat i `phpunit.xml`.
- Exempeltest finns: `tests/CryptoTest.php` (round‑trip, unika IV, tamper detection).
- Rek. strategi för fler integrationstester: använd SQLite in‑memory och skapa minitabeller i `setUp()` (tänk på att vissa MySQL‑specifika uttryck som `NOW()` inte funkar i SQLite – justera insättningar i testade klasser eller gör SQL kompatibel i testläget).

## Demodata för Grafer
För att snabbt se hur graferna ser ut med data kan du köra ett demo-skript som skapar en testanvändare med 30 dagars historik och en aktiv plan:
1. Kör `php scripts/seed_demo_data.php test@example.com lösenord123`
2. Logga in med dessa uppgifter och gå till fliken "Grafer".
3. Du kan även ange egna uppgifter som parametrar om du vill. Standardvärden är `test@example.com` och `lösenord123`.

## Mappstruktur
```
/public
  index.php
  .htaccess
/app
  /Core (Config, Container, Database, Request, Response, View, Logger, SessionHandler, Csrf, Crypto, Mailer)
  /Http
    routes.php
    /Middleware (Auth, Admin, Csrf)
    /Controllers (AuthController, DayController, WeightController, OverviewController, ChartController, WizardController, PlanController, AdminController, ExportController)
  /Repositories (User, Plan, Weight, DayLog, Invite, Token, Throttle, Audit)
  /Services (AuthService, DayService)
/database (schema.sql)
/scripts (gen_master_key.php, seed_admin.php)
/storage (logs/cache/sessions)/.gitkeep
/tests (PHPUnit)
/views (layouts, errors, auth, day, overview, charts, profile, admin, export, plans, wizard)
```

## Kända begränsningar (MVP)
- Redigera daglogg är stub (vy finns, uppdatering TODO).
- Password reset och invite‑verifiering vid register är TODO (skelett finns).
- Plan/Week/Charts är enkla summeringar för att komma igång.
- Full IANA‑lista i profil: för nu fritextfält (lagras som IANA‑sträng). Standard `Europe/Stockholm`.

## Kör lokalt – snabbchecklista
1. `composer install`
2. Kopiera `.env.example` → `.env`; generera `APP_MASTER_KEY` via skriptet
3. Skapa DB och kör `database/schema.sql`
4. `php scripts/seed_admin.php <mail> <lösen>`
5. Peka din webserver mot `public/`
6. Logga in och gå till `/wizard` för att skapa första planen

## Deploy till shared hosting
- Ladda upp hela projektet utom `/vendor` om du installerar på servern; annars ladda upp `/vendor` också.
- Sätt `DocumentRoot`/"Webroot" till mappen `public/` (ofta kan detta ställas via kontrollpanel; annars använd `.htaccess`).
- Skapa databasen via hostingens phpMyAdmin och kör `database/schema.sql`.
- Skapa `.env` på servern (sätt korrekta DB‑uppgifter, `APP_URL`, `APP_MASTER_KEY`, `MAIL_DRIVER` etc.).
- Sätt filrättigheter så att `storage/` är skrivbar.
- Rekommenderat: aktivera HTTPS och se till att cookies blir `secure`.

## Säkerhetsnotiser
- Förlora aldrig `APP_MASTER_KEY` – då kan inte viktdata dekrypteras.
- Loggar innehåller aldrig hemligheter, men loggar säkerhetshändelser.
- CSRF krävs på alla POST – formulär inkluderar `_token` automatiskt i vyerna.
- Vid inloggning används `session_regenerate_id(true)`.


## Administration & Inbjudningar

- Endast användare med rollen `admin` kan nå adminsidor (kontrolleras av `AdminMiddleware`).
- Adminsida för inbjudningar: gå till `APP_URL/admin/invites`.
  - Skapa nya inbjudningskoder (valfritt med kopplad e‑post som anteckning).
  - Se status (förbrukad/aktiv) för befintliga koder.
- Navigering: När du är inloggad som admin visas en länk "Admin" i toppmenyn (desktop) och i den nedre mobilnavigeringen. Länken pekar på `/admin/invites`.

### Skapa din första admin

1. Skapa ett konto (eller seeda via `php scripts/seed_admin.php epost lösen`).
2. Koppla admin‑rollen om den inte redan är satt:
```sql
INSERT INTO bmi_user_roles (user_id, role_id)
SELECT u.id, r.id
FROM bmi_users u
JOIN bmi_roles r ON r.name = 'admin'
WHERE u.email = 'din-admin@exempel.se';
```
3. Logga in som admin och öppna `APP_URL/admin/invites`.

### Registrering med invite

- Om `.env` har `ALLOW_PUBLIC_REGISTRATION=0` krävs invite vid registrering.
- Registreringsvyn visar då fältet "Inbjudningskod". Efter lyckad registrering förbrukas koden automatiskt.
