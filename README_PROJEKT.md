# Aukční portál (Maturitní projekt)

Tento projekt je webová aplikace pro online aukce vytvořená v rámci maturitní práce. Aplikace je postavena na moderním PHP frameworku Symfony a poskytuje kompletní zázemí pro uživatele k vytváření aukcí, přihazování a správě financí přes integrovanou platební bránu.

## Hlavní funkce systému

* **Autentizace a uživatelé:** Registrace, přihlášení, ověření e-mailu a automatické promazávání neověřených účtů po 3 dnech.
* **Aukční modul:** Vytváření vlastních aukcí, správa aktivních aukcí, detailní výpis a bezpečné přihazování.
* **Finance a Kredity:** Systém virtuálních kreditů s možností reálného dobíjení pomocí platební brány **Stripe**.
* **Komunita a Moderace:** Komentování a hodnocení aukcí, možnost reportovat problémové aukce.
* **Notifikace:** Upozornění uživatelů na klíčové události (přehození nabídky, konec aukce, výhra).
* **Administrace:** Dedikovaný panel pro správu uživatelů, aukcí a řešení reportů (skrývání aukcí, blokace uživatelů).
* **UI/UX:** Tmavý a světlý režim, responzivní design, osobní statistiky uživatele.

## Použité technologie

* **Backend:** PHP 8+, Symfony, Doctrine ORM
* **Databáze:** MySQL / MariaDB
* **Frontend:** Twig, JavaScript, CSS (s podporou dark/light mode)
* **Infrastruktura:** Docker
* **API a služby:** Stripe API pro platby

## Spuštění projektu (Lokální vývoj)

Aplikace je kontejnerizována pomocí Dockeru pro snadné nasazení.

1. Naklonujte repozitář a přejděte do složky projektu.
2. Spusťte Docker prostředí:
   ```bash
   docker compose up -d
3. Aplikace bude dostupná v prohlížeči na adrese:
     http://localhost:8080

## Testování plateb přes Stripe

Platební systém je aktuálně napojen v testovacím režimu. Při dobíjení kreditů se používají **výhradně fiktivní peníze**, žádné reálné platby neprobíhají.

Pro úspěšné otestování platební brány použijte tyto testovací údaje:
* **Číslo karty:** `4242 4242 4242 4242`
* **Datum platnosti:** `12/34` (případně jakékoliv datum v budoucnosti)
* **Bezpečnostní kód (CVC):** `123`
* **Jméno a e-mail:** Zadejte libovolné (vymyšlené) hodnoty

Aby systém dokázal při lokálním spuštění přijmout informaci o úspěšné platbě a připsat uživateli kredity, je nutné naslouchat událostem (webhookům) ze systému Stripe. V novém okně terminálu spusťte:
```bash
    stripe listen --forward-to localhost:8080/stripe/webhook
