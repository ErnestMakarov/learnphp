# Vastavus Tahvli ülesannetele

Kontrolli alus: kasutaja edastatud Tahvli kirjeldused ning õpetaja
[TA24B backend-materjalid](https://github.com/Kasparsu/TA24B/tree/main/backend).
See tabel kirjeldab koodi ja demonstratsiooni, mitte õpetaja antud hinnet.

## 02.10 — Technology lehe lisamine (X)

| Nõue | Teostus | Kuidas näidata |
| --- | --- | --- |
| Oma vaade, samad partial'id nagu U.S. lehel | `views/tech.php` ja `views/us.php` | Mõlemad kasutavad header, posts, sidebar ja footer partial'e |
| Controller'i meetod ja oma postitused | `PublicController::tech()` | Kaks tehnoloogiateemalist postitust |
| Marsruut | `routes.php`: GET `/tech` | Ava `/tech`, ka query string'iga |
| Töötav menüülink | `views/partials/nav.php` | Vajuta Technology |

## 11.11 — Kasutajate CRUD (X)

| Nõue | Teostus | Kuidas näidata |
| --- | --- | --- |
| UsersController ja `/admin/users` | `src/Controllers/UsersController.php`, `routes.php` | Admin menüüs Manage users |
| Nimekiri | `views/users/index.php` | ID, nimi, e-post, roll, loomise/muutmise aeg, tegevused |
| Ühe kasutaja vaatamine | `view()`, `views/users/view.php` | View nupp |
| Lisamine | `create()`, `store()`, `views/users/create.php` | New user ja Save user |
| Muutmine, sh parool | `edit()`, `update()`, `views/users/edit.php` | Muuda nimi/e-post/parool; tühi parool jätab vana alles |
| Kustutamine | `delete()`, POST-vorm | Delete ja kinnitamine; enda kontot kustutada ei saa |
| Parooli räsi ei ole nimekirjas | Mallides puudub parooli väljund | Räsi ei kuvata ka detailvaates |
| Mitu commit'i | Olemasolevad eraldi CRUD commit'id + uuenduse eraldi commit'id | `git log --oneline` |
| Giti lingi esitamine | Õpilase tegevus Teamsis | Esita https://github.com/ErnestMakarov/learnphp |

## Tunnis käsitletud alus

| Teema | Kus see projektis on |
| --- | --- |
| Andmetüübid, massiivid, funktsioonid, kordused, rekursioon | `exercises/basics.php`, rakenduse massiivid ja tsüklid |
| Klassid, pärimine, nähtavus, interface ja trait | `Model`, `User`, `Article`; eraldi varaste tundide näited `exercises/basics.php` |
| Bootstrap ja partial'id | `views/partials/`, avalehe hero/featured; U.S. ja Tech ilma nendeta |
| Autoload ja nimeruumid | Composer PSR-4: `App` → `src/` |
| Route, Router, GET ja POST | `src/Route.php`, `src/Router.php`, `routes.php` |
| view, compact ja extract | `helpers.php`, `PublicController` |
| Vormi näide | GET/POST `/forms` ja `views/forms.php` |
| PDO, DB, fetch-režiim, mudelid | `src/DB.php`, `PDO::FETCH_CLASS`, abstraktne `Model` |
| MariaDB/Docker ja SQL | `docker-compose.yml`, `tables.sql`; SQLite on lihtsam kohalik alternatiiv |
| Artiklite CRUD ja tabel | `ArticlesController`, `views/articles`, `/admin/articles`; `/admin/posts` alias |
| Küpsised ja sessioonid | `public/index.php`, `AuthController`, HttpOnly sessiooniküpsis |
| Registreerimine, login, logout, nimi päises | `AuthController`, auth-vaated, Bootstrap dropdown päises |
| Paroolide räsimine ja login-vea parandus | `password_hash`, tingimus `!password_verify(...)` |
| Composer, PSR, Packagist | `composer.json`, `composer.lock`, `symfony/var-dumper`, `vendor/autoload.php` |

Üksikud tunni katsetused ei ole kõik avalikud rakenduse funktsioonid. Näiteks hashcat/MD5
näidised selgitasid paroolide räsimise vajadust; rakendus kasutab bcrypti. PSR-1/4/12
on käsitletud standardid, mitte eraldi hinnatav veebileht. Laravel on teine projekt.

## Näitamine õpetajale (umbes 5 minutit)

1. Käivita projekt README järgi ja näita Technology linki ning kahte postitust.
2. Näita `tech()` meetodit, route'i, vaadet ja kasutatud partial'e.
3. Logi administraatorina sisse. Loo testkasutaja ja ava tema detailvaade.
4. Muuda nime/e-posti, jättes parooli tühjaks; seejärel vaheta parool eraldi.
5. Näita, et parooli/räsi ei väljastata. Kustuta testkasutaja.
6. Ava privaatses aknas Register, loo tavakasutaja ja näita admini ligipääsu keelamist.
7. Näita `git log --oneline`, `composer.json` ja käivita `php tests/smoke.php`.

Esimese perioodi X tähendab, et kõik vajalikud tööd ei ole tehtud või positiivselt hinnatud.
See ei ole kolmas programmeerimisülesanne. Technology ja CRUD link tuleb esitada ning
õpetaja peab tööd üle vaatama; koodi olemasolu ei muuda Tahvlit automaatselt.
