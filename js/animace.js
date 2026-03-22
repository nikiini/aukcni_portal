document.addEventListener('DOMContentLoaded', () => {
    // animace karet
    const karty = document.querySelectorAll('.karta');

    karty.forEach((karta, index) => {
        karta.style.opacity = '0';
        karta.style.transform = 'translateY(20px)';

        setTimeout(() => {
            karta.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
            karta.style.opacity = '1';
            karta.style.transform = 'translateY(0)';
        }, index * 120);
    });

    const textPrvkyKaret = document.querySelectorAll('.karta p');

    textPrvkyKaret.forEach((odstavec, index) => {
        odstavec.style.opacity = '0';
        odstavec.style.transform = 'translateY(10px)';

        setTimeout(() => {
            odstavec.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
            odstavec.style.opacity = '1';
            odstavec.style.transform = 'translateY(0)';
        }, index * 80);
    });

    // dark mode
    const prepinace = [
        document.getElementById('dark-mode'),
        document.getElementById('prepinac-rezimu'),
    ].filter(Boolean);

    function ulozRezim() {
        const jeTmavy = document.documentElement.classList.contains('dark');
        localStorage.setItem('rezim-vzhledu', jeTmavy ? 'dark' : 'light');
    }

    const ulozeny = localStorage.getItem('rezim-vzhledu');
    if (ulozeny === 'dark') {
        document.documentElement.classList.add('dark');
    }

    prepinace.forEach((p) => {
        p.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            ulozRezim();
        });
    });


    // filtry podle kategorií
    const tlacitkaKategorie = document.querySelectorAll('.filtr-kategorie');
    const kartyAukci = document.querySelectorAll('.aukce-grid .karta');

    if (tlacitkaKategorie.length > 0) {

        tlacitkaKategorie.forEach(tlacitko => {
            tlacitko.addEventListener('click', () => {

                const vybranaKategorie = tlacitko.dataset.kategorie;

                tlacitkaKategorie.forEach(t => t.classList.remove('aktivni'));
                tlacitko.classList.add('aktivni');

                kartyAukci.forEach(karta => {
                    const kategorieAukce = karta.dataset.kategorie;

                    if (
                        vybranaKategorie === 'vse' ||
                        (kategorieAukce &&
                            kategorieAukce.split(',').includes(vybranaKategorie))
                    ) {
                        karta.style.display = '';
                    } else {
                        karta.style.display = 'none';
                    }
                });

            });
        });
    }

    // filtry sekcí aukcí (Vše, Doporučené, Nově dnes, Končí brzy)
    const sekceTlacitka = document.querySelectorAll('.filtr-button');
    const sekceBloky = document.querySelectorAll('.sekce-aukce');

    if (sekceTlacitka.length > 0 && sekceBloky.length > 0) {
        sekceTlacitka.forEach((tlacitko) => {
            tlacitko.addEventListener('click', () => {
                const cilovaSekce = tlacitko.dataset.sekce;

                // přepnutí aktivního tlačítka
                sekceTlacitka.forEach(t => t.classList.remove('akce-aktivni'));
                tlacitko.classList.add('akce-aktivni');

                // přepnutí viditelné sekce
                sekceBloky.forEach(blok => {
                    const jeCilova = blok.dataset.sekce === cilovaSekce;

                    if (jeCilova) {
                        // zobrazit cílovou sekci
                        blok.classList.remove('skryta-sekce');
                    } else {
                        // skrýt ostatní sekce
                        blok.classList.add('skryta-sekce');
                    }
                });

                // zapsat sekci do URL (?sekce=...)
                const url = new URL(window.location.href);
                url.searchParams.set('sekce', cilovaSekce);
                window.history.replaceState({}, '', url);
            });
        });

        // Po načtení stránky otevře sekci z URL
        const aktivniSekce = new URLSearchParams(window.location.search).get('sekce');

        if (aktivniSekce) {
            const tlacitko = document.querySelector(
                `.filtr-button[data-sekce="${aktivniSekce}"]`
            );

            if (tlacitko) {
                tlacitko.click();
            }
        }
    }


    // filtr notifikací
    const tlacitkaNotifikace = document.querySelectorAll('.filtr-notifikace');

    if (tlacitkaNotifikace.length > 0) {

        tlacitkaNotifikace.forEach(btn => {

            btn.addEventListener('click', function () {

                tlacitkaNotifikace.forEach(b => b.classList.remove('akce-aktivni'));
                this.classList.add('akce-aktivni');

                const typ = this.dataset.typ;
                const notifikace = document.querySelectorAll('.notifikace-polozka');

                notifikace.forEach(n => {
                    if (typ === 'vse' || n.dataset.typ === typ) {
                        n.style.display = 'block';
                    } else {
                        n.style.display = 'none';
                    }
                });

            });

        });
    }

});