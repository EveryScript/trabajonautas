# SICOES

Sistema de extraccion y normalizacion de convocatorias SICOES.

## Requisitos

- Node.js 18 o superior; el scraper utiliza el `fetch` global incluido en Node.

## Archivo principal

- `sicoes.js`: contiene la extraccion, normalizacion y generacion de ficha final.

## Comando habitual

```bash
npm run fichas -- 19/06/2026
```

## Descargar Word desde SICOES

```bash
npm run descargar:words -- 19/06/2026
```

El navegador se abre con el perfil persistente. Si aparece captcha o verificacion, resuelvelo en la ventana y vuelve a la terminal. Los Word se guardan en `entrada/words/19-06-2026`.

## Salida que debes revisar

- `fichas-finales/19-06-2026.json`

Esa es la ficha limpia para UI/API. Los archivos dentro de `salida/` son datos tecnicos de respaldo y depuracion.

## Catalogos sin IA

- `data/bolivia_municipios.json`: municipios por departamento.

## Profesiones demo sin IA

El enriquecimiento de profesiones funciona completamente offline con JSON locales:

- `data/profesiones.json`
- `data/areas.json`
- `data/area_profesion.json`

Si no hay coincidencia exacta/parcial contra esos archivos, `profesiones_detectadas` queda como `[]`.
