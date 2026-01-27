import "./ptacek.js"

function inicializujOdpocetAukce(){
    const prvekOdpocet = document.getElementById('odpocet-aukce');
    if(!prvekOdpocet) return;

    const casKonceSekundy = parseInt(prvekOdpocet.dataset.casKonce, 10) * 1000;
    const boxPrihazovani = document.getElementById('sazka-box');

    function aktualizujOdpocet(){
        const aktualniCas = Date.now();
        const rozdil = casKonceSekundy - aktualniCas;

        if(rozdil<=0){
            prvekOdpocet.textContent = 'Aukce byla ukončena.';
            if(boxPrihazovani){
                boxPrihazovani.style.display = 'none';
            }
            return;
        }
        const dny = Math.floor(rozdil / (1000*60*60*24));
        const hodiny = Math.floor((rozdil % (1000*60*60*24)) / (1000*60*60));
        const minuty = Math.floor((rozdil % (1000*60*60)) / (1000*60));
        const sekundy = Math.floor((rozdil % (1000*60)) / 1000);

        prvekOdpocet.textContent = `Zbývá: ${dny} dnů, ${hodiny} hodin, ${minuty} minut, ${sekundy} sekund.`;
    }

    aktualizujOdpocet();
    setInterval(aktualizujOdpocet, 1000);
}
function inicializujPocitadloZnakuKomentare(){
    const poleTextu = document.getElementById('text_komentare');
    const pocitadlo = document.getElementById('pocitadlo-znaku');

    if(!poleTextu || !pocitadlo) return;
    function aktualizuj(){
        const delka = poleTextu.value.length;
        pocitadlo.textContent = `${delka} / 500`
    }
    poleTextu.addEventListener('input', aktualizuj);
    aktualizuj();
}
document.addEventListener('DOMContentLoaded', ()=>{
    inicializujOdpocetAukce();
    inicializujPocitadloZnakuKomentare();
});

