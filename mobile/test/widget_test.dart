import 'package:agro_trace/main.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('muestra la trazabilidad del producto seleccionado', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(390, 844);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.resetPhysicalSize);
    addTearDown(tester.view.resetDevicePixelRatio);

    await tester.pumpWidget(const AgroTraceApp());

    expect(find.text('AgroTrace'), findsOneWidget);
    expect(find.text('Consultar trazabilidad'), findsOneWidget);
    expect(find.text('Historial del producto'), findsNothing);

    await tester.tap(find.byKey(const Key('searchButton')));
    await tester.pump();
    expect(find.text('Verificando registros...'), findsOneWidget);

    await tester.pump(const Duration(milliseconds: 900));
    await tester.pumpAndSettle();

    expect(find.text('Quinua Real Orgánica'), findsWidgets);
    expect(find.text('Historial del producto'), findsOneWidget);
    expect(find.text('Siembra'), findsOneWidget);
    expect(find.text('Distribución'), findsOneWidget);
    expect(find.text('Verificado'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });
}
