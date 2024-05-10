create table carga_consolidada_cotizaciones_cabecera(
ID_Cotizacion int not null auto_increment,
Fe_Creacion date not null,
N_Cliente text,
Empresa text,
Cotizacion decimal(10,2),
ID_Tipo_Cliente int not null,
ID_Cotizacion_Estado int not null,
primary key(ID_Cotizacion),
foreign key(ID_Tipo_cliente) references tipo_cliente(ID_TIPO)
);

create table tipo_carga_consolidada_cotizaciones_status(
ID_Tipo int not null auto_increment,
Nombre varchar(50)
)
create table tipo_cliente(
ID_Tipo int not null auto_increment,
Nombre varchar(255),
primary key(ID_Tipo)
);
select * from carga_consolidada_cotizaciones_cabecera;
insert into tipo_cliente(Nombre) values
("Nuevo");
("Cliente"); 

create table carga_consolidada_cotizaciones_detalle(
ID_Detalle int not null auto_increment,
ID_Cotizacion int not null,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
primary key(ID_Detalle),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)
-- table carga_consolidada_cotizaciones_cabecera
-- ID_Cotizacion int auto increment , Fe_Creacion date ,Empresa text ,CotizacionPrecio decimal(10,2),T.Cliente foreign key 

-- table carga_consolidada_cotizaciones_detalle;
-- ID_Detalle,ID_Cotizacion,CBM_Total,Peso_Total
create table carga_consolidada_cotizaciones_detalle(
ID_Detalle int not null auto_increment,
ID_Cotizacion int not null,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
primary key(ID_Detalle),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)
-- table carga_consolidada_cotizaciones_detalles_proovedor;
-- ID_Proveedor,ID_Detalle,CBM_Total,Peso_Total,URL_Proforma,URL_Packing

create table carga_consolidada_cotizaciones_detalles_proovedor(
ID_Proveedor int not null auto_increment,
ID_Cotizacion int not null ,
CBM_Total decimal(10,2) not null,
Peso_Total decimal(10,2) not null,
URL_Proforma text,
URL_Packing text,
primary key(ID_Proveedor),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)

-- table carga_consolidada_cotizaciones_detalles_productos;
-- ID_Producto,ID_Cotizacion,ID_Proveedor,URL_Image,URL_Link,Nombre_Comercial,Uso,Cantidad,Valor_Unitario,
create table carga_consolidada_cotizaciones_detalles_producto(
ID_Producto int  not null auto_increment,
ID_Cotizacion int not null,
ID_Proveedor int not null,
URL_Image text not null,
URL_Link text,
Nombre_Comercial varchar(500) not null,
Uso text,
Cantidad decimal(10,2) not null,
Valor_unitario decimal(10,2) not null,
primary key(ID_Producto),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion),
foreign key(ID_Proveedor) references  carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor)
)
-- table carga_consolidada_cotizaciones_detalles_tributos;
-- ID_Tributo,ID_Producto,ID_Proveedor,Status
 
create table  carga_consolidada_cotizaciones_detalles_tributo(
ID_Tributo int not null auto_increment,
ID_Producto int not null,
ID_Proveedor int not null,
ID_Cotizacion int not null,
Status enum("Pending","Completed"),
primary key(ID_Tributo),
foreign key(ID_Producto) references carga_consolidada_cotizaciones_detalles_producto(ID_Producto),
foreign key(ID_Proveedor) references carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor),
foreign key(ID_Cotizacion) references carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
)
select * from carga_consolidada_cotizaciones_detalle_proveedor;
-- table tipo_carga_consolidada_cotizaciones_tributo
-- ID_Tipo_Tributo, Nombre
se
create table tipo_carga_consolidada_cotizaciones_tributo(
ID_Tipo_Tributo int not null auto_increment,
Nombre varchar(255) not null,
primary key(ID_Tipo_Tributo)
)