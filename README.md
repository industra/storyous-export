# storyous-export
Export účtenek z pokladního systému Storyous "done right"

Tento skript je k dispozici všem uživatelům pokladního systému Storyous, které trápí nekonzistentní a neúplné exporty
účtenek do tvaru, ve kterém je lze použít pro plnohodnotné účetnictví.

Storyous nabízí 3 typy exportu účtenek:

- **CSV soubor** export obsahuje stornované účtenky, rozlišuje platbu kartou / hotově, neobsahuje rozlišení sazeb DPH
- **Flexibee XML** export vynechává stornované účtenky, nerozlišuje platbu kartou / hotově, obsahuje rozlišení sazeb DPH
- **Pohoda XML** jako Flexibee

Výstupem skriptu je CSV soubor obsahující exportované i stornované účtenky, rozlišení typu platby a rozlišení sazby DPH.
