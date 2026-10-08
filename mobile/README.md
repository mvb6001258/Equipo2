# AgroTrace

Aplicación demostrativa en Flutter para mostrar la trazabilidad agrícola de un
producto. La consulta a blockchain es simulada y toda la información se guarda
localmente en la aplicación.

## Funcionalidad

- Selección entre quinua, café y cacao.
- Simulación de una consulta a blockchain.
- Historial completo desde el cultivo o cosecha hasta la distribución.
- Datos de lote, origen, estado de verificación y hash demostrativo.

## Ejecutar

```bash
flutter pub get
flutter run
```

Para abrir la versión web:

```bash
flutter run -d chrome
```

## Verificación

```bash
flutter analyze
flutter test
```
