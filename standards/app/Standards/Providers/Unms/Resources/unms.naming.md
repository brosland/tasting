UNMS API naming table
==========================

| UNMS                  | App                     | Type    | Description                                  |
|-----------------------|-------------------------|---------|----------------------------------------------|
| `catalogueNo`         | `catalogueNumber`       | integer | Katalógové číslo normy                       |
| `sdName`              | `code`                  | string  | Označenie normy                              |
| `name`                | `title`                 | string  | Slovenský názov normy                        |
| `enName`              | `englishTitle`          | string  | Anglický názov normy                         |
| `url`                 | `url`                   | string  | URL adresa normy                             |
| `classSeq`            | `classificationCode`    | string  | Triediaci znak                               |
| `dateVyd`             | `publicationDate`       | string  | Dátum vydania (YYYY-MM-DD)                   |
| `dateSchvl`           | `approvalDate`          | string  | Dátum schválenia (YYYY-MM-DD)                |
| `dateUcin`            | `effectiveDate`         | string  | Dátum účinnosti (YYYY-MM-DD)                 |
| `dateZrus`            | `withdrawalDate`        | string  | Dátum zrušenia (YYYY-MM-DD)                  |
| `dateZv`              | `announcementDate`      | string  | Dátum zverejnenia (YYYY-MM-DD)               |
| `language`            | `language`              | string  | Jazyk normy                                  |
| `changes`             | `revisions`             | string  | Zmeny normy                                  |
| `urovSprac`           | `processingLevel`       | string  | Úroveň spracovania                           |
| `isValid`             | `isValid`               | integer | 1 = platná, 0 = zrušená                      |
| `vestnik`             | `journal`               | string  | Vestník                                      |
| `vestnikNote`         | `journalNote`           | string  | Poznámka vo Vestníku                         |
| `standardSubject`     | `description`           | string  | Predmet normy                                |
| `pageCount`           | `pageCount`             | integer | Počet strán                                  |
| `pageFormat`          | `pageFormat`            | string  | Formát normy                                 |
| `govReg`              | `governmentRegulation`  | string  | Nariadenie vlády                             |
| `vestnikHarm`         | `harmonizationNotice`   | string  | Vestník harmonizácie                         |
| `ics`                 | `icsCode`               | string  | ICS kód normy                                |
| `replacedStandard`    | `replacedStandards`     | string  | Nahradené normy                              |
| `standardReplacement` | `replacementStandards`  | string  | Nahradzujúce normy                           |
| `contents`            | `contents`              | string  | Obsah normy                                  |
| `canBuy`              | `isAvailablePrinted`    | integer | 1 = dostupná na predaj v listinnej forme     |
| `canBuyOnline`        | `isAvailableElectronic` | integer | 1 = dostupná na predaj v elektronickej forme |
