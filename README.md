# storyous-export
Export účtenek z pokladního systému Storyous "done right"

Tento skript je k dispozici všem uživatelům pokladního systému Storyous, které trápí nekonzistentní a neúplné exporty
účtenek do tvaru, ve kterém je lze použít pro plnohodnotné účetnictví.

Storyous nabízí 3 typy exportu účtenek:

- **CSV soubor** (export obsahuje stornované účtenky, rozlišuje platbu kartou a platbu hotově, neobsahuje rozlišení sazeb DPH)</li>
- **Flexibee XML** (export vynechává stornované účtenky, nerozlišuje platbu kartou a platbu hotově, obsahuje rozlišení sazeb DPH)</li>
- **Pohoda XMLV (jako Flexibee)</li>

Výstupem skriptu je CSV soubor obsahující exportované i stornované účtenky, rozlišení typu platby a rozlišení sazby DPH.
