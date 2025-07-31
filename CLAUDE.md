# Claude.md

## Project Overview

**Krypto Airdrop Management Portal** je moderný webový portál na správu, prehľad a monitoring airdropov v krypto svete.
Projekt je postavený na **Laravel (PHP 8.2+) + MySQL 8.0+** na backend,
a **HTML5 + Vue.js/Alpine.js + Tailwind CSS** na frontend, s podporou viacjazyčnosti, notifikácií, wallet loginov a AI prekladov.

Portal má **administratívnu** časť (CRUD & manažment obsahu, preklady, konfigurácie)
a **verejnú** časť s pokročilým vyhľadávaním, personalizáciou a UX/UI vychytávkami.

## Tech Stack

- **Backend:** Laravel (PHP 8.2+)
- **Database:** MySQL 8.0+
- **Frontend:** Vue.js/Alpine.js, Vite, Tailwind CSS
- **API Integrations:** OpenAI/ChatGPT (pre preklady), Web3/Ethers.js, @solana/web3.js, @cosmjs/stargate
- **Styling:** Tailwind CSS (preferované, dark/light mode), alebo Bootstrap 5 s custom dark theme
- **Testing:** PHPUnit (backend), Jest/Vitest/Cypress (frontend – odporúčané)
- **Containerization:** Docker, Docker Compose (web, db, redis, mailhog)
- **CI/CD:** Odporúčaný Github Actions workflow (lint, build, test, coverage)
- **Security:** Vstavaná Laravel ochrana + rozšírené bezpečnostné pravidlá

## Directory Structure

```
/src, /app            # Laravel application code (Controllers, Models, ...)
...
/.env, .env.example    # Konfigurácia
```

## Key Features (zhrnutie)

- **Admin rozhranie**: CRUD nad airdropmi, blockchainmi, projektmi, jazykmi, preklady cez ChatGPT API
- **Frontend**: Prehliadanie, filtrovanie, hľadanie, hodnotenie airdropov, grid/list view, dark/light theme
- **Autentifikácia**: E-mail, sociálne siete, crypto wallet (MetaMask, Phantom, Keplr…)
- **Notifikácie**: E-mail, in-app, push (pripravene pre PWA)
- **SEO & Performance**: Sitemap, caching, image optimization, CDN
- **Bezpečnosť**: CSRF, XSS, SQLi protection, GDPR tools
- **Docker development stack**: `docker-compose up -d`

## How To Contribute

- Kód musí byť **plne pokrytý testami** (PHPUnit pre backend, frontend testy podľa potreby)
- Všetky pull requesty musia prejsť cez CI: lint, build, test, coverage
- Funkcie musia byť **testovateľné, izolované, pokryté edge-cases**
- Používať **dependency injection**, nevyužívať globálne stavy ani helpery ak to nie je nutné
- Dodržiavať **PSR-12**, typovanie všade kde je možné, návrhové vzory (Service, Repository, DTO...)

## Running Locally

```bash
cp .env.example .env
docker-compose up -d
./init-fedora.sh       # Init DB, seeders, migrácie
composer install
npm install
npm run dev
```
- Spusti testy:
```bash
./vendor/bin/phpunit
npm run test    # (ak budú frontend testy)
```

## Testing

- **Backend:** Všetky kontroléry, služby, modely musia mať pokrytie testami (Feature aj Unit)
- **Frontend:** Testovať základnú funkcionalitu a komponenty (Vitest/Jest/Cypress – odporúčané)
- **Minimálne 80%+ code coverage** (CI pipeline musí padať pri nižšom pokrytí)
- **Test Naming:** `tests/Feature/*Test.php` pre celky, `tests/Unit/*Test.php` pre triedy/služby
- **Príklady:**
```php
// tests/Feature/AirdropTest.php
public function test_admin_can_create_airdrop(): void {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->post('/admin/airdrops', [...])
        ->assertStatus(201)
        ->assertJsonFragment(['name' => 'Test Airdrop']);
}
```

## Security & Compliance

- SQLi: Laravel Query Builder a Eloquent
- XSS: blade escaping, CSP headers
- CSRF: Laravel CSRF middleware
- Rate limiting: Laravel throttle
- GDPR: data export, right to be forgotten, cookie consent

## Best Practices

- Service Layer medzi Controllerom a Modelom (lepšia testovateľnosť)
- Validácia requestov cez FormRequest triedy
- Používať Laravel Events na notifikácie, logging zmien, async operácie
- Všetky externé API cez repozitárové/služby triedy s rozhraním (interface)
- Používať migrácie & seedre na štart projektu, žiadny hardcode v DB
- Multijazyčnosť: všetky texty v lang files + dynamický obsah prekladateľný cez ChatGPT API

## Claude Tasks & Prompts (príklady)

- "Vygeneruj PHPUnit testy pre CRUD operácie airdropov s validáciou vstupov a edge-case testami"
- "Navrhni API endpoint na správu blockchainov vrátane pokrytia testami"
- "Pridaj možnosť filtrovať airdropy podľa custom fieldov s testami"
- "Vygeneruj migrácie a seedre pre všetky core entity (users, airdrops, blockchains, projects, ...)"
- "Navrhni štruktúru notification systému (e-mail, push, in-app), s testami a interface kontraktmi"
- "Priprav Dockerfile a docker-compose.yml pre produkciu"

---

## References

- Pozri [README.md](README.md), [specifikacia.pdf], [install.md], `.env.example`
- Dodržiavaj špecifikáciu a dizajn zo `specifikacia.pdf`

---

## Contact

- Maintainer: [Meno / kontakt]
- Issues: [link to issues tracker]
