# Kirpyklų sistema
## 1. Sprendžiamo uždavinio aprašymas
###   1.1. Sistemos paskirtis
Pagrindinis projekto tikslas yra padėti kirpykloms surasti klientus, kurie norėtų pasinaudoti paslaugomis teikiamomis minėtose kirpyklose dirbančių kirpėjų.
Kirpyklos vadovas, norėdamas naudotis sistema, turės užsiregistruoti. Tai padarius ir administratoriui patvirtinus kirpyklos vadovo registraciją, jis galės pridėti, šalinti kirpyklas, kurioms vadovauja, pateikti bei atnaujinti informaciją apie jas. Kirpėjai, taip pat galės užsiregistruoti. Užsiregistravęs kirpėjas galės nurodyti, kurioje kirpykloje dirba. Kirpyklos vadovui reikės patvirtinti, kad kirpėjas tikrai dirba jam priklausančioje kirpykloje. Atlikus tai, kirpėjas galės įkelti šukuosenų, kurias moka daryti, nuotraukas, nurodyti bei redaguoti šukuosenų kainas ir kitą informaciją. Jeigu kirpėjas nuspręs nebedaryti vienos iš anksčiau darytų šukuosenų, jis galės pašalinti informaciją apie nebedaromą šukuoseną. Potencialiam klientui bus galima filtruoti (pvz. pagal miestą) bei peržiūrėti sistemoje egzistuojančias kirpyklas, matyti visą informaciją apie pasirinktą kirpyklą bei joje dirbančius kirpėjus, peržiūrėti kirpykloje dirbančio kirpėjo daromas šukuosenas.
###   1.2. Funkciniai reikalavimai
Sistemos svečias galės:
- Peržiūrėti sistemoje egzistuojančias kirpyklas;
- Filtruoti sistemoje egzistuojančias kirpyklas (pvz. pagal miestą);
- Peržiūrėti pasirinktos kirpyklos informaciją;
- Matyti pasirinkto kirpėjo daromas šukuosenas;
- Prisiregistruoti prie sistemos.

Kirpėjas galės:
* Prisijungti prie sistemos;
* Atsijungti nuo sistemos;
* Nurodyti, kurioje kirpykloje dirba;
* Pateikti informaciją apie daromas šukuosenas;
* Redaguoti informaciją apie daromas šukuosenas;
* Šalinti nebedaromas šukuosenas;
* Šalinti paskyrą;
* Atnaujinti paskyros informaciją.

Kirpyklos vadovas galės:
- Prisijungti prie sistemos;
- Atsijungti nuo sistemos;
- Pridėti kirpyklas;
- Atnaujinti kirpyklų informaciją;
- Šalinti kirpyklas;
- Patvirtinti, kad kirpėjas tikrai dirba vadovui priklausančioje kirpykloje;
- Pašalinti kirpėjus dirbančius vadovui priklausančioje kirpykloje.

Administratorius galės:
* Prisijungti prie sistemos;
* Atsijungti nuo sistemos;
* Patvirtinti kirpyklos vadovų registracijas.

## 2. Sistemos architektūra
Sistemos sudedamosios dalys:
- Kliento pusė – bus realizuota naudojant Vue.js;
- Serverio pusė – bus realizuota naudojant PHP Laravel;
- Duomenų bazė – MySQL.
Žemiau pavaizduota kuriamos sistemos architektūra pasitelkiant diegimo diagramą.
