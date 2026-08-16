# AP Union

> **Een social media platform dat mensen verbindt op basis van ideeën, interesses en gedachten — niet op basis van populariteit of uiterlijkheden.**

AP Union is een backend project voor de cursus Backend Web. Het platform laat gebruikers posts zien van accounts die ze volgen, en biedt een ruimte om vragen te beantwoorden en echte gesprekken te voeren met mensen over deze vragen en hun topics.

---

## Inhoudsopgave

- [Features](#features)
- [Technische stack](#technische-stack)
- [Installatie](#installatie)
- [Standaard admin](#standaard-admin)
- [Databasestructuur](#databasestructuur)
- [Bronvermeldingen](#bronvermeldingen)

---

## Features

### Minimum requirements
- **Login systeem** — Registreren, inloggen, uitloggen, wachtwoord vergeten/resetten, "Remember me"
- **Profielpagina** — Publiek zichtbaar; bevat username, verjaardag, profielfoto, "over mij" tekst en showcase-posts
- **Nieuws** — Admins beheren nieuwsitems (titel, afbeelding, content, publicatiedatum); iedereen kan lezen
- **FAQ** — Gegroepeerd per categorie; admins beheren categorieën en vraag/antwoord-paren
- **Contact** — Contactformulier voor iedereen; admin ontvangt e-mail bij inzending
- **Admin paneel** — Gebruikers, nieuws, FAQ, vragen en contact beheren; andere gebruikers tot admin verheffen of dit intrekken; gebruikers handmatig aanmaken

### Extra features
- **Berichten / Privéchat** — Gebruikers kunnen privégesprekken starten met mensen die ze volgen; afbeeldingen meesturen, reageren op specifieke berichten (reply), gesprekken wissen
- **Volgsysteem met verzoeken** — Profielen zijn privé; volgen vereist een verzoek dat de ontvanger kan accepteren of weigeren; volgers/volgend-overzicht per profiel
- **Posts met media** — Tekst- en/of media-posts (afbeeldingen én video); posts aanmaken en verwijderen
- **Showcase** — Elke gebruiker kiest max. 3 posts die bezoekers van hun (privé-)profiel altijd kunnen zien
- **Comments en antwoorden** — Gebruikers kunnen reageren op posts, inclusief geneste replies
- **Likes** — Posts liken en unliken
- **Vragen & Antwoorden** — Admins en gebruikers kunnen vragen indienen; iedereen kan antwoorden plaatsen, bewerken en bekijken
- **Explore-pagina** — Ontdek andere leden en posts buiten je volglijst
- **Ledenzoek met live suggesties** — Zoek op username, naam of interesse; live JSON-suggesties terwijl je typt
- **Interests** — Gebruikers kiezen max. 6 interesses uit een catalogus van 68 onderwerpen; interesses zijn zichtbaar op profiel en zoekresultaten
- **Notificaties** — 10 types: nieuw volgverzoek, verzoek geaccepteerd, nieuwe volger, like, comment, nieuw bericht, post in review, post verwijderd, beroep beantwoord, contact beantwoord
- **Post review systeem** — Admins kunnen posts verbergen voor review (met reden); auteur wordt automatisch gewaarschuwd via DM; admin kan post herstellen of definitief verwijderen
- **Beroepssysteem (Post Appeals)** — Auteur kan beroep indienen tegen een review of verwijdering; beroep doorloopt meerdere fases (contest → second appeal → after delete); admins beheren beroepen via aparte pagina
- **Admin contactbeheer** — Admins zien alle ingevulde contactformulieren; kunnen rechtstreeks antwoorden via de applicatie (antwoord wordt per e-mail bezorgd)
- **Geautomatiseerde e-mails** — Contactbevestiging voor de inzender, antwoord-e-mail, admin-notificatie bij contactinzending

---

## Technische stack

| Onderdeel | Versie / Tool |
|-----------|--------------|
| PHP | 8.3 |
| Laravel | 13 |
| Authenticatie | Laravel Breeze |
| Frontend | Blade + Tailwind CSS v4 + Vite |
| Database | MySQL / SQLite |
| Tests | PestPHP |
| Styling | Tailwind CSS v4 |

---

## Installatie

### Vereisten
- PHP >= 8.3
- Composer
- Node.js >= 18 & npm
- MySQL of SQLite

### Stappen

```bash
# 1. Kloon de repository
git clone <repository-url>
cd ap-union

# 2. Installeer PHP-dependencies
composer install

# 3. Maak het .env-bestand aan
cp .env.example .env

# 4. Genereer de applicatiesleutel
php artisan key:generate
```

Stel in `.env` je database in:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ap_union
DB_USERNAME=root
DB_PASSWORD=
```

Of voor SQLite:
```dotenv
DB_CONNECTION=sqlite
```

```bash
# 5. Voer de migraties en seeders uit
php artisan migrate:fresh --seed

# 6. Maak de storage-link aan (voor geüploade bestanden)
php artisan storage:link

# 7. Installeer JavaScript-dependencies en compileer assets
npm install
npm run build

# 8. Start de development server
php artisan serve
```

De applicatie is nu bereikbaar op [http://localhost:8000](http://localhost:8000).

> **Development mode** (met hot-reload): open twee terminals en voer uit:
> ```bash
> php artisan serve
> npm run dev
> ```

---

## Standaard admin

Na het uitvoeren van `migrate:fresh --seed` is er automatisch een admin aangemaakt:

| Veld | Waarde |
|------|--------|
| Username | `admin` |
| E-mail | `admin@ehb.be` |
| Wachtwoord | `Password!321` |

---

## Databasestructuur

Het project bevat **32 migraties** en de volgende tabellen:

| Tabel | Beschrijving |
|-------|-------------|
| `users` | Gebruikersaccounts (naam, username, e-mail, verjaardag, profielfoto, about me, is_admin) |
| `posts` | Posts met content, review-status en verwijderstatus |
| `post_media` | Gekoppelde afbeeldingen/video's per post |
| `comments` | Reacties op posts, met ondersteuning voor geneste replies (`parent_id`) |
| `likes` | Likes per post per gebruiker |
| `follows` | Volg-relaties (many-to-many, met `accepted_at` voor verzoekstatus) |
| `conversations` | Privégesprekken tussen twee gebruikers |
| `messages` | Berichten per gesprek (tekst, afbeelding, reply, systeemtype) |
| `notifications` | In-app notificaties |
| `questions` | Vragen van admins of gebruikers |
| `answers` | Antwoorden op vragen |
| `interests` | Catalogus van 68 interesses |
| `interest_user` | Many-to-many koppeling tussen gebruikers en interesses |
| `news` | Nieuwsitems (titel, afbeelding, content, publicatiedatum) |
| `faqs` | Veelgestelde vragen met antwoorden |
| `faq_categories` | Categorieën voor de FAQ |
| `contacts` | Ingevulde contactformulieren |
| `post_appeals` | Beroepen die auteurs indienen bij verwijderde/gereviewde posts |

**Relaties:**
- `User hasMany Post, Comment, Like, Message, Question, Answer, News`
- `User belongsToMany User` (follows — many-to-many)
- `User belongsToMany Interest` (many-to-many)
- `User belongsToMany Conversation` (many-to-many met pivot `last_read_at`, `cleared_at`)
- `Post hasMany Comment, Like, PostMedia, PostAppeal`
- `Conversation hasMany Message`

---

## Tests

Het project bevat **18 feature testbestanden** geschreven met PestPHP:

```bash
php artisan test
# of
composer test
```

Gedekte scenarios: toegangscontrole, admin-supervisie, gebruikersbeheer, contactformulier, FAQ, follow-feed, gastervaring, berichten, nieuws, notificaties, post appeals, posts, profielprivacy, profielupdates, vragen & antwoorden, uploads en validatie.

---

## Bronvermeldingen

> Vul hier je eigen bronnen aan die je tijdens het project hebt gebruikt.

- [Laravel documentatie](https://laravel.com/docs) — Officiële documentatie voor routing, Eloquent, validatie, notificaties, mail en authenticatie
- [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze) — Authenticatie scaffold
- [PestPHP documentatie](https://pestphp.com/docs) — Test framework
- [Tailwind CSS v4 documentatie](https://tailwindcss.com/docs) — Styling
