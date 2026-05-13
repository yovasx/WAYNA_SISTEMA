CREATE TABLE "usuarios"(
    "id" SERIAL NOT NULL,
    "nombre_completo" VARCHAR(150) NOT NULL,
    "email" VARCHAR(255) NOT NULL,
    "telefono" VARCHAR(20) NULL,
    "password_hash" VARCHAR(255) NOT NULL,
    "foto_perfil" VARCHAR(500) NULL,
    "estado" VARCHAR(255) CHECK
        (
            "estado" IN('activo', 'suspendido', 'pendiente')
        ) NOT NULL DEFAULT 'pendiente',
        "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
        "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "usuarios" ADD PRIMARY KEY("id");
ALTER TABLE
    "usuarios" ADD CONSTRAINT "usuarios_email_unique" UNIQUE("email");
CREATE TABLE "roles"(
    "id" SERIAL NOT NULL,
    "nombre" VARCHAR(255) CHECK
        (
            "nombre" IN(
                'ADMINISTRADOR',
                'EMPRENDEDOR',
                'COMPRADOR',
                'DONADOR',
                'TURISTA'
            )
        ) NOT NULL
);
ALTER TABLE
    "roles" ADD PRIMARY KEY("id");
ALTER TABLE
    "roles" ADD CONSTRAINT "roles_nombre_unique" UNIQUE("nombre");
CREATE TABLE "usuario_roles"(
    "id" SERIAL NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "rol_id" INTEGER NOT NULL,
    "activo" BOOLEAN NOT NULL DEFAULT TRUE,
    "asignado_en" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "usuario_roles" ADD CONSTRAINT "usuario_roles_usuario_id_rol_id_unique" UNIQUE("usuario_id", "rol_id");
ALTER TABLE
    "usuario_roles" ADD PRIMARY KEY("id");
CREATE INDEX "usuario_roles_usuario_id_index" ON
    "usuario_roles"("usuario_id");
CREATE TABLE "categorias"(
    "id" SERIAL NOT NULL,
    "nombre" VARCHAR(100) NOT NULL,
    "icono" VARCHAR(255) NULL,
    "descripcion" TEXT NULL
);
ALTER TABLE
    "categorias" ADD PRIMARY KEY("id");
ALTER TABLE
    "categorias" ADD CONSTRAINT "categorias_nombre_unique" UNIQUE("nombre");
CREATE TABLE "emprendedores"(
    "id" SERIAL NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "nombre_negocio" VARCHAR(150) NOT NULL,
    "descripcion" TEXT NULL,
    "historia" TEXT NULL,
    "foto_portada" VARCHAR(500) NULL,
    "categoria_id" INTEGER NULL,
    "NIT" VARCHAR(20) NULL,
    "estado" VARCHAR(255) CHECK
        (
            "estado" IN('pendiente', 'activo', 'suspendido')
        ) NOT NULL DEFAULT 'pendiente',
        "aprobado_por" INTEGER NULL,
        "aprobado_en" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
        "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "emprendedores" ADD PRIMARY KEY("id");
ALTER TABLE
    "emprendedores" ADD CONSTRAINT "emprendedores_usuario_id_unique" UNIQUE("usuario_id");
CREATE TABLE "productos"(
    "id" SERIAL NOT NULL,
    "emprendedor_id" INTEGER NOT NULL,
    "nombre" VARCHAR(200) NOT NULL,
    "descripcion" TEXT NULL,
    "categoria_id" INTEGER NULL,
    "precio" DECIMAL(10, 2) NOT NULL,
    "stock" INTEGER NOT NULL,
    "estado_stock" VARCHAR(255) CHECK
        (
            "estado_stock" IN(
                'disponible',
                'ultimas_unidades',
                'agotado'
            )
        ) NOT NULL DEFAULT 'disponible',
        "foto_principal" VARCHAR(500) NULL,
        "qr_codigo" VARCHAR(100) NULL,
        "qr_url" VARCHAR(500) NULL,
        "activo" BOOLEAN NOT NULL DEFAULT TRUE,
        "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
        "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "productos" ADD PRIMARY KEY("id");
CREATE INDEX "productos_emprendedor_id_index" ON
    "productos"("emprendedor_id");
ALTER TABLE
    "productos" ADD CONSTRAINT "productos_qr_codigo_unique" UNIQUE("qr_codigo");
CREATE TABLE "producto_fotos"(
    "id" SERIAL NOT NULL,
    "producto_id" INTEGER NOT NULL,
    "url_foto" VARCHAR(500) NOT NULL,
    "orden" INTEGER NOT NULL
);
ALTER TABLE
    "producto_fotos" ADD PRIMARY KEY("id");
CREATE TABLE "pedidos"(
    "id" SERIAL NOT NULL,
    "codigo" VARCHAR(20) NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "emprendedor_id" INTEGER NOT NULL,
    "estado" VARCHAR(255) CHECK
        (
            "estado" IN(
                'pendiente',
                'confirmado',
                'entregado',
                'cancelado'
            )
        ) NOT NULL DEFAULT 'pendiente',
        "metodo_entrega" VARCHAR(255)
    CHECK
        (
            "metodo_entrega" IN(
                'retiro_tienda',
                'coordinado_emprendedor'
            )
        ) NOT NULL DEFAULT 'retiro_tienda',
        "total" DECIMAL(10, 2) NOT NULL,
        "notas" TEXT NULL,
        "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
        "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "pedidos" ADD PRIMARY KEY("id");
ALTER TABLE
    "pedidos" ADD CONSTRAINT "pedidos_codigo_unique" UNIQUE("codigo");
CREATE INDEX "pedidos_usuario_id_index" ON
    "pedidos"("usuario_id");
CREATE INDEX "pedidos_emprendedor_id_index" ON
    "pedidos"("emprendedor_id");
CREATE TABLE "pedido_items"(
    "id" SERIAL NOT NULL,
    "pedido_id" INTEGER NOT NULL,
    "producto_id" INTEGER NOT NULL,
    "cantidad" INTEGER NOT NULL,
    "precio_unitario" DECIMAL(10, 2) NOT NULL,
    "subtotal" DECIMAL(10, 2) NOT NULL
);
ALTER TABLE
    "pedido_items" ADD PRIMARY KEY("id");
CREATE INDEX "pedido_items_pedido_id_index" ON
    "pedido_items"("pedido_id");
CREATE TABLE "transacciones"(
    "id" SERIAL NOT NULL,
    "referencia_id" INTEGER NOT NULL,
    "referencia_tipo" VARCHAR(255) CHECK
        (
            "referencia_tipo" IN('pedido', 'donacion', 'reserva')
        ) NOT NULL,
        "usuario_id" INTEGER NOT NULL,
        "metodo_pago" VARCHAR(255)
    CHECK
        (
            "metodo_pago" IN('qr', 'nfc', 'microtransaccion')
        ) NOT NULL,
        "monto" DECIMAL(10, 2) NOT NULL,
        "estado" VARCHAR(255)
    CHECK
        (
            "estado" IN(
                'pendiente',
                'completada',
                'fallida',
                'reembolsada'
            )
        ) NOT NULL DEFAULT 'pendiente',
        "codigo_qr" VARCHAR(255) NULL,
        "respuesta_pasarela" jsonb NULL,
        "procesado_en" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
        "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX "transacciones_referencia_id_referencia_tipo_index" ON
    "transacciones"("referencia_id", "referencia_tipo");
ALTER TABLE
    "transacciones" ADD PRIMARY KEY("id");
CREATE TABLE "reservas"(
    "id" SERIAL NOT NULL,
    "codigo" VARCHAR(20) NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "emprendedor_id" INTEGER NOT NULL,
    "tipo" VARCHAR(255) CHECK
        (
            "tipo" IN(
                'producto',
                'taller',
                'experiencia_cultural'
            )
        ) NOT NULL,
        "producto_id" INTEGER NULL,
        "fecha_reserva" DATE NOT NULL,
        "hora_reserva" TIME(0) WITHOUT TIME ZONE NOT NULL,
        "estado" VARCHAR(255)
    CHECK
        (
            "estado" IN(
                'pendiente',
                'confirmada',
                'cancelada',
                'reprogramada'
            )
        ) NOT NULL DEFAULT 'pendiente',
        "qr_acceso" VARCHAR(255) NULL,
        "notas" TEXT NULL,
        "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "reservas" ADD PRIMARY KEY("id");
ALTER TABLE
    "reservas" ADD CONSTRAINT "reservas_codigo_unique" UNIQUE("codigo");
CREATE INDEX "reservas_usuario_id_index" ON
    "reservas"("usuario_id");
CREATE TABLE "donaciones"(
    "id" SERIAL NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "emprendedor_id" INTEGER NULL,
    "monto" DECIMAL(10, 2) NOT NULL,
    "es_anonima" BOOLEAN NOT NULL,
    "mensaje" TEXT NULL,
    "certificado_url" VARCHAR(500) NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "donaciones" ADD PRIMARY KEY("id");
CREATE INDEX "donaciones_usuario_id_index" ON
    "donaciones"("usuario_id");
CREATE TABLE "puntos_donador"(
    "id" SERIAL NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "puntos_total" INTEGER NOT NULL,
    "nivel" VARCHAR(255) CHECK
        ("nivel" IN('bronce', 'plata', 'oro')) NOT NULL DEFAULT 'bronce',
        "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "puntos_donador" ADD PRIMARY KEY("id");
ALTER TABLE
    "puntos_donador" ADD CONSTRAINT "puntos_donador_usuario_id_unique" UNIQUE("usuario_id");
CREATE TABLE "historial_puntos"(
    "id" SERIAL NOT NULL,
    "usuario_id" INTEGER NOT NULL,
    "donacion_id" INTEGER NOT NULL,
    "puntos_ganados" INTEGER NOT NULL,
    "multiplicador" DECIMAL(3, 1) NOT NULL DEFAULT 1,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    "historial_puntos" ADD PRIMARY KEY("id");
CREATE TABLE "insignias"(
    "id" SERIAL NOT NULL,
    "nombre" VARCHAR(100) NOT NULL,
    "descripcion" TEXT NULL,
    "icono" VARCHAR(500) NULL,
    "criterio_json" jsonb NULL
);
ALTER TABLE
    "insignias" ADD PRIMARY KEY("id");
ALTER TABLE
    "insignias" ADD CONSTRAINT "insignias_nombre_unique" UNIQUE("nombre");
ALTER TABLE
    "reservas" ADD CONSTRAINT "reservas_emprendedor_id_foreign" FOREIGN KEY("emprendedor_id") REFERENCES "emprendedores"("id");
ALTER TABLE
    "pedido_items" ADD CONSTRAINT "pedido_items_pedido_id_foreign" FOREIGN KEY("pedido_id") REFERENCES "pedidos"("id");
ALTER TABLE
    "historial_puntos" ADD CONSTRAINT "historial_puntos_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "emprendedores" ADD CONSTRAINT "emprendedores_categoria_id_foreign" FOREIGN KEY("categoria_id") REFERENCES "categorias"("id");
ALTER TABLE
    "productos" ADD CONSTRAINT "productos_emprendedor_id_foreign" FOREIGN KEY("emprendedor_id") REFERENCES "emprendedores"("id");
ALTER TABLE
    "reservas" ADD CONSTRAINT "reservas_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "pedidos" ADD CONSTRAINT "pedidos_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "donaciones" ADD CONSTRAINT "donaciones_emprendedor_id_foreign" FOREIGN KEY("emprendedor_id") REFERENCES "emprendedores"("id");
ALTER TABLE
    "donaciones" ADD CONSTRAINT "donaciones_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "pedido_items" ADD CONSTRAINT "pedido_items_producto_id_foreign" FOREIGN KEY("producto_id") REFERENCES "productos"("id");
ALTER TABLE
    "producto_fotos" ADD CONSTRAINT "producto_fotos_producto_id_foreign" FOREIGN KEY("producto_id") REFERENCES "productos"("id");
ALTER TABLE
    "pedidos" ADD CONSTRAINT "pedidos_emprendedor_id_foreign" FOREIGN KEY("emprendedor_id") REFERENCES "emprendedores"("id");
ALTER TABLE
    "usuario_roles" ADD CONSTRAINT "usuario_roles_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "emprendedores" ADD CONSTRAINT "emprendedores_aprobado_por_foreign" FOREIGN KEY("aprobado_por") REFERENCES "usuarios"("id");
ALTER TABLE
    "reservas" ADD CONSTRAINT "reservas_producto_id_foreign" FOREIGN KEY("producto_id") REFERENCES "productos"("id");
ALTER TABLE
    "usuario_roles" ADD CONSTRAINT "usuario_roles_rol_id_foreign" FOREIGN KEY("rol_id") REFERENCES "roles"("id");
ALTER TABLE
    "historial_puntos" ADD CONSTRAINT "historial_puntos_donacion_id_foreign" FOREIGN KEY("donacion_id") REFERENCES "donaciones"("id");
ALTER TABLE
    "emprendedores" ADD CONSTRAINT "emprendedores_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "puntos_donador" ADD CONSTRAINT "puntos_donador_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "transacciones" ADD CONSTRAINT "transacciones_usuario_id_foreign" FOREIGN KEY("usuario_id") REFERENCES "usuarios"("id");
ALTER TABLE
    "productos" ADD CONSTRAINT "productos_categoria_id_foreign" FOREIGN KEY("categoria_id") REFERENCES "categorias"("id");