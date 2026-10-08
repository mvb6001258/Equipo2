import 'package:flutter/material.dart';

void main() => runApp(const AgroTraceApp());

class AgroTraceApp extends StatelessWidget {
  const AgroTraceApp({super.key});

  @override
  Widget build(BuildContext context) {
    const seed = Color(0xFF1E6B4E);

    return MaterialApp(
      title: 'AgroTrace',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: seed,
          brightness: Brightness.light,
          surface: const Color(0xFFF8FAF7),
        ),
        scaffoldBackgroundColor: const Color(0xFFF3F6F1),
        inputDecorationTheme: const InputDecorationTheme(
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.all(Radius.circular(16)),
            borderSide: BorderSide(color: Color(0xFFDDE5DF)),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.all(Radius.circular(16)),
            borderSide: BorderSide(color: Color(0xFFDDE5DF)),
          ),
        ),
      ),
      home: const TraceabilityPage(),
    );
  }
}

class TraceabilityPage extends StatefulWidget {
  const TraceabilityPage({super.key});

  @override
  State<TraceabilityPage> createState() => _TraceabilityPageState();
}

class _TraceabilityPageState extends State<TraceabilityPage> {
  final List<ProductTrace> _products = demoProducts;
  late ProductTrace _selectedProduct;
  ProductTrace? _visibleTrace;
  bool _isSearching = false;

  @override
  void initState() {
    super.initState();
    _selectedProduct = _products.first;
  }

  Future<void> _searchTraceability() async {
    setState(() {
      _isSearching = true;
      _visibleTrace = null;
    });

    await Future<void>.delayed(const Duration(milliseconds: 900));
    if (!mounted) return;

    setState(() {
      _isSearching = false;
      _visibleTrace = _selectedProduct;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: CustomScrollView(
          slivers: [
            SliverToBoxAdapter(child: _buildHeader()),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(20, 12, 20, 36),
              sliver: SliverToBoxAdapter(
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 720),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        _buildSearchCard(),
                        const SizedBox(height: 20),
                        AnimatedSwitcher(
                          duration: const Duration(milliseconds: 350),
                          child: _isSearching
                              ? const _SearchingCard(key: ValueKey('loading'))
                              : _visibleTrace == null
                              ? const _WelcomeCard(key: ValueKey('welcome'))
                              : _TraceResult(
                                  key: ValueKey(_visibleTrace!.lotCode),
                                  product: _visibleTrace!,
                                ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF174F3B), Color(0xFF2D7D55)],
        ),
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
      ),
      padding: const EdgeInsets.fromLTRB(24, 20, 24, 26),
      child: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 720),
          child: Row(
            children: [
              Container(
                width: 54,
                height: 54,
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.14),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: const Icon(
                  Icons.eco_rounded,
                  color: Colors.white,
                  size: 30,
                ),
              ),
              const SizedBox(width: 14),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'AgroTrace',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 25,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    SizedBox(height: 2),
                    Text(
                      'Trazabilidad agrícola con blockchain',
                      style: TextStyle(color: Color(0xFFD4E9DE), fontSize: 14),
                    ),
                  ],
                ),
              ),
              const _NetworkBadge(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSearchCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        boxShadow: const [
          BoxShadow(
            color: Color(0x120E3A29),
            blurRadius: 24,
            offset: Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Consultar producto',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 5),
          Text(
            'Selecciona un producto para consultar su historial verificado.',
            style: TextStyle(color: Colors.grey.shade600, height: 1.4),
          ),
          const SizedBox(height: 18),
          DropdownButtonFormField<ProductTrace>(
            key: const Key('productSelector'),
            isExpanded: true,
            initialValue: _selectedProduct,
            decoration: const InputDecoration(
              labelText: 'Producto',
              prefixIcon: Icon(Icons.inventory_2_outlined),
            ),
            items: _products
                .map(
                  (product) => DropdownMenuItem(
                    value: product,
                    child: Text(
                      '${product.name} · ${product.lotCode}',
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                )
                .toList(),
            onChanged: _isSearching
                ? null
                : (product) {
                    if (product == null) return;
                    setState(() {
                      _selectedProduct = product;
                      _visibleTrace = null;
                    });
                  },
          ),
          const SizedBox(height: 14),
          SizedBox(
            height: 52,
            child: FilledButton.icon(
              key: const Key('searchButton'),
              onPressed: _isSearching ? null : _searchTraceability,
              icon: const Icon(Icons.search_rounded),
              label: Text(
                _isSearching
                    ? 'Consultando blockchain...'
                    : 'Consultar trazabilidad',
                style: const TextStyle(fontWeight: FontWeight.w700),
              ),
              style: FilledButton.styleFrom(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(15),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _NetworkBadge extends StatelessWidget {
  const _NetworkBadge();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 7),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(20),
      ),
      child: const Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.circle, color: Color(0xFF76E0A4), size: 9),
          SizedBox(width: 6),
          Text(
            'Red activa',
            style: TextStyle(color: Colors.white, fontSize: 12),
          ),
        ],
      ),
    );
  }
}

class _WelcomeCard extends StatelessWidget {
  const _WelcomeCard({super.key});

  @override
  Widget build(BuildContext context) {
    return _InfoCard(
      key: key,
      child: Column(
        children: [
          Container(
            width: 68,
            height: 68,
            decoration: const BoxDecoration(
              color: Color(0xFFE7F2EB),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.qr_code_scanner_rounded,
              size: 34,
              color: Color(0xFF1E6B4E),
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'Conoce el recorrido de tus alimentos',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 8),
          Text(
            'Cada etapa queda registrada para ofrecer información transparente y confiable.',
            textAlign: TextAlign.center,
            style: TextStyle(color: Colors.grey.shade600, height: 1.45),
          ),
        ],
      ),
    );
  }
}

class _SearchingCard extends StatelessWidget {
  const _SearchingCard({super.key});

  @override
  Widget build(BuildContext context) {
    return _InfoCard(
      key: key,
      child: Column(
        children: [
          SizedBox(
            width: 36,
            height: 36,
            child: CircularProgressIndicator(strokeWidth: 3),
          ),
          SizedBox(height: 18),
          Text(
            'Verificando registros...',
            style: TextStyle(fontSize: 17, fontWeight: FontWeight.w700),
          ),
          SizedBox(height: 6),
          Text('Consultando la información almacenada en blockchain.'),
        ],
      ),
    );
  }
}

class _InfoCard extends StatelessWidget {
  const _InfoCard({required this.child, super.key});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(26),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: const Color(0xFFE1E8E3)),
      ),
      child: child,
    );
  }
}

class _TraceResult extends StatelessWidget {
  const _TraceResult({required this.product, super.key});

  final ProductTrace product;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFFFFFBF2), Color(0xFFF0F7EE)],
            ),
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: const Color(0xFFDDE8D9)),
          ),
          child: Row(
            children: [
              Container(
                width: 62,
                height: 62,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Center(
                  child: Text(
                    product.emoji,
                    style: const TextStyle(fontSize: 34),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      product.name,
                      style: const TextStyle(
                        fontSize: 21,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Lote ${product.lotCode}  ·  ${product.origin}',
                      style: TextStyle(color: Colors.grey.shade700),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 7,
                ),
                decoration: BoxDecoration(
                  color: const Color(0xFFDDF3E5),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      Icons.verified_rounded,
                      color: Color(0xFF167547),
                      size: 17,
                    ),
                    SizedBox(width: 5),
                    Text(
                      'Verificado',
                      style: TextStyle(
                        color: Color(0xFF165C3B),
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 22),
        const Text(
          'Historial del producto',
          style: TextStyle(fontSize: 19, fontWeight: FontWeight.w800),
        ),
        const SizedBox(height: 14),
        Container(
          padding: const EdgeInsets.fromLTRB(18, 20, 18, 8),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: const Color(0xFFE1E8E3)),
          ),
          child: Column(
            children: [
              for (var i = 0; i < product.steps.length; i++)
                _TimelineItem(
                  step: product.steps[i],
                  isLast: i == product.steps.length - 1,
                ),
            ],
          ),
        ),
        const SizedBox(height: 14),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.lock_outline_rounded,
              size: 15,
              color: Colors.grey.shade600,
            ),
            const SizedBox(width: 6),
            Flexible(
              child: Text(
                'Registro inmutable: ${product.blockHash}',
                overflow: TextOverflow.ellipsis,
                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
              ),
            ),
          ],
        ),
      ],
    );
  }
}

class _TimelineItem extends StatelessWidget {
  const _TimelineItem({required this.step, required this.isLast});

  final TraceStep step;
  final bool isLast;

  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 42,
            child: Column(
              children: [
                Container(
                  width: 36,
                  height: 36,
                  decoration: const BoxDecoration(
                    color: Color(0xFFE4F2E9),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    step.icon,
                    color: const Color(0xFF1E6B4E),
                    size: 20,
                  ),
                ),
                if (!isLast)
                  Expanded(
                    child: Container(
                      width: 2,
                      margin: const EdgeInsets.symmetric(vertical: 5),
                      color: const Color(0xFFD5E5DA),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Padding(
              padding: EdgeInsets.only(bottom: isLast ? 18 : 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          step.title,
                          style: const TextStyle(
                            fontWeight: FontWeight.w800,
                            fontSize: 15,
                          ),
                        ),
                      ),
                      Text(
                        step.date,
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey.shade600,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 5),
                  Text(
                    step.description,
                    style: TextStyle(color: Colors.grey.shade700, height: 1.4),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    step.location,
                    style: const TextStyle(
                      color: Color(0xFF337456),
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class ProductTrace {
  const ProductTrace({
    required this.name,
    required this.emoji,
    required this.lotCode,
    required this.origin,
    required this.blockHash,
    required this.steps,
  });

  final String name;
  final String emoji;
  final String lotCode;
  final String origin;
  final String blockHash;
  final List<TraceStep> steps;
}

class TraceStep {
  const TraceStep({
    required this.title,
    required this.date,
    required this.description,
    required this.location,
    required this.icon,
  });

  final String title;
  final String date;
  final String description;
  final String location;
  final IconData icon;
}

const demoProducts = <ProductTrace>[
  ProductTrace(
    name: 'Quinua Real Orgánica',
    emoji: '🌾',
    lotCode: 'QR-2026-0142',
    origin: 'Uyuni, Potosí',
    blockHash: '0x8F2A...C19D',
    steps: [
      TraceStep(
        title: 'Siembra',
        date: '12 Ene 2026',
        description: 'Semilla certificada y cultivo orgánico registrado.',
        location: 'Comunidad Chacala · Uyuni',
        icon: Icons.grass_rounded,
      ),
      TraceStep(
        title: 'Cosecha',
        date: '28 May 2026',
        description: 'Cosecha manual con control de humedad del 11,8%.',
        location: 'Parcela QR-42 · Uyuni',
        icon: Icons.agriculture_rounded,
      ),
      TraceStep(
        title: 'Procesamiento',
        date: '02 Jun 2026',
        description: 'Lavado, secado y clasificación del grano.',
        location: 'Planta Andina · Challapata',
        icon: Icons.factory_outlined,
      ),
      TraceStep(
        title: 'Distribución',
        date: '06 Jun 2026',
        description: 'Lote sellado y enviado al punto de venta.',
        location: 'Centro logístico · La Paz',
        icon: Icons.local_shipping_outlined,
      ),
    ],
  ),
  ProductTrace(
    name: 'Café de Altura',
    emoji: '☕',
    lotCode: 'CA-2026-0087',
    origin: 'Caranavi, La Paz',
    blockHash: '0x3C71...A4E8',
    steps: [
      TraceStep(
        title: 'Cultivo',
        date: '08 Feb 2026',
        description: 'Cultivo bajo sombra a 1.650 metros de altura.',
        location: 'Finca El Mirador · Caranavi',
        icon: Icons.park_outlined,
      ),
      TraceStep(
        title: 'Recolección',
        date: '18 Jun 2026',
        description: 'Selección manual de cerezas maduras.',
        location: 'Finca El Mirador · Caranavi',
        icon: Icons.agriculture_rounded,
      ),
      TraceStep(
        title: 'Tostado',
        date: '24 Jun 2026',
        description: 'Tostado medio y control de calidad aprobado.',
        location: 'Tostaduría Yungas · La Paz',
        icon: Icons.local_fire_department_outlined,
      ),
      TraceStep(
        title: 'Empaque',
        date: '25 Jun 2026',
        description: 'Empaque hermético con válvula de frescura.',
        location: 'Tostaduría Yungas · La Paz',
        icon: Icons.inventory_2_outlined,
      ),
    ],
  ),
  ProductTrace(
    name: 'Cacao Amazónico',
    emoji: '🍫',
    lotCode: 'CC-2026-0031',
    origin: 'Riberalta, Beni',
    blockHash: '0xB915...72F0',
    steps: [
      TraceStep(
        title: 'Cosecha',
        date: '04 Mar 2026',
        description: 'Mazorca madura cosechada por productores locales.',
        location: 'Asociación Amazónica · Riberalta',
        icon: Icons.eco_outlined,
      ),
      TraceStep(
        title: 'Fermentación',
        date: '05 Mar 2026',
        description: 'Fermentación controlada durante seis días.',
        location: 'Centro de acopio · Riberalta',
        icon: Icons.science_outlined,
      ),
      TraceStep(
        title: 'Secado',
        date: '13 Mar 2026',
        description: 'Secado solar hasta alcanzar 7% de humedad.',
        location: 'Centro de acopio · Riberalta',
        icon: Icons.wb_sunny_outlined,
      ),
      TraceStep(
        title: 'Comercialización',
        date: '19 Mar 2026',
        description: 'Grano clasificado, empacado y listo para venta.',
        location: 'Mercado nacional · Bolivia',
        icon: Icons.storefront_outlined,
      ),
    ],
  ),
];
