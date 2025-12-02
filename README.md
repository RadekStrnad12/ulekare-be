# uLékaře – BE část aplikace 

Backend jsem psal kompletně sám až na drobnost s řešením CORS politiky na vývojovém prostředí.

Techstack:
* PHP 8.2+ (Symfony Console s doinstalovanými balíčky)
* MySQL

## Instalace
1. nainstalovat závislosti
```sh
composer install
```
2. vytvořit .env podle minimální konfigurace z .env.dist (zejména nastavit cestu k DB)
3. vytvořit databázi
```sh
php bin/console doctrine:database:create
```
4. provést databázové migrace
```sh
php bin/console doctrine:migrations:migrate
```
5. spustit server
```sh
symfony server:start
```

## Popis & architektura
Zadáním bylo vytvořit API server, který bude vracet poznámky, ukládat je, upravovat a mazat. Z důvodu omezené časové dotace na úkol s vyhlídkou toho, že se nebude rozšiřovat a je to jen testovací zadání, připravil jsem opravdu jednoduché API rozhraní: minimální konfiguraci Symfony, pár základních balíčků (ORM, Maker bundle, Serializer).

Základním kamenem je entita Note. Pro číselníkové hodnoty jsem použil výchozí enum třídy, které se potom do DB ukládají jako textový kód. Kromě priority jsem přidal také možnost barevného označení poznámky. 

Aplikace má tři controllery:
1. NoteApiController: ten vrací a upravuje vše, co se týká poznámek. Jednotlivé metody jsou omezeny na metody volání (GET/POST/PATCH/DELETE).
2. ColorApiController a PriorityApiController jsou více méně shodné: vrací pouze seznam číselníkových hodnot pro použití ve formulářích.

Pro zajištění konzistence výstupních dat se entita Note serializuje a je zde třída ApiResponseFactory, která je zodpovědná za tvar dat, která jdou na frontend – aby šly vždy ve stejném tvaru obaleny stejnými metadaty.

Aplikace nyní neřeší vůbec žádnou autorizaci, všechna data jsou dostupná všem. 
