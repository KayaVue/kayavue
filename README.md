# KayaVue

KayaVue is a consumer guide for people in India comparing cosmetic and aesthetic procedures, clinics, and patient stories. It is modeled on the job a site like that does — cost ranges, recovery in plain language, city filters, reviews — and it is not affiliated with any other directory.

This repository is a WordPress demo. Clinic names, doctors, addresses, and reviews are fictional. Nothing here is medical advice. Results vary.

## Run it

Docker and Docker Compose are required.

```sh
./bin/up.sh
```

That copies `.env.example` to `.env` if you do not already have one, starts MySQL and WordPress, installs WordPress, activates the KayaVue theme, and seeds the demo. The site is then at [http://localhost:8080](http://localhost:8080).

Useful URLs after the first start:

| Page | URL |
| --- | --- |
| Home | http://localhost:8080/ |
| A procedure | http://localhost:8080/procedures/fue-hair-transplant/ |
| A clinic | http://localhost:8080/clinics/nira-hair-studio/ |
| A review | http://localhost:8080/reviews/meera-fue-bandra/ |
| A city | http://localhost:8080/city/mumbai/ |
| Procedure + city filter | http://localhost:8080/explore/?procedure=fue-hair-transplant&city=mumbai |
| Admin | http://localhost:8080/wp-admin |

If port 8080 is taken, change `WP_PORT` and `WP_URL` together in `.env`, then run `./bin/up.sh` again.

### Admin login

Local demo only. Change these before any shared or public deploy.

- User: `kayavue`
- Password: `kayavue-demo`
- Email: `demo@kayavue.local`

The values come from `.env` (`WP_ADMIN_USER`, `WP_ADMIN_PASSWORD`, `WP_ADMIN_EMAIL`). `.env.example` documents them. `.env` is gitignored.

### Stop and reset

```sh
docker compose stop
docker compose down -v   # also deletes the database and WordPress files in the Docker volume
```

`down -v` is the way to re-seed from scratch. The seeder is idempotent: running `./bin/up.sh` again will not duplicate posts. To rebuild content in an existing database:

```sh
docker compose --profile setup run --rm --entrypoint wp wpcli kayavue seed --force
```

## Where content lives

Custom types are registered by the must-use plugin, so they stay available even if the theme is switched:

- `wp-content/mu-plugins/kayavue-core.php` loads the plugin
- `wp-content/mu-plugins/kayavue/post-types.php` registers content types
- `wp-content/mu-plugins/kayavue/seed-data.php` is the demo copy
- `wp-content/mu-plugins/kayavue/seed.php` inserts it once

| Type | What it is | URL base |
| --- | --- | --- |
| `kv_procedure` | Procedure guides, fee range, recovery | `/procedures/` |
| `kv_clinic` | Clinic and lead-doctor profiles | `/clinics/` |
| `kv_review` | Stories linked to a procedure and a clinic | `/reviews/` |
| `kv_city` | Taxonomy on clinics and reviews | `/city/mumbai/` |
| `kv_concern` | Taxonomy on procedures (hair, face, eyes…) | `/concern/hair/` |

A clinic stores the procedures it offers as repeated `kv_offers` post meta (procedure IDs). A review stores `kv_procedure_id`, `kv_clinic_id`, a 1–5 `kv_rating`, and `kv_worth` (`yes`, `no`, or `unsure`). The theme turns those into the average and the “worth it for them” share. That share is self-reported. It is not a clinical success rate.

The theme is a classic PHP theme, `wp-content/themes/kayavue`. It does not use a page builder. `theme.json` only shares the palette with the editor. Diagrams are original SVG drawings in `inc/diagrams.php`, not photographs.

Static pages (How it works, About, For clinics, Privacy, Medical disclaimer, Cities, Explore) are ordinary WordPress pages created by the seeder.

## Stack

- WordPress 6.8, PHP 8.2, Apache (`wordpress:6.8-php8.2-apache`)
- MySQL 8.4
- WP-CLI for the one-shot install (`wordpress:cli-php8.2`)

Database credentials live only in `.env` / the compose environment. They are local defaults, not production secrets. Do not commit a real `.env`.

Fonts are Fraunces and Outfit, shipped as WOFF2 under `wp-content/themes/kayavue/assets/fonts/` with their SIL Open Font Licenses. The theme PHP is GPL-2.0-or-later, as WordPress requires.
