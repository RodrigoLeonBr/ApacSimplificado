# Pasta de Imagens do Sistema

Esta pasta contém imagens utilizadas pelo sistema, como:

- Logo do SUS
- Ícones personalizados
- Imagens de documentos/relatórios
- Outros recursos visuais

## Estrutura Recomendada

```
public/assets/images/
├── logo/
│   └── sus-logo.png          # Logo oficial do SUS
├── icons/
│   └── *.svg, *.png          # Ícones personalizados
└── docs/
    └── *.png, *.jpg          # Imagens de documentos/relatórios
```

## Uso

Para usar imagens nesta pasta, utilize o `UrlHelper::asset()`:

```php
<?php use App\Utils\UrlHelper; ?>
<img src="<?= UrlHelper::asset('assets/images/logo/sus-logo.png') ?>" alt="Logo SUS">
```

